<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Neighborhood_Block_Adminhtml_Neighborhood_Edit_Form 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Form
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Block_Adminhtml_Neighborhood_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * _prepareForm 
     * 
     * @return void
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('adminhtml/neighborhood_index/save'),
                'method' => 'post'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
