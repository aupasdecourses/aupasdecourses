<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Dataflow
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Dataflow_Model_Catalog_Convert_Adapter_Product 
 * 
 * @category Apdc
 * @package  Dataflow
 * @uses     Mage
 * @uses     Mage_Catalog_Model_Convert_Adapter_Product
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Dataflow_Model_Catalog_Convert_Adapter_Referentiel extends Mage_Catalog_Model_Convert_Adapter_Product
{
    public function __construct()
    {
        parent::__construct();
        $filters = Mage::app()->getRequest()->getParam('filters');
        $attrFilterArray = [];
        if (is_array($filters) && !empty($filters)) {
            foreach ($filters as $attributeCode => $value) {
                if ($value && $value != '') {
                    if ($attributeCode == 'store') {
                        $this->setVar($attributeCode, $value);
                    } else {
                        $attrFilterArray[$attributeCode] = 'eq';
                        $this->setVar('filter/' . $attributeCode, $value);
                    }
                }
            }
        }
        if (!empty($attrFilterArray)) {
            $attrToDb = array(
                'type'          => 'type_id',
                'attribute_set' => 'attribute_set_id'
            );
            parent::setFilter($attrFilterArray, $attrToDb);
        }
    }
}
