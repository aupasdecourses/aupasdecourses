<?php

namespace Apdc\ApdcBundle\Services;

include_once '../../app/Mage.php';

class Magento
{
    use Credimemo;
    use Products;

    const AUTHORIZED_GROUP = ['Administrators'];
    const ATTRIBUTE_CODES = array('commercant', 'produit_biologique', 'produit_de_saison');
    const CLASS_TAX_IDS = array(5 => '5.5%', 9 => '10%', 10 => '20%');

    public function __construct()
    {
        \Mage::app();
        $this->_attributeArraysLabels = $this->getAttributesLabelFromId(self::ATTRIBUTE_CODES);
        $this->_attributeArraysIds = $this->getAttributesIdFromLabel(self::ATTRIBUTE_CODES);
    }

    public function mediaPath()
    {
        return realpath(__DIR__.'/../../../../../media');
    }

    public function mediaUrl()
    {
        return \Mage::getBaseUrl('media');
    }

    /** Ancienne version de getMerchants. Ne prend pas en compte un marchand dans plusieurs shops
     *
     */
    /*public function getShops($commercantId = -1)
    {
    
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
     
    }
     */

    public function getWarningDays($delivery_days, $closed_periods)
    {
        if ($closed_periods != array()) {
            $tmp = '';
            foreach ($closed_periods as $key => $period) {
                $tmp = 'du '.$period['start'].' au '.$period['end'];
                $closed_periods[$key] = $tmp;
            }
            $period_warning = 'Magasin fermé '.implode(', ', $closed_periods).'.';
        }

        $warning_days = array_diff([2, 3, 4, 5], $delivery_days);
        if ($warning_days != array()) {
            $tmp = \Mage::helper('apdc_commercant')->getDays();
            foreach ($warning_days as $key => $day) {
                $warning_days[$key] = $tmp[$day - 1];
            }
            $day_warning = 'Attention! Magasin fermé le '.implode(',', $warning_days).'.';
        }

        return $day_warning.' '.$period_warning;
    }

    /**	Le nouveau getShops
     *	Prend en compte un marchand dans différents shops
     *	N'est PAS utilisé pour la facturation.
     */
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

                $delivery_days = $shop->getDeliveryDays();
                $closed_periods = $shop->getClosingPeriods();

