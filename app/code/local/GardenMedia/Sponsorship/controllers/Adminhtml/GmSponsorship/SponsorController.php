<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * GardenMedia_Sponsorship_Adminhtml_GmSponsorship_SponsorController 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Adminhtml_GmSponsorship_SponsorController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('customer/gm_sponsorship')
            ->_title($this->__('Customers'))->_title($this->__('Sponsors'))
			->_addBreadcrumb($this->__('Customers'), $this->__('Customers'))
			->_addBreadcrumb($this->__('Sponsors'), $this->__('Sponsors'));

        $this->renderLayout();
    }
}
