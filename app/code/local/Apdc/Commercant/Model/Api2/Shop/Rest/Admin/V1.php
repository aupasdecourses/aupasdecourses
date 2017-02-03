<?php

/**
 * Class Apdc_Commercant_Model_Api2_Shop_Rest_Admin_V1
 */
class Apdc_Commercant_Model_Api2_Shop_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{
    protected function _retrieve()
    {
        $id = $this->getRequest()->getParam('id');

        $shop = Mage::getModel('apdc_commercant/shop')->load($id);

        if (!$shop->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $shop->__toArray();
    }

    protected function _retrieveCollection()
    {
        $collection = Mage::getModel('apdc_commercant/shop')->getCollection();
        $collection->addFieldToSelect(
            array_keys(
                $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
            )
        );
        return $collection->load()->toArray();
    }

    protected function _create($filteredData)
    {
        $shop = Mage::getModel('apdc_commercant/shop');
        $shop->setData($filteredData);
        $shop->save();
    }

    protected function _update($filteredData)
    {
        $id = $this->getRequest()->getParam('id');

        $shop = Mage::getModel('apdc_commercant/shop')->load($id);

        if (!$shop->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        foreach ($filteredData as $key => $value) {
            $shop->setData($key, $value);
        }
        $shop->save();
    }

    protected function _delete()
    {
        $id = $this->getRequest()->getParam('id');

        $shop = Mage::getModel('apdc_commercant/shop')->load($id);

        if (!$shop->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        $shop->delete();
    }
}
