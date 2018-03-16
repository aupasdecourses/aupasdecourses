<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */

require_once(Mage::getModuleDir('controllers','Apdc_Partner').DS.'AbstractController.php');

/**
 * Apdc_Partner_CartController 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Apdc
 * @uses     Apdc_Partner_AbstractController
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_CartController extends Apdc_Partner_AbstractController
{
    /**
     * listAction 
     * 
     * @return string
     */
    public function createAction()
    {
        return parent::mainAction();
    }

    protected function execute(Apdc_Partner_Model_Partner $partner)
    {
        try {
            $post = $this->getRequest()->getPost();
            if (!isset($post['products'])) {
                throw new \Exception('Unable to find Products datas');
            }
            if (!isset($post['postcode'])) {
                throw new \Exception('Unable to find postcode');
            }
            $cart = Mage::getModel('apdc_partner/data_cart')
                ->setPartner($partner)
                ->setCartData($post)
                ->createCart();

            // emulate store to get the redirectUrl
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($cart->getStoreId());
            echo json_encode(['quote_id' => $cart->getId(), 'redirect_url' => $this->getRedirectUrl($cart->getId())]);
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        } catch (Exception $e) {
            echo json_encode(['message' => $e->getMessage(), 'error' => 500]);
            exit(1);
        }

        return;
    }

    public function getRedirectUrl($quoteId)
    {
        $post = $this->getRequest()->getPost();
        $key = $post['key'];
        $signature = $this->getSignature();
        $params = [
            'quote_id' => $quoteId,
            'key' => $key,
            'signature' => $signature
        ];
        return Mage::getUrl('*/*/redirect', ['aaa' => base64_encode(json_encode($params))]);
    }

    public function redirectAction()
    {
        try {
            if ($params = $this->getRequest()->getParam('aaa', false)) {
                $params = json_decode(base64_decode($params), true);
                if (isset($params['quote_id']) && isset($params['key']) && isset($params['signature'])) {
                    $partner = Mage::getModel('apdc_partner/partner');
                    if ($partner->login($params['key'], $params['signature'])) {
                        Mage::getModel('apdc_partner/data_cart')->loadQuote($params['quote_id']);
                        return $this->_redirectUrl(Mage::getUrl('/') . '?opencart=1');
                    }

                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this->norouteAction();

    }
}
