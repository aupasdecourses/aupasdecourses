<?php

class Pmainguet_Customhome_IndexController extends Mage_Core_Controller_Front_Action
{
	private $_urlTab = [
		75001	=>	'../quartiers/Paris_1er',
		75002	=>	'../saintmartin',
		75003	=>	'../saintmartin',
		75004	=>	'../quartiers/Paris_4e',
		75005	=>	'../quartiers/Paris_5e',
		75006	=>	'../quartiers/Paris_6e',
		75007	=>	'../quartiers/Paris_7e',
		75008	=>	'../batignolles',
		75009	=>	'../batignolles',
		75010	=>	'../saintmartin',
		75011	=>	'../saintmartin',
		75012	=>	'../quartiers/Paris_12e',
		75013	=>	'../quartiers/Paris_13e',
		75014	=>	'../quartiers/Paris_14e',
		75015	=>	'../quartiers/Paris_15e',
		75016	=>	'../quartiers/Paris_16e',
		75116	=>	'../quartiers/Paris_16e',
		75017	=>	'../batignolles',
		75018	=>	'../batignolles',
		75019	=>	'../quartiers/Paris_19e',
		75020	=>	'../quartiers/Paris_20e'
	];

/*
	switch ($zipcode) {
	case 'Boulogne':
		$storeId = '../quartiers/Boulogne';
		$url = $coreUrl.$storeId;
		Mage::app()->getResponse()->setRedirect($url);
		break;
	case 'Issy Les Moulineaux':
		$storeId = '../quartiers/Issy-Les-Moulineaux';
		$url = $coreUrl.$storeId;
		Mage::app()->getResponse()->setRedirect($url);
		break;
	case 'Levallois Perret':
		$storeId = '../quartiers/Levallois-Perret';
		$url = $coreUrl.$storeId;
		Mage::app()->getResponse()->setRedirect($url);
		break;
	case 'Montrouge':
		$storeId = '../quartiers/Montrouge';
		$url = $coreUrl.$storeId;
		Mage::app()->getResponse()->setRedirect($url);
		break;
	case 'Vincennes':
		$storeId = '../quartiers/Vincennes';
		$url = $coreUrl.$storeId;
		Mage::app()->getResponse()->setRedirect($url);
		break;
	default:
		break;
	}
 */

    public function redirectAction()
    {
        $baseurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

		$data = $this->getRequest()->getPost();
		if (isset($data['zipcode'])) {
			$zipcode = $data['zipcode'];
			$refererUrl = Mage::helper('core/http')->getHttpReferer(true);
			$coreUrl = Mage::getBaseUrl();

			if (array_key_exists($zipcode, $this->_urlTab))
				Mage::app()->getResponse()->setRedirect($coreUrl . $this->_urlTab[$zipcode]);
			else
				Mage::app()->getResponse()->setRedirect($baseurl);
		} else {
			Mage::app()->getResponse()->setRedirect($refererUrl);
		}
	}
}
