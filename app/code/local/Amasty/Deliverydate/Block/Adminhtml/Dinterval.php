<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */
class Amasty_Deliverydate_Block_Adminhtml_Dinterval extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller     = 'adminhtml_dinterval';
        $this->_headerText     = Mage::helper('amdeliverydate')->__('Manage Exceptions: Date Intervals');
        $this->_blockGroup     = 'amdeliverydate';
        $this->_addButtonLabel = Mage::helper('amdeliverydate')->__('Add New Date Interval');
        parent::__construct();
    }
}