<?php

class Apdc_Delivery_Model_Entity_Increment extends Mage_Eav_Model_Entity_Increment_Abstract{

    const STEP = 1;

    protected $_first_store_id;

    public function getLastStoreId(){
        if(!isset($this->_first_store_id)){
            $this->_first_store_id = end(Mage::app()->getStores())->getStoreId();
        }
        return $this->_first_store_id;
    }

    private function getPrefixFromOrder(){
        $productEntityType = Mage::getModel('eav/entity_type')
            ->loadByCode('order');
        $entityStoreConfig = Mage::getModel('eav/entity_store')
            ->loadByEntityStore($productEntityType->getId(), $this->getLastStoreId());
        $prefix=$entityStoreConfig->setEntityTypeId($productEntityType->getId())->getIncrementPrefix();
        return $prefix;
    }

    public function format($id)
    {
        $result = $this->getPrefixFromOrder();
        $result.= str_pad((string)$id, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);
        return $result;
    }

    public function getNextId()
    {
        $last = $this->getLastId();
        if (strpos($last, $this->getPrefixFromOrder()) === 0) {
            $last = (int)substr($last, strlen($this->getPrefixFromOrder()));
        } else {
            $last = (int)$last;
        }
        $next = $last + self::STEP;
        return $this->format($next);
    }

}