<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Commercant_Edit_Form
 */
class Apdc_Commercant_Block_Adminhtml_Commercant_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('commercant');

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
            $fieldset->addField('id_commercant', 'hidden', ['name' => 'id_commercant']);
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => $this->__('Name'),
            'required' => true,
        ]);

        $fieldset->addField('siren', 'text', [
            'name' => 'siren',
            'label' => $this->__('SIREN'),
            'required' => true,
        ]);

        $fieldset->addField('vat_number', 'text', [
            'name' => 'vat_number',
            'label' => $this->__('VAT number'),
            'required' => true,
        ]);

        $availableBankInfo = Mage::getModel('apdc_commercant/bankInfo')->getCollection()->toOptionArray();
        $fieldset->addField('id_bank_information', 'select', [
            'name' => 'id_bank_information',
            'label' => $this->__('Bank information'),
            'required' => false,
            'values' => $availableBankInfo,
        ]);

        $availableCeos = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addFieldToFilter('type', Apdc_Commercant_Model_Source_Contact_Type::TYPE_CEO)
            ->toOptionArray();
        $fieldset->addField('id_contact_ceo', 'select', [
            'name' => 'id_contact_ceo',
            'label' => $this->__('CEO contact'),
            'required' => true,
            'values' => $availableCeos,
        ]);

        $fieldset->addField('ceo_dob', 'date', [
            'name' => 'ceo_dob',
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label' => $this->__('CEO Date of birth'),
            'required' => true,
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) //Varien_Date::DATE_INTERNAL_FORMAT,
        ]);

        $availableBillingContacts = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addFieldToFilter('type', Apdc_Commercant_Model_Source_Contact_Type::TYPE_BILLING)
            ->toOptionArray();
        $fieldset->addField('id_contact_billing', 'select', [
            'name' => 'id_contact_billing',
            'label' => $this->__('Billing contact'),
            'required' => true,
            'values' => $availableBillingContacts,
        ]);


        $fieldset = $form->addFieldset(
            'headquarters',
            ['legend' => $this->__('Headquarters')]
        );

        $fieldset->addField('hq_siret', 'text', [
            'name' => 'hq_siret',
            'label' => $this->__('SIRET'),
            'required' => true,
        ]);

        $fieldset->addField('hq_street', 'text', [
            'name' => 'hq_street',
            'label' => $this->__('Address'),
            'required' => true,
        ]);

        $fieldset->addField('hq_postcode', 'text', [
            'name' => 'hq_postcode',
            'label' => $this->__('Zip/Postal Code'),
            'required' => true,
        ]);

        $fieldset->addField('hq_city', 'text', [
            'name' => 'hq_city',
            'label' => $this->__('City'),
            'required' => true,
        ]);

        $countries = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
        unset($countries[0]);
        $fieldset->addField('hq_country', 'select', [
            'name' => 'hq_country',
            'label' => $this->__('Country'),
            'required' => true,
            'values' => $countries,
        ]);

        $data = $model->getData();
        $form->setValues($data);


        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
