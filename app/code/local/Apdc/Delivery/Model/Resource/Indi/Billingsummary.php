<?php
/*
* @author Pierre Mainguet
*/
class Apdc_Delivery_Model_Resource_Indi_Billingsummary extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('pmainguet_delivery/indi_billingsummary', 'id');
    }
}