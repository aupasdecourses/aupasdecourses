<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Popup
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Popup_Block_Adminhtml_Popup 
 * 
 * @category Apdc
 * @package  Popup
 * @uses     Apdc
 * @uses     Apdc_Popup_Block_Popup
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Popup_Block_Adminhtml_Popup extends Apdc_Popup_Block_Popup
{
    /**
     * getAjaxUrl 
     * 
     * @return string
     */
    public function getAjaxUrl()
    {
        return Mage::helper('adminhtml')->getUrl(
            'adminhtml/apdcPopup/templateAjax',
            array(
                'form_key' => Mage::getSingleton('core/session')->getFormKey()
            )
        ) . '?isAjax=true';
    }
}
