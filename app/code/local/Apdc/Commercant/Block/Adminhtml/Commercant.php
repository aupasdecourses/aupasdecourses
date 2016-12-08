<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Commercant
 */
class Apdc_Commercant_Block_Adminhtml_Commercant extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'apdc_commercant';
        $this->_controller = 'adminhtml_commercant';
        $this->_headerText = $this->__('Commercants');
        $this->_addButtonLabel = $this->__('Ajouter un commercant');
        parent::__construct();
    }
}
