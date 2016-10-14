<?php
/**
/ @author Pierre Mainguet
*/

# Controllers are not autoloaded so we will have to do it manually:
require_once 'MW/Ddate/controllers/Checkout/OnepageController.php';

class Apdc_Checkout_Checkout_OnepageController extends MW_Ddate_Checkout_OnepageController
{
    public function indexAction()
    {
        $payment_activated = Mage::getStoreConfig('apdc_general/activation/payment');
        if (!$payment_activated) {
            Mage::getSingleton('core/session')->setData('main_popup', 'false');
            $refererUrl = Mage::helper('core/http')->getHttpReferer(true);
            Mage::app()->getResponse()->setRedirect($refererUrl);
        } else {
            parent::indexAction();
        }
    }

    /**
     * Billing & Shipping Steps save action.
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
                            'html' => $this->_getPaymentMethodsHtml(),
                        );
                    } else {
                        $result['goto_section'] = 'shipping_method';
                        $result['update_section'] = array(
                            'name' => 'shipping-method',
                            'html' => $this->_getShippingMethodsHtml(),
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
 * Shipping method save action.
 */
    //Made to change order of checkout steps
    public function saveShippingMethodAction()
    {

        // Pierre Mainguet - Problem with haveAnySlotAvailable() function (to be checked)
        //if(Mage::getModel('ddate/dtime')->getCollection()->count() > 0 && Mage::helper('ddate')->haveAnySlotAvailable()) {
        if (Mage::getModel('ddate/dtime')->getCollection()->count() > 0) {
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
                                  'quote' => $this->getOnepage()->getQuote(), ));
                        $this->getOnepage()->getQuote()->collectTotals();
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                        Mage::register('commercants_spotty', Mage::helper('apdc_checkout')->getSpottyCom(true));

                        $result['goto_section'] = 'payment';
                        $result['update_section'] = array(
                            'name' => 'payment-method',
                            'html' => $this->_getPaymentMethodsHtml(),
                        );
                    }
                }
                $this->getOnepage()->getQuote()->collectTotals()->save();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        } else {
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
                              'quote' => $this->getOnepage()->getQuote(), ));
                    $this->getOnepage()->getQuote()->collectTotals();
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml(),
                    );
                }
                $this->getOnepage()->getQuote()->collectTotals()->save();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }
}
