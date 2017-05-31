<?php
namespace AppBundle\Controller;

use AutoBundle\Controller\AbstractController;
use AutoBundle\Controller\EmailTrait;

use FOS\RestBundle\Controller\Annotations\FileParam;
use FOS\RestBundle\Controller\Annotations\View as ViewTemplate;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use EmailTrait;

    protected $entityName = 'Product';

    protected $acl = [
        'default' => 'ROLE_ADMIN',
        'list'    => 'ROLE_USER',
        'get'     => 'ROLE_USER',
        'post'    => 'ROLE_USER',
        'put'     => 'ROLE_USER',
        'patch'   => 'ROLE_USER',
        'delete'  => 'ROLE_USER',
    ];

    protected $orderable = ['status', 'name', 'origine', 'produit_biologique'];

    protected $filterable = ['shop_id'];

    /** @var null|array The name of the changed fields */
    private $original = null;

    public function init($type = 'default')
    {
        parent::init($type);

        /**
         * - Force user for non-admin
         */
        $this->dispatcher->addListener(
            'Product.onCreateBeforeSubmit',
            function (GenericEvent $event) {
                if ($this->isGranted('ROLE_ADMIN')) {
                    return;
                }

                /** @var \Symfony\Component\HttpFoundation\Request $request */
                if (!$request = $event->getArgument('request')) {
                    return;
                }

                $request->request->add(
                    [
                        'shop_id' => $this->getUser()->getShopId(),
                    ]
                );
            }
        );

        /**
         * - Send email when a product is created or updated
         */
        $this->dispatcher->addListener(
            'Product.onCreateAfterSave',
            function (GenericEvent $event) {
                /** @var \AppBundle\Entity\Product $entity */
                if (!$entity = $event->getArgument('entity')) {
                    return;
                }

                $title = 'Produit créé chez le commerçant ID : '.$entity['commercant'];
                $body  = 'Le produit suivant a été créé par '.$this->getUser()->getUsername().', le '.date("Y-m-d").' :'.PHP_EOL
                    .'SKU : '.$entity['sku'].PHP_EOL
                    .'Nom du produit : '.$entity['name'].PHP_EOL.PHP_EOL
                    .'Référence : '.$entity['reference_interne_magasin'].PHP_EOL
                    .'Disponible : '.($entity['status'] ? 'Oui' : 'Non').PHP_EOL
                    .'Sélection APDC : '.($entity['on_selection']  ? 'Oui' : 'Non').PHP_EOL
                    .'Prix : '.$entity['price'].PHP_EOL
                    .'Unit : '.$entity['unite_prix'].PHP_EOL
                    .'Description : '.$entity['short_description'].PHP_EOL
                    .'Poids portion : '.$entity['poids_portion'].PHP_EOL
                    .'Nombre portion : '.$entity['nbre_portion'].PHP_EOL
                    .'Tax : '.$entity['tax_class_id'].PHP_EOL
                    .'Origin : '.$entity['origine'].PHP_EOL
                    .'Bio : '.($entity['produit_biologique'] ? 'Oui' : 'Non').PHP_EOL
                ;

                $from = $this->getParameter('from_email');
                $to   = $this->getParameter('to_email');

                $message = $this->prepareEmail(
                    $title,
                    $body,
                    $from,
                    $to
                );

                if (!$result = $this->get('mailer')->send($message)) {
                    // TODO: Maybe log error later?
                    // TODO: Maybe we can also add some notification to the user
                }
            }
        );
        $this->dispatcher->addListener(
            'Product.onUpdateBeforeSave',
            function (GenericEvent $event) {
                if (!$entity = $event->getArgument('entity')) {
                    return;
                }

                $this->original = $entity;

                $this->getModel('ProductHistory')->addHistory($entity);
                die;
            }
        );
        $this->dispatcher->addListener(
            'Product.onUpdateAfterSave',
            function (GenericEvent $event) {
                if (!$entity = $event->getArgument('entity')) {
                    return;
                }

                $entity  = $entity->getData();
                $changes = array_diff($this->original, $entity);

                // $this->getModel('ProductHistory')->addHistory($entity);

                $photo = null;
//                $photo = $this->get('request_stack')->getMasterRequest()
//                    ->getUriForPath('/uploads/products/'.$entity->getId().'/'.$entity->getPhoto());

                $bodyChanges = [
                    'reference_interne_magasin' => 'Référence : ' . $entity['reference_interne_magasin'],
                    'status'                    => 'Disponible : ' . ($entity['status'] ? 'Oui' : 'Non'),
                    'on_selection'              => 'Sélection APDC : ' . ($entity['on_selection'] ? 'Oui' : 'Non'),
                    'price'                     => 'Prix : ' . $entity['price'],
                    'unite_prix'                => 'Unit : ' . $entity['unite_prix'],
                    'short_description'         => 'Description : ' . $entity['short_description'],
                    'poids_portion'             => 'Poids portion : ' . $entity['poids_portion'],
                    'nbre_portion'              => 'Nombre portion : ' . $entity['nbre_portion'],
                    'tax_class_id'              => 'Tax : ' . $entity['tax_class_id'],
                    'origine'                   => 'Origin : ' . $entity['origine'],
                    'produit_biologique'        => 'Bio : ' . ($entity['produit_biologique'] ? 'Oui' : 'Non'),
                    'photo'                     => 'Photo : ' . $photo,
                ];

                $title = 'Produit mise à jour chez le commerçant ID : '.$entity['commercant'];
                $body  = 'Le produit suivant a été mis à jour par '.$this->getUser()->getUsername().', le '.date("Y-m-d").' :'.PHP_EOL
                    .'SKU : '.$entity['sku'].PHP_EOL
                    .'Nom du produit : '.$entity['name'].PHP_EOL.PHP_EOL
                    .implode(PHP_EOL, array_intersect_key($bodyChanges, $changes))
                ;

                $from = $this->getParameter('from_email');
                $to   = $this->getParameter('to_email');

                $message = $this->prepareEmail(
                    $title,
                    $body,
                    $from,
                    $to
                );

                if (!$result = $this->get('mailer')->send($message)) {
                    // TODO: Maybe log error later?
                    // TODO: Maybe we can also add some notification to the user
                }
            }
        );
    }

    public function getModel($name = null, $setForm = true)
    {
        if (isset($name)) {
            // Note: Well, not good, if the needed model has a service like here, it will not work

            return parent::getModel($name, $setForm);
        }

        if (!isset($this->modelInstances['Product'])) {
            $this->modelInstances['Product'] = $this->get('apdc_apdc.repository.products');

            if ($setForm) {
                $this->modelInstances['Product']->setFormBuilder(
                    $this->get('form.factory')->createBuilder(
                        $this->makeForm('Product'),
                        null,
                        [
                            'attr' => ['id' => 'form-Main']
                        ]
                    )
                );
            }
        }

        return $this->modelInstances['Product'];
    }

    /**
     * @inheritdoc
     */
    protected function getFilterBy(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return parent::getFilterBy($request);
        }

        $filters = parent::getFilterBy($request);
        $filters['user'] = $this->getUser()->getId();

        return $filters;
    }
    // TODO : Security, check user on GET and PUT for non-admin

    /**
     * Set the status to false and send an email to the admin
     *
     * @inheritdoc
     *
     * @ViewTemplate()
     * @ApiDoc()
     */
    public function deleteAction($id, Request $request)
    {
        $this->init('delete');

        if (!$entity = $this->getModel()->find($id)) {
            return $this->notFound();
        }

        $title = 'Suppression d\'un produit chez'.$this->getUser()->getUsername();
        $body  = 'La suppression du produit suivant a été demandé par '.$this->getUser()->getUsername().', le '.date("Y-m-d").' :'.PHP_EOL
            .'ID : '.$entity['entity_id'].PHP_EOL
            .'SKU : '.$entity['sku'].PHP_EOL
        ;

        $from = $this->getParameter('from_email');
        $to   = $this->getParameter('to_email');

        $message = $this->prepareEmail(
            $title,
            $body,
            $from,
            $to
        );

        if (!$result = $this->get('mailer')->send($message)) {
            // TODO: Maybe log error later?
            // TODO: Maybe we can also add some notification to the user
        }

        $request->request->replace(['status' => false]);

        return $this->putPatch($id, $request, false);
    }

    /**
     * Patch an existing entity
     *
     * @param integer $id      The entity id
     * @param Request $request The Request
     *
     * @return object|Form|JsonResponse
     *
     * @FileParam(name="photoFile", nullable=true)
     * @ViewTemplate()
     * @ApiDoc()
     */
    public function postUploadAction($id, Request $request, ParamFetcher $paramFetcher)
    {
        $this->init('patch');

        $request->request->add($paramFetcher->all());

        return $this->putPatch($id, $request, false);
    }
}
