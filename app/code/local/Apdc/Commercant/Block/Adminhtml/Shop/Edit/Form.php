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

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
