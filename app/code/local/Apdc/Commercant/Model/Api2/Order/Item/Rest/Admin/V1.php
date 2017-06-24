<?php

/**
 * Class Apdc_Commercant_Model_Api2_Order_Item_Rest_Admin_V1
 */
class Apdc_Commercant_Model_Api2_Order_Item_Rest_Admin_V1 extends Mage_Sales_Model_Api2_Order_Item_Rest_Admin_V1
{
    /**
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        $collection = parent::_getCollectionForRetrieve();

        $commercant = $this->getRequest()->getParam('commercant');
        if ($commercant) {
            $collection->addAttributeToFilter('commercant', $commercant);
        }

        return $collection;
    }
}
