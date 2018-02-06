<?php
/**
/ @author Pierre Mainguet
*/

# Controllers are not autoloaded so we will have to do it manually:
require_once 'MW/Ddate/controllers/Checkout/OnepageController.php';

class Apdc_Checkout_Checkout_OnepageController extends MW_Ddate_Checkout_OnepageController
{
    protected function _ddateIsNotAvailable()
    {
        $ddate = Mage::getSingleton('core/session')->getDdate();
        if ($ddate) {
            $hasError = false;
            if ($ddate >= date('Y-m-d')) {
                $dtimeId = Mage::getSingleton('core/session')->getDtimeId();
                $block = new MW_Ddate_Block_Onepage_Ddate();
                Mage::log("New _ddateIsNotAvailable",null,"ddate.log");
                Mage::log(Mage::getSingleton('core/session')->getData()['last_url'],null,"ddate.log");
                Mage::log(Mage::getSingleton('core/session')->getData()['visitor_data'],null,"ddate.log");
                Mage::log(Mage::getSingleton('core/session')->getData()['ddate'],null,"ddate.log");
                Mage::log(Mage::getSingleton('core/session')->getData()['dtime'],null,"ddate.log");
                Mage::log(Mage::getSingleton('core/session')->getData()['dtime_id'],null,"ddate.log");
                if (!$block->isEnabled($dtimeId, $ddate,true)) {
                    Mage::log($ddate."-".$dtimeId." is not enabled!",null,"ddate.log");
                    $hasError = true;
                }
            } else {
                Mage::log("Warning: date ".$ddate." < ".date('Y-m-d'),null,"ddate.log");
                $hasError = true;
            }
            if ($hasError) {
                Mage::helper('apdc_checkout')->cleanDdate();
                Mage::getSingleton('checkout/session')->addError($this->__('Votre créneau horaire n\'est plus valide. Veuillez en sélectionner un autre.'));
                $this->_ajaxRedirectResponse();
                return true;
            }
        }
        return false;
    }

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

    // protected function _expireAjax()
    // {
    //     $isNotValide = parent::_expireAjax();
    //     if($isNotValide) {
    //         Mage::log("_expireAjax triggered error!",null,"ddate.log");
    //     }else {
    //         $isNotValide = $this->_ddateIsNotAvailable();
    //     }

    //     return $isNotValide;
    // }

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
												
                        /*$result['goto_section'] = 'payment';
                        $result['update_section'] = array(
                            'name' => 'payment-method',
                            'html' => $this->_getPaymentMethodsHtml(),
                        );*/
						$result['goto_section'] = 'checkcart';
						$result['update_section'] = array(
                            'name' => 'checkcart', 
                            'html' => $this->_getCheckcartHtml(),
                        );
                        /*$result['update_section'] = array(
                            'name' => 'payment-method',
                            'html' => $this->_getPaymentMethodsHtml(),
                        );*/
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

