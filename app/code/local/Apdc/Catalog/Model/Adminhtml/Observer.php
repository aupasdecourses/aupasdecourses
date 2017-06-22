<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Model_Adminhtml_Observer 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Adminhtml_Observer
{
    /**
     * setImageBrowserTypeForCategoryImages 
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function setImageBrowserTypeForCategoryImages(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();
        if ($form->getData('html_id_prefix') == 'group_4') {
            $elements = $form->getElements();
            $fieldset = $form->getElement('fieldset_group_4');

            foreach ($fieldset->getElements() as $element) {
                if ($element->getData('html_id') == 'thumbnail') {
                    $thumbnailValue = $element->getValue();
                    $fieldset->removeField($element->getData('html_id'));
                } else if ($element->getData('html_id') == 'image') {
                    $imageValue = $element->getValue();
                    $fieldset->removeField($element->getData('html_id'));
                }
            }

            if ($thumbnailValue && !preg_match('/^wysiwyg\//', $thumbnailValue) && !preg_match('/^catalog\/category\//', $thumbnailValue)) {
                $thumbnailValue = 'catalog/category/' . $thumbnailValue;
            }
            if ($imageValue && !preg_match('/^wysiwyg\//', $imageValue) && !preg_match('/^catalog\/category\//', $imageValue)) {
                $imageValue = 'catalog/category/' . $imageValue;
            }
            $fieldset->addType('image_browser', 'Apdc_Media_Model_Data_Form_Element_ImageBrowser');
            $fieldset->addField(
                'thumbnail',
                'image_browser',
                array(
                    'name' => 'general[thumbnail]',
                    'label' => Mage::helper('catalog')->__('Thumbnail'),
                    'required' => false,
                    'style' => 'height:22px; width:50%;',
                    'value' => $thumbnailValue
                ),
                'description'
            );
            $fieldset->addField(
                'image',
                'image_browser',
                array(
                    'name' => 'general[image]',
                    'label' => Mage::helper('catalog')->__('Image'),
                    'required' => false,
                    'style' => 'height:22px; width:50%;',
                    'value' => $imageValue
                ),
                'thumbnail'
            );
        }
    }
}
