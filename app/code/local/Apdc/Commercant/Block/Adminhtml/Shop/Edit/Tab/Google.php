<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_Google 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_Google
    extends Mage_Adminhtml_Block_Widget_Form 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('shop');
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset(
            'google',
            ['legend' => $this->__('Tableau produits Google Sheets')]
        );

        $fieldset->addField(
            'flag_magmi',
            'select',
            array(
                'name' => 'flag_magmi',
                'label' => $this->_helper()->__('Actif dans Magmi'),
                'title' => $this->_helper()->__('Actif dans Magmi'),
                'values' => Mage::getSingleton('adminhtml/system_config_source_enabledisable')->toOptionArray(),
                'required' => true,
            )
        );
        
        $fieldset->addField('google_id', 'text', [
            'name' => 'google_id',
            'label' => $this->__('Google id'),
            'required' => false,
        ]);

        $fieldset->addField('google_key', 'text', [
            'name' => 'google_key',
            'label' => $this->__('Google key'),
            'required' => false,
        ]);

        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
        } else {
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * getTableLabel
     * 
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_helper()->__('Google');
    }

    /**
     * getTabTitle
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('Google');
    }

    /**
     * canShowTab
     * 
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * isHidden
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * _helper 
     * 
     * @return Apdc_Commercant_Helper_Data
     */
    private function _helper()
    {
        return Mage::helper('apdc_commercant');
    }

    /**
     * _isAllowedAction
     * 
     * @param string $action : action
     * 
     * @return boolean
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/commercant/shop/' . $action);
    }
}
