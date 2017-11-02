<?php

class Apdc_Catalog_Model_Adminhtml_Request extends Mage_Core_Model_Abstract
{   
    protected $_sqlarray;

    public function __construct()
   	{
        parent::__construct();
        $this->_init('apdc_catalog/adminhtml_request');
        $this->_sqlarray = Mage::helper('apdc_catalog/adminhtml_scripts')->getSqlRequests();
    }

    public function getSqlRequests()
    {
        return array_keys($this->_sqlarray);
    }

    public function getSqlLabel($id)
    {
        return $this->_sqlarray[$id]['label'];
    }

    public function getSqlHint($id)
    {
        return $this->_sqlarray[$id]['hint'];
    }

    public function getSqlGrid($id)
    {
        return $this->_sqlarray[$id]['grid'];
    }

    public function getSqlUrl($id)
    {
        return Mage::helper("adminhtml")->getUrl('petitcommisadmin/apdc_scripts_clean/details/sql/'.$id);
    }

    public function mySqlRequest($name)
    {
        if (array_key_exists($name, $this->_sqlarray)) {
            $collection=new Apdc_Catalog_Model_Resource_Adminhtml_Request_Collection($name);
            return $collection;
        } else {
            return 'Erreur de nom de requête!';
        }
    }

}