<?php

class Apdc_Catalog_Model_Resource_Adminhtml_Request extends Mage_Eav_Model_Entity_Abstract
{
    public function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_product');
        $this->setConnection(
            $resource->getConnection('core_read'),
            $resource->getConnection('core_wire')
        );
    }
}