                $shop_manager = \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactManager())->getFirstItem();

                $commercants[$storeinfo['store_id']][$shop->getData('id_attribut_commercant')] = [
                        'active' => $shop->getData('enabled'),
                        'id' => $shop->getData('id_attribut_commercant'),
                        'code' => $shop->getData('code'),
                        'shop_id' => $shop->getIdShop(),
                        'store' => $storeinfo['name'],
                        'store_id' => $storeinfo['store_id'],
                        'name' => $shop->getName(),
                        'addr' => $shop->getStreet().' '.$shop->getPostCode().' '.$shop->getCity(),
                        'phone' => $shop->getPhone(),
                        'mail3' => \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactEmployeeBis())->getFirstItem()->getEmail(),
                        'mailc' => \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactManager())->getFirstItem()->getEmail(),
                        'mailp' => \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactEmployee())->getFirstItem()->getEmail(),
                        'manager_name' => $shop_manager->getFirstname().' '.$shop_manager->getLastname(),
                        'mobile' => $shop_manager->getPhone(),
                        'manager_id' => $shop_manager->getIdContact(),
                        'orders' => [],
                        'timetable' => implode(',', $shop->getTimetable()),
                        'closing_periods' => $closed_periods,
                        'delivery_days' => $delivery_days,
                        'warning_days' => $this->getWarningDays($delivery_days, $closed_periods),
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

    /** Mettre dans trait Model **/
    public function addEntryToBillingDetails(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/indi_billingdetails')),
            $data
        );
    }

    /** Mettre dans trait Model **/
    public function updateEntryToBillingDetails(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/indi_billingdetails');
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
    public function addEntryToBillingSummary(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel(\Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/indi_billingsummary')),
                $data
        );
    }

    public function addEntryToGeocode(array $data)
    {
        $this->addEntryToModel(
            \Mage::getModel('pmainguet_delivery/geocode_customers'),
            $data
        );
    }

    /** Mettre dans trait Model **/
    public function updateEntryToBillingSummary(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/indi_billingsummary');
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
    public function updateEntryToGeocode(array $filters, array $updatedFields)
    {
        $model = \Mage::getModel('pmainguet_delivery/geocode_customers');
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
        $S = \Mage::helper('apdc_commercant')->getStoresArray('storeid');

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
        $S = \Mage::helper('apdc_commercant')->getStoresArray('storeid');

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
                    'refund_total_commercant' => 0.0,
                    'refund_prix_commercant' => 0.0,
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
                'refund_prix_commercant' => 'refund.prix_commercant',
                'refund_diff_commercant' => 'refund.diffprixcommercant',
            ]);

            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                $prod_data['refund_prix'] = $product->getData('refund_prix');
                $prod_data['refund_diff'] = $product->getData('refund_diff');
                $prod_data['refund_prix_commercant'] = $product->getData('refund_prix_commercant');
                $prod_data['refund_diff_commercant'] = $product->getData('refund_diff_commercant');
                if (!isset($rsl[$prod_data['commercant_id']]['merchant'])) {
                    $rsl[$prod_data['commercant_id']]['merchant'] = $merchants[$orderHeader['store_id']][$prod_data['commercant_id']];
                    $rsl[$prod_data['commercant_id']]['merchant']['total'] = 0.0;
                    $rsl[$prod_data['commercant_id']]['merchant']['refund_total'] = 0.0;
                    $rsl[$prod_data['commercant_id']]['merchant']['refund_diff'] = 0.0;
                    $rsl[$prod_data['commercant_id']]['merchant']['refund_total_commercant'] = 0.0;
                    $rsl[$prod_data['commercant_id']]['merchant']['refund_diff_commercant'] = 0.0;
                }
                $rsl[$prod_data['commercant_id']]['products'][$prod_data['id']] = $prod_data;
                $rsl[$prod_data['commercant_id']]['merchant']['total'] += $prod_data['prix_total'];
                $rsl[$prod_data['commercant_id']]['merchant']['refund_total'] += $prod_data['refund_prix'];
                $rsl[$prod_data['commercant_id']]['merchant']['refund_diff'] += $prod_data['refund_diff'];
                $rsl[$prod_data['commercant_id']]['merchant']['refund_total_commercant'] += $prod_data['refund_prix_commercant'];
                $rsl[$prod_data['commercant_id']]['merchant']['refund_diff_commercant'] += $prod_data['refund_diff_commercant'];
                $rsl[-1]['merchant']['total'] += $prod_data['prix_total'];
                $rsl[-1]['merchant']['refund_total'] += $prod_data['refund_prix'];
                $rsl[-1]['merchant']['refund_diff'] += $prod_data['refund_diff'];
                $rsl[-1]['merchant']['refund_total_commercant'] += $prod_data['refund_prix_commercant'];
                $rsl[-1]['merchant']['refund_diff_commercant'] += $prod_data['refund_diff_commercant'];
            }
        }

        return $rsl;
    }

    /**	Retourne le contenu de la table adyen/order_payment
     *	Trié par reference marchante ( ex : 2016000723 ).
     *
     *	Utilisé dans RefundController, refundAdyenIndexAction
     *	pour la liste des commandes remboursables. (Remboursement Back-up )
     */
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

    /**	Retourne le contenu de la table adyen/order_payment
     *	Utilisé dans les deux formulaires de soumission de remboursement Adyen
     *	refundFinalAction & refundAdyenFormAction.
     */
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

    /**	Retourne le contenu de la table adyen/event_queue
     *	Utilisé dans les 2 formulaires de soumission de remboursement.
     */
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

    /** Pour les payouts */
    public function getApdcBankFields()
    {
        $tab = [];

        $merchants = \Mage::getModel('apdc_commercant/commercant')->getCollection();

        $merchants->getSelect()->join('apdc_bank_information', 'main_table.id_bank_information = apdc_bank_information.id_bank_information');

        $merchants->getSelect()->join('apdc_commercant_contact', 'main_table.id_contact_billing = apdc_commercant_contact.id_contact');

        $merchants->getSelect()->join('apdc_shop', 'main_table.id_commercant = apdc_shop.id_commercant')->group('main_table.id_commercant');

        foreach ($merchants as $merchant) {
            $tab[$merchant->getData('name')] = [
                'id' => $merchant->getData('id_commercant'),
                'reference' => 'PAY-'.date('Y-m').'-'.$merchant->getData('code').'-',
                'name' => $merchant->getData('name'),
                'ownerName' => $merchant->getData('owner_name'),
                'iban' => $merchant->getData('account_iban'),
                'shopperEmail' => $merchant->getData('email'),
                'shopperReference' => $merchant->getData('firstname').' - '.$merchant->getData('lastname'),
            ];
        }

        return $tab;
    }
}
