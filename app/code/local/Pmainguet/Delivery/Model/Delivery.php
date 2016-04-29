<?php
class Pmainguet_Delivery_Model_Delivery extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('pmainguet_delivery/refund_order');
     }

}