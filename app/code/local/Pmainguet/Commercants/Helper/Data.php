<?php
class Pmainguet_Commercants_Helper_Data extends Mage_Core_Helper_Abstract{
  
   public function getCommercantname($object){

    $category_ids=$object->getProduct()->getCategoryIds();
    $category = Mage::getModel('catalog/category')->load($category_ids[count($category_ids)-3]);
    if ($category->getIsActive()) {
    	$name=$category->getName();
    	$url=$category->getUrl();
    	return [$name,$url];
    }

	 }
}
?>