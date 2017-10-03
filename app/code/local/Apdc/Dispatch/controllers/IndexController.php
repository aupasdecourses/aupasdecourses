<?php

class Apdc_Dispatch_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction(){
		$run[1]="apdcdispatch/cron";
		$run[2]="processCronFtp";
		$callback = array($model, $run[2]);
		$arguments = array($schedule);

		$messages = call_user_func_array($callback, $arguments);
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

}