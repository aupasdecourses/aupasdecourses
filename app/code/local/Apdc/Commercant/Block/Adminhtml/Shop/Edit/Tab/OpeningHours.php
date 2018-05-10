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
 * Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_OpeningHours 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Tab_OpeningHours
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

        $timetableFieldset = $form->addFieldset(
            'timetable_fieldset',
            [
                'legend' => $this->__('Horaires d\'ouverture'),
            ]
        );
        $timetableFieldset->addField('timetable_hint', 'note', [
            'text' => $this->__('Format attendu: hh:mm-hh:mm, ie 9:30-17:00. Laisse VIDE si le magasin est fermé.')
        ]);
        $days = Mage::helper('apdc_commercant')->getDays();
        $deliveryDaysValues = [];
        foreach ($days as $index => $day) {
            $timetableFieldset->addField('timetable_'.$index, 'text', [
                'name' => 'timetable['.$index.']',
                'label' => $this->__($day),
                'required' => false,
                'disabled' => $isElementDisabled
            ]);

            $deliveryDaysValues[] = ['value' =>  $index + 1, 'label' => $this->__($day)];
        }

        $timetableFieldset->addField('delivery_days', 'multiselect', [
            'name' => 'delivery_days',
            'label' => $this->__('Jour de livraison APDC'),
            'required' => false,
            'values' => $deliveryDaysValues,
            'note'=>$this->__('Correspond au jour où APDC peut réaliser des livraisons.'),
            'disabled' => $isElementDisabled
        ]);

        $fieldset = $form->addFieldset(
            'closing_period',
            ['legend' => $this->__('Fermetures & Vacances')]
        );
        $fieldset->addType('closing_periods', Mage::getConfig()->getBlockClassName('apdc_commercant/adminhtml_form_element_closing'));
        $fieldset->addField('closing_periods', 'closing_periods', [
            'name' => 'closing_periods',
            'label' => $this->__('Périodes de fermeture'),
            'disabled' => $isElementDisabled
        ]);
        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
        } else {
            $data = $model->getData();
            if (empty($data['timetable'])) {
                foreach (Mage::helper('apdc_commercant')->getDays() as $index => $day) {
                    $data['timetable_'.$index] = '9:00-20:00';
                }
            } else {
                foreach ($data['timetable'] as $index => $dayData) {
                    $data['timetable_'.$index] = $dayData;
                }
            }
            $form->setValues($data);
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
        return $this->_helper()->__('Horaires d\'ouverture');
    }

    /**
     * getTabTitle
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('Horaires d\'ouverture');
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
