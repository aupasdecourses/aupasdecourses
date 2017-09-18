<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Contact_Edit
 */
class Apdc_Commercant_Block_Adminhtml_Contact_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id_contact';
        $this->_blockGroup = 'apdc_commercant';
        $this->_controller = 'adminhtml_contact';
        $this->_headerText = $this->__('Contact');

        parent::__construct();
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }

        return $this->getUrl('*/*/save');
    }
}
