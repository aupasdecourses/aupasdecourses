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
class Apdc_Customer_Block_Inchoo_Google_Button extends Inchoo_SocialConnect_Block_Google_Button
{
    /**
     * _getButtonUrl 
     * 
     * @return string
     */
    protected function _getButtonUrl()
    {
        if(is_null($this->userInfo) || !$this->userInfo->hasData()) {
            if ($this->getCurrentControllerPath() == 'socialconnect/account/google') {
                return $this->client->createAuthUrl();
            }
            return $this->getUrl('apdc_customer/google/ajaxLogin');
        } else {
            return $this->getUrl('socialconnect/google/disconnect');
        }
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
                $text = $this->__('Se Connecter');
            }
        } else {
            $text = $this->__('Se Déconnecter');
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
}
