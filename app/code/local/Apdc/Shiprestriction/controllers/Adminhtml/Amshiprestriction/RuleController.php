<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Shiprestriction
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */

require_once(Mage::getModuleDir('controllers','Amasty_Shiprestriction').DS.'Adminhtml/Amshiprestriction/RuleController.php');

/**
 * Apdc_Shiprestriction_Adminhtml_RuleController 
 * 
 * @category Apdc
 * @package  Shiprestriction
 * @uses     Amasty
 * @uses     Amasty_Shiprestriction_Adminhtml_RuleController
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Shiprestriction_Adminhtml_Amshiprestriction_RuleController extends Amasty_Shiprestriction_Adminhtml_Amshiprestriction_RuleController
{
    protected function _setActiveMenu($menuPath)
    {
        $menuPath = preg_replace('/^sales/', 'neighborhoods', $menuPath);
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        $this->_title($this->__('Sales'))->_title($this->__($this->_title));	 
        return $this;
    } 

    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/amshiprestriction');
    }
}
