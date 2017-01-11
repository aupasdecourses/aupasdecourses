<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */
class Amasty_Deliverydate_Block_Sales_Order_Info_Deliverydate extends Amasty_Deliverydate_Block_Sales_Order_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/amdeliverydate/order.phtml');
    }
}