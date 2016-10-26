<?php

class Magento
{
	const AUTHORIZED_GROUP = ['Administrators'];

	public function isLoggedAction() {
		\Mage::getSingleton('core/session',['name' => 'adminhtml']);
		if (isset($_SESSION['delivery'])) {
			return (true);
		}
		$user = \Mage::getSingleton('admin/session')->getUser();
		if (isset($user)) {
			$username = $user->getUsername();
			$group = \Mage::getModel('admin/user')->load($user->getUserId())->getRole()->getData('role_name');
			if (in_array($group, self::AUTHORIZED_GROUP)) {
				$_SESSION['delivery'] = [
					'username'	=>	$username,
					'groupname'	=>	$group
				];
				return (true);
			}
		}
		return (false);
	}

	public function loginAction($username = null, $password = null) {
		if (!isset($username) || !isset($password)) {
			$username = $this->params('username');
			$password = $this->params('password');
		}
		\Mage::getModel('admin/session')->login($username, $password);
		return $this->isLoggedAction();
	}

	public function logoutAction() {
		\Mage::getSingleton('core/session',['name' => 'adminhtml']);
		$adminSession = \Mage::getSingleton('admin/session');
		$adminSession->unsetAll();
		$adminSession->getCookie()->delete($adminSession->getSessionName());
		unset($_SESSION['delivery']);
	}

	public function getMerchantsAction($commercantId = -1) {
		$commercants = [];

		$categories = \Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect('*')
			->addFieldToFilter('estcom_commercant', [ 'neq' => false ])
			->addIsActiveFilter();
		if ($commercantId <> -1)
			$categories->addFieldToFilter("att_com_id", [ 'eq' => $commercantId ]);
		$S = [];
		$app = \Mage::app();
		$stores = $app->getStores();
		foreach ($stores as $id => $osef) {
			$S[$app->getStore($id)->getRootCategoryId()]['id'] = $app->getStore($id)->getRootCategoryId();
			$S[$app->getStore($id)->getRootCategoryId()]['name'] = $app->getStore($id)->getName();
		}
		foreach ($categories as $category) {
			$commercants[$category->getData('att_com_id')] = [
					'id'		=> $category->getData('att_com_id'),
					'store'		=> $S[explode('/', $category->getPath())[1]]['name'],
					'name'		=> $category->getName(),
					'addr'		=> $category->getAdresseCommercant(),
					'phone'		=> $category->getTelephone(),
					'mobile'	=> $category->getPortable(),
					'mail3'		=> $category->getData('mail_3'),
					'mailc'		=> $category->getMailContact(),
					'mailp'		=> $category->getMailPro(),
				];
		}
		return $commercants;
	}

