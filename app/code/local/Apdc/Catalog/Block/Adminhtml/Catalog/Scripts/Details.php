<?php

class Apdc_Catalog_Block_Adminhtml_Catalog_Scripts_Details extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	protected $_sqlarray;

    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'apdc_catalog';
        $this->_controller = 'adminhtml_catalog_scripts_details';
        $this->_sqlarray = Mage::helper('apdc_catalog/adminhtml_scripts')->getSqlRequests();
        $this->_headerText = $this->__('Scripts de checkup et nettoyage du catalogue');
        $this->_removeButton('add');
        $backUrl = $this->getUrl('*/*/');
        $this->_addButton('back', array(
            'label'   => $this->__('Back'),
            'onclick' => "setLocation('{$backUrl}')",
            'class'   => 'back'
        ));

    }

    public function getHeaderText()
    {
        return  $this->_headerText = $this->__($this->_sqlarray[$this->getData('sql')]['label']);;
    }


}
