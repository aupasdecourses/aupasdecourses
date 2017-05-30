<?php
namespace AutoBundle\Helper;

use Doctrine\ORM\QueryBuilder;

class Paginator extends AbstractPaginator
{
    /** @var QueryBuilder */
    protected $qb;

    /**
     * @param QueryBuilder $queryBuilder
     * @param int          $limit
     * @param int          $offset
     * @param array        $options
     */
    public function __construct($queryBuilder, $limit = 20, $offset = 0, $options = [10, 20, 30, 50, 100, 500])
    {
        $this->qb = clone $queryBuilder;
        $this->qb->select('count(magic.id)');

        $this->limit  = $limit;
        $this->offset = $offset;

        $this->options = $options;

        $this->getTotalCount();
        $this->getSearchCount();
    }

    /**
     * @return int|mixed
     */
    public function getTotalCount()
    {
        if (!isset($this->totalCount)) {
            $reset            = clone $this->qb;
            $this->totalCount = $reset
                ->resetDQLPart('join')
                ->resetDQLPart('where')
                ->resetDQLPart('groupBy')
                ->getQuery()->getSingleScalarResult();
        }

        return $this->totalCount;
    }

    /**
     * @return int|mixed
     */
    public function getSearchCount()
    {
        if (!isset($this->searchCount)) {
            $reset             = clone $this->qb;
            $this->searchCount = $reset
                ->resetDQLPart('groupBy')
                ->getQuery()->getSingleScalarResult();
        }

        return $this->searchCount;
    }
}
