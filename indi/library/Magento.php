<?php

include '../../app/Mage.php';

class Magento
{
	const AUTHORIZED_GROUP = ['Administrators'];

	static private $_this;

	static function getInstance() {
		if (!isset(static::$_this))
			static::$_this = new Magento();
		return (static::$_this);
	}

	protected function __construct() {
		\Mage::app();
	}

	public static function mediapath() {
		return realpath(__DIR__.'/../../media');
	}

	public function isLogged() {
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

	public function login($username = null, $password = null) {
		\Mage::getModel('admin/session')->login($username, $password);
		return $this->isLogged();
	}

	public function logout() {
		\Mage::getSingleton('core/session',['name' => 'adminhtml']);
		$adminSession = \Mage::getSingleton('admin/session');
		$adminSession->unsetAll();
		$adminSession->getCookie()->delete($adminSession->getSessionName());
		unset($_SESSION['delivery']);
	}

	public function getMerchants($commercantId = -1) {
		$commercants = [];

		$categories = \Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect('*')
			->addFieldToFilter('estcom_commercant', [ 'neq' => false ]);
			//->addIsActiveFilter();
		if ($commercantId <> -1)
			$categories->addFieldToFilter("att_com_id", [ 'eq' => $commercantId ]);
		$S = [];
		$app = \Mage::app();
		$stores = $app->getStores();
		foreach ($stores as $id => $idc) {
			$S[$app->getStore($id)->getRootCategoryId()]['id'] = $app->getStore($id)->getRootCategoryId();
			$S[$app->getStore($id)->getRootCategoryId()]['name'] = $app->getStore($id)->getName();
		}
		foreach ($categories as $category) {
			$commercants[$category->getData('att_com_id')] = [
					'active'	=> $category->getData('is_active'),
					'id'		=> $category->getData('att_com_id'),
					'store'		=> $S[explode('/', $category->getPath())[1]]['name'],
					'name'		=> $category->getName(),
					'addr'		=> $category->getAdresseCommercant(),
					'phone'		=> $category->getTelephone(),
					'mobile'	=> $category->getPortable(),
					'mail3'		=> $category->getData('mail_3'),
					'mailc'		=> $category->getMailContact(),
					'mailp'		=> $category->getMailPro(),
					'orders'	=> []
				];
		}
		uasort($commercants, function ($lhs, $rhs) {
			if ($lhs['active'] < $rhs['active'])
				return true;
			return false;
		});
		return $commercants;
	}

	private function OrdersQuery($dfrom, $dto, $commercantId = -1, $orderId = -1) {
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
		$orders->getSelect()->join(array('attachment' => 'amasty_amorderattach_order_field'), 'attachment.order_id=main_table.entity_id',
			array (
				'upload'				=>	'attachment.upload',
				'input'					=>	'attachment.input',
				'digest'				=>	'attachment.digest',
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
		return ($orders);
	}

	private function OrderHeaderParsing($order) {
		$orderHeader = [];

		$shipping = $order->getShippingAddress();
		$orderHeader['mid']				=	$order->getData('entity_id');
		$orderHeader['id']				=	$order->getData('increment_id');
		$orderHeader['store']			=	\Mage::app()->getStore($order->getData('store_id'))->getName();
		$orderHeader['status']			=	$order->getStatusLabel();
		$orderHeader['upload']			=	$order->getData('upload');
		$orderHeader['input']			=	$order->getData('input');
		$orderHeader['digest']			=	$order->getData('digest');
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
		$orderHeader['total_quantite']	=	0;
		$orderHeader['total_prix']		=	0.0;
		$orderHeader['products']		=	[];

		return $orderHeader;
	}

	private function ProductParsing($product) {
		$prod_data = [
			'id' => $product->getItemId(),
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

	private function addEntryToModel($model, $data) {
		foreach ($datas as $k => $v) {
			$model->setData($k, $v);
		}
		$model->save();
	}

	private function updateEntryToModel($model, Array $filters, Array $updatedFields) {
		$entry = $model->getCollection();
		foreach ($filters as $k => $v) {
			$entry->addFieldToFilter($k, $v);
		}
		if (($id = $entry->getFirstItem()->getId()) <> null) {
			$model->load($id);
			foreach ($updatedFields as $k => $v) {
				$model->setData($k, $v);
			}
			$model->save();
		} else {
			$this->addEntryToModel($model, $updatedFields);
		}
	}

	public function addEntryToOrderField(Array $data) {
		$this->addEntryToModel(
			\Mage::getModel('amorderattach/order_field'),
			$data
		);
	}

	public function updateEntryToOrderField(Array $filters, Array $updatedFields) {
		$this->updateEntryToModel(
			\Mage::getModel('amorderattach/order_field'),
			$filters,
			$updatedFields
		);
	}

	public function getOrders($dfrom = null, $dto = null, $commercantId = -1, $orderId = -1) {
		if (!isset($dfrom))
			$dfrom = date('Y-m-d');
		if (!isset($dto))
			$dto = $dfrom;
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
				$orderHeader['total_quantite'] += $prod_data['quantite'];
				$orderHeader['total_prix'] += $prod_data['prix_total'];
			}
			$rsl[$orderHeader['id']] = $orderHeader;
		}
		return ($rsl);
	}

	public function getOrderByMerchants($orderId) {
		$merchants = $this->getMerchants();
		$orders = $this->OrdersQuery(null, null, -1, $orderId);

		$rsl = [ -1 => [ 'merchant' => [ 'name' => 'All', 'total' => 0.0 ] ] ];
		foreach ($orders as $order) {
			$rsl[-1]['order'] = $this->OrderHeaderParsing($order);
			$products = $order->getAllItems();
			foreach ($products as $product) {
				$prod_data = $this->ProductParsing($product);
				if (!isset($rsl[$prod_data['commercant_id']]['merchant'])){
					$rsl[$prod_data['commercant_id']]['merchant'] = $merchants[$prod_data['commercant_id']];
					$rsl[$prod_data['commercant_id']]['merchant']['total'] = 0.0;
				}
				$rsl[$prod_data['commercant_id']]['products'][] = $prod_data;
				$rsl[$prod_data['commercant_id']]['merchant']['total'] += $prod_data['prix_total'];
				$rsl[-1]['merchant']['total'] += $prod_data['prix_total'];
			}
		}
		return ($rsl);
	}

	public function getOrdersByStore($dfrom = null, $dto = null, $commercantId = -1, $orderId = -1) {
		if (!isset($dfrom))
			$dfrom = date('Y-m-d');
		if (!isset($dto))
			$dto = $dfrom;
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
				$orderHeader['total_quantite'] += $prod_data['quantite'];
				$orderHeader['total_prix'] += $prod_data['prix_total'];
			}
			$rsl[$orderHeader['store']][$orderHeader['id']] = $orderHeader;
		}
		return ($rsl);
	}

	public function getMerchantsOrders($commercantId = -1, $dfrom = null, $dto = null, $order_id = -1) {
		if (!isset($dfrom))
			$dfrom = date('Y-m-d');
		if (!isset($dto))
			$dto = $dfrom;
		$dfrom .=  " 00:00:00";
		$dto .=  " 00:00:00";
		$commercants = $this->getMerchants($commercantId, $order_id);
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
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_quantite'] += $prod_data['quantite'];
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_prix'] += $prod_data['prix_total'];
			}
		}
		return ($commercants);
	}

	public function getMerchantsOrdersByStore($commercantId = -1, $dfrom = null, $dto = null, $order_id = -1) {
		if (!isset($dfrom))
			$dfrom = date('Y-m-d');
		if (!isset($dto))
			$dto = $dfrom;
		$dfrom .=  " 00:00:00";
		$dto .=  " 00:00:00";
		$commercants = $this->getMerchants($commercantId);
		$orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $order_id);
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
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_quantite'] += $prod_data['quantite'];
				$commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_prix'] += $prod_data['prix_total'];
			}
		}
		$rsl = [];
		foreach ($commercants as $cid => $commercant) {
			$rsl[$commercant['store']][$cid] = $commercant;
		}
		return ($rsl);
	}

	public function getRefunds($orderId){
		$commercants = $this->getMerchants();
		$orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $order_id);
		$orders = $this->OrdersQuery(null, null, -1, $orderId);
		$rsl = [];
		$table_id = [];
		$refundTable = [];
		foreach ($orders as $order) {
			$orderHeader = $this->OrderHeaderParsing($order);
			$products = $order->getAllItems();
			foreach ($products as $product) {
				$prod_data = $this->ProductParsing($product);
				$prod_data['commercant_name'] = $commercants[$prod_data['commercant_id']]['name'];
				$table_id[] = $prod_data['id'];
				$orderHeader['products'][$prod_data['id']] = $prod_data;
				$orderHeader['total_quantite'] += $prod_data['quantite'];
				$orderHeader['total_prix'] += $prod_data['prix_total'];
			}
			$rsl[$orderHeader['store']][$orderHeader['id']] = $orderHeader;
		}
		$refundItems = \Mage::getModel('pmainguet_delivery/refund_items')->getCollection();
		$refundItems->addFieldToFilter('order_item_id', array('in' => $table_id));
		foreach ($refundItems as $refundItem){
			// inserer donnees refund dans tableau $result
			$rsl[$orderHeader['store']][$orderHeader['id']]['products'][$refundItem->getData('order_item_id')]['final_prix'] = $refundItem['prix_final'];
			$rsl[$orderHeader['store']][$orderHeader['id']]['products'][$refundItem->getData('order_item_id')]['final_prix_diff'] = $refundItem['diffprixfinal'];
			$rsl[$orderHeader['store']][$orderHeader['id']]['products'][$refundItem->getData('order_item_id')]['in_tick'] = $refundItem['in_ticket'];
		}
		return($rsl);
	}
}