                    /*$result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml(),
                    );*/
					$result['goto_section'] = 'checkcart';
					$result['update_section'] = array(
						'name' => 'checkcart',
						'html' => $this->_getCheckcartHtml(),
					);
                }
                $this->getOnepage()->getQuote()->collectTotals()->save();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }
	
	public function deleteProductInCheckcartAction() {
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$entityId = $this->getRequest()->getPost("entity_id", "");
			$result = $this->getOnepage()->cleanQuote(array($entityId));
			$this->getOnepage()->getQuote()->collectTotals()->save();
			
			$result["goto_section"] = "checkcart";
			$result["update_section"] = array(
				"name" => "checkcart",
				"html" => $this->_getCheckcartInfoHtml()
			);
			$this->getResponse()->setBody(Zend_Json::encode($result));
		}
	}
	
	public function saveCommentInCheckcartAction() {
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$entityId = $this->getRequest()->getPost("entity_id", "");
			$comment = $this->getRequest()->getPost("comment", "");
			$result = $this->getOnepage()->saveComment($entityId, $comment);
			$this->getOnepage()->getQuote()->collectTotals()->save();
			
			$result["goto_section"] = "checkcart";
			$result["update_section"] = array(
				"name" => "checkcart",
				"html" => $this->_getCheckcartInfoHtml()
			);
			$this->getResponse()->setBody(Zend_Json::encode($result));
		}
	}
	
	public function saveCheckcartAction() {
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$attrQuote = $this->getRequest()->getPost("custom_attr_quote", "");
			$entityId = $this->getRequest()->getPost("entity_id", "");
			$result = $this->getOnepage()->saveCheckcart($attrQuote);
			$result = $this->getOnepage()->cleanQuote($entityId);
			$this->getOnepage()->getQuote()->collectTotals()->save();
			
			$result["goto_section"] = "payment";
			$result["update_section"] = array(
				"name" => "payment-method",
				"html" => $this->_getPaymentMethodsHtml()
			);
			$this->getResponse()->setBody(Zend_Json::encode($result));
		}
	}
	
	public function _getCheckcartHtml() {
		$layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_checkcart');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
	}
	public function _getCheckcartInfoHtml() {
		$layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_checkcart');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getBlock('checkout.onepage.checkcart.info')->toHtml();
	}
	
	protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_payment');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    public function saveDdateAjaxAction()
    {
        $date = $this->getRequest()->getPost('date', '');
        $dtime = $this->getRequest()->getPost('dtime', '');
        $ddatei = $this->getRequest()->getPost('ddatei', '');
        $url = $this->getRequest()->getPost('url', Mage::getBaseUrl());
        Mage::helper('apdc_checkout')->saveDdate($date, $dtime, $ddatei);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('redirect' => $url)));
        return;
    }
	

    /**
     * Check can page show for unregistered users
     *
     * @return boolean
     */
    protected function _canShowForUnregisteredUsers()
    {
        return parent::_canShowForUnregisteredUsers() || $this->getRequest()->getActionName() == 'saveDdateAjax';
    }


    public function savePaymentAction()
    {
        if(Mage::getModel('ddate/dtime')->getCollection()->count() > 0 && Mage::helper('ddate')->haveAnySlotAvailable()) {

            if ($this->_expireAjax()) {
                return;
            }
            if ($this->_ddateIsNotAvailable()) {
                return;
            }
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost('payment', array());

                /*
                * first to check payment information entered is correct or not
                */
                try {
                    $result = $this->getOnepage()->savePayment($data);
                } catch (Mage_Payment_Exception $e) {
                    if ($e->getFields()) {
                        $result['fields'] = $e->getFields();
                    }
                    $result['error'] = $e->getMessage();
                } catch (Exception $e) {
                    $result['error'] = $e->getMessage();
                }

                $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                if (empty($result['error']) && !$redirectUrl) {
                    $result['goto_section'] = 'ddate';
                }

                if ($redirectUrl) {
                    $result['redirect'] = $redirectUrl;
                }

                $this->getResponse()->setBody(Zend_Json::encode($result));
            }
        } else {
            if ($this->_expireAjax()) {
                return;
            }
            if ($this->_ddateIsNotAvailable()) {
                return;
            }
            try {
                if (!$this->getRequest()->isPost()) {
                    $this->_ajaxRedirectResponse();
                    return;
                }

                $data = $this->getRequest()->getPost('payment', array());
                $result = $this->getOnepage()->savePayment($data);

                // get section and redirect data
                $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                if (empty($result['error']) && !$redirectUrl) {
                    $this->loadLayout('checkout_onepage_review');
                    $result['goto_section'] = 'review';
                    $result['update_section'] = array(
                        'name' => 'review',
                        'html' => $this->_getReviewHtml()
                    );
                }
                if ($redirectUrl) {
                    $result['redirect'] = $redirectUrl;
                }
            } catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            } catch (Mage_Core_Exception $e) {
                $result['error'] = $e->getMessage();
            } catch (Exception $e) {
                Mage::logException($e);
                $result['error'] = $this->__('Unable to set Payment Method.');
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

}
