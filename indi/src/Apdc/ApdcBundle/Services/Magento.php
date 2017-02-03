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

    public function getMerchants($commercantId = -1)
    {
        $commercants = [];

        $shops = \Mage::getModel('apdc_commercant/shop')->getCollection();
        if ($commercantId != -1) {
            $shops->addFieldToFilter('id_attribut_commercant', ['eq' => $commercantId]);
        }
        $shops->getSelect()->join('catalog_category_entity', 'main_table.id_category=catalog_category_entity.entity_id', array('catalog_category_entity.path'));
        $shops->addFilterToMap('path', 'catalog_category_entity.path');

        $S = [];
        $app = \Mage::app();
        $stores = $app->getStores();
        foreach ($stores as $id => $idc) {
            $S[$app->getStore($id)->getRootCategoryId()]['id'] = $app->getStore($id)->getRootCategoryId();
            $S[$app->getStore($id)->getRootCategoryId()]['name'] = $app->getStore($id)->getName();
        }

        foreach ($shops as $shop) {
            $commercants[$shop->getData('id_attribut_commercant')] = [
                    'active' => $shop->getData('enabled'),
                    'id' => $shop->getData('id_attribut_commercant'),
                    'store' => $S[explode('/', $shop->getPath())[1]]['name'],
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
        uasort($commercants, function ($lhs, $rhs) {
            if ($lhs['active'] < $rhs['active']) {
                return true;
            }

            return false;
         });

        return $commercants;
    }

    private function OrdersQuery($dfrom, $dto, $commercantId = -1, $orderId = -1)
    {
        $orders = \Mage::getModel('sales/order')->getCollection();
        $orders->getSelect()->join('mwddate_store', 'main_table.entity_id=mwddate_store.sales_order_id',
            array(
                'mwddate_store.ddate_id',
            ));
        $orders->getSelect()->join('mwddate', 'mwddate_store.ddate_id=mwddate.ddate_id',
            array(
                'ddate' => 'mwddate.ddate',
                'dtime' => 'mwddate.dtime', ));
        $orders->getSelect()->join('mwdtime', 'mwddate.dtime=mwdtime.dtime_id',
            array(
                'dtime' => 'mwdtime.interval',
        ));
        $orders->getSelect()->join(array('order_attribute' => 'amasty_amorderattr_order_attribute'), 'order_attribute.order_id = main_table.entity_id',
            array(
                'produit_equivalent' => 'order_attribute.produit_equivalent',
                'contactvoisin' => 'order_attribute.contactvoisin',
                'codeporte1' => 'order_attribute.codeporte1',
                'codeporte2' => 'order_attribute.codeporte2',
                'batiment' => 'order_attribute.batiment',
                'etage' => 'order_attribute.etage',
                'telcontact' => 'order_attribute.telcontact',
                'info' => 'order_attribute.infoscomplementaires',
            ));
        $orders->getSelect()->joinLeft(array('attachment' => 'amasty_amorderattach_order_field'), 'attachment.order_id=main_table.entity_id',
            array(
                'upload' => 'attachment.upload',
                'input' => 'attachment.input',
                'digest' => 'attachment.digest',
				'refund' => 'attachment.refund',
				'commentaires_ticket'			=> 'attachment.commentaires_ticket',
				'commentaires_fraislivraison'	=> 'attachment.commentaires_fraislivraison',
            ));
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
                    array('order_item' => \Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                    'order_item.order_id = main_table.entity_id'
                )->where("order_item.commercant={$commercantId}")->group('order_item.order_id');
            }
        }

        return $orders;
    }

    private function OrderHeaderParsing($order)
    {
        $orderHeader = [];
        $shipping = $order->getShippingAddress();
        $orderHeader['mid'] = $order->getData('entity_id');
        $orderHeader['id'] = $order->getData('increment_id');
        $orderHeader['store'] = \Mage::app()->getStore($order->getData('store_id'))->getName();
        $orderHeader['status'] = $order->getStatusLabel();
        $orderHeader['upload'] = $order->getData('upload');
        $orderHeader['input'] = $order->getData('input');
        $orderHeader['digest'] = $order->getData('digest');
        $orderHeader['refund'] = $order->getData('refund');
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

        return $orderHeader;
    }

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

    private function checkEntryToModel($model, array $filters)
    {
        $entry = $model->getCollection();
        foreach ($filters as $k => $v) {
            $entry->addFieldToFilter($k, $v);
        }
        if ($entry->getFirstItem()->getId() <> null) {
            return true;
        } else {
            return false;
        }
    }

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

    public function addEntryToOrderField(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel('amorderattach/order_field'),
            $data
        );
    }

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

    public function addEntryToRefundItem(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/refund_items')),
            $data
        );
    }

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
                    $rsl[$prod_data['commercant_id']]['merchant'] = $merchants[$prod_data['commercant_id']];
                    $rsl[$prod_data['commercant_id']]['merchant']['total'] = 0.0;
                }
                $rsl[$prod_data['commercant_id']]['products'][] = $prod_data;
                $rsl[$prod_data['commercant_id']]['merchant']['total'] += $prod_data['prix_total'];
                $rsl[-1]['merchant']['total'] += $prod_data['prix_total'];
            }
        }

        return $rsl;
    }

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

    public function getMerchantsOrders($commercantId = -1, $dfrom = null, $dto = null, $order_id = -1)
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
            $products->addFieldToFilter('order_id', ['eq' => $order->getData('entity_id')]);
            if ($commercantId != -1) {
                $products->addFieldToFilter('commercant', ['eq' => $commercantId]);
            }
            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                if (!isset($commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']])) {
                    $commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']] = $orderHeader;
                }
                $commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['products'][] = $prod_data;
                $commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_quantite'] += $prod_data['quantite'];
                $commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_prix'] += $prod_data['prix_total'];
            }
        }

        return $commercants;
    }

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
                if (!isset($commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']])) {
                    $commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']] = $orderHeader;
                }
                $commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['products'][] = $prod_data;
                $commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_quantite'] += $prod_data['quantite'];
                $commercants[$prod_data['commercant_id']]['orders'][$orderHeader['id']]['total_prix'] += $prod_data['prix_total'];
            }
        }
        $rsl = [];
        foreach ($commercants as $cid => $commercant) {
            $rsl[$commercant['store']][$cid] = $commercant;
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
            $rsl[-1]['order'] = $this->OrderHeaderParsing($order);
//			$rsl[-1]['order']['pspreference'] = $order->getData('pspreference');
			$products =  \Mage::getModel('sales/order_item')->getCollection();
			$products->addFieldToFilter('main_table.order_id', ['eq' => $rsl[-1]['order']['mid']]);
			$products->getSelect()->joinLeft(['refund' => \Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/refund_items')], 'refund.order_item_id=main_table.item_id', [
				'refund_prix'	=> 'refund.prix_final',
				'refund_diff'	=> 'refund.diffprixfinal',
				'refund_com'	=> 'refund.comment'
			]);
			/* $order->getData('comment')   EST POSSIBLE ICI */
			//var_dump($order->getData('commentaires_ticket'));
				
			foreach ($products as $product) {
				$prod_data = $this->ProductParsing($product, $orderId);
				$prod_data['refund_prix'] = $product->getData('refund_prix');
				$prod_data['refund_diff'] = $product->getData('refund_diff');
				$prod_data['refund_com'] = $product->getData('refund_com');
				if (!isset($rsl[$prod_data['commercant_id']]['merchant'])) {
					$rsl[$prod_data['commercant_id']]['merchant'] = $merchants[$prod_data['commercant_id']];
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

				/* I GOT A DOUBT WITH THAT */
				$rsl[-1]['merchant']['commentaires_fraislivraison'] = $order->getData('commentaires_fraislivraison');
				$rsl[-1]['merchant']['commentaires_ticket']			= $order->getData('commentaires_ticket');
			}
		}
		return ($rsl);
	}
	
	/* Affichage du tableau de remboursement de BACK-UP */
    public function getAdyenOrderPaymentTable()
    {
        $collection = \Mage::getModel('adyen/order_payment')->getCollection();
        $ref = [];
        $cpt = 1;
        foreach ($collection as $fields) {
            $ref[$fields->getData('merchant_reference')][$cpt]['merchant_reference']	= $fields->getData('merchant_reference');
            $ref[$fields->getData('merchant_reference')][$cpt]['pspreference']			= $fields->getData('pspreference');
            $ref[$fields->getData('merchant_reference')][$cpt]['amount']				= $fields->getAmount();
            $ref[$fields->getData('merchant_reference')][$cpt]['total_refunded']		= $fields->getData('total_refunded');
            ++$cpt;
        }
        return $ref;
    }

	/* Formulaires de soumission de remboursement à Adyen*/
    public function getAdyenPaymentByPsp()
    {
        $collection = \Mage::getModel('adyen/order_payment')->getCollection();
        $collection->addFieldToFilter('pspreference', ['neq' => null]);
        $ref = [];
        $cpt = 1;
        foreach ($collection as $col) {
            $ref[$cpt]['amount']				= $col->getAmount();
            $ref[$cpt]['total_refunded']		= $col->getData('total_refunded');
            $ref[$cpt]['pspreference']			= $col->getData('pspreference');
            $ref[$cpt]['merchant_reference']	= $col->getData('merchant_reference');
            ++$cpt;
        }

        return $ref;
    }
    /* Formulaires de soumissions à Adyen, historique des remboursements d'une commande  */
    public function getAdyenEventData()
    {
        $collection = \Mage::getModel('adyen/event')->getCollection();
        $collection->addFieldToFilter('increment_id', ['neq' => null]);
        $ref = [];
        $cpt = 1;
        foreach ($collection as $col) {
            $ref[$cpt]['increment_id']			= $col->getData('increment_id');
            $ref[$cpt]['psp_reference']			= $col->getData('psp_reference');
			$ref[$cpt]['adyen_event_result']	= $col->getData('adyen_event_result');
			$ref[$cpt]['success']				= $col->getData('success');
			$ref[$cpt]['created_at']			= $col->getData('created_at');
            ++$cpt;
        }

        return $ref;
	}
}
