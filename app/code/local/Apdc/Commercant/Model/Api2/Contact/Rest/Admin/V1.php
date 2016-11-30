<?php

/**
 * Class Apdc_Commercant_Model_Api2_Contact_Rest_Admin_V1
 */
class Apdc_Commercant_Model_Api2_Contact_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{
    protected function _retrieve()
    {
        $id = $this->getRequest()->getParam('id');

        $contact = Mage::getModel('apdc_commercant/contact')->load($id);

        if (!$contact->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $contact->__toArray();
    }

    protected function _retrieveCollection()
    {
        $collection = Mage::getModel('apdc_commercant/contact')->getCollection();
        $collection->addFieldToSelect(
            array_keys(
                $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
            )
        );
        return $collection->load()->toArray();
    }

    protected function _create($filteredData)
    {
        $contact = Mage::getModel('apdc_commercant/contact');
        $contact->setData($filteredData);
        $contact->save();
    }

    protected function _update($filteredData)
    {
        $id = $this->getRequest()->getParam('id');

        $contact = Mage::getModel('apdc_commercant/contact')->load($id);

        if (!$contact->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        foreach ($filteredData as $key => $value) {
            $contact->setData($key, $value);
        }
        $contact->save();
    }

    protected function _delete()
    {
        $id = $this->getRequest()->getParam('id');

        $contact = Mage::getModel('apdc_commercant/contact')->load($id);

        if (!$contact->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        $contact->delete();
    }
}
