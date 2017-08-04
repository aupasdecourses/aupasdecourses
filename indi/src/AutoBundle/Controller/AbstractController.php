<?php
namespace AutoBundle\Controller;

use AutoBundle\Repository\AbstractRepository;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View as ViewTemplate;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Base Controller for classic crud
 */
abstract class AbstractController extends Controller implements ClassResourceInterface
{
    /** @var bool True if init() already done once */
    private $initiated = false;

    /** @var ContainerAwareEventDispatcher */
    protected $dispatcher;

    /** @var string */
    protected $bundleName = 'AppBundle';

    /** @var string */
    protected $entityName = null;

    /** @var array The ACL for each Action */
    protected $acl = [];

    /** @var array Default orderBy used in indexAction */
    protected $defaultOrder = ['id' => 'desc'];

    /** @var array Default filter used in indexAction */
    protected $defaultFilters = [];

    /** @var int */
    protected $defaultLimit = 20;

    /** @var null|array */
    protected $orderable = null;

    /** @var null|array */
    protected $filterable = null;

    /** @var boolean */
    protected $searchable = true;

    /** @var  AbstractRepository[] */
    protected $modelInstances = [];

    /** @var  FormBuilder */
    protected $form = null;

    /**
     * Replace __construct that doesn't exist in Symfony
     *
     * @param string $type
     */
    public function init($type = 'default')
    {
        if ($this->initiated) {
            return;
        }

        $this->checkAcl($type);

        $this->dispatcher = new ContainerAwareEventDispatcher($this->container);

        $this->initiated = true;
    }

    /**
     * Get the entity repository
     *
     * @param string $name
     * @param bool   $setForm
     *
     * @return AbstractRepository
     */
    public function getModel($name = null, $setForm = true)
    {
        if (!empty($name)) {
            $modelName = $name;
        } else {
            $modelName = $this->entityName;
        }

        if (!isset($this->modelInstances[$modelName])) {
            $this->modelInstances[$modelName] = $this->getDoctrine()->getManager()
                ->getRepository($this->bundleName.':'.$modelName);

            if ($setForm) {
                $this->modelInstances[$modelName]->setFormBuilder(
                    $this->get('form.factory')->createBuilder(
                        $this->makeForm($modelName),
                        null,
                        [
                            'attr' => ['id' => 'form-Main'],
                            'em'   => $this->getDoctrine()->getManager()
                        ]
                    )
                );
            }
        }

        return $this->modelInstances[$modelName];
    }

    /**
     * @param string $modelName
     *
     * @return string
     */
    protected function makeForm($modelName)
    {
        return $this->bundleName.'\Form\\'.$modelName.'Type';
    }

    /**
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        return $this->getModel()->getFormBuilder();
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->getModel()->getForm();
    }

    /**
     * List all entities
     *
     * @param Request $request The Request
     *
     * @return array
     *
     * @ViewTemplate()
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Start pagination offset")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, description="Number of entities to display")
     * @QueryParam(name="order", map=true, nullable=true, description="Sort order")
     *
     * @ApiDoc(
     *     output={"collection"=true}
     * )
     */
    public function cgetAction(Request $request)
    {
        $this->init('list');

        $search  = $this->getSearch($request);
        $filters = $this->getFilterBy($request);
        $orderBy = $this->getOrderBy($request);

        $limit  = $request->get('limit');
        $offset = $request->get('offset');

        if (!$limit && '0' !== $limit) {
            $limit = $this->defaultLimit;
        }
        if (!$offset) {
            $offset = 0;
        }

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->getModel(null, false);

        /** Check if repository implemented method */
        if (method_exists($repository, 'searchAndfindBy')) {
            $only = $request->get('_only');

            /** @var \AutoBundle\Repository\AbstractRepository $repository */
            $entities = $repository->searchAndfindBy(
                $search,
                $filters,
                $orderBy,
                $limit,
                $offset,
                $only
            );

            $paginator = $repository->getPaginator();
        } else {
            $entities = $repository->findBy(
                $filters,
                $orderBy,
                $limit,
                $offset
            );

            $paginator = null;
        }

        $total   = $paginator ? $paginator->getTotalCount() : null;
        $current = $paginator ? $paginator->getSearchCount() : null;

        $return = [
            'recordsTotal'    => (int) $total,
            'recordsFiltered' => (int) $current,
            'data'            => $entities,
        ];

        return $return;
    }

