<?php
namespace AutoBundle\Repository;

use AutoBundle\Helper\Paginator;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Magic Repository
 */
abstract class AbstractRepository extends EntityRepository
{
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

    /** @var null|array Order from relations */
    protected $orderWiths = null;

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
     * @param integer $id
     *
     * @return $this
     */
    public function load($id)
    {
        $this->find($id, LockMode::NONE, null);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $this->entity = parent::find($id, $lockMode, $lockVersion);

        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $entityName = $this->getEntityName();
        $this->entity = new $entityName();

        return $this->entity;
    }

    /**
     * @return void
     */
    public function save()
    {
        $em = $this->getEntityManager();

        $em->merge($this->entity);
        $em->flush();
    }

    /**
     * Check if the Request is valid using the Form
     *
     * @param Request $request
     * @param bool    $clearMissing
     * @param bool    $clone
     *
     * @return bool
     */
    public function isValid(Request $request, $clearMissing = false, $clone = true)
    {
        $entity = is_object($this->entity) && $clone ? clone $this->entity : $this->entity;

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
        return $this->searchAndfindQuery($search, $filters, $orderBy, $limit, $offset, $only)->getResult();
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
        $qb = $this->createQueryBuilder('magic');

        if ($only) {
            if (!is_array($only)) {
                $only = [$only];
            }

            $qb->select('magic.'.implode(', magic.', $only));
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
                    $val = $val.'%';
                    break;
                case 'endWith':
                    $val = '%'.$val;
                    break;
                case 'exactWord':
                    break;
                case 'contains':
                    $val = '%'.$val.'%';
                    break;
                default:
                    $val = '%'.$val.'%';
            }

            $this->searchQuery($val, $qb);
        }

        $this->filtersQuery($filters, $qb);

        if ((isset($offset)) && (isset($limit))) {
            $this->setPaginator($qb, $limit, $offset);

            if ($limit > 0) {
                $qb->setFirstResult($offset);
                $qb->setMaxResults($limit);
            }
        }

        foreach ($orderBy as $key => $value) {
            if (isset($this->orderWiths) && array_key_exists($key, $this->orderWiths)) {
                $qb->add('orderBy', $this->orderWiths[$key].' '.$value);
            } else {
                $qb->add('orderBy', 'magic.'.$key.' '.$value);
            }
        }

        return $qb->getQuery();
    }

    /**
     * @param string                     $search
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
                    $qb->andWhere('magic.'.$name.' IN ('.implode(',', $filter).')');
                } elseif (!isset($filter)) {
                    $qb->andWhere('magic.'.$name.' IS NULL');
                } else {
                    $qb->andWhere('magic.'.$name.' = \''.$filter.'\'');
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
        if (!$alias) {
            //TODO: generate alias based on relation name (eg. magic.revisions -> revisions or magicRevisions)
        }

        $allAlias = $qb->getAllAliases();

        if (in_array($alias, $allAlias)) {
            return;
        }

        $qb->leftJoin($relation, $alias);

        if (isset($groupBy)) {
            $qb->groupBy($groupBy);
        }
    }

    /**
     * @param $qb
     * @param $limit
     * @param $offset
     *
     * @return $this
     */
    public function setPaginator($qb, $limit, $offset)
    {
        $this->paginator = new Paginator($qb, $limit, $offset);

        return $this;
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        if (!isset($this->paginator)) {
            $this->paginator = new Paginator($this->createQueryBuilder('magic'));
        }

        return $this->paginator;
    }

    /**
     * @return mixed
     */
    public function count()
    {
        $count = $this->createQueryBuilder('magic')
            ->select('count(magic.'.$this->getFieldAlias('id').')')
            ->setMaxResults(1)
            ->getQuery()->getSingleScalarResult();

        return $count;
    }
}
