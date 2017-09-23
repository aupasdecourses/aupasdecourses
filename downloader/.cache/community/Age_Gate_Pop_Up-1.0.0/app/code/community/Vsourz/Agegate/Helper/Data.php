<?php
class Vsourz_Agegate_Helper_Data extends Mage_Core_Helper_Abstract{ 
	public function getDelayTime(){
		$delay = Mage::getStoreConfig('agegate/settings/delay');
		$delayHours = $delay/1440; 
		return $delayHours;
	}
	public function getCookieExpire(){
		$cookie = Mage::getStoreConfig('agegate/settings/cookie');
		$cookieHours = $cookie/24; 
		return $cookieHours;
	}
	public function getBlockId(){
		$blockId = Mage::getStoreConfig('agegate/settings/promotionblock');
		return $blockId;
	}
	public function getDissagreeBlockId(){
		$dissagreeblockId = Mage::getStoreConfig('agegate/settings/notverifyblock');
		return $dissagreeblockId;
	}
	public function getBlockTitle(){
		$blockId = $this->getBlockId();
		$blockTitle = Mage::getModel('cms/block')->load($blockId)->getTitle();
		return $blockTitle;
	}
	public function showPopUp(){
		$allowedPages = Mage::getStoreConfig('agegate/settings/pages');
		$currCmsPage = Mage::getSingleton('cms/page')->getIdentifier();
		$currModule = Mage::app()->getFrontController()->getRequest()->getModuleName();
		$pageArr = explode(',',$allowedPages);
		foreach($pageArr as $key => $value){
			if($currCmsPage == $value || $currModule == $value){
				return "Y";
			}
		}
	}
	public function getHeight(){
		$height = Mage::getStoreConfig('agegate/settings/height');
		if($height == "auto"){
			return $height;
		}
		else{
			return $height."px";
		}
	}
	public function getWidth(){
		$width = Mage::getStoreConfig('agegate/settings/width');
		if($width == "auto"){
			return $width;
		}
		else{
			return $width."px";
		}
	}
	public function getAgree(){
		$agree = Mage::getStoreConfig('agegate/settings/agree');
		return $agree;
	}
	public function getDisagree(){
		$disagree = Mage::getStoreConfig('agegate/settings/disagree');
		return $disagree;
	}
}