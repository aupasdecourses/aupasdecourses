<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_BankInfo
 */
class Apdc_Commercant_Block_Adminhtml_BankInfo extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'apdc_commercant';
        $this->_controller = 'adminhtml_bankInfo';
        $this->_headerText = $this->__('Infos bancaires &amp; légales');
        $this->_addButtonLabel = $this->__('Ajouter une info');
        parent::__construct();
    }
}
