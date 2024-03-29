<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Block_Adminhtml_Catalog_Categories_Import_Tabs 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Tabs
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Adminhtml_Catalog_Categories_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('apdc_catalog_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('apdc_catalog')->__('Informations'));
    }
}
