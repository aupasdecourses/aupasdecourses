<?php

class Apdc_Dispatch_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction(){
		echo "not implemented";
	}

	public function processApiAction(){
		$params["data"]=array(
			'action'	 => 'getpictures',
			'PartnerRef' => "APDC5535",
			'OrderRef'	 => "2017000854-272",
		);
		$params["medium"] = "ftp";
		$result = Mage::getModel("apdcdispatch/export")->processRequest($params);
	}

	public function processFtpAction(){
		$params["medium"] = "ftp";
		Mage::getModel("apdcdispatch/export")->processRequest($params);
	}

	public function processMailAction(){
		$params["medium"] = "mail";
		return Mage::getModel("apdcdispatch/export")->processRequest($params);
	}

}