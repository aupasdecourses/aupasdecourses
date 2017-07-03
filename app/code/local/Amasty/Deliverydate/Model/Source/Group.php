<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */
class Amasty_Deliverydate_Model_Source_Group
{
    public function toOptionArray()
    {
        return Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
    }
}
