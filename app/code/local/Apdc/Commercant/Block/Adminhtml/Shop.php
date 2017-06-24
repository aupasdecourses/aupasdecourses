<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Shop
 */
class Apdc_Commercant_Block_Adminhtml_Shop extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'apdc_commercant';
        $this->_controller = 'adminhtml_shop';
        $this->_headerText = $this->__('Magasins');
        $this->_addButtonLabel = $this->__('Ajouter un magasin');
        parent::__construct();
    }
}
