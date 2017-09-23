<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_BankInfo_Edit_Form
 */
class Apdc_Commercant_Block_Adminhtml_BankInfo_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('bankInfo');

        $form = new Varien_Data_Form(
            [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ]
        );

        $fieldset = $form->addFieldset(
            'base',
            ['legend' => $this->__('Général')]
        );

        if ($model->getId()) {
            $fieldset->addField('id_bank_information', 'hidden', ['name' => 'id_bank_information']);
        }

        $fieldset->addField('owner_name', 'text', [
            'name' => 'owner_name',
            'label' => $this->__('Nom du propriétaire du compte'),
            'required' => true,
            'note' => $this->__('Utiliser le nom qui apparaît sur le IBAN'),
        ]);

        $fieldset->addField('account_iban', 'text', [
            'name' => 'account_iban',
            'label' => $this->__('IBAN'),
            'required' => true,
        ]);

        $fieldset->addField('account_bic', 'text', [
            'name' => 'account_bic',
            'label' => $this->__('BIC'),
            'required' => true,
        ]);

        $fieldset = $form->addFieldset(
            'legal',
            ['legend' => $this->__('Documents légaux')]
        );
        $fieldset->addType('file', Mage::getConfig()->getBlockClassName('apdc_commercant/adminhtml_form_element_file'));

        $fieldset->addField('id_card', 'file', [
            'name' => 'id_card',
            'label' => $this->__('Carte d\'identité'),
            'required' => false,
            'path' => $model->getMediaSubpath('id_card'),
        ]);
        $fieldset->addField('bank_account', 'file', [
            'name' => 'bank_account',
            'label' => $this->__('RIB'),
            'required' => false,
            'path' => $model->getMediaSubpath('bank_account'),
        ]);
        $fieldset->addField('kbis', 'file', [
            'name' => 'kbis',
            'label' => $this->__('KBIS'),
            'required' => false,
            'path' => $model->getMediaSubpath('kbis'),
        ]);
        $fieldset->addField('status', 'file', [
            'name' => 'status',
            'label' => $this->__('Statuts'),
            'required' => false,
            'path' => $model->getMediaSubpath('status'),
        ]);
        $fieldset->addField('licence', 'file', [
            'name' => 'licence',
            'label' => $this->__('Autres docs (licence caviste ...)'),
            'required' => false,
            'path' => $model->getMediaSubpath('licence'),
        ]);

        $fieldset = $form->addFieldset(
            'bank',
            ['legend' => $this->__('Détails compte bancaire')]
        );

        $fieldset->addField('bank_name', 'text', [
            'name' => 'bank_name',
            'label' => $this->__('Nom'),
            'required' => true,
        ]);

        $fieldset->addField('bank_street', 'text', [
            'name' => 'bank_street',
            'label' => $this->__('Rue'),
            'required' => true,
        ]);

        $fieldset->addField('bank_postcode', 'text', [
            'name' => 'bank_postcode',
            'label' => $this->__('Code Postal'),
            'required' => true,
        ]);

        $fieldset->addField('bank_city', 'text', [
            'name' => 'bank_city',
            'label' => $this->__('Ville'),
            'required' => true,
        ]);
        $countries = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
        $fieldset->addField('bank_country', 'select', [
            'name' => 'bank_country',
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
