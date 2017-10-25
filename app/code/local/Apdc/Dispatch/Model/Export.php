<?php

class Apdc_Dispatch_Model_Export extends Apdc_Dispatch_Model_Mistral_Ftp
{

	protected function _processRequestApi($params){
		$params["data"];
	}	

	public function processRequest($params){

		Mage::log("Model Export - start process request",null,"export.log");
		
		if(!isset($params)||!isset($params['medium'])){
			Mage::log("params are not set!", null, 'export.log');
			Mage::throwException(Mage::helper('cron')->__('params are not set in processRequest!'));
		}

		$currentTime = Mage::getSingleton('core/date')->timestamp();

		//Mode production ou test
		if(Mage::getStoreConfig('apdcdispatch/general/mode')){
        	$c_date=date('Y-m-d', $currentTime);
        	$to = date('Y-m-d',strtotime('+5 day', $currentTime));
		}else{
			//$c_date=date('Y-m-d', $currentTime);
            $c_date = date("Y-m-d",mktime(0, 0, 0, 10, 10, 2017));
			$to = date('Y-m-d',strtotime('+5 day', $currentTime));
		}

        try {
            $params['c_date'] = $c_date;

            switch ($params['medium']) {
                case 'ftp':
                    
                    //Fix to remove order for the day after 4PM (10AM for Saturdays) to remove errors in Mistral
                    if((int)date('H',$currentTime)>=16||(date('D',$currentTime)=="Sat"&&(int)date('H',$currentTime)>=10)){
                       $c_date = date('Y-m-d',strtotime('+1 day', $currentTime)); 
                    }

                    $params['orders'] = Mage::getModel('pmainguet_delivery/orders_shop')->getShopsOrdersAction($c_date, $to);
                    if (Mage::getStoreConfig('apdcdispatch/general/mistral_active')) {
                        Mage::getModel('apdcdispatch/mistral_ftp')->_processRequestFtp($params);
                    } else {
                        Mage::log('Export via FTP est désactivé', null, 'export.log');
                        Mage::getModel('apdcadmin/mail')->warnMistralDeactivated();
                    }
                    break;
                case 'api':
                    break;
                case 'mail':
                    //send only for current day
                    $params['orders'] = Mage::getModel('pmainguet_delivery/orders_shop')->getShopsOrdersAction($c_date, $c_date,false);
                    if (Mage::getStoreConfig('apdcdispatch/general/mail_active')) {
                        Mage::getModel('apdcdispatch/mail')->processRequestMail($params,false);
                    } else {
                        Mage::log('Envoi par mail des commerçants est désactivé', null, 'export.log');
                        Mage::getModel('apdcadmin/mail')->warnMailShopDeactivated();
                    }
                default:
                    break;
            }
        } catch (Exception $e) {
            Mage::log($e, null, 'dispatch.log');
            Mage::getModel('apdcadmin/mail')->warnErrorMistral($e->getMessage());
        }
	}

}