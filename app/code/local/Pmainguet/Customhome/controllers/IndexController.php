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
					case 'Paris 1er':
						$storeId='../quartiers/Paris_1er';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 2e':
						$storeId='../quartiers/Paris_2e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 3e':
						$storeId='../quartiers/Paris_3e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 4e':
						$storeId='../quartiers/Paris_4e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 5e':
						$storeId='../quartiers/Paris_5e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 6e':
						$storeId='../quartiers/Paris_6e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 7e':
						$storeId='../quartiers/Paris_7e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 10e':
						$storeId='../quartiers/Paris_10e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 11e':
						$storeId='../quartiers/Paris_11e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 12e':
						$storeId='../quartiers/Paris_12e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 13e':
						$storeId='../quartiers/Paris_13e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 14e':
						$storeId='../quartiers/Paris_14e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 15e':
						$storeId='../quartiers/Paris_15e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 16e':
						$storeId='../quartiers/Paris_16e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 19e':
						$storeId='../quartiers/Paris_19e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Paris 20e':
						$storeId='../quartiers/Paris_20e';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Boulogne':
						$storeId='../quartiers/Boulogne';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Issy Les Moulineaux':
						$storeId='../quartiers/Issy-Les-Moulineaux';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Levallois Perret':
						$storeId='../quartiers/Levallois-Perret';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Montrouge':
						$storeId='../quartiers/Montrouge';
						$url=$coreUrl.$storeId;
						Mage::app()->getResponse()->setRedirect($url);
						break;
					case 'Vincennes':
						$storeId='../quartiers/Vincennes';
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