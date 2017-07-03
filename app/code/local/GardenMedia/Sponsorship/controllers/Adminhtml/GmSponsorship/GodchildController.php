<?php

class GardenMedia_Sponsorship_Adminhtml_GmSponsorship_GodchildController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('customer/gm_sponsorship')
            ->_title($this->__('Customers'))->_title($this->__('Godchilds'))
			->_addBreadcrumb($this->__('Customers'), $this->__('Customers'))
			->_addBreadcrumb($this->__('Godchilds'), $this->__('Godchilds'));

        $this->renderLayout();
    }
}
