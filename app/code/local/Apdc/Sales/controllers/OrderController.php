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
	
	/**
     * Action for reorder
     */
    public function reorderAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }
        $order = Mage::registry('current_order');

        $cart = Mage::getSingleton('checkout/cart');
        $cartTruncated = false;
        /* @var $cart Mage_Checkout_Model_Cart */

        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $cart->addOrderItem($item);
            } catch (Mage_Core_Exception $e){
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                }
                else {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                $this->_redirect('*/*/history');
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e,
                    Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
                );
				return $this->getResponse()->setRedirect(Mage::getBaseUrl().'?opencart=1');
                //$this->_redirect('checkout/cart');
            }
        }

        $cart->save();
		return $this->getResponse()->setRedirect(Mage::getBaseUrl().'?opencart=1');
        //$this->_redirect('checkout/cart');
    }
	
}
