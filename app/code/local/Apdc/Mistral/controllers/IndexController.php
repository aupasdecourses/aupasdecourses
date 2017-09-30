<?php

class Apdc_Mistral_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction(){

	}

	public function processAction(){
		$data=array(
			'action'	 => 'getpictures',
			'PartnerRef' => "APDC5535",
			'OrderRef'	 => "2017000854-272",
		);
		$result = Mage::getModel("apdcmistral/api")->processRequest($data);
		var_dump($result);
	}

}