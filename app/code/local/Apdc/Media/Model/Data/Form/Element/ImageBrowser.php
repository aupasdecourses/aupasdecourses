<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Media
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Media_Model_Data_Form_Element_ImageBrowser 
 * 
 * @category Apdc
 * @package  Media
 * @uses     Varien
 * @uses     Varien_Data_Form_Element_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Media_Model_Data_Form_Element_ImageBrowser extends Varien_Data_Form_Element_Abstract
{
    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        if ((string)$this->getValue()) {
            $url = $this->_getUrl();

            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media') . $url;
            }

            $html = '<a href="' . $url . '"'
                . ' onclick="imagePreview(\'' . $this->getHtmlId() . '_image\'); return false;">'
                . '<img src="' . $url . '" id="' . $this->getHtmlId() . '_image" title="' . $this->getValue() . '"'
                . ' alt="' . $this->getValue() . '" height="22" width="22" class="small-image-preview v-middle" />'
                . '</a> ';
        }
        $this->setClass('input-file');
        $html .= parent::getElementHtml();
        $html .= $this->getBrowserButtonHtml();

        return $html;
    }

    /**
     * getBrowserButtonHtml 
     * 
     * @return string
     */
    protected function getBrowserButtonHtml()
    {
        $button = '<button type="button" class="scalable add-image plugin"'
            . ' onclick="MediabrowserUtility.openDialog(\'' . Mage::helper('adminhtml')->getUrl('adminhtml/apdcImageBrowser/index', array('target_element_id' => $this->getHtmlId())) . '\')" style="">'
            . '<span><span><span>Insert Image...</span></span></span>'
            . '</button>';
        return $button;
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return  $this->getData('name');
    }
}
