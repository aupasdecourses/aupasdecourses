<?php

class Apdc_Catalog_Adminhtml_Apdc_Scripts_CleanController extends Mage_Adminhtml_Controller_Action
{
    protected $toggleAttributes = null;

    /**
     * initActions 
     * 
     * @return void
     */
    protected function initActions()
    {
        $this->loadLayout()->_setActiveMenu('catalog/categories');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Categories'), Mage::helper('adminhtml')->__('Categories'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Toggle'), Mage::helper('adminhtml')->__('Toggle'));

        return $this;
    }
    /**
     * indexAction 
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Toggle enable/disable catégories'));
        $this->initActions();
        $this->renderLayout();
    }

    public function detailsAction()
    {
        $sql=$this->getRequest()->getParam('sql');
        $this->initActions();
        $this->loadLayout(array('default', 'adminhtml_apdc_scripts_clean_details'));
        $this->getLayout()->getBlock('adminhtml_catalog_scripts_details.grid')->setData('sql', $sql);
        $this->renderLayout();
    }

    protected function getResource()
    {
        return Mage::getSingleton('core/resource');
    }

    protected function getConnection()
    {
        return $this->getResource()->getConnection('core_write');
    }
}
