<?php

/**
 * Class Apdc_Commercant_Model_Api2_Commercant_Rest_Admin_V1
 */
class Apdc_Commercant_Model_Api2_Commercant_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{
    protected function _retrieve()
    {
        $id = $this->getRequest()->getParam('id');

        $commercant = Mage::getModel('apdc_commercant/commercant')->load($id);

        if (!$commercant->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $commercant->__toArray();
    }

    protected function _retrieveCollection()
    {
        $collection = Mage::getModel('apdc_commercant/commercant')->getCollection();
        $collection->addFieldToSelect(
            array_keys(
                $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
            )
        );
        return $collection->load()->toArray();
    }

    protected function _create($filteredData)
    {
        $commercant = Mage::getModel('apdc_commercant/commercant');
        $commercant->setData($filteredData);
        $commercant->save();
    }

    protected function _update($filteredData)
    {
        $id = $this->getRequest()->getParam('id');

        $commercant = Mage::getModel('apdc_commercant/commercant')->load($id);

        if (!$commercant->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        foreach ($filteredData as $key => $value) {
            $commercant->setData($key, $value);
        }
        $commercant->save();
    }

    protected function _delete()
    {
        $id = $this->getRequest()->getParam('id');

        $commercant = Mage::getModel('apdc_commercant/commercant')->load($id);

        if (!$commercant->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        $commercant->delete();
    }
}
