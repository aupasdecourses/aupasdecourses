<?php
	class Pmainguet_Customhome_IndexController extends Mage_Core_Controller_Front_Action
	{  
	  public function redirectAction(){
		if ($data = $this->getRequest()->getPost()) {
			$zipcode = $data['zipcode'];
			$refererUrl = Mage::helper('core/http')->getHttpReferer(true);
			$coreUrl = Mage::getBaseUrl();
			if(isset($zipcode)){
				switch($zipcode){
					case 'Paris 17e':
						$storeId='../batignolles';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 18e':
						$storeId='../batignolles';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 9e':
						$storeId='../batignolles';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 8e':
						$storeId='../batignolles';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 3e':
						$storeId='../marais';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					default:
						Mage::app()->getResponse()->setRedirect($refererUrl."#download");
						break;
				}
			}else{
				Mage::app()->getResponse()->setRedirect($refererUrl);
			}
			
		}
	  }
	}

?>