	private function OrdersQuery($dfrom, $dto, $commercantId = -1, $orderId = -1) {
		// set magento database query ===>
		$orders = \Mage::getModel('sales/order')->getCollection();
		$orders->getSelect()->join('mwddate_store', 'main_table.entity_id=mwddate_store.sales_order_id',
			array(
				'mwddate_store.ddate_id'
			));
		$orders->getSelect()->join('mwddate', 'mwddate_store.ddate_id=mwddate.ddate_id',
			array(
				'ddate'					=>	'mwddate.ddate',
				'dtime'					=>	'mwddate.dtime'));
		$orders->getSelect()->join('mwdtime', 'mwddate.dtime=mwdtime.dtime_id',
			array(
				'dtime'						=>	'mwdtime.interval'
		));
		$orders->getSelect()->join(array('order_attribute' => 'amasty_amorderattr_order_attribute'), 'order_attribute.order_id = main_table.entity_id',
			array(
				'produit_equivalent'	=>	'order_attribute.produit_equivalent',
				'contactvoisin'			=>	'order_attribute.contactvoisin',
				'codeporte1'			=>	'order_attribute.codeporte1',
				'codeporte2'			=>	'order_attribute.codeporte2',
				'batiment'				=>	'order_attribute.batiment',
				'etage'					=>	'order_attribute.etage',
				'telcontact'			=>	'order_attribute.telcontact',
				'info'					=>	'order_attribute.infoscomplementaires'
			));
		$orders->addFilterToMap('ddate', 'mwddate.ddate');
		$orders->addFilterToMap('dtime', 'mwdtime.interval')
			->addFieldToFilter('main_table.status', array('nin' => array('pending_payment', 'payment_review', 'holded', 'closed', 'canceled')))
			->addAttributeToSort('dtime', 'asc');
		if ($orderId <> -1) {
			$orders->addFieldToFilter('main_table.increment_id', [ 'eq' => $orderId ]);
		} else {
			$orders->addAttributeToFilter('ddate', array(
				'from'	=>	$dfrom,
				'to'	=>	$dto
			));
			if ($commercantId <> -1) {
				$orders->getSelect()->join(
					array('order_item' => \Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
					'order_item.order_id = main_table.entity_id'
				)->where("order_item.commercant={$commercantId}")->group('order_item.order_id');
			}
		}
		// <===

		return ($orders);
	}

	private function OrderHeaderParsing($order) {
		$orderHeader = [];

		$shipping = $order->getShippingAddress();
		$orderHeader['id']				=	$order->getData('increment_id');
		$orderHeader['store']			=	\Mage::app()->getStore($order->getData('store_id'))->getName();
		$orderHeader['status']			=	$order->getStatusLabel();
		$orderHeader['customer_id']		=	$order->getData('customer_id');
		$orderHeader['first_name']		=	$shipping->getData('firstname');
		$orderHeader['last_name']		=	$shipping->getData('lastname');
		$orderHeader['address']			=	$shipping->getStreet()[0] . ' ' . $shipping->getPostcode() . ' ' . $shipping->getCity();
		$orderHeader['phone']			=	$shipping->getTelephone();
		$orderHeader['mail']			=	$order->getData('customer_email');
		$orderHeader['info']			=	$order->getData('codeporte1') . ' | ' . $order->getData('codeprote2') . ' | ' . $order->getData('info');
		$orderHeader['contact']			=	$order->getData('contactvoisin');
		$orderHeader['contact_phone']	=	$order->getData('telcontact');
		$orderHeader['order_date']		=	$order->getData('created_at');
		$orderHeader['delivery_date']	=	$order->getData('ddate');
		$orderHeader['delivery_time']	=	$order->getData('dtime');
		$orderHeader['equivalent_replacement']	=	$order->getData('produit_equivalent');
		$orderHeader['Total quantite']	=	0;
		$orderHeader['Total prix']		=	0.0;
		$orderHeader['products']		=	[];

		return $orderHeader;
	}

	private function ProductParsing($product) {
		$prod_data = [
			'nom' => $product->getName(),
				'prix_kilo'		=>	$product->getPrixKiloSite(),
				'quantite'		=>	round($product->getQtyOrdered(), 0),
				'description'	=>	$product->getShortDescription(),
				'prix_unitaire'	=>	round($product->getPriceInclTax(), 2),
				'prix_total'	=>	round($product->getRowTotalInclTax(), 2),
				'commercant_id'	=>	$product->getCommercant()
			];
		$prod_data['comment'] = '';
		$options = $product->getProductOptions()['options'];
		foreach ($options as $option) {
			$prod_data['comment'] .= $option['label'].': '.$option['value'].' | ';
		}
		$prod_data['comment'] .= $product->getData('item_comment');
		return ($prod_data);
	}

	public function getOrders($dfrom = null, $dto = null, $commercantId = -1, $orderId = -1) {
		if (!isset($dfrom))
			$dfrom = date('Y-m-d');
		if (!isset($dto))
			$dto = $dfrom;
		if ($orderId == -1 && $this->params('id') <> '')
			$orderId = $this->params('id');
		$dfrom .=  " 00:00:00";
		$dto .=  " 00:00:00";
		$orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $orderId);
		$rsl = [];
		foreach ($orders as $order) {
			$orderHeader = $this->OrderHeaderParsing($order);
			$products = $order->getAllItems();
			foreach ($products as $product) {
				$prod_data = $this->ProductParsing($product);
				$orderHeader['products'][] = $prod_data;
				$orderHeader['Total quantite'] += $prod_data['quantite'];
				$orderHeader['Total prix'] += $prod_data['prix_total'];
			}
			$rsl[$orderHeader['id']] = $orderHeader;
		}
		return ($rsl);
	}

