<?php

namespace AutoBundle\Repository;

use Apdc\ApdcBundle\Services\Magento;
use AutoBundle\Helper\MagePaginator as Paginator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract Mage Repository
 */
abstract class AbstractMageRepository
{
    /**
     * @var Magento
     */
    protected $mage;

    /**
     * @var object
     */
    protected $model;

    /**
     * @var string
     */
    protected $modelName;

    protected $entity;

    /** @var  FormBuilder */
    protected $formBuilder;

    /** @var  Form */
    protected $form;

    /** @var   array  Table field name aliases, defined as aliasFieldName => actualFieldName */
    protected $aliasFields = [];

    /** @var  Paginator */
    protected $paginator;

    /** @var array Array alreay join table */
    protected $withs = [];

    protected $orderWithjoin = null;

    /**
     * AbstractMageRepository constructor.
     *
     * @param $mage
     */
    public function __construct(Magento $mage)
    {
        $this->mage  = $mage;
        $this->model = $mage->getModel($this->modelName);
    }

    /**
     * @param FormBuilder $form
     *
     * @return $this
     */
    public function setFormBuilder(FormBuilder $form)
    {
        $this->formBuilder = $form;

        return $this;
    }

    /**
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function find($id)
    {
        $qb = $this->model->load($id);

        if (!$qb) {
            return null;
        }

        $this->entity = $qb;

        return $this->entity->toArray();
    }

    /**
     * @return void
     */
    public function save()
    {
        \Mage::app()->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);

        foreach ($this->form->getData() as $key => $data) {
            $this->entity->setData($key, $data);
        }

        $this->entity->save();
    }

    /**
     * Check if the Request is valid using the Form
     *
     * @param Request $request
     * @param bool    $clearMissing
     *
     * @return bool
     */
    public function isValid(Request $request, $clearMissing = false)
    {
        $entity = $this->entity->toArray();

        $this->form = $this->formBuilder
            ->setData($entity)
            ->getForm();

        return $this->form
            ->submit($request->request->all(), $clearMissing)
            ->isValid();
    }

    /**
     * Get the current entity
     *
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the real name of a field name based on its alias.
     * If the field is not aliased $alias is returned
     *
     * @param   string $alias The field to get an alias for
     *
     * @return  string  The real name of the field
     */
    public function getFieldAlias($alias)
    {
        if (array_key_exists($alias, $this->aliasFields)) {
            return $this->aliasFields[$alias];
        } else {
            return $alias;
        }
    }

    /**
     * Search, filter, order and create the pagination
     *
     * @param string $search
     * @param array  $filters
     * @param array  $orderBy
     * @param int    $limit
     * @param int    $offset
     * @param mixed  $only
     *
     * @return array
     */
    public function searchAndfindBy(
        $search = null,
        $filters = [],
        $orderBy = [],
        $limit = null,
        $offset = null,
        $only = null
    ) {
        $return = [];

        if ($items = $this->searchAndfindQuery($search, $filters, $orderBy, $limit, $offset, $only)) {
            foreach ($items as $item) {
                $return[] = $item->toArray();
            }
        }

        return $return;
    }

    /**
     * @param string $search
     * @param array  $filters
     * @param array  $orderBy
     * @param int    $limit
     * @param int    $offset
     * @param mixed  $only
     *
     * @return \Doctrine\ORM\Query
     */
    public function searchAndfindQuery(
        $search = null,
        $filters = [],
        $orderBy = [],
        $limit = null,
        $offset = null,
        $only = null
    ) {
        $qb = $this->model->getCollection();

        if ($only) {
            if (!is_array($only)) {
                $only = [$only];
            }

            $qb->addAttributeToSelect(implode(', ', $only));
        } else {
            $qb->addAttributeToSelect('*');
        }

        if (!empty($search['value'])) {
            $utf8 = [
                '/[áàâãªä]/u' => 'a',
                '/[ÁÀÂÃÄ]/u'  => 'A',
                '/[ÍÌÎÏ]/u'   => 'I',
                '/[íìîï]/u'   => 'i',
                '/[éèêë]/u'   => 'e',
                '/[ÉÈÊË]/u'   => 'E',
                '/[óòôõºö]/u' => 'o',
                '/[ÓÒÔÕÖ]/u'  => 'O',
                '/[úùûü]/u'   => 'u',
                '/[ÚÙÛÜ]/u'   => 'U',
                '/ç/'         => 'c',
                '/Ç/'         => 'C',
                '/ñ/'         => 'n',
                '/Ñ/'         => 'N',
            ];

            $val = preg_replace(array_keys($utf8), array_values($utf8), $search['value']);
            $val = strtolower($val);

            switch ($search['type']) {
                case 'startWith':
                    $val = $val . '%';
                    break;
                case 'endWith':
                    $val = '%' . $val;
                    break;
                case 'exactWord':
                    $val = $val;
                    break;
                case 'contains':
                    $val = '%' . $val . '%';
                    break;
                default:
                    $val = '%' . $val . '%';
            }

            $this->searchQuery($val, $qb);
        }

        $this->filtersQuery($filters, $qb);

        if ((isset($offset)) && (isset($limit))) {
            $this->paginator = new Paginator($qb, $limit, $offset);

            if ($limit > 0) {
                $page = 1 + ($offset / $limit);

                $qb->setPageSize($limit);
                $qb->setCurPage($page);
            }
        }

        foreach ($orderBy as $key => $value) {
            if (isset($this->orderWithjoin) && array_key_exists($key, $this->orderWithjoin)) {
                // $qb->add('orderBy', $this->orderWithjoin[$key] . ' ' . $value); TODO: Reimplement
            } else {
                $qb->addAttributeToSort($key, $value);
            }
        }

        return $qb;
    }

    /**
     * @param string $search
     * @param \Doctrine\ORM\QueryBuilder $qb
     *
     * @return void
     */
    protected function searchQuery($search, $qb)
    {
        //
    }

    /**
     * @param                            $filters
     * @param \Doctrine\ORM\QueryBuilder $qb
     *
     * @return void
     */
    protected function filtersQuery($filters, $qb)
    {
        if (!empty($filters)) {
            foreach ($filters as $name => $filter) {
                if (is_array($filter)) {
                    $qb->addFieldToFilter($name, ['in' => $filter]);
                } else {
                    $qb->addFieldToFilter($name, $filter);
                }
            }
        }
    }

    /**
     * Manage Relation +1, mainly used for filters
     *
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param string                     $relation
     * @param string|null                $alias
     * @param string|null                $groupBy
     */
    protected function with($qb, $relation, $alias = null, $groupBy = null)
    {
        // TODO: reimplement in needed
        return;

        if (!$alias) {
            //TODO: generate alias based on relation name (eg. magic.revisions -> revisions or magicRevisions)
        }

        if (isset($this->withs[$alias])) {
            return;
        }

        $qb->leftJoin($relation, $alias);

        if (isset($groupBy)) {
            $qb->groupBy($groupBy);
        }

        $this->withs[$alias] = 1;
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        if (!isset($this->paginator)) {
            $this->paginator = new Paginator($this->model->getCollection());
        }

        return $this->paginator;
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->model->getCollection()->getSize;
    }
}
