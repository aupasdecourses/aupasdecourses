<?php

namespace Apdc\ApdcBundle\Services;

include '../../app/Mage.php';

class Magento
{
    use Credimemo;

    const AUTHORIZED_GROUP = ['Administrators'];

    public function __construct()
    {
        \Mage::app();
    }

    public function mediaPath()
    {
        return realpath(__DIR__.'/../../../../../media');
    }

    public function mediaUrl()
    {
        return \Mage::getBaseUrl('media');
    }

	/** OLD VERSION OF GETMERCHANTS. **/
	public function getShops($commercantId = -1)
	{
	/*
	 	$commercants = [];
		
		$shops = \Mage::getModel('apdc_commercant/shop')->getCollection();
        if ($commercantId != -1) {
            $shops->addFieldToFilter('id_attribut_commercant', ['eq' => $commercantId]);
        }
		$shops->getSelect()->join('catalog_category_entity', 'main_table.id_category=catalog_category_entity.entity_id', array('catalog_category_entity.path'));
		$shops->addFilterToMap('path' , 'catalog_category_entity.path');

		$S = [];
		$app = \Mage::app();
		$stores = $app->getStores();
		foreach ($stores as $id => $idc) {
			$S[$app->getStore($id)->getRootCategoryId()]['id']		= $app->getStore($id)->getRootCategoryId();
			$S[$app->getStore($id)->getRootCategoryId()]['name']	= $app->getStore($id)->getName();
		}

		foreach ($shops as $shop) {
			$commercants[$shop->getData('id_attribut_commercant')] = [
				'active'			=> $shop->getData('enabled'),
				'id'				=> $shop->getData('id_attribut_commercant'),
				'store'				=> $S[explode('/', $shop->getPath())[1]]['name'],
				'name'				=> $shop->getName(),
				'addr'				=> $shop->getStreet().' '.$shop->getPostCode().' '.$shop->getCity(),
				'phone'				=> $shop->getPhone(),
				'mail3'				=> \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactEmployeeBis())->getFirstItem()->getEmail(),
                'mailc'				=> \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactManager())->getFirstItem()->getEmail(),
	            'mailp'				=> \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactEmployee())->getFirstItem()->getEmail(),
                'orders'			=> [],
                'timetable' => implode(',', $shop->getTimetable()),
                'closing_periods'	=> $shop->getClosingPeriods(),
                'delivery_days'		=> 'Du Mardi au Vendredi',
                ];
        }
        uasort($commercants, function ($lhs, $rhs) {
            if ($lhs['active'] < $rhs['active']) {
                return true;
            }

            return false;
		});
		asort($commercants);
		return $commercants;
	 */
	}






	/** Delivery version of getShops => facturation **/
	public function getShops($id = -1, $filter = 'none')
	{
		$return = [];
		$shops = \Mage::getModel('apdc_commercant/shop')->getCollection();
		if ($id == -1) {
			if ($filter == 'none') {
				foreach ($shops as $shop) {
					$return[$shop->getIdAttributCommercant()] = $shop->getName();
				}
			} elseif ($filter == 'store') {
				$shops->getSelect()->join('catalog_category_entity', 'main_table.id_category=catalog_category_entity.entity_id', array('catalog_category_entity.path'));
				$shops->addFilterToMap('path', 'catalog_category_entity.path');
				foreach ($shops as $shop) {
					$storeid = explode('/', $shop->getPath())[1];
					$return[$storeid][$shop->getIdAttributCommercant()] = array(
						'name'		=> $shop->getName(),
						'adresse'	=> $shop->getStreet().' '.$shop->getPostcode().' '.$shop->getCity(),
						'telephone'	=> $shop->getPhone(),
					);
				}
			}
			arsort($return);
		} else {
			$data = $shops->addFieldToFilter('id_attribut_commercant', $id)->getFirstItem()->getData();
			$return['name']				= $data['name'];
			$return['adresse']			= $data['street'].' '.$data['postcode'].' '.$data['city'];
			$return['url_adresse']		= 'https://www.google.fr/maps/place/'.str_replace(' ', '+', $return['adresse']);
			$return['phone']			= $data['phone'];
			$return['website']			= $data['website'];
			$return['timetable']		= implode(',', $data['timetable']);
			$return['closing_periods']	= $data['closing_periods'];
			$return['delivery_days']	= 'Du Mardi au Vendredi';
			$return['mail_contact']		= \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_manager'])->getFirstItem()->getEmail();
			$return['mail_pro']			= \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_employee'])->getFirstItem()->getEmail();
			$return['mail_3']			= \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_employee_bis'])->getFirstItem()->getEmail();
		}

		return $return;
	}



























	/** PAS FACTU MAIS ALL INDI **/
    public function getMerchants($commercantId = -1)
    {
        $commercants = [];

        $shops = \Mage::getModel('apdc_commercant/shop')->getCollection();
        if ($commercantId != -1) {
            $shops->addFieldToFilter('id_attribut_commercant', ['eq' => $commercantId]);
        }

        $cat_array = \Mage::helper('apdc_commercant')->getCategoriesArray();
        $S = \Mage::helper('apdc_commercant')->getStoresArray();

        foreach ($shops as $shop) {
            $cats = $shop->getIdCategory();
            foreach ($cats as $cat) {
                $storeinfo = $S[explode('/', $cat_array[$cat])[1]];
                $commercants[$storeinfo['store_id']][$shop->getData('id_attribut_commercant')] = [
                        'active' => $shop->getData('enabled'),
                        'id' => $shop->getData('id_attribut_commercant'),
                        'store' => $storeinfo['name'],
                        'store_id' => $storeinfo['store_id'],
                        'name' => $shop->getName(),
                        'addr' => $shop->getStreet().' '.$shop->getPostCode().' '.$shop->getCity(),
                        'phone' => $shop->getPhone(),
                        'mobile' => '',
                        'mail3' => \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactEmployeeBis())->getFirstItem()->getEmail(),
                        'mailc' => \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactManager())->getFirstItem()->getEmail(),
                        'mailp' => \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactEmployee())->getFirstItem()->getEmail(),
                        'orders' => [],
                        'timetable' => implode(',', $shop->getTimetable()),
                        'closing_periods' => $shop->getClosingPeriods(),
                        'delivery_days' => 'Du Mardi au Vendredi',
                    ];
            }
        }
        uasort($commercants, function ($lhs, $rhs) {
            if ($lhs['active'] < $rhs['active']) {
                return true;
			}

            return false;
		});
		/* Sort associative array in ascending order, according to the VALUE */
		asort($commercants);

        return $commercants;
    }

	/** Mettre dans trait Order **/
    private function OrdersQuery($dfrom, $dto, $commercantId = -1, $orderId = -1)
    {
        $orders = \Mage::getModel('sales/order')->getCollection();
		$orders->getSelect()->join(
			'mwddate_store',
		   	'main_table.entity_id=mwddate_store.sales_order_id',
            array(
                'mwddate_store.ddate_id',
			)
		);
		$orders->getSelect()->join(
			'mwddate',
		   	'mwddate_store.ddate_id=mwddate.ddate_id',
            array(
                'ddate' => 'mwddate.ddate',
				'dtime' => 'mwddate.dtime',
			)
		);
		$orders->getSelect()->join(
			'mwdtime',
			'mwddate.dtime = mwdtime.dtime_id',
            array(
                'dtime' => 'mwdtime.interval',
			)
		);
		$orders->getSelect()->join(
			array(
				'order_attribute' => 'amasty_amorderattr_order_attribute',
			),
		   	'order_attribute.order_id = main_table.entity_id',
            array(
                'produit_equivalent' => 'order_attribute.produit_equivalent',
                'contactvoisin' => 'order_attribute.contactvoisin',
                'codeporte1' => 'order_attribute.codeporte1',
                'codeporte2' => 'order_attribute.codeporte2',
                'batiment' => 'order_attribute.batiment',
                'etage' => 'order_attribute.etage',
                'telcontact' => 'order_attribute.telcontact',
                'info' => 'order_attribute.infoscomplementaires',
			)
		);
		$orders->getSelect()->joinLeft(
			array(
				'attachment' => 'amasty_amorderattach_order_field',
			),
			'attachment.order_id = main_table.entity_id',
            array(
                'upload' => 'attachment.upload',
                'input' => 'attachment.input',
                'digest' => 'attachment.digest',
                'refund' => 'attachment.refund',
                'refund_shipping' => 'attachment.refund_shipping',
                'commentaire_commercant' => 'attachment.commentaires_ticket',
                'commentaire_client' => 'attachment.commentaires_fraislivraison',
			)
		);
        $orders->addFilterToMap('ddate', 'mwddate.ddate');
        $orders->addFilterToMap('dtime', 'mwdtime.interval')
            ->addFieldToFilter('main_table.status', array('nin' => array('pending_payment', 'payment_review', 'holded', 'closed', 'canceled')))
            ->addAttributeToSort('main_table.increment_id', 'dsc');
        if ($orderId != -1) {
            $orders->addFieldToFilter('main_table.increment_id', ['eq' => $orderId]);
        } else {
            $orders->addAttributeToFilter('ddate', array(
                'from' => $dfrom,
                'to' => $dto,
            ));
            if ($commercantId != -1) {
                $orders->getSelect()->join(
					array(
						'order_item' => \Mage::getSingleton('core/resource')->getTableName('sales/order_item'),
					),
                    'order_item.order_id = main_table.entity_id'
                )->where("order_item.commercant={$commercantId}")->group('order_item.order_id');
            }
        }

        return $orders;
    }

	/** Mettre dans trait Order **/
    private function OrderHeaderParsing($order)
    {
        $orderHeader = [];
        $shipping = $order->getShippingAddress();
        $orderHeader['mid'] = $order->getData('entity_id');
        $orderHeader['id'] = $order->getData('increment_id');
        $orderHeader['store'] = \Mage::app()->getStore($order->getData('store_id'))->getName();
        $orderHeader['store_id'] = \Mage::app()->getStore($order->getData('store_id'))->getId();
        $orderHeader['status'] = $order->getStatusLabel();
        $orderHeader['upload'] = $order->getData('upload');
        $orderHeader['input'] = $order->getData('input');
        $orderHeader['digest'] = $order->getData('digest');
        $orderHeader['refund'] = $order->getData('refund');
        $orderHeader['refund_shipping'] = $order->getData('refund_shipping');
        $orderHeader['commentaire_commercant'] = $order->getData('commentaire_commercant');
        $orderHeader['commentaire_client'] = $order->getData('commentaire_client');
        $orderHeader['customer_id'] = $order->getData('customer_id');
        $orderHeader['first_name'] = $shipping->getData('firstname');
        $orderHeader['last_name'] = $shipping->getData('lastname');
        $orderHeader['address'] = $shipping->getStreet()[0].' '.$shipping->getPostcode().' '.$shipping->getCity();
        $orderHeader['phone'] = $shipping->getTelephone();
        $orderHeader['mail'] = $order->getData('customer_email');
        $orderHeader['codeporte1'] = $order->getData('codeporte1');
        $orderHeader['codeporte2'] = $order->getData('codeporte2');
        $orderHeader['batiment'] = $order->getData('batiment');
        $orderHeader['etage'] = $order->getData('etage');
        $orderHeader['info'] = $order->getData('info');
        $orderHeader['contact'] = $order->getData('contactvoisin');
        $orderHeader['contact_phone'] = $order->getData('telcontact');
        $orderHeader['order_date'] = $order->getData('created_at');
        $orderHeader['delivery_date'] = $order->getData('ddate');
        $orderHeader['delivery_time'] = $order->getData('dtime');
        $orderHeader['equivalent_replacement'] = $order->getData('produit_equivalent');
        $orderHeader['total_quantite'] = 0;
        $orderHeader['total_prix'] = 0.0;
        $orderHeader['products'] = [];

        if ($order->getData('refund_shipping')) {
            $orderHeader['refund_shipping_amount'] = $order->getShippingAmount() + $order->getShippingTaxAmount();
        } else {
            $orderHeader['refund_shipping_amount'] = 0;
		}

        return $orderHeader;
    }

	/** Mettre dans trait Order **/
    private function ProductParsing($product, $order_id)
    {
        $prod_data = [
            'id' => $product->getItemId(),
                'nom' => $product->getName(),
                'order_id' => $order_id,
                'prix_kilo' => $product->getPrixKiloSite(),
                'quantite' => round($product->getQtyOrdered(), 0),
                'description' => $product->getShortDescription(),
                'prix_unitaire' => round($product->getPriceInclTax(), 2),
                'prix_total' => round($product->getRowTotalInclTax(), 2),
                'commercant_id' => $product->getCommercant(),
                'refund_comment' => $product->getRefundComment(),
            ];
        $prod_data['comment'] = '';
        $options = $product->getProductOptions()['options'];
        foreach ($options as $option) {
            $prod_data['comment'] .= $option['label'].': '.$option['value'].' | ';
        }
        $prod_data['comment'] .= $product->getData('item_comment');

        return $prod_data;
    }

	/** Mettre dans trait Model **/
    private function checkEntryToModel($model, array $filters)
    {
        $entry = $model->getCollection();
        foreach ($filters as $k => $v) {
            $entry->addFieldToFilter($k, $v);
        }
        if ($entry->getFirstItem()->getId() != null) {
            return true;
        } else {
            return false;
        }
    }

	/** Mettre dans trait Model **/
    private function addEntryToModel($model, $data, $updatedFields)
    {
        foreach ($data as $k => $v) {
            $model->setData($k, $v);
        }
        foreach ($updatedFields as $k => $v) {
            $model->setData($k, $v);
        }
        $model->save();
    }

	/** Mettre dans trait Model **/
    private function updateEntryToModel($model, array $filters, array $updatedFields)
    {
        $entry = $model->getCollection();
        foreach ($filters as $k => $v) {
            $entry->addFieldToFilter($k, $v);
        }
        if (($id = $entry->getFirstItem()->getId()) != null) {
            $model->load($id);
            foreach ($updatedFields as $k => $v) {
                $model->setData($k, $v);
            }
            $model->save();
        } else {
            $this->addEntryToModel($model, $updatedFields);
        }
    }

	/** Mettre dans trait Model **/
    public function addEntryToOrderField(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel('amorderattach/order_field'),
            $data
        );
    }

	/** Mettre dans trait Model **/
    public function updateEntryToOrderField(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('amorderattach/order_field');
        $check = $this->checkEntryToModel($model, $filters);

        if ($check) {
            $this->updateEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        } else {
            $this->addEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        }
    }

	/** Mettre dans trait Model **/
    public function addEntryToRefundItem(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/refund_items')),
            $data
        );
    }

	/** Mettre dans trait Model **/
    public function updateEntryToRefundItem(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/refund_items');
        $check = $this->checkEntryToModel($model, $filters);

        if ($check) {
            $this->updateEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        } else {
            $this->addEntryToModel(
                $model,
                $filters,
                $updatedFields
            );
        }
    }

	/** Mettre dans trait Order **/
    public function getOrders($dfrom = null, $dto = null, $commercantId = -1, $orderId = -1)
    {
        if (!isset($dfrom)) {
            $dfrom = date('Y-m-d');
        }
        if (!isset($dto)) {
            $dto = $dfrom;
        }
        $dfrom .=  ' 00:00:00';
        $dto .=  ' 00:00:00';
        $orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $orderId);
        $rsl = [];
        foreach ($orders as $order) {
            $orderHeader = $this->OrderHeaderParsing($order);
            $products = $order->getAllItems();
            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                $orderHeader['products'][] = $prod_data;
                $orderHeader['total_quantite'] += $prod_data['quantite'];
                $orderHeader['total_prix'] += $prod_data['prix_total'];
            }
            $rsl[$orderHeader['id']] = $orderHeader;
        }

        return $rsl;
    }

	/** Mettre dans trait Order **/
    public function getOrderByMerchants($orderId)
    {
        $merchants = $this->getMerchants();
        $orders = $this->OrdersQuery(null, null, -1, $orderId);

        $rsl = [-1 => ['merchant' => ['name' => 'All', 'total' => 0.0]]];
        foreach ($orders as $order) {
            $rsl[-1]['order'] = $this->OrderHeaderParsing($order);
            $products = $order->getAllItems();
            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                if (!isset($rsl[$prod_data['commercant_id']]['merchant'])) {
                    $rsl[$prod_data['commercant_id']]['merchant'] = $merchants[$rsl[-1]['order']['store_id']][$prod_data['commercant_id']];
                    $rsl[$prod_data['commercant_id']]['merchant']['total'] = 0.0;
                }
                $rsl[$prod_data['commercant_id']]['products'][] = $prod_data;
                $rsl[$prod_data['commercant_id']]['merchant']['total'] += $prod_data['prix_total'];
                $rsl[-1]['merchant']['total'] += $prod_data['prix_total'];
            }
        }

        return $rsl;
    }

	/** Mettre dans trait Order **/
    public function getOrdersByStore($dfrom = null, $dto = null, $commercantId = -1, $orderId = -1)
    {
        if (!isset($dfrom)) {
            $dfrom = date('Y-m-d');
        }
        if (!isset($dto)) {
            $dto = $dfrom;
        }
        $dfrom .=  ' 00:00:00';
        $dto .=  ' 00:00:00';
        $orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $orderId);
        $rsl = [];
        foreach ($orders as $order) {
            $orderHeader = $this->OrderHeaderParsing($order);
            $products = $order->getAllItems();
            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                $orderHeader['products'][] = $prod_data;
                $orderHeader['total_quantite'] += $prod_data['quantite'];
                $orderHeader['total_prix'] += $prod_data['prix_total'];
            }
            $rsl[$orderHeader['store']][$orderHeader['id']] = $orderHeader;
        }

        return $rsl;
    }

	/** Mettre dans trait Order **/
    public function getMerchantsOrders($commercantId, $dfrom = null, $dto = null, $order_id = -1)
    {
        if (!isset($dfrom)) {
            $dfrom = date('Y-m-d');
        }
        if (!isset($dto)) {
            $dto = $dfrom;
        }
        $dfrom .=  ' 00:00:00';
        $dto .=  ' 00:00:00';
        $commercants = $this->getMerchants($commercantId, $order_id);
        $orders = $this->OrdersQuery($dfrom, $dto, $commercantId);
        foreach ($orders as $order) {
            $orderHeader = $this->OrderHeaderParsing($order);
            $products = \Mage::getModel('sales/order_item')->getCollection();
            $products->addFieldToFilter('order_id', ['eq' => $order->getData('entity_id')])
                     ->addFieldToFilter('commercant', ['eq' => $commercantId]);
            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                if (!isset($commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']])) {
                    $commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']] = $orderHeader;
                }
                $commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']]['products'][] = $prod_data;
                $commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_quantite'] += $prod_data['quantite'];
                $commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_prix'] += $prod_data['prix_total'];
            }
        }

        $rsl = [];
        $S = \Mage::helper('apdc_commercant')->getStoresArray("storeid");

        foreach ($commercants as $storeid => $commercant) {
            foreach ($commercant as $com_id => $com) {
                $rsl[$S[$storeid]['name']][$com_id] = $com;
            }
		}

        return $rsl;
    }

	/** Mettre dans trait Order **/
    public function getMerchantsOrdersByStore($commercantId = -1, $dfrom = null, $dto = null, $order_id = -1)
    {
        if (!isset($dfrom)) {
            $dfrom = date('Y-m-d');
        }
        if (!isset($dto)) {
            $dto = $dfrom;
        }
        $dfrom .=  ' 00:00:00';
        $dto .=  ' 00:00:00';
        $commercants = $this->getMerchants($commercantId);
        $orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $order_id);
        foreach ($orders as $order) {
            $orderHeader = $this->OrderHeaderParsing($order);
            $products = \Mage::getModel('sales/order_item')->getCollection();
            $products->addFieldToFilter('order_id', ['eq' => $order->getData('entity_id')]);
            if ($commercantId != -1) {
                $products->addFieldToFilter('commercant', ['eq' => $commercantId]);
            }

            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                if (!isset($commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']])) {
                    $commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']] = $orderHeader;
                }
                $commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']]['products'][] = $prod_data;
                $commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_quantite'] += $prod_data['quantite'];
                $commercants[$orderHeader['store_id']][$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_prix'] += $prod_data['prix_total'];
            }
        }

        $rsl = [];
        $S = \Mage::helper('apdc_commercant')->getStoresArray("storeid");

        foreach ($commercants as $storeid => $commercant) {
            $rsl[$S[$storeid]['name']] = $commercant;
        }



        return $rsl;
    }

    public function getRefunds($orderId)
    {
        $merchants = $this->getMerchants();
        $orders = $this->OrdersQuery(null, null, -1, $orderId);
//		$orders->getSelect()->join(['adyen' => \Mage::getSingleton('core/resource')->getTableName('adyen/event_data')], 'adyen.merchant_reference=main_table.increment_id', [
//			'pspreference' => 'adyen.pspreference'
//		]);
//echo \Mage::getSingleton('core/resource')->getTableName('adyen/event_data');
        $rsl = [
            -1 => [
                'merchant' => [
                    'name' => 'All',
                    'total' => 0.0,
                    'refund_total' => 0.0,
                    'refund_prix' => 0.0,
                ],
            ],
        ];
        foreach ($orders as $order) {
            $orderHeader = $this->OrderHeaderParsing($order);
            $rsl[-1]['order'] = $orderHeader;
//			$rsl[-1]['order']['pspreference'] = $order->getData('pspreference');
            $products = \Mage::getModel('sales/order_item')->getCollection();
            $products->addFieldToFilter('main_table.order_id', ['eq' => $orderHeader['mid']]);
            $products->getSelect()->joinLeft(['refund' => \Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/refund_items')], 'refund.order_item_id=main_table.item_id', [
                'refund_prix' => 'refund.prix_final',
                'refund_diff' => 'refund.diffprixfinal',
                'refund_comment' => 'refund.comment',
            ]);

            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                $prod_data['refund_prix'] = $product->getData('refund_prix');
                $prod_data['refund_diff'] = $product->getData('refund_diff');
                if (!isset($rsl[$prod_data['commercant_id']]['merchant'])) {
                    $rsl[$prod_data['commercant_id']]['merchant'] = $merchants[$orderHeader['store_id']][$prod_data['commercant_id']];
                    $rsl[$prod_data['commercant_id']]['merchant']['total'] = 0.0;
                    $rsl[$prod_data['commercant_id']]['merchant']['refund_total'] = 0.0;
                    $rsl[$prod_data['commercant_id']]['merchant']['refund_diff'] = 0.0;
                }
                $rsl[$prod_data['commercant_id']]['products'][$prod_data['id']] = $prod_data;
                $rsl[$prod_data['commercant_id']]['merchant']['total'] += $prod_data['prix_total'];
                $rsl[$prod_data['commercant_id']]['merchant']['refund_total'] += $prod_data['refund_prix'];
                $rsl[$prod_data['commercant_id']]['merchant']['refund_diff'] += $prod_data['refund_diff'];
                $rsl[-1]['merchant']['total'] += $prod_data['prix_total'];
                $rsl[-1]['merchant']['refund_total'] += $prod_data['refund_prix'];
                $rsl[-1]['merchant']['refund_diff'] += $prod_data['refund_diff'];
            }
        }

        return $rsl;
    }

	/** Affichage du tableau de remboursement de BACK-UP **/
	public function getAdyenOrderPaymentTable()
	{
		$collection = \Mage::getModel('adyen/order_payment')->getCollection();
		$ref = [];
		$cpt = 1;
		foreach ($collection as $fields) {
			$ref[$fields->getData('merchant_reference')][$cpt]['merchant_reference'] = $fields->getData('merchant_reference');
			$ref[$fields->getData('merchant_reference')][$cpt]['pspreference'] = $fields->getData('pspreference');
			$ref[$fields->getData('merchant_reference')][$cpt]['amount'] = $fields->getAmount();
			$ref[$fields->getData('merchant_reference')][$cpt]['total_refunded'] = $fields->getData('total_refunded');
			++$cpt;
		}

		return $ref;
	}

    /** Formulaires de soumission de remboursement à Adyen **/
    public function getAdyenPaymentByPsp()
    {
        $collection = \Mage::getModel('adyen/order_payment')->getCollection();
        $collection->addFieldToFilter('pspreference', ['neq' => null]);
        $ref = [];
        $cpt = 1;
        foreach ($collection as $col) {
            $ref[$cpt]['amount'] = $col->getAmount();
            $ref[$cpt]['total_refunded'] = $col->getData('total_refunded');
            $ref[$cpt]['pspreference'] = $col->getData('pspreference');
            $ref[$cpt]['merchant_reference'] = $col->getData('merchant_reference');
            ++$cpt;
        }

        return $ref;
	}

    /** Pour les formulaires de soumissions à Adyen **/
    public function getAdyenQueueFields()
    {
        $collection = \Mage::getModel('adyen/event_queue')->getCollection();
        $collection->addFieldToFilter('increment_id', ['neq' => null]);
        $ref = [];
        $cpt = 1;
        foreach ($collection as $col) {
            $ref[$cpt]['increment_id'] = $col->getData('increment_id');
            $ref[$cpt]['psp_reference'] = $col->getData('psp_reference');
            $ref[$cpt]['adyen_event_result'] = $col->getData('adyen_event_result');
            $ref[$cpt]['success'] = $col->getData('success');
            $ref[$cpt]['created_at'] = $col->getData('created_at');
            ++$cpt;
        }

        return $ref;
	}




