<?php

class Apdc_Dispatch_Model_Export extends Apdc_Dispatch_Model_Mistral_Ftp
{

	public function processRequest($params){
		
		if(!isset($params)||!isset($params['medium'])){
			Mage::log("params are not set!", null, 'dispatch.log');
			break;
		}

		//$c_date = date("Y-m-d",mktime(0, 0, 0, 2, 23, 2017));
		//$to = date("Y-m-d",strtotime("+5 day"));

		$c_date = date("Y-m-d");
		$to = date("Y-m-d",strtotime("+5 day"));

		try {
			
			$params["c_date"] = $c_date;

			switch($params['medium']){
				case "ftp":
					$params["orders"] = Mage::getModel('pmainguet_delivery/orders_shop')->getShopsOrdersAction($c_date,$to);
					Mage::getModel('apdcdispatch/mistral_ftp')->_processRequestFtp($params);
					break;
				case "api":
					break;
				case "mail":
					//send only for current day
					$params["orders"] = Mage::getModel('pmainguet_delivery/orders_shop')->getShopsOrdersAction($c_date,$c_date);
					Mage::getModel('apdcdispatch/mail')->processRequestMail($params);
				default:
					break;
			}
		} catch  (Exception $e) {
			Mage::log($e, null, 'dispatch.log');
			Mage::getModel('apdcadmin/mail')->warnErrorMistral($e->getMessage());
		}
	}

	public function processCronFtp(){
		$params['medium']='ftp';
		$this->processRequest($params);
	}

}