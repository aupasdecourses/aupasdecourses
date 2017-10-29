<?php

class Apdc_Catalog_Model_Resource_Adminhtml_Request_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
	protected $_sql;

    public function __construct($sql=null)
    {
        parent::__construct();
        $this->_sql=$sql;
    }

    protected function _construct($sql=null)
    {
        $this->_init('apdc_catalog/adminhtml_request');
    }

    protected function selectIllegalCharactersActive(){
        $this->_select=$this->getConnection()->select()
        ->from(array('table1' => 'catalog_product_entity'),
            array('table1.entity_id', 'table1.sku'))
        ->joinLeft(array('table2'=>'catalog_product_entity_int'),'table1.entity_id = table2.entity_id')
        ->joinLeft(array('table3'=>'eav_attribute'),'table2.attribute_id = table3.attribute_id')
        ->where('table3.attribute_code = ?', 'status')
        ->where('value = ?', 1)
        ->where("sku <> CONVERT(sku USING ASCII) OR sku LIKE '% %' OR sku REGEXP '\r\n' OR sku REGEXP '\n' OR sku NOT REGEXP '[[:<:]]([[:alnum:]]{1,}-[[:alnum:]]{1,}-[[:alnum:]]{1,})[[:>:]]'");
    }

    public function getData($select=null)
    {
        if ($this->_data === null) {
            $this->_renderFilters()
                 ->_renderOrders()
                 ->_renderLimit();

            if(!is_null($select)){
                switch($this->_sql){
                    case 'illegal_characters_active':
                        $this->selectIllegalCharactersActive();
                        break;
                    default:
                        break;
                }
            }

            if ($this->_pageSize) {
                $this->getSelect()->limitPage($this->getCurPage(), $this->_pageSize);
            }

            $query = $this->_prepareSelect($this->getSelect());
            $this->_data = $this->_fetchAll($query);
            $this->_afterLoadData();
        }
        return $this->_data;
    }

    public function limitPage(){
    	$page     = ($this->getCurPage() > 0)     ? $this->getCurPage()     : 1;
        $rowCount = ($this->getCurPage() > 0) ? $this->_pageSize : 1;
        return ' LIMIT '.(int) $rowCount * ($page - 1).','.(int) $rowCount;
    }

        /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        $sql = $this->getSelectCountSql();
        $this->_totalRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
        return intval($this->_totalRecords);
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_beforeLoad();

        $this->_renderFilters();
        $this->_renderOrders();

        $this->printLogQuery($printQuery, $logQuery);

        $data = $this->getData($this->_sql);
        $this->resetData();

        foreach ($data as $v) {
            $object = $this->getNewEmptyItem()
                ->setData($v);
            $this->addItem($object);
            if (isset($this->_itemsById[$object->getId()])) {
                $this->_itemsById[$object->getId()][] = $object;
            } else {
                $this->_itemsById[$object->getId()] = array($object);
            }
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }

}