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
 * Apdc_Popup_Block_Popup 
 * 
 * @category Apdc
 * @package  Popup
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Popup_Block_Popup extends Mage_Core_Block_Template
{
    /**
     * getId 
     * 
     * @return string
     */
    public function getId()
    {
        if ($this->getData('id') != '') {
            return $this->getData('id');
        }
        return 'apdc';
    }

    /**
     * getAjaxUrl 
     * 
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl(
            'apdc_popup/index/templateAjax',
            array(
                'isAjax' => true
            )
        );
    }
}
