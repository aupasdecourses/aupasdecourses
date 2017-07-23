<?php
namespace AutoBundle\Helper;

abstract class AbstractPaginator
{
    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var int */
    protected $totalCount;

    /** @var int */
    protected $searchCount;

    /** @var int */
    protected $currentPage;

    /** @var int */
    protected $totalPages;

    /** @var array */
    protected $options;

    /**
     * @param $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param $offset
     *
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param $total
     *
     * @return $this
     */
    public function setTotalCount($total)
    {
        $this->totalCount = $total;

        return $this;
    }

    /**
     * @return int|mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param $total
     *
     * @return $this
     */
    public function setSearchCount($total)
    {
        $this->searchCount = $total;

        return $this;
    }

    /**
     * @return int|mixed
     */
    public function getSearchCount()
    {
        return $this->searchCount;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        if (!$this->currentPage) {
            $this->currentPage = ($this->offset / $this->limit) + 1;
        }

        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        if (!$this->totalPages) {
            if (($this->limit == 0) || ($this->limit > $this->searchCount)) {
                $this->totalPages = 1;
            } else {
                $this->totalPages = ceil($this->searchCount / $this->limit);
            }
        }

        return $this->totalPages;
    }

    /**
     * @param $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
