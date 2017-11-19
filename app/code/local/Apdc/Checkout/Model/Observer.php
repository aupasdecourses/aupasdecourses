<?php

class Apdc_Checkout_Model_Observer extends Mage_Core_Model_Abstract
{
    public function saveOrder($observer)
    {
        $controllerAction = $observer->getEvent()->getControllerAction();
        $response = $controllerAction->getResponse();
        $paymentResponse = Mage::helper('core')->jsonDecode($response->getBody());
        if ($paymentResponse!=array() && (!isset($paymentResponse['error']) || !$paymentResponse['error'])) {
            $controllerAction->getRequest()->setParam('form_key', Mage::getSingleton('core/session')->getFormKey());
            $controllerAction->getRequest()->setPost('agreement', array_flip(Mage::helper('checkout')->getRequiredAgreementIds()));
            $controllerAction->saveOrderAction();
            $orderResponse = Mage::helper('core')->jsonDecode($response->getBody());
            if ($orderResponse['error'] === false && $orderResponse['success'] === true) {
                if (!isset($orderResponse['redirect']) || !$orderResponse['redirect']) {
                    $orderResponse['redirect'] = Mage::getUrl('*/*/success');
                }
                $controllerAction->getResponse()->setBody(Mage::helper('core')->jsonEncode($orderResponse));
            }
        }
    }

    public function cleanDdateSessionData()
    {
        $session = Mage::getSingleton('core/session');
        $session->unsDdate();
        $session->unsDtime();
        $session->unsDdatei();
        $session->unsHeaderDdate();
        if (isset($_SESSION['ddate'])) {
            unset($_SESSION['ddate']);
        }
        if (isset($_SESSION['dtime'])) {
            unset($_SESSION['dtime']);
        }
    }
}
