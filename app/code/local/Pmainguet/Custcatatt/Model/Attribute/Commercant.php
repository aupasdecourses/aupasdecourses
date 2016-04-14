<?php
class Pmainguet_Custcatatt_Model_Attribute_Commercant extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
     $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','commercant');
     $collection =Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setPositionOrder('asc')
                    ->setAttributeFilter($attributeId)
                    ->setStoreFilter(1)
                    ->load();
    $options=$collection->toOptionArray();
    if ($withEmpty) array_unshift($options, array('label' => '', 'value' => '0'));
    return $options;
    }
}