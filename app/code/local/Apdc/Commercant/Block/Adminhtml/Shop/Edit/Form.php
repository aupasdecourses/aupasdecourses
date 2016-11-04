<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Form
 */
class Apdc_Commercant_Block_Adminhtml_Shop_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('shop');

        $form = new Varien_Data_Form(
            [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ]
        );

        $fieldset = $form->addFieldset(
            'base',
            ['legend' => $this->__('General')]
        );

        if ($model->getId()) {
            $fieldset->addField('id_shop', 'hidden', ['name' => 'id_shop']);
        }

        $commercants = Mage::getModel('apdc_commercant/commercant')->getCollection()->toOptionArray();
        $fieldset->addField('id_commercant', 'select', [
            'name' => 'id_commercant',
            'label' => $this->__('Commercant'),
            'required' => true,
            'values' => $commercants,
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => $this->__('Name'),
            'required' => true,
        ]);

        $fieldset->addField('code', 'text', [
            'name' => 'code',
            'label' => $this->__('Code'),
            'required' => true,
        ]);

        $fieldset->addField('website', 'text', [
            'name' => 'website',
            'label' => $this->__('Website'),
            'required' => false,
        ]);

        $fieldset->addField('siret', 'text', [
            'name' => 'siret',
            'label' => $this->__('SIRET'),
            'required' => true,
        ]);

        $fieldset->addField('vat_number', 'text', [
            'name' => 'vat_number',
            'label' => $this->__('VAT number'),
            'required' => true,
        ]);

        $availableManagers = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addFieldToFilter('type', Apdc_Commercant_Model_Source_Contact_Type::TYPE_MANAGER)
            ->toOptionArray();
        $fieldset->addField('id_contact_manager', 'select', [
            'name' => 'id_contact_manager',
            'label' => $this->__('Manager contact'),
            'required' => false,
            'values' => $availableManagers,
        ]);

        $availableEmployees = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addFieldToFilter('type', Apdc_Commercant_Model_Source_Contact_Type::TYPE_EMPLOYEE)
            ->toOptionArray();
        $fieldset->addField('id_contact_employee', 'select', [
            'name' => 'id_contact_employee',
            'label' => $this->__('Employee contact'),
            'required' => false,
            'values' => $availableEmployees,
        ]);

        $fieldset->addField('id_category', 'text', [
            'name' => 'id_category',
            'label' => $this->__('Category ID'),
            'required' => false,
            'values' => [],
        ]);


        $fieldset = $form->addFieldset(
            'address',
            ['legend' => $this->__('Address')]
        );

        $fieldset->addField('street', 'text', [
            'name' => 'street',
            'label' => $this->__('Address'),
            'required' => true,
        ]);

        $fieldset->addField('postcode', 'text', [
            'name' => 'postcode',
            'label' => $this->__('Zip/Postal Code'),
            'required' => true,
        ]);

        $fieldset->addField('city', 'text', [
            'name' => 'city',
            'label' => $this->__('City'),
            'required' => true,
        ]);

        $fieldset->addField('phone', 'text', [
            'name' => 'phone',
            'label' => $this->__('Telephone'),
            'required' => true,
        ]);

        $this->_addTimetable($form);

        $fieldset = $form->addFieldset(
            'closing_period',
            ['legend' => $this->__('Closing periods')]
        );
        $fieldset->addType('closing_periods', Mage::getConfig()->getBlockClassName('apdc_commercant/adminhtml_form_element_closing'));
        $field = $fieldset->addField('closing_periods', 'closing_periods', [
            'name' => 'closing_periods',
            'label' => $this->__('Closing periods'),
        ]);

        $fieldset = $form->addFieldset(
            'google',
            ['legend' => $this->__('Google document')]
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

        $data = $model->getData();
        if (isset($data['timetable'])) {
            foreach ($data['timetable'] as $index => $dayData) {
                $data['timetable_'.$index] = $dayData;
            }
        }
        $form->setValues($data);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param Varien_Data_Form $form
     */
    protected function _addTimetable(Varien_Data_Form $form)
    {
        $timetableFieldset = $form->addFieldset(
            'timetable_fieldset',
            [
                'legend' => $this->__('Timetable'),
            ]
        );
        $timetableFieldset->addField('timetable_hint', 'note', [
            'text' => $this->__('Expected format is hh:mm-hh:mm, e.g. 9:30-17:00')
        ]);
        $days = Mage::helper('apdc_commercant')->getDays();
        foreach ($days as $index => $day) {
            $timetableFieldset->addField('timetable_'.$index, 'text', [
                'name' => 'timetable['.$index.']',
                'label' => $this->__($day),
                'required' => false,
            ]);
        }
    }
}
