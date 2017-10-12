<?php

namespace Apdc\ApdcBundle\Services;

include_once '../../app/Mage.php';

class Accounting
{
	public function __construct()
	{
		\Mage::app();
	}

	public function getOrdersByCustomer()
	{
		$data = [];
		$cpt = 0;

		$orders = \Mage::getModel('sales/order')->getCollection();
		$orders->addAttributeToSelect('*');
		$orders->addAttributeToSort('created_at', 'DESC');

//		if ($customerId != null) {
//			$customerOrders->addFieldToFilter('customer_id', $customerId);
//		}
		foreach ($orders as $order) {
			$data[$order->getCustomerId()][$cpt] = [
				'customer_id'	=> $order->getCustomerId(),
				'name'			=> $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
				'increment_id'	=> $order->getIncrementId(),
			];

			$cpt++;
		}		

		return $data;
	}
}