    /**
     * Return the search used in the list, based on Request and Session
     *
     * @param Request $request
     *
     * @return array|null
     */
    protected function getSearch(Request $request)
    {
        $search = $request->get('search');

        if (null !== $search) {
            if (!is_array($search)) {
                $search = ['value' => $search, 'type' => 'contains'];
            }

            if (!isset($search['type'])) {
                $search['type'] = 'contains';
            }
        }

        return $search;
    }

    /**
     * Return the filters array used in the list, based on Request and Session
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getFilterBy(Request $request)
    {
        if (isset($this->filterable)) {
            $filters = [];

            foreach ($this->filterable as $name) {
                $filter = $request->get($name);
                $filters[$name] = $filter;
            }

            $filters = array_filter(
                $filters,
                function ($v) {
                    return !(null == $v && '0' !== $v);
                }
            );
        } else {
            $filters = $this->defaultFilters;
        }

        return $filters;
    }

    /**
     * Return the orderBy array used in the list
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getOrderBy(Request $request)
    {
        if (isset($this->orderable)) {
            $sortBy = $request->get('sortBy');
            $sortDir = $request->get('sortDir');

            if (!in_array($sortDir, ['asc', 'desc'])) {
                $sortDir = 'asc';
            }

            if (in_array($sortBy, $this->orderable)) {
                $orderBy = [$sortBy => $sortDir];
            } else {
                $orderBy = $this->defaultOrder;
            }
        } else {
            $orderBy = $this->defaultOrder;
        }

        return $orderBy;
    }
    /** /Index */

    /**
     * Get entities count
     *
     * @param Request $request The Request
     *
     * @return int
     *
     * @ViewTemplate()
     * @ApiDoc()
     */
    public function getCountAction(Request $request)
    {
        $this->init('count');

        /** @var \AutoBundle\Repository\AbstractRepository $repository */
        $repository = $this->getModel(null, false);

        return $repository->count();
    }

    /**
     * Return an existing entity
     *
     * @param integer $id      The entity id
     * @param Request $request The Request
     *
     * @return array|object
     *
     * @ViewTemplate()
     * @ApiDoc(output={})
     */
    public function getAction($id, Request $request)
    {
        $this->init('get');

        if (!$entity = $this->getModel(null, false)->find($id)) {
            return $this->notFound();
        }

        return $entity;
    }

    /**
     * Save a new entity
     *
     * @param Request $request The Request
     *
     * @return object|\Symfony\Component\Form\Form|JsonResponse
     *
     * @ViewTemplate(statusCode=Response::HTTP_CREATED)
     * @ApiDoc(
     *     statusCodes = {
     *        201 = "Creation successful",
     *        400 = "Invalid form"
     *    }
     * )
     */
    public function postAction(Request $request)
    {
        $this->init('post');

        if (null === $entity = $this->getModel()->create()) {
            return $this->notFound();
        }

        $this->triggerEvent(
            'onCreateBeforeSubmit',
            [
                'entity'  => $entity,
                'request' => $request,
            ]
        );

        if ($this->getModel()->isValid($request, true, false)) {
            $this->triggerEvent(
                'onCreateBeforeSave',
                [
                    'entity' => $this->getModel()->getEntity()->toArray(),
                ]
            );

            $this->getModel()->save();

            $this->triggerEvent(
                'onCreateAfterSave',
                [
                    'entity' => $this->getModel()->getEntity()->toArray(),
                ]
            );

            return $this->getModel()->getEntity()->toArray(['entity_id'] + $request->request->keys());
        } else {
            return $this->getForm();
        }
    }

    /**
     * Save an existing entity
     *
     * @param integer $id      The entity id
     * @param Request $request The Request
     *
     * @return object|\Symfony\Component\Form\Form|JsonResponse
     *
     * @ViewTemplate()
     * @ApiDoc()
     */
    public function putAction($id, Request $request)
    {
        $this->init('put');

        return $this->putPatch($id, $request);
    }

    /**
     * Patch an existing entity
     *
     * @param integer $id      The entity id
     * @param Request $request The Request
     *
     * @return object|\Symfony\Component\Form\Form|JsonResponse
     *
     * @ViewTemplate()
     * @ApiDoc()
     */
    public function patchAction($id, Request $request)
    {
        $this->init('patch');

        return $this->putPatch($id, $request, false);
    }

