<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */
class Amasty_Deliverydate_Block_Adminhtml_Holidays extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller     = 'adminhtml_holidays';
        $this->_headerText     = Mage::helper('amdeliverydate')->__('Manage Exceptions: Dates and Holidays');
        $this->_blockGroup     = 'amdeliverydate';
        $this->_addButtonLabel = Mage::helper('amdeliverydate')->__('Add New Holiday');
        parent::__construct();
    }
}