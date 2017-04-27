<?php
/*
* @author Pierre Mainguet
*/
class Apdc_Delivery_Model_Indi_Billingsummary extends Mage_Core_Model_Abstract{
    
    protected function _construct()
    {
        $this->_init('pmainguet_delivery/indi_billingsummary');
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getIncrementId()) {
            $incrementId = Mage::getSingleton('eav/config')
                ->getEntityType('billing')
                ->fetchNewIncrementId($this->getStoreId());
            $this->setIncrementId($incrementId);
        }

        return $this;
    }


}