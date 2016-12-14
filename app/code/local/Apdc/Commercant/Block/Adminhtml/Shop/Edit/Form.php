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

        $fieldset->addField('enabled', 'select', [
            'name' => 'enabled',
            'label' => $this->__('Enabled'),
            'required' => false,
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ]);
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
            ->addRoleFilter(Apdc_Commercant_Model_Source_Contact_Type::TYPE_MANAGER)
            ->toOptionArray();
        $fieldset->addField('id_contact_manager', 'select', [
            'name' => 'id_contact_manager',
            'label' => $this->__('Manager contact'),
            'required' => false,
            'values' => $availableManagers,
        ]);

        $availableEmployees = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addRoleFilter(Apdc_Commercant_Model_Source_Contact_Type::TYPE_EMPLOYEE)
            ->toOptionArray();
        $fieldset->addField('id_contact_employee', 'select', [
            'name' => 'id_contact_employee',
            'label' => $this->__('Employee contact'),
            'required' => false,
            'values' => $availableEmployees,
        ]);
        $fieldset->addField('id_contact_employee_bis', 'select', [
            'name' => 'id_contact_employee_bis',
            'label' => $this->__('Employee contact 2'),
            'required' => false,
            'values' => $availableEmployees,
        ]);

        $commercantCategories = Mage::getModel('catalog/category')
            ->getCollection()
            ->setOrder('name')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('estcom_commercant', 70);
        $values = [];
        foreach ($commercantCategories as $category) {
            $values[$category->getId()] = $category->getName();
        }
        $fieldset->addField('id_category', 'select', [
            'name' => 'id_category',
            'label' => $this->__('Category'),
            'required' => true,
            'values' => $values,
            'note' => 'Category owning the products associated to this shop',
        ]);
        $values = Mage::getSingleton('eav/config')
            ->getAttribute('catalog_product', 'commercant')
            ->getSource()
            ->getAllOptions();
        $fieldset->addField('id_attribut_commercant', 'select', [
            'name' => 'id_attribut_commercant',
            'label' => $this->__('Attribut Commercant'),
            'required' => true,
            'values' => $values,
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
        $fieldset->addField('closing_periods', 'closing_periods', [
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
            'text' => $this->__('Expected format is hh:mm-hh:mm, e.g. 9:30-17:00. Leave empty if the shop is closed.')
        ]);
        $days = Mage::helper('apdc_commercant')->getDays();
        $deliveryDaysValues = [];
        foreach ($days as $index => $day) {
            $timetableFieldset->addField('timetable_'.$index, 'text', [
                'name' => 'timetable['.$index.']',
                'label' => $this->__($day),
                'required' => false,
            ]);

            $deliveryDaysValues[] = ['value' =>  $index + 1, 'label' => $this->__($day)];
        }

        $timetableFieldset->addField('delivery_days', 'multiselect', [
            'name' => 'delivery_days',
            'label' => $this->__('Delivery days'),
            'required' => false,
            'values' => $deliveryDaysValues,
        ]);
    }
}
