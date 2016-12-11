<?php

/**
 * Class Apdc_Commercant_Model_Api2_Product_Rest_Admin_V1
 */
class Apdc_Commercant_Model_Api2_Product_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Rest_Admin_V1
{
    /**
     * Retrieve list of products
     * Override allows to filter the collection with the commercant attribute
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $store = $this->_getStore();
        $collection->setStoreId($store->getId());
        $collection->addAttributeToSelect(array_keys(
                                              $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
                                          ));
        $this->_applyCategoryFilter($collection);
        $this->_applyCollectionModifiers($collection);

        // filter with commercant attribute
        $commercant = $this->getRequest()->getParam('commercant');
        if ($commercant) {
            $collection->addAttributeToFilter('commercant', $commercant);
        }

        $products = $collection->load()->toArray();
        return $products;
    }
}
