<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Form_Element_File
 */
class Apdc_Commercant_Block_Adminhtml_Form_Element_File extends Mage_Adminhtml_Block_Customer_Form_Element_File
{
    /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        return Mage::getBaseUrl('media') . $this->getPath() . '/' . $this->getValue();
    }

    protected function _getHiddenInput()
    {
        return '';
    }
}
