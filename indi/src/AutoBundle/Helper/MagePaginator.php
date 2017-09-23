<?php
namespace AutoBundle\Helper;

use Doctrine\ORM\QueryBuilder;

class MagePaginator extends AbstractPaginator
{
    /** @var object */
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
            $this->totalCount = $this->qb->getSize();
        }

        return $this->totalCount;
    }

    /**
     * @return int|mixed
     */
    public function getSearchCount()
    {
        if (!isset($this->searchCount)) {
            $this->searchCount = $this->qb->getSize();
        }

        return $this->searchCount;
    }
}