	public function getOrdersByStore($dfrom = null, $dto = null, $commercantId = -1, $orderId = -1) {
		if (!isset($dfrom))
			$dfrom = date('Y-m-d');
		if (!isset($dto))
			$dto = $dfrom;
		if ($orderId == -1 && $this->params('id') <> '')
			$orderId = $this->params('id');
		$dfrom .=  " 00:00:00";
		$dto .=  " 00:00:00";
		$orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $orderId);
		$rsl = [];
		foreach ($orders as $order) {
			$orderHeader = $this->OrderHeaderParsing($order);
			$products = $order->getAllItems();
			foreach ($products as $product) {
				$prod_data = $this->ProductParsing($product);
				$orderHeader['products'][] = $prod_data;
				$orderHeader['Total quantite'] += $prod_data['quantite'];
				$orderHeader['Total prix'] += $prod_data['prix_total'];
			}
			$rsl[$orderHeader['store']][$orderHeader['id']] = $orderHeader;
		}
		return ($rsl);
	}

	public function getMerchantsOrders($commercantId = -1, $dfrom = null, $dto = null) {
		if (!isset($dfrom))
			$dfrom = date('Y-m-d');
		if (!isset($dto))
			$dto = $dfrom;
		$dfrom .=  " 00:00:00";
		$dto .=  " 00:00:00";
		$commercants = $this->getMerchantsAction($commercantId);
		$orders = $this->OrdersQuery($dfrom, $dto, $commercantId);
		foreach ($orders as $order) {
			$orderHeader = $this->OrderHeaderParsing($order);
			$products = \Mage::getModel('sales/order_item')->getCollection();
			$products->addFieldToFilter('order_id', ['eq' => $order->getData('entity_id')]);
			if ($commercantId <> -1)
				$products->addFieldToFilter('commercant', [ 'eq' => $commercantId ]);
			foreach ($products as $product) {
				$prod_data = $this->ProductParsing($product);
				if (!isset($commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]))
					$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']] = $orderHeader;
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['products'][] = $prod_data;
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['Total quantite'] += $prod_data['quantite'];
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['Total prix'] += $prod_data['prix_total'];
			}
		}
		return ($commercants);
	}

	public function getMerchantsOrdersByStore($commercantId = -1, $dfrom = null, $dto = null) {
		if (!isset($dfrom))
			$dfrom = date('Y-m-d');
		if (!isset($dto))
			$dto = $dfrom;
		$dfrom .=  " 00:00:00";
		$dto .=  " 00:00:00";
		$commercants = $this->getMerchantsAction($commercantId);
		$orders = $this->OrdersQuery($dfrom, $dto, $commercantId);
		foreach ($orders as $order) {
			$orderHeader = $this->OrderHeaderParsing($order);
			$products = \Mage::getModel('sales/order_item')->getCollection();
			$products->addFieldToFilter('order_id', ['eq' => $order->getData('entity_id')]);
			if ($commercantId <> -1)
				$products->addFieldToFilter('commercant', [ 'eq' => $commercantId ]);
			foreach ($products as $product) {
				$prod_data = $this->ProductParsing($product);
				if (!isset($commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]))
					$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']] = $orderHeader;
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['products'][] = $prod_data;
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['Total quantite'] += $prod_data['quantite'];
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['Total prix'] += $prod_data['prix_total'];
			}
		}
		$rsl = [];
		foreach ($commercants as $cid => $commercant) {
			$rsl[$commercant['store']][$cid] = $commercant;
		}
		return ($rsl);
	}
}
