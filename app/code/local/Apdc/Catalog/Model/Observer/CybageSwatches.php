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
 * Apdc_Catalog_Model_Observer_CybageSwatches 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Cybage
 * @uses     Cybage_Swatches_Model_Observer
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Observer_CybageSwatches extends Cybage_Swatches_Model_Observer
{
     /* @return  list page swatches */

    public function onListBlockHtmlBefore($observer)    
    {
        $block              = $observer->getBlock();
        $transport          = $observer->getTransport();
        $html = $transport->getHtml();
        if (($block instanceof Mage_Catalog_Block_Product_List) && Mage::getStoreConfig('swatches/list/swatches_on_list')) {
            
            $collection = clone $block->getLoadedProductCollection();
        
            foreach ($collection->getItems() as $item) {
                $productsIdList[] = $item->getEntityId();
            }
            $collectionProduct = Mage::getResourceModel('catalog/product_collection')
                                ->addFieldToFilter('entity_id', $productsIdList)
                                ->addAttributeToSelect('*');
   
            foreach($collectionProduct as $product){  
                if($product->isSaleable() && $product->isConfigurable()){
                    $template = '@(product-price-'.$product->getId().'">(.*?)div>)@s';
                    preg_match_all($template, $html, $res);
                    if($res[0]){
                         $replace =  Mage::helper('swatches')->getSwatchesBlock($product, $res[1][0]);
                         if(strpos($html, $replace) === false) {
                            $html= str_replace($res[0][0], $replace, $html);
                         }
                    }
                }
            }
            $transport->setHtml($html);
        }
    }
}
