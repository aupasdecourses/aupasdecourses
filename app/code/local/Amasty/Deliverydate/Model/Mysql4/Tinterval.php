<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */  
class Amasty_Deliverydate_Model_Mysql4_Tinterval extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amdeliverydate/tinterval', 'tinterval_id');
    }
}