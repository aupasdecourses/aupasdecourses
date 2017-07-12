<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Dataflow
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Dataflow_Block_Adminhtml_System_Convert_Profile_Edit_Tab_Run 
 * 
 * @category Apdc
 * @package  Dataflow
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Run
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Dataflow_Block_Adminhtml_System_Convert_Profile_Edit_Tab_Run extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Run
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('apdc/apdc_dataflow/system/convert/profile/run.phtml');
    }
    public function getRunButtonHtml()
    {
        $html = parent::getRunButtonHtml();

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $this->_helper()->__('Filtrage')));
        $this->getCommercantsOptions();
        $fieldset->addField(
            'commercant',
            'select',
            array(
                'name' => 'filters[commercant]',
                'label' => $this->_helper()->__('Commercant'),
                'title' => $this->_helper()->__('Commercant'),
                'class' => 'profile_filter',
                'values' => $this->getCommercantsOptions(),
                'required' => false
            )
        );

        return $form->toHtml() . $html;
    }

    protected function getCommercantsOptions()
    {
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('catalog_product','commercant');
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        $options = $attribute ->getSource()->getAllOptions(false);
        array_unshift($options, array('label' => $this->_helper()->__('-- Tous les commerçants --'), 'value' => ''));
        return $options;
    }

    /**
     * _helper 
     * 
     * @return Apdc_Dataflow_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('apdc_dataflow');
    }
}
