<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Apdc_Coupon_IndexController extends Mage_Checkout_CartController
{
    public function customcouponPostAction()
    {
        /*
         * No reason continue with empty shopping cart
         */

        $response = array();
        $response['status'] = 'ERROR';
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            //$this->_goBack();
            return;
        }
   //echo $this->getRequest()->getParam('remove'); die;
        $couponCode = (string) $this->getRequest()->getParam('coupon_code');

        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            //$this->_goBack();
            return;
        }

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
            ->collectTotals()
            ->save();

            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    //$this->_getSession()->addSuccess(
                    //      $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    //);
                    $response['msg'] = $this->__('Le code a été appliqué.', Mage::helper('core')->htmlEscape($couponCode));
                    $response['status'] = 'SUCCESS';
                } else {
                    //$this->_getSession()->addError(
                    //      $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    //);
                    $response['msg'] = $this->__('Le code n\'est pas valide.', Mage::helper('core')->htmlEscape($couponCode));
                    $response['status'] = 'ERROR';
                }
            } else {
                $response['status'] = 'SUCCESS';
                //$this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
                $response['msg'] = $this->__('Le code a été annulé.');
            }
        } catch (Mage_Core_Exception $e) {
            //$this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            //$this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            //Mage::logException($e);
            $response['msg'] = $this->__('Le code ne peut être appliqué.');
        }
        $this->loadLayout(false);
        $review = $this->getLayout()->getBlock('ajaxcoupon')->toHtml();
        $totals=$this->getLayout()->getBlock('totals')->toHtml();
        $progress=$this->getLayout()->getBlock('progress')->toHtml();
        $response['review'] = $review;
        $response['totals'] = $totals;
        $response['progress'] = $progress;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
}
