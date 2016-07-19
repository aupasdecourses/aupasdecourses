<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

# Controllers are not autoloaded so we will have to do it manually:
require_once 'MW/Ddate/controllers/Checkout/OnepageController.php';

class Pmainguet_CustomCheckout_Checkout_OnepageController extends MW_Ddate_Checkout_OnepageController
{
	public function indexAction() {
		$payment_activated = Mage::getModel('core/variable')->loadByCode('payment_activated')->getValue('plain');
		if ($payment_activated == 'false') {
			Mage::getSingleton('core/session')->setData('main_popup','false');
			$refererUrl = Mage::helper('core/http')->getHttpReferer(true);
			Mage::app()->getResponse()->setRedirect($refererUrl);
		} else {
			if (!Mage::helper('checkout')->canOnepageCheckout()) {
				Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
				$this->_redirect('checkout/cart');
				return;
			}
			$quote = $this->getOnepage()->getQuote();
			if (!$quote->hasItems() || $quote->getHasError()) {
				$this->_redirect('checkout/cart');
				return;
			}
			if (!$quote->validateMinimumAmount()) {
				$error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
					Mage::getStoreConfig('sales/minimum_order/error_message') :
					Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

				Mage::getSingleton('checkout/session')->addError($error);
				$this->_redirect('checkout/cart');
				return;
			}
			Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
			Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure' => true)));
			$this->getOnepage()->initCheckout();
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
			$this->renderLayout();
		}


	}

    /**
     * Billing & Shipping Steps save action
     */
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            
            $billingData = $this->getRequest()->getPost('billing', array());
            $customerBillingAddressId = $this->getRequest()->getPost('billing_address_id', false);
            
            if (isset($billingData['email'])) {
                $billingData['email'] = trim($billingData['email']);
            }
            $result = $this->getOnepage()->saveBilling($billingData, $customerBillingAddressId);
            
            if (!isset($result['error'])) {
                
                $shippingData = $this->getRequest()->getPost('shipping', array());
                $customerShippingAddressId = $this->getRequest()->getPost('shipping_address_id', false);
                $result = $this->getOnepage()->saveShipping($shippingData, $customerShippingAddressId);
                
                if (!isset($result['error'])) {
                    if ($this->getOnepage()->getQuote()->isVirtual()) {
                        $result['goto_section'] = 'payment';
                        $result['update_section'] = array(
                            'name' => 'payment-method',
                            'html' => $this->_getPaymentMethodsHtml()
                        );
                    } else {
                        $result['goto_section'] = 'shipping_method';
                        $result['update_section'] = array(
                            'name' => 'shipping-method',
                            'html' => $this->_getShippingMethodsHtml()
                        );
    
                        $result['allow_sections'] = array('shipping_method');
                        //$result['duplicateBillingInfo'] = 'false';
                    }
                }
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping method save action
     */
    //Made to change order of checkout steps
    public function saveShippingMethodAction()
    {

        // Pierre Mainguet - Problem with haveAnySlotAvailable() function (to be checked)
        //if(Mage::getModel('ddate/dtime')->getCollection()->count() > 0 && Mage::helper('ddate')->haveAnySlotAvailable()) {
        if(Mage::getModel('ddate/dtime')->getCollection()->count() > 0) {
            if ($this->_expireAjax()) {
                return;
            }
            if ($this->getRequest()->isPost()) {
                $shippingdata = $this->getRequest()->getPost('shipping_method', '');
                $result = $this->getOnepage()->saveShippingMethod($shippingdata);
                // $result will contain error data if shipping method is empty
                if (!$result) {
                    $ddatedata = $this->getRequest()->getPost('ddate', '');
                    $result = $this->getOnepage()->saveDdate($ddatedata);
                    if (!$result) {
                        Mage::dispatchEvent(
                            'checkout_controller_onepage_save_shipping_method',
                             array(
                                  'request' => $this->getRequest(),
                                  'quote'   => $this->getOnepage()->getQuote()));
                        $this->getOnepage()->getQuote()->collectTotals();
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    Mage::register('commercants_spotty',Mage::helper('customcheckout')->getSpottyCom(true));

                        $result['goto_section'] = 'payment';
                        $result['update_section'] = array(
                            'name' => 'payment-method',
                            'html' => $this->_getPaymentMethodsHtml()
                        );
                    }
                }
                $this->getOnepage()->getQuote()->collectTotals()->save();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }else{
            if ($this->_expireAjax()) {
                return;
            }
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost('shipping_method', '');
                $result = $this->getOnepage()->saveShippingMethod($data);
                // $result will contain error data if shipping method is empty
                if (!$result) {
                    Mage::dispatchEvent(
                        'checkout_controller_onepage_save_shipping_method',
                         array(
                              'request' => $this->getRequest(),
                              'quote'   => $this->getOnepage()->getQuote()));
                    $this->getOnepage()->getQuote()->collectTotals();
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                }
                $this->getOnepage()->getQuote()->collectTotals()->save();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }

    }

}
