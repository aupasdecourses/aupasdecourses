<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Block_Product_View_Type_Configurable 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Cybage
 * @uses     Cybage_Swatches_Block_Catalog_Product_View_Type_Configurable
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Product_View_Type_Configurable extends Cybage_Swatches_Block_Catalog_Product_View_Type_Configurable
{
    public function getJsonConfig() {
        $jsonConfig = parent::getJsonConfig();
        $config = Zend_Json::decode($jsonConfig);
        $swatchesIds = array();
        if (Mage::helper('swatches')->getOptionsImageSize()){
             $config['swatches_size_list'] = Mage::helper('swatches')->getOptionsImageSize();
        }
        foreach ($this->getAllowProducts() as $product) {
            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                if (isset($config['attributes'][$productAttribute->getId()])) {
                    $config['attributes'][$productAttribute->getId()]['default_value'] = $productAttribute->getDefaultValue();
                }
            }
        }
        return Zend_Json::encode($config);
    }
}
