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
/**
 * Apdc_Partner_Block_Adminhtml_Partner_Edit_Tabs 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Tabs
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Block_Adminhtml_Partner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('partner_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('apdc_partner')->__('Informations sur le partenaire'));
    }
}