    /**
     * Method to manage put or path entity
     *
     * @param integer $id
     * @param Request $request
     * @param bool    $clearMissing
     *
     * @return object|\Symfony\Component\Form\Form|JsonResponse
     */
    protected function putPatch($id, Request $request, $clearMissing = true)
    {
        if (!$entity = $this->getModel()->find($id)) {
            return $this->notFound();
        }

        $this->triggerEvent(
            'onUpdateBeforeSubmit',
            [
                'entity'  => $entity,
                'request' => $request,
            ]
        );

        if ($this->getModel()->isValid($request, $clearMissing, false)) {
            $this->triggerEvent(
                'onUpdateBeforeSave',
                [
                    'entity' => $entity,
                ]
            );

            $this->getModel()->save();

            $this->triggerEvent(
                'onUpdateAfterSave',
                [
                    'entity' => $this->getModel()->getEntity(),
                ]
            );

            return $entity;
        } else {
            return $this->getForm();
        }
    }

    /**
     * Delete an entity
     *
     * @param integer $id      The entity id
     * @param Request $request The Request
     *
     * @return void
     *
     * @ViewTemplate(statusCode=Response::HTTP_NO_CONTENT)
     * @ApiDoc()
     */
    public function deleteAction($id, Request $request)
    {
        $this->init('delete');

        $em = $this->getDoctrine()->getManager();

        if ($entity = $this->getModel(null, false)->find($id)) {
            $em->remove($entity);
            $em->flush();
        }
    }

    /**
     * Return a PDF version of the entity
     *
     * @param $id
     * @param $request
     *
     * @return Response
     *
     * @ApiDoc()
     */
    public function getPdfAction($id, Request $request)
    {
        $this->init('pdf');

        $html = $this->returnPrint($id, $request);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$this->entityName.'.pdf"',
            ]
        );
    }

    /**
     * Return a print version of the entity
     *
     * @param $id
     * @param $request
     *
     * @return Response
     *
     * @ApiDoc()
     */
    public function getPrintAction($id, Request $request)
    {
        $this->init('print');

        $html = $this->returnPrint($id, $request);

        return new Response($html);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return string
     */
    protected function returnPrint($id, Request $request)
    {
        // TODO
    }

    /**
     * @param Request $request
     *
     * @return StreamedResponse
     *
     * @ApiDoc()
     */
    public function exportAction(Request $request)
    {
        $this->init('export');

        // http://obtao.com/blog/2013/12/export-data-to-a-csv-file-with-symfony/
        ini_set('max_execution_time', 300);

        $container = $this->container;

        $options = null;

        $response = new StreamedResponse(function () use ($container, $request) {
            $em = $this->getDoctrine()->getManager();

            $search  = $this->getSearch($request);
            $filters = $this->getFilterBy($request);
            $orderBy = $this->getOrderBy($request);

            // The getExportQuery method returns a query that is used to retrieve
            // all the objects (lines of your csv file) you need. The iterate method
            // is used to limit the memory consumption
            $results = $this->getModel(null, false)->searchAndfindQuery(
                $search,
                $filters,
                $orderBy
            )->iterate();

            $handle = fopen('php://output', 'r+');

            while (false !== ($row = $results->next())) {
                // add a line in the csv file. You need to implement a toArray() method
                // to transform your object into an array
                fputcsv($handle, $row[0]->toArray());
                // used to limit the memory consumption
                $em->detach($row[0]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$this->entityName.'.csv"');

        return $response;
    }

    /**
     * Return a 404 if the entity is not found
     *
     * @return static
     */
    protected function notFound()
    {
        return View::create(['message' => 'Entity not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @param string $name
     */
    protected function checkAcl($name = 'default')
    {
        if ($this->acl) {
            if (isset($this->acl[$name])) {
                $this->denyAccessUnlessGranted($this->acl[$name]);
            } elseif (isset($this->acl['default'])) {
                $this->denyAccessUnlessGranted($this->acl['default']);
            }
        }
    }

    /**
     * Trigger an event
     *
     * @param string $event
     * @param array  $arguments
     */
    protected function triggerEvent($event, array $arguments = [])
    {
        $generic   = new GenericEvent($event, $arguments);
        $eventName = $this->entityName.'.'.$event;

        $this->dispatcher->dispatch($eventName, $generic);
    }

    /**
     * Get the env
     *
     * @return string
     */
    protected function getEnv()
    {
        return $this->get('kernel')->getEnvironment();
    }

    /**
     * @return string
     */
    public function getNSEntity()
    {
        return 'AppBundle\Entity\\'.$this->entityName;
    }
}
