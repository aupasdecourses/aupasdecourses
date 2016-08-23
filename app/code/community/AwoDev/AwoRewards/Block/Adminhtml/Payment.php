<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Payment extends AwoDev_AwoRewards_Block_Adminhtml_Widget_Grid_Container {
	public function __construct() {
		// The blockGroup must match the first half of how we call the block, and controller matches the second half
		// ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'awodev_aworewards';
        $this->_controller = 'adminhtml_payment';
        $this->_headerText = $this->__('Payments');
         
        parent::__construct();
    }
	
	public function _beforeToHtml() {
		$this->removeButton('add');
	}

	public function initTotals() {
		$payment_id = (int)$this->getRequest()->getParam('id');
		if(empty($payment_id)) return;
		
		$payment = (object)Mage::getModel('awodev_aworewards/payment')->load($payment_id)->getData();
		if(empty($payment->id)) return;
		$this->setPayment($payment);
		
		$customer = (object) Mage::getModel("customer/customer")->load($payment->user_id)->getData();
		$this->setCustomer($customer);
		
		
	}
	function getCredits() {

		$payment_id = (int)$this->getRequest()->getParam('id');
		if(empty($payment_id)) return;
		
        $credits = Mage::getResourceModel('awodev_aworewards/credit_collection')
            ->addFieldToFilter('main_table.payment_id', $payment_id)
			->setOrder('main_table.timestamp', 'desc')
        ;
		
		$credits->getSelect()->joinLeft(
				array('r' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_rule')),
				'r.id=main_table.rule_id',
				array('r.rule_name')
			);
		$credits->getSelect()->joinLeft(
				array('o' => Mage::helper('awodev_aworewards')->getTable('sales_flat_order')),
				'o.entity_id=main_table.item_id AND main_table.rule_type="order"',
				array('o.increment_id')
			);
			
		$rows = array();
		foreach($credits as $credit) {
			$rows[] = (object) $credit->getData();
		}
		
		return $rows;

	}
}