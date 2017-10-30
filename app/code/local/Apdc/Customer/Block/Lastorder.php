<?php

class Apdc_Customer_Block_Lastorder extends Apdc_Sales_Block_Order_Abstract
{
    public function getLastOrdersByCustomer() {
		$orders = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
			->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
			->setOrder('created_at', 'desc');
		$orders->getSelect()->limit(3);
		return $orders;
	}
}
