<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Core
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Core_Adminhtml_Apdc_Core_CategoriesController 
 * 
 * @category Apdc
 * @package  Core
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Core_Adminhtml_Apdc_Core_CategoriesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * treeViewAction 
     * 
     * @return void
     */
    public function treeViewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * categoriesJsonAction 
     * 
     * @return void
     */
    public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('apdc_core/adminhtml_tab_categoriesTreeView')
            ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}
