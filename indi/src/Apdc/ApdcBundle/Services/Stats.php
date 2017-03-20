<?php

namespace Apdc\ApdcBundle\Services;

//include '../../app/Mage.php';

class Stats
{

/*
	public function __construct()
	{
		\Mage::app();
	}
 */
	/* Used only in
	 * function stats_clients()
	 * */
	private function array_columns($array, $column_name)
	{
		return array_map(
			function ($element) use ($column_name) {
				return $element[$column_name];
			},
				$array
			);
	}

	//Used in /var/www/html/apdcdev/delivery/modules/clients/views/clients_stat.phtml
	public function stats_clients()
	{
		$data = [];
		$orders = \Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToFilter('status', array('eq' => \Mage_Sales_Model_Order::STATE_COMPLETE));
		$orders->getSelect()->columns('COUNT(*) AS nb_order')
			->columns('SUM(base_grand_total) AS amount_total')
			->columns('MAX(updated_at) AS last_order')
			->group('customer_id');
		foreach ($orders as $order) {
			$nom_client = $order->getCustomerName();
			$nb_order = $order->getNbOrder();
			$amount_total = round($order->getAmountTotal(), FLOAT_NUMBER, PHP_ROUND_HALF_UP);
			$last_order = $order->getLastOrder();
			$mail = $order->getCustomerEmail();
			$dataadd = \Mage::getModel('sales/order_address')->load($order->getShippingAddressId());
			$address = $dataadd->getStreet()[0].' '.$dataadd->getPostcode().' '.$dataadd->getCity();
			array_push($data, [
				'Nom Client' => $nom_client,
				'Nb Commande' => $nb_order,
				'Total' => $amount_total,
				'Dernière commande' => $last_order,
				'Mail client' => $mail,
				'Adresse client' => $address,
			]);
		}
		//Add customer who never ordered
		$customers = \Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToSelect('*');
		foreach ($customers as $customer) {
			$key = array_search($customer->getEmail(), array_columns($data, 'Mail client'));
			if ($key == false) {
				$nom_client = $customer->getFirstname().' '.$customer->getLastname();
				$nb_order = 0;
				$amount_total = 0;
				$last_order = 0;
				$mail = $customer->getEmail();
				array_push($data, [
					'Nom Client' => $nom_client,
					'Nb Commande' => $nb_order,
					'Total' => $amount_total,
					'Dernière commande' => $last_order,
					'Mail client' => $mail,
					'Adresse client' => '',
				]);
			}
		}
		return $data;
	}

}
