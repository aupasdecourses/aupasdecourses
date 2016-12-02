<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  SuperMenu
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_SuperMenu_Block_Adminhtml_Catalog_Category_Form_Element_Color 
 * 
 * @category Apdc
 * @package  SuperMenu
 * @uses     Varien
 * @uses     Varien_Data_Form_Element_Text
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_SuperMenu_Block_Adminhtml_Catalog_Category_Form_Element_Color extends Varien_Data_Form_Element_Text
{
    public function getElementHtml()
    {
        $html = '<input id="'.$this->getHtmlId().'" name="'.$this->getName()
             .'" value="'.$this->getEscapedValue().'" '.$this->serialize($this->getHtmlAttributes()).'  style="width:200px!important;"/>'."\n";
        $html .= '<input id="'.$this->getHtmlId() .'_colorpicker" value="'.$this->getEscapedValue().'" type="color" />'."\n";
        $html .= '<script type="text/javascript">';
        $html .= '(function() {';
        $html .= 'var text = document.getElementById("' . $this->getHtmlId() . '");';
        $html .= 'var color = document.getElementById("' . $this->getHtmlId() . '_colorpicker");';
        $html .= 'text.addEventListener("change", function() {';
        $html .= 'color.value = text.value;';
        $html .= '});';
        $html .= 'color.addEventListener("change", function() {';
        $html .= 'text.value = color.value;';
        $html .= '});';
        $html .= '})();';
        $html .= '</script>';
        $html .= $this->getAfterElementHtml();
        return $html;
    }
}

