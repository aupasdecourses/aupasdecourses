<?php
/**
 * @author Pierre Mainguet
 * @copyright Copyright (c) 2016 Pierre Mainguet - mainguetpierre@gmail.com
 * @package Apdc_Notation
 */
class Apdc_Notation_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
	        $this->loadLayout();
	        $this->renderLayout();
    }

    public function surveyAction() {
	        $this->loadLayout();
	        $this->renderLayout();
    }

    public function noteAction(){
		$request=$this->getRequest();
		$orderId=$request->getParam('order_id');
		$note=$request->getParam('note');

		if(isset($orderId) && isset($note) && $orderId!=null && $note!=null){
			if(!Mage::helper('apdc_notation')->noteExists($orderId)){
				$notationClient = Mage::getSingleton('apdc_notation/notation');
				$notationClient->setOrderId($orderId);
				if($note <1 || $note >5){$note=0;}
	    		$notationClient->setNote($note);
	    		$notationClient->save();
			}else{
				$notationClient = Mage::getSingleton('apdc_notation/notation');
	   			$notationClient->load($orderId, 'order_id');
	   			$note=$notationClient->getNote();
			}
				$refererUrl = Mage::getUrl('votrecommande/index?note='.$note);
		}else{
			$refererUrl = Mage::getUrl();
		}

	    $this->getResponse()->setRedirect($refererUrl);
	    return $this;
    }

}