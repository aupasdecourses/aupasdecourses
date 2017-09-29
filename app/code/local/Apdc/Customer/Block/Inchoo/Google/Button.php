<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Customer
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Customer_Block_Inchoo_Google_Button 
 * 
 * @category Apdc
 * @package  Customer
 * @uses     Inchoo
 * @uses     Inchoo_SocialConnect_Block_Google_Button
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Customer_Block_Inchoo_Google_Button extends Mage_Core_Block_Template
{
    /**
     *
     * @var Inchoo_SocialConnect_Model_Facebook_Oauth2_Client
     */
    protected $client = null;

    /**
     *
     * @var Inchoo_SocialConnect_Model_Facebook_Info_User
     */
    protected $userInfo = null;

    protected function _construct() {
        parent::_construct();

        $this->client = Mage::getSingleton('inchoo_socialconnect/google_oauth2_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $this->userInfo = Mage::registry('inchoo_socialconnect_google_userinfo');

        // CSRF protection
        $csrf = Mage::getSingleton('core/session')->getGoogleCsrf();
        if (!$csrf) {
            Mage::getSingleton('core/session')->setGoogleCsrf($csrf = md5(uniqid(rand(), true)));
        }
        $this->client->setState($csrf);

        if (!$this->isCurrentUrlAjax()) {
            Mage::getSingleton('customer/session')->setSocialConnectRedirect(Mage::helper('core/url')->getCurrentUrl());
        }

        $this->setTemplate('inchoo/socialconnect/google/button.phtml');
    }
    /**
     * _getButtonUrls 
     * 
     * @return array
     */
    protected function _getButtonUrls()
    {
        $urls = [
            'href' => '#',
            'ajax_url' => '#'
        ];
        if(is_null($this->userInfo) || !$this->userInfo->hasData()) {
            if ($this->getCurrentControllerPath() == 'socialconnect/account/google') {
                $urls['href'] = $this->client->createAuthUrl();
            } else {
                $urls['ajax_url'] = $this->getUrl('apdc_customer/google/ajaxLogin');
            }
        } else {
            $urls['href'] = $this->getUrl('socialconnect/google/disconnect');
        }
        return $urls;
    }

    /**
     * _getButtonText 
     * 
     * @return string
     */
    protected function _getButtonText()
    {
        if(is_null($this->userInfo) || !$this->userInfo->hasData()) {
            if(!($text = Mage::registry('inchoo_socialconnect_button_text'))){
                $text = $this->__('Connexion avec Google');
            }
        } else {
            $text = $this->__('Déconnection de Google');
        }

        return $text;
    }

    /**
     * accountIsLinked 
     * 
     * @return boolean
     */
    public function accountIsLinked()
    {
        return !(is_null($this->userInfo) || !$this->userInfo->hasData());
    }

    /**
     * getButtonId 
     * 
     * @return string
     */
    public function getButtonId()
    {
        if ($this->accountIsLinked() || $this->getCurrentControllerPath() == 'socialconnect/account/google') {
            return 'inchoo_google_account';
        }
        return 'connect_with_google';
    }

    /**
     * getCurrentControllerPath 
     * 
     * @return string
     */
    protected function getCurrentControllerPath()
    {
        return Mage::app()->getRequest()->getModuleName() . '/' .
            Mage::app()->getRequest()->getControllerName() . '/' .
            Mage::app()->getRequest()->getActionName();
    }

    protected function isCurrentUrlAjax()
    {
        if (Mage::app()->getRequest()->getParam('isAjax', false)) {
            return true;
        }
        return false;
    }

    /**
     * mustHide 
     * 
     * @return boolean
     */
    public function mustHide()
    {
        if (!$this->isCurrentUrlAjax() && $this->getCurrentControllerPath() != 'socialconnect/account/google') {
            return true;
        }
        return false;
    }
}
