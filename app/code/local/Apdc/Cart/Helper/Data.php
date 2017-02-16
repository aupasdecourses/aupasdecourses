<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Cart
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Cart_Helper_Data 
 * 
 * @category Apdc
 * @package  Cart
 * @uses     Mage
 * @uses     Mage_Core_Helper_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Cart_Helper_Data extends Mage_Checkout_Helper_Cart
{
    /**
     * Retrieve url for add product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAjaxAddUrl($product, $additional = array())
    {
        $routeParams = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->_getHelperInstance('core')
                ->urlEncode($this->getCurrentUrl()),
            'product' => $product->getEntityId(),
            Mage_Core_Model_Url::FORM_KEY => $this->_getSingletonModel('core/session')->getFormKey()
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart') {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('addajax/index/add', $routeParams);
    }

    /**
     * Retrieve url for add product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAjaxUpdateItemOptionsUrl($product, $additional = array())
    {
        $routeParams = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->_getHelperInstance('core')
                ->urlEncode($this->getCurrentUrl()),
            'product' => $product->getEntityId(),
            'id' => $product->getCartItemId(),
            'isAjax' => 1,
            Mage_Core_Model_Url::FORM_KEY => $this->_getSingletonModel('core/session')->getFormKey()
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart') {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('checkout/cart/ajaxUpdateItemOptions', $routeParams);
    }

    /**
     * getAjaxUpdateUrl 
     * Get item ajax update url
     * 
     * @param Mage_Sales_Model_Quote_Item $item item 
     * 
     * @return string
     */
    public function getAjaxUpdateUrl(Mage_Sales_Model_Quote_Item $item)
    {
        $lastUrl = null;
        if (Mage::app()->getRequest()->getParam('uenc')) {
            $lastUrl = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('uenc'));
        }
        return Mage::getUrl(
            'checkout/cart/ajaxUpdate',
            array(
                'id' => $item->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core/url')->getEncodedUrl($lastUrl),
                '_secure' => Mage::app()->getStore()->isCurrentlySecure(),
            )
        );
    }

    /**
     * getAjaxDeleteUrl 
     * Get item ajax delete url
     * 
     * @param Mage_Sales_Model_Quote_Item $item item 
     * 
     * @return string
     */
    public function getAjaxDeleteUrl(Mage_Sales_Model_Quote_Item $item)
    {
        $lastUrl = null;
        if (Mage::app()->getRequest()->getParam('uenc')) {
            $lastUrl = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('uenc'));
        }
        return Mage::getUrl(
            'checkout/cart/ajaxDelete',
            array(
                'id' => $item->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core/url')->getEncodedUrl($lastUrl),
                '_secure' => Mage::app()->getStore()->isCurrentlySecure(),
            )
        );
    }
}
