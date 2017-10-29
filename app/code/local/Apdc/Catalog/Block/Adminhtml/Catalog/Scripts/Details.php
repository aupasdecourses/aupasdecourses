<?php

class Apdc_Catalog_Block_Adminhtml_Catalog_Scripts_Details extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'apdc_catalog';
        $this->_controller = 'adminhtml_catalog_scripts_details';
        $this->_headerText = $this->__('Scripts de checkup et nettoyage du catalogue');

    }

}
