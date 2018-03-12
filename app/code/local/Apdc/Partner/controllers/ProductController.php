<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */

require_once(Mage::getModuleDir('controllers','Apdc_Partner').DS.'AbstractController.php');

/**
 * Apdc_Partner_ProductController 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Controller_Front_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_ProductController extends Apdc_Partner_AbstractController
{
    /**
     * listAction 
     * 
     * @return string
     */
    public function listAction()
    {
        return parent::mainAction();
    }

    protected function execute(Apdc_Partner_Model_Partner $partner)
    {
        echo $partner->getProductList();
        return;
    }
}
