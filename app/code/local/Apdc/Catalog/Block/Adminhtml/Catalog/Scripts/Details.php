<?php

class Apdc_Catalog_Block_Adminhtml_Catalog_Scripts_Details extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_resource;
    protected $_connection;
    protected $_sqlarray;

    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'apdc_catalog';
        $this->_controller = 'adminhtml_catalog_scripts_details';
        $this->_headerText = $this->__('Scripts de checkup et nettoyage du catalogue');

        //$this->setTemplate('apdc/apdc_catalog/scripts/details.phtml');

        $this->_resource = Mage::getSingleton('core/resource');
        $this->_connection = $this->_resource->getConnection('core_read');

        $this->_sqlarray = Mage::helper('apdc_catalog/adminhtml_scripts')->getSqlRequests();
    }

    public function mySqlRequest($name)
    {
        if (array_key_exists($name, $this->_sqlarray)) {
            $sql = $this->_sqlarray[$name]['sql'];

            return $this->_connection->fetchAll($sql);
        } else {
            return 'Erreur de nom de requête!';
        }
    }
}
