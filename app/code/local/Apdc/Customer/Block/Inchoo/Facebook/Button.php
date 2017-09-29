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
 * Apdc_Customer_Block_Inchoo_Facebook_Button 
 * 
 * @category Apdc
 * @package  Customer
 * @uses     Inchoo
 * @uses     Inchoo_SocialConnect_Block_Facebook_Button
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Customer_Block_Inchoo_Facebook_Button extends Mage_Core_Block_Template
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

        $this->client = Mage::getSingleton('inchoo_socialconnect/facebook_oauth2_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $this->userInfo = Mage::registry('inchoo_socialconnect_facebook_userinfo');

        // CSRF protection
        $csrf = Mage::getSingleton('core/session')->getFacebookCsrf();
        if (!$csrf) {
            Mage::getSingleton('core/session')->setFacebookCsrf($csrf = md5(uniqid(rand(), true)));
        }
        $this->client->setState($csrf);

        if (!$this->isCurrentUrlAjax()) {
            Mage::getSingleton('customer/session')->setSocialConnectRedirect(Mage::helper('core/url')->getCurrentUrl());
        }

        $this->setTemplate('inchoo/socialconnect/facebook/button.phtml');
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
            if ($this->getCurrentControllerPath() == 'socialconnect/account/facebook') {
                $urls['href'] = $this->client->createAuthUrl();
            } else {
                $urls['ajax_url'] = $this->getUrl('apdc_customer/facebook/ajaxLogin');
            }
        } else {
            $urls['href'] = $this->getUrl('socialconnect/facebook/disconnect');
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
                $text = $this->__('Connexion avec Facebook');
            }
        } else {
            $text = $this->__('Déconnection de Facebook');
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
        if ($this->accountIsLinked() || $this->getCurrentControllerPath() == 'socialconnect/account/facebook') {
            return 'inchoo_facebook_account';
        }
        return 'connect_with_facebook';
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

    /**
     * isCurrentUrlAjax 
     * 
     * @return boolean
     */
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
        if (!$this->isCurrentUrlAjax() && $this->getCurrentControllerPath() != 'socialconnect/account/facebook') {
            return true;
        }
        return false;
    }
}
