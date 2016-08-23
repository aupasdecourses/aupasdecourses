<?php

require_once 'Mks/Ajaxcoupon/controllers/IndexController.php';
class Pmainguet_CustomCheckout_Ajaxcoupon_IndexController extends Mks_Ajaxcoupon_IndexController
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
                    //		$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    //);
                    $response['msg'] = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
                    $response['status'] = 'SUCCESS';
                } else {
                    //$this->_getSession()->addError(
                    //		$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    //);
                    $response['msg'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                    $response['status'] = 'ERROR';
                }
            } else {
                $response['status'] = 'SUCCESS';
                //$this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
                $response['msg'] = $this->__('Coupon code was canceled.');
            }
        } catch (Mage_Core_Exception $e) {
            //$this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            //$this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            //Mage::logException($e);
            $response['msg'] = $this->__('Cannot apply the coupon code.');
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
