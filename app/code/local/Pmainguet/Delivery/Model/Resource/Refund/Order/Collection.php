<?php

class Pmainguet_Delivery_Model_Resource_Refund_Order_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('pmainguet_delivery/refund_order');
    }
}