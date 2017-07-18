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
			$name = "Tous les produits de ".$catCurrent->getName();
		}
		else {
			$name = $catCurrent->getName()." chez ".$this->_category->getName();//$this->_category->getParentCategory()->getName()." - ".$this->_category->getName();
		}
		return $name;
    }

}