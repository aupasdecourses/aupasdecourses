<?php
/**
 * @category   Apdc Modules
 * @package    Apdc_DisableCompare
 * @copyright  Pierre Mainguet
 */
class Apdc_DisableCompare_Helper_Compare extends Mage_Catalog_Helper_Product_Compare
{
    /**
     * Retrieve url for adding product to compare list
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  string
     */
    public function getAddUrl($product)
    {
        return '';
    }
}