<?php

class Apdc_Catalog_Block_Adminhtml_Catalog_Scripts_Clean extends Mage_Adminhtml_Block_Widget_View_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'apdc_catalog';
        $this->_controller = 'adminhtml_catalog';
        $this->_mode = 'scripts_clean';
        $this->_headerText = $this->__('Scripts de checkup et nettoyage du catalogue');

        $this->setTemplate('apdc/apdc_catalog/scripts/container.phtml');

    }

}

