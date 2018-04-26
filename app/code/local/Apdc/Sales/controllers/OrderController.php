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
        if ($this->getRequest()->getParam('neighborhood_id')) {
            $neighborhood = Mage::app()->getStore($this->getRequest()->getParam('neighborhood_id'));
            if ($neighborhood && $neighborhood->getId()) {
                Mage::helper('apdc_neighborhood')->setNeighborhood($neighborhood);
                $appEmulation = Mage::getSingleton('core/app_emulation');
                $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($neighborhood->getId());
                $url = Mage::getUrl('sales/order/reorder', ['order_id' => $this->getRequest()->getParam('order_id')]);
                Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('checkout')->__('Vous êtes maintenant sur le quartier %s', $neighborhood->getName()));
                return $this->_redirectUrl($url);
            }
        }
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
            }
        }

        $cart->save();
        Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('checkout')->__('Tous vos articles ont été ajoutés à votre panier'));
		return $this->getResponse()->setRedirect(Mage::getBaseUrl().'?opencart=1');
    }
	
}