/**
 *
 * -------------------- ICI COMMENCE LA FACTURATION -------------
 *
 **/


	public function getOrderAttachments($order)
	{
		$attachments = \Mage::getModel('amorderattach/order_field')->load($order->getId(), 'order_id');
		//$remboursement_client = '|*REMBOURSEMENTS*|</br>'.$attachments->getData('remboursements').'</br>';
		$commentaires_ticket = '|*COM. TICKET*|</br>'.$attachments->getData('commentaires_ticket').'</br>';
		$commentaires_interne = '|*COM. INTERNE*|</br>'.$attachments->getData('commentaires_commande').'</br>';
		$commentaires_fraislivraison = '|*COM. FRAISLIV*|</br>'.$attachments->getData('commentaires_fraislivraison');
		$comments = $remboursement_client.$commentaires_ticket.$commentaires_interne.$commentaires_fraislivraison;
		return $comments;
	}



	public function startsWith($haystack, $needle)
	{
		$length = strlen($needle);

		return substr($haystack, 0, $length) === $needle;
	}





	public function getOrderComments($order)
	{
		$order_comments = '';
		foreach ($order->getAllStatusHistory() as $status) {
			$comment_status = $status->getData('status');
			$comment = $status->getData('comment');
			if ($comment_status == 'processing' && $comment != null && $comment != '' && !startsWith($comment, 'Notification paiement Hipay') && !startsWith($comment, 'Le client a payé par Hipay avec succès')) {
				$order_comments .= '=> '.$comment.'<br/>';
			}
		}

		return '|*ORDER HISTORY*|</br>'.$order_comments;
	}


	/** Récupère l'information commercant dans la table order
	 *  Used in function data_facturation_products() **/
	public function comid_item($item, $order)
	{
		$pid = $item->getProductId();
		$items = $order->getAllItems();
		$commercant = null;
		foreach ($items as $itemId => $item) {
			if ($item->getProductId() == $pid) {
				$commercant = $item->getCommercant();
			}
		}

		return $commercant;
	}

	/** Récupère l'information marge dans la table order
	 * Used in function data_facturation_products() **/
	public function marge_item($item, $order)
	{
		$pid = $item->getProductId();
		$items = $order->getAllItems();
		$commercant = null;
		foreach ($items as $itemId => $item) {
			if ($item->getProductId() == $pid) {
				$commercant = $item->getMargeArriere();
			}
		}

		return $commercant;
	}


	/** Used in data_facturation_products() **/
	public function getRefundorderdata($order, $output)
	{
		$refund_order = \Mage::getModel('pmainguet_delivery/refund_order');
		$orders = $refund_order->getCollection()->addFieldToFilter('order_id', array('in' => $order->getIncrementId()));
		$response = array();
		if ($output == 'comment') {
			$orderAttachment = $this->getOrderAttachments($order);
			$order_comments = $this->getOrderComments($order);
			if ((int) $order->getIncrementId() > $GLOBALS['REFUND_ITEMS_INFO_ID_LIMIT']) {
				foreach ($orders as $o) {
					//$response[$o->getData('commercant')]= $o->getData($output);
					$response[$o->getData('commercant')] .= $orderAttachment;
					//$response[$o->getData('commercant')].=$order_comments;
				}
			} else {
				$response = $orderAttachment;
				//$response.=$order_comments;
			}
		} else {
			foreach ($orders as $o) {
				$response[$o->getData('commercant')] = $o->getData($output);
			}
		}

		return $response;
	}




	/** Used in data_facturation_products() **/
	public function getRefunditemdata($item, $output)
	{
		$refund_items = \Mage::getModel('pmainguet_delivery/refund_items');
		$item = $refund_items->load($item->getOrderItemId(), 'order_item_id');
		$response = $item->getData($output);

		return $response;
	}







	/** Tableau de Facturation **/
	public function data_facturation_products($debut, $fin)
	{
		$data = [];
		$debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));
		$list_commercant = $this->getShops();
		$orders = \Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToFilter('status', array('eq' => \Mage_Sales_Model_Order::STATE_COMPLETE))
			->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin));
		$orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id=mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
		$orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id=mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));

		foreach ($orders as $order) {
			$parentid = $order->getData('relation_parent_real_id');
			if ($parentid != null) {
				$firstparent = false;
				while (!$firstparent) {
					$temp = \Mage::getModel("sales/order")->loadByIncrementId($parentid);
					$temp_parentid = $temp->getData('relation_parent_real_id');
					if ($temp_parentid == null) {
						$firstparent = true;
					} else {
						$parentid = $temp_parentid;
					}
				}
			}
			//Ordered Items
/*			if ($parentid!=NULL)
		   	{
 */			$ordered_items = \Mage::getModel("sales/order")->loadByIncrementId($parentid)->getAllVisibleItems();
		/*	}else
			{
			$ordered_items = $order->getAllVisibleItems();
			$credit_comments = $this->getRefundorderdata($order, 'comment');
			}

			if ($order->hasInvoices()) 
			{
				$invoices = $order->getInvoiceCollection();
			}*/
			foreach ($invoices as $invoice) {
				$invoiced_items = $invoice->getAllItems();
			}
			foreach ($list_commercant as $id => $com) {
				$nb_products = 0;
				$sum_items = 0;
				$sum_items_HT = 0;
				foreach ($ordered_items as $item) {
					if ($item->getCommercant() !== null) {
						if ($item->getData('commercant') == $id) {
							$nb_products += floatval($item->getQtyOrdered());
							$sum_items += floatval($item->getRowTotalInclTax());
							$sum_items_HT += floatval($item->getRowTotal());
						}
					} else {
						$product = \Mage::getModel('catalog/product')->load($item->getProduct()->getId());
						if ($product->getCategoryIds()[2] == $id) {
							$nb_products += floatval($item->getQtyOrdered());
							$sum_items += floatval($item->getRowTotalInclTax());
							$sum_items_HT += floatval($item->getRowTotal());
						}
					}
				}
			 	if ($order->hasInvoices()) {
				/*	if ($order->hasCreditmemos())
					{
						if ($order->hasCreditmemos())
					   	{
							$creditmemos = \Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToFilter('order_id', $order->getId());
							foreach ($creditmemos as $creditmemo)
						   	{
								$credit_items = $creditmemo->getAllItems();
							}
						}
					}*/
					$sum_items_invoice = 0;
					$sum_items_invoice_HT = 0;
					$sum_items_credit = 0;
					$sum_items_credit_HT = 0;
					$sum_commission_HT = 0;
					foreach ($invoiced_items as $item) {
						$com_done = false;
						$commercant_id = $this->comid_item($item, $order);
						if ($commercant_id !== null) {
							if ($commercant_id == $id) {
								$sum_items_invoice += floatval($item->getRowTotalInclTax());
								$sum_items_invoice_HT += floatval($item->getRowTotal());
								$TVApercent = ($sum_items_invoice - $sum_items_invoice_HT) / $sum_items_invoice_HT;
								$marge_arriere = $this->marge_item($item, $order);
								if ($order->hasCreditmemos()) {
									foreach ($credit_items as $citem) {
										if ($item->getProductID() == $citem->getProductID()) {
											$sum_items_credit += floatval($citem->getRowTotalInclTax());
											$sum_items_credit_HT += floatval($citem->getRowTotal());
											$sum_commission_HT += (floatval($item->getRowTotal()) - floatval($citem->getRowTotal())) * floatval(str_replace(',', '.', $marge_arriere));
											$com_done = true;
										}
									}
									$creditdata = $this->getRefunditemdata($item, 'diffprixfinal');
									$sum_items_credit += floatval($creditdata);
									$sum_items_credit_HT += floatval($creditdata) / (1 + $TVApercent);
									$sum_commission_HT += (floatval($item->getRowTotal()) - floatval($creditdata) / (1 + $TVApercent)) * floatval(str_replace(',', '.', $marge_arriere));
									$sum_items_credit_TVA = $sum_items_credit_HT * $TVApercent;
									$com_done = true;
								}
								if (!$com_done) {
									$sum_commission_HT += floatval($item->getRowTotal()) * floatval(str_replace(',', '.', $marge_arriere));
								}
							}
						} else {
							$product = \Mage::getModel('catalog/product')->load($item->getProductID());
							if ($product->getCategoryIds()[2] == $id) {
								$sum_items_invoice += floatval($item->getRowTotalInclTax());
								$sum_items_invoice_HT += floatval($item->getRowTotal());
								if ($order->hasCreditmemos()) {
									foreach ($credit_items as $citem) {
										if ($item->getProductID() == $citem->getProductID()) {
											$cproduct = \Mage::getModel('catalog/product')->load($citem->getProductID());
											$sum_items_credit += floatval($citem->getRowTotalInclTax());
											$sum_items_credit_HT += floatval($citem->getRowTotal());
											$sum_commission_HT += (floatval($item->getRowTotal()) - floatval($citem->getRowTotal())) * floatval(str_replace(',', '.', $product->getData('marge_arriere')));
											$com_done = true;
										}
									}
								}
								if (!$com_done) {
									$sum_commission_HT += floatval($item->getRowTotal()) * floatval(str_replace(',', '.', $product->getData('marge_arriere')));
								}
							}
						}
					}

					if ($sum_items != 0 || ($sum_items_invoice != 0 && $order->hasInvoices())) {
						$date_creation = date('d/m/Y', strtotime($order->getCreatedAt()));
						if (!is_null($order->getDdate())) {
							$date_livraison = date('d/m/Y', strtotime($order->getDdate()));
						} else {
							$date_livraison = 'Non Dispo';
						}
						if ($parentid == null) {
							$parentid = $order->getIncrementId();
						}
						$incrementid = $order->getIncrementId();
						$nom_client = $order->getCustomerName();
						$com;
						$sum_items = round($sum_items, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						$sum_items_HT = round($sum_items_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						$sum_items_TVA = round($sum_items - $sum_items_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						if ($order->hasInvoices()) {
							$sum_items_invoice = round($sum_items_invoice, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_items_invoice_HT = round($sum_items_invoice_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_items_invoice_TVA = round($sum_items_invoice - $sum_items_invoice_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						} else {
							$sum_items_invoice = $sum_items_invoice_HT = $sum_items_invoice_TVA = 0;
						}
						if ($order->hasCreditMemos()) {
							$sum_items_credit = round($sum_items_credit, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_items_credit_HT = round($sum_items_credit_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_items_credit_TVA = round($sum_items_credit - $sum_items_credit_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						} else {
							$sum_items_credit = $sum_items_credit_HT = $sum_items_credit_TVA = 0;
						}
						if ($order->hasInvoices()) {
							$sum_commission = round($sum_commission_HT * (1 + TAX_SERVICE), FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_commission_HT = round($sum_commission_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_commission_TVA = round($sum_commission_HT * TAX_SERVICE, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						} else {
							$sum_commission = $sum_commission_HT = $sum_commission_TVA = 0;
						}
						if ($order->hasInvoices()) {
							$sum_versement = round($sum_items_invoice - $sum_items_credit - $sum_commission_HT * (1 + TAX_SERVICE), FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_versement_HT = round($sum_items_invoice_HT - $sum_items_credit_HT - $sum_commission_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_versement_TVA = round($sum_items_invoice - $sum_items_invoice_HT - ($sum_items_credit - $sum_items_credit_HT) - $sum_commission_HT * TAX_SERVICE, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						} else {
							$sum_versement = $sum_versement_HT = $sum_versement_TVA = 0;
						}
						if ((int) $order->getIncrementId() > $GLOBALS['REFUND_ITEMS_INFO_ID_LIMIT']) {
							$creditcom = $credit_comments[$com];
						} else {
							$creditcom = $credit_comments;
						}
						array_push($data, [
							'date_creation' => $date_creation,
							'date_livraison' => $date_livraison,
							'increment_id' => $incrementid,
							'nom_client' => $nom_client,
							'commercant' => $com,
							'sum_items' => $sum_items,
							'sum_items_HT' => $sum_items_HT,
							'sum_items_credit' => $sum_items_credit,
							'sum_items_credit_HT' => $sum_items_credit_HT,
							'remboursements' => $creditcom,
							'sum_ticket' => $sum_items - $sum_items_credit,
							'sum_ticket_HT' => $sum_items_HT - $sum_items_credit_HT,
							'sum_commission' => $sum_commission,
							'sum_commission_HT' => $sum_commission_HT,
							'sum_versement' => $sum_versement,
							'sum_versement_HT' => $sum_versement_HT,
						]);
					}
				}
			}
		}

		echo'<pre>';
		var_dump($data);
		echo'<pre>';
	//	return $data;
	}
}
