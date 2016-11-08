<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Contact_Edit_Form
 */
class Apdc_Commercant_Block_Adminhtml_Contact_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('contact');

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
            $fieldset->addField('id_contact', 'hidden', ['name' => 'id_contact']);
        }

        $fieldset->addField('lastname', 'text', [
            'name' => 'lastname',
            'label' => $this->__('Last name'),
            'required' => true,
        ]);

        $fieldset->addField('firstname', 'text', [
            'name' => 'firstname',
            'label' => $this->__('First name'),
            'required' => true,
        ]);

        $fieldset->addField('dob', 'date', [
            'name' => 'dob',
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label' => $this->__('Date of birth'),
            'required' => false,
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ]);

        $fieldset->addField('email', 'text', [
            'name' => 'email',
            'label' => $this->__('Email'),
            'required' => false,
        ]);

        $fieldset->addField('phone', 'text', [
            'name' => 'phone',
            'label' => $this->__('Telephone'),
            'required' => false,
        ]);

        $fieldset->addField('role_id', 'multiselect', [
            'name' => 'role_id',
            'label' => $this->__('Contact roles'),
            'required' => true,
            'values' => Mage::getModel('apdc_commercant/source_contact_type')->toOptionArray(),
        ]);

        $data = $model->getData();
        $form->setValues($data);


        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
