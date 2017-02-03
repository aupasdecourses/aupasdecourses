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
            ['legend' => $this->__('Général')]
        );

        if ($model->getId()) {
            $fieldset->addField('id_commercant', 'hidden', ['name' => 'id_commercant']);
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => $this->__('Nom'),
            'required' => true,
            'note' => $this->__('Utiliser le nom qui apparait sur le KBIS ou par défaut le nom du magasin principal en MAJUSCULE'),
        ]);

        $fieldset->addField('siren', 'text', [
            'name' => 'siren',
            'label' => $this->__('SIREN'),
            'required' => true,
        ]);

        $fieldset->addField('vat_number', 'text', [
            'name' => 'vat_number',
            'label' => $this->__('Numéro TVA'),
            'required' => true,
        ]);

        $availableBankInfo = Mage::getModel('apdc_commercant/bankInfo')->getCollection()->toOptionArray();
        array_unshift($availableBankInfo, ['value' => '', 'label' => '']);
        $fieldset->addField('id_bank_information', 'select', [
            'name' => 'id_bank_information',
            'label' => $this->__('Infos bancaires'),
            'required' => false,
            'values' => $availableBankInfo,
        ]);

        $availableCeos = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addRoleFilter(Apdc_Commercant_Model_Source_Contact_Type::TYPE_CEO)
            ->toOptionArray();
        array_unshift($availableCeos, ['value' => '', 'label' => '']);
        $fieldset->addField('id_contact_ceo', 'select', [
            'name' => 'id_contact_ceo',
            'label' => $this->__('Contact gérant'),
            'required' => true,
            'values' => $availableCeos,
        ]);

        $availableBillingContacts = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addRoleFilter(Apdc_Commercant_Model_Source_Contact_Type::TYPE_BILLING)
            ->toOptionArray();
        array_unshift($availableBillingContacts, ['value' => '', 'label' => '']);
        $fieldset->addField('id_contact_billing', 'select', [
            'name' => 'id_contact_billing',
            'label' => $this->__('Contact facturation'),
            'required' => true,
            'values' => $availableBillingContacts,
        ]);


        $fieldset = $form->addFieldset(
            'headquarters',
            ['legend' => $this->__('Siège social')]
        );

        $fieldset->addField('hq_siret', 'text', [
            'name' => 'hq_siret',
            'label' => $this->__('SIRET'),
            'required' => true,
        ]);

        $fieldset->addField('hq_street', 'text', [
            'name' => 'hq_street',
            'label' => $this->__('Rue'),
            'required' => true,
        ]);

        $fieldset->addField('hq_postcode', 'text', [
            'name' => 'hq_postcode',
            'label' => $this->__('Code Postal'),
            'required' => true,
        ]);

        $fieldset->addField('hq_city', 'text', [
            'name' => 'hq_city',
            'label' => $this->__('Ville'),
            'required' => true,
        ]);

        $countries = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
        unset($countries[0]);
        $fieldset->addField('hq_country', 'select', [
            'name' => 'hq_country',
            'label' => $this->__('Pays'),
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
