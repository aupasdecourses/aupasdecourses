<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Contact
 */
class Apdc_Commercant_Block_Adminhtml_Contact extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'apdc_commercant';
        $this->_controller = 'adminhtml_contact';
        $this->_headerText = $this->__('Contacts');
        $this->_addButtonLabel = $this->__('Ajouter un contact');
        parent::__construct();
    }
}
