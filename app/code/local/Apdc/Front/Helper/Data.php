<?php
/**
 * @copyright  Pierre Mainguet
 */
class Apdc_Front_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_category;

    public function __construct()
    {
        $currentcat=Mage::getSingleton('catalog/layer')->getCurrentCategory();
        if($currentcat->getLevel()==2){
            $this->_category = $currentcat;
        } else {
            $this->_category=$currentcat->getParentCategory();
        }
    }

    public function getCatThumbnailUrl()
    {
        $url = false;
        if ($image = $this->_category->getThumbnail()) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
        }

        return $url;
    }

    public function getCatTitle(){
		$catCurrent = Mage::getSingleton('catalog/layer')->getCurrentCategory();
		if($catCurrent->getLevel() == 3) {
			// $name = "Tous les produits - ".$catCurrent->getName();
            $name = "Tous les produits";
		} elseif ($catCurrent->getLevel() == 4) {
			//$name = $catCurrent->getName()." - ".$this->_category->getName();
            $name = $catCurrent->getName();
		} else {
            //$name = $catCurrent->getName()." - ". $this->_category->getParentCategory()->getName();
            $name = $catCurrent->getName();
        }
		return $name;
    }

}