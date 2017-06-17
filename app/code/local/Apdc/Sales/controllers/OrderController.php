<?php

require_once(Mage::getModuleDir('controllers','Mage_Sales').DS.'OrderController.php');

class Apdc_Sales_OrderController  extends Mage_Sales_OrderController {

	public function ongoingAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('Mes commandes en cours'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
	
}
