<?php
/*
* @author Pierre Mainguet
*/
class Apdc_Delivery_Model_Resource_Refund_Items extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('pmainguet_delivery/refund_items', 'refund_item_id');
    }
}