<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {  
		$this->loadLayout(false);
		
		//echo  $this->getUrl("*/ajax");
		//echo  Mage::helper("adminhtml")->getUrl("*/ajax") ;
			
		$task = $this->getRequest()->getParam('task');
		
		switch($task) {
			case 'ajax_user': {
				$q =$this->getRequest()->getParam('term');
				if(empty($q) || strlen($q)<2) exit;

				$result = array();
				
				$collection = Mage::getModel('customer/customer')
						->getCollection()
						->addAttributeToSelect('*')
						->addAttributeToSort('lastname', 'desc')
						->addAttributeToSort('firstname', 'ASC')
						->addAttributeToSort('entity_id', 'ASC')
				;
				$collection->getSelect()->columns('CONCAT(at_lastname.value,", ",at_firstname.value," ",email) AS label');
				$collection->getSelect()->having('label LIKE "%'.$q.'%"');
				$collection->getSelect()->limit(25);
				foreach($collection as $item) {
					$label = $item->getData('label');
					if(!Mage::app()->isSingleStoreMode()) $label = '['.Mage::app()->getWebsite($item->getData('website_id'))->getName().'] '.$label;
					array_push($result,array('id'=>$item->getData('entity_id'),'label'=>$label,'value'=>strip_tags($label)));
				}
		
				echo Mage::helper('core')->jsonEncode($result);
				exit;
			}
			
			case 'ajax_users': {
				
				$result = array();
				
				$collection = Mage::getModel('customer/customer')
						->getCollection()
						->addAttributeToSelect('*')
						->addAttributeToSort('lastname', 'desc')
						->addAttributeToSort('firstname', 'ASC')
						->addAttributeToSort('entity_id', 'ASC')
				;
				$collection->getSelect()->columns('CONCAT(at_lastname.value,", ",at_firstname.value," ",email) AS label');
				foreach($collection as $item) {
					$label = $item->getData('label');
					if(!Mage::app()->isSingleStoreMode()) $label = '['.Mage::app()->getWebsite($item->getData('website_id'))->getName().'] '.$label;
					array_push($result,array('id'=>$item->getData('entity_id'),'label'=>$label,'value'=>strip_tags($label)));
				}
		
				echo Mage::helper('core')->jsonEncode($result);
				exit;
			}
			
			default:
			
		}
				
		/*
		$result = array();
		$collection = Mage::getModel('customer/customer')
				->getCollection()
				->addAttributeToSelect('*')
				//->addFieldToFilter('is_active', 1)
				->addAttributeToSort('lastname', 'desc')
				->addAttributeToSort('firstname', 'ASC')
				->addAttributeToSort('entity_id', 'ASC')
				;
		$collection->getSelect()->columns('CONCAT(at_lastname.value,", ",at_firstname.value," ",email) AS label');
		//$collection->getSelect()->having('label LIKE "%jo%"');
		//$collection->getSelect()->limit(1);	
		//foreach($collection as $item) {echo '<pre>'; print_r($item);exit; }
		foreach($collection as $item) {
			$label = $item->getData('label');
			if(!Mage::app()->isSingleStoreMode()) $label = '['.Mage::app()->getWebsite($item->getData('website_id'))->getName().'] '.$label;
			array_push($result,array('id'=>$item->getData('entity_id'),'label'=>$label,'value'=>strip_tags($label)));
		}
		echo Mage::helper('core')->jsonEncode($result);
		//*/
		
		
		
		
		
		
		
		
		exit;
	}  


}
