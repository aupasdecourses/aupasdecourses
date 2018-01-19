<?php

namespace Apdc\ApdcBundle\Services;

include_once '../../app/Mage.php';

class Magento
{
    //Réutiliser les fonctions de Delivery et de Dispatch pour simplifier code

    use Helpers\Credimemo;
    use Helpers\Products;
    use Helpers\Order;
    use Helpers\Model;
    use Helpers\Media;

    const AUTHORIZED_GROUP = ['Administrators'];
    const ATTRIBUTE_CODES = array('commercant', 'produit_biologique', 'produit_de_saison', 'tax_class_id');
    const CLASS_TAX_IDS = array(5 => '5.5%', 9 => '10%', 10 => '20%');

    public function __construct()
    {
        \Mage::app();
        $this->_attributeArraysLabels = $this->getAttributesLabelFromId(self::ATTRIBUTE_CODES);
        $this->_attributeArraysIds = $this->getAttributesIdFromLabel(self::ATTRIBUTE_CODES);
    }

    //Réutiliser les fonctions Magento développée pour les infos de fermeture magasin
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

    //Récupère toutes les commandes

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

    /**	VUE ECLATEE PAR QUARTIERS ET PAR COMMERCANTS*/
    public function getMerchantsByStore($commercantId = -1)
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

                if (!isset($commercants[$storeinfo['store_id']][$shop->getData('id_attribut_commercant')])) {
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
                            'timetable' => implode('<br/>', $shop->getTimetable()),
                            'closing_periods' => $closed_periods,
                            'delivery_days' => $delivery_days,
                            'warning_days' => $this->getWarningDays($delivery_days, $closed_periods),
                            'blacklist' => $shop->getBlacklist(),
                        ];
                }
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

    //Récupère la liste des commandes par quartiers
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

    //Récupère la liste des commandes par quartiers et par commerçant
    public function getOrdersByStoreByMerchants($commercantId = -1, $dfrom = null, $dto = null, $order_id = -1)
    {
        if (!isset($dfrom)) {
            $dfrom = date('Y-m-d');
        }
        if (!isset($dto)) {
            $dto = $dfrom;
        }
        $dfrom .=  ' 00:00:00';
        $dto .=  ' 00:00:00';
        $commercants = $this->getMerchantsByStore($commercantId);
        $orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $order_id);
        foreach ($orders as $order) {
            $orderHeader = $this->OrderHeaderParsing($order);
            $products = \Mage::getModel('sales/order_item')->getCollection();
            $products->addFieldToFilter('order_id', ['eq' => $order->getData('entity_id')]);
            $products->addFieldToFilter('main_table.product_type', ['neq' => 'bundle']);
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

    /*VUE AGGREGEE PAR COMMERCANTS ET PAS DECOMPOSEE PAR QUARTIERS*/

    //  Récupère tous les commerçants
    public function getMerchants($commercantId = -1)
    {
        $commercants = [];

        $shops = \Mage::getModel('apdc_commercant/shop')->getCollection();
        if ($commercantId != -1) {
            $shops->addFieldToFilter('id_attribut_commercant', ['eq' => $commercantId]);
        }

        foreach ($shops as $shop) {
            $delivery_days = $shop->getDeliveryDays();
            $closed_periods = $shop->getClosingPeriods();

            $shop_manager = \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $shop->getIdContactManager())->getFirstItem();

            $commercants[$shop->getData('id_attribut_commercant')] = [
                    'active' => $shop->getData('enabled'),
                    'id' => $shop->getData('id_attribut_commercant'),
                    'code' => $shop->getData('code'),
                    'shop_id' => $shop->getIdShop(),
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
                    'timetable' => implode('<br/>', $shop->getTimetable()),
                    'closing_periods' => $closed_periods,
                    'delivery_days' => $delivery_days,
                    'warning_days' => $this->getWarningDays($delivery_days, $closed_periods),
                    'blacklist' => $shop->getBlacklist(),
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

    //Récupère toutes les commandes d'un commerçant
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
        $commercants = $this->getMerchantsByStore($commercantId, $order_id);
        $orders = $this->OrdersQuery($dfrom, $dto, $commercantId);
        foreach ($orders as $order) {
            $orderHeader = $this->OrderHeaderParsing($order);
            $products = \Mage::getModel('sales/order_item')->getCollection();
            $products->addFieldToFilter('order_id', ['eq' => $order->getData('entity_id')])
                     ->addFieldToFilter('commercant', ['eq' => $commercantId]);
            $products->addFieldToFilter('main_table.product_type', ['neq' => 'bundle']);
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

    //Récupère une commande et l'affiche par commerçant
    public function getOrderByMerchants($orderId)
    {
        $merchants = $this->getMerchantsByStore();
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

    /** Récupère toutes les commandes et les affichent par commerçant (Similaire à getMerchantsOrdersByStore) */
    public function getOrdersByMerchants($commercantId = -1, $dfrom = null, $dto = null, $order_id = -1)
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

        $rsl = [];

        $orders = $this->OrdersQuery($dfrom, $dto, $commercantId, $order_id);
        foreach ($orders as $order) {
            $orderHeader = $this->OrderHeaderParsing($order);
            $products = \Mage::getModel('sales/order_item')->getCollection();
            $products->addFieldToFilter('order_id', ['eq' => $order->getData('entity_id')]);
            $products->addFieldToFilter('main_table.product_type', ['neq' => 'bundle']);
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

        foreach ($commercants as $storeid => $commercant) {
            $rsl[$storeid] = $commercant;
        }

        usort($rsl, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $rsl;
    }

    /** ------- **/
    /** REFUNDS **/
    /** ------- **/
    public function getRefunds($orderId)
    {
        $merchants = $this->getMerchantsByStore();
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
            $products->addFieldToFilter('main_table.product_type', ['neq' => 'bundle']);
            $products->getSelect()->joinLeft(['refund' => \Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/refund_items')], 'refund.order_item_id=main_table.item_id', [
                'refund_prix' => 'refund.prix_final',
                'refund_diff' => 'refund.diffprixfinal',
                'refund_comment' => 'refund.comment',
                'refund_prix_commercant' => 'refund.prix_commercant',
                'refund_diff_commercant' => 'refund.diffprixcommercant',
            ]);

            foreach ($products as $product) {
                $prod_data = $this->ProductParsing($product, $orderId);
                $prod_data['refund_prix'] = floatval($product->getData('refund_prix'));
                $prod_data['refund_diff'] = floatval($product->getData('refund_diff'));
                $prod_data['refund_prix_commercant'] = floatval($product->getData('refund_prix_commercant'));
                $prod_data['refund_diff_commercant'] = floatval($product->getData('refund_diff_commercant'));

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

                $rsl[-1]['merchant']['total'] += floatval($prod_data['prix_total']);
                $rsl[-1]['merchant']['refund_total'] += floatval($prod_data['refund_prix']);
                $rsl[-1]['merchant']['refund_diff'] += floatval($prod_data['refund_diff']);
                $rsl[-1]['merchant']['refund_total_commercant'] += floatval($prod_data['refund_prix_commercant']);
                $rsl[-1]['merchant']['refund_diff_commercant'] += floatval($prod_data['refund_diff_commercant']);
            }
        }

        $rsl[-1]['merchant']['total'] = round($rsl[-1]['merchant']['total'], 2);
        $rsl[-1]['merchant']['refund_total'] = round($rsl[-1]['merchant']['refund_total'], 2);
        $rsl[-1]['merchant']['refund_diff'] = round($rsl[-1]['merchant']['refund_diff'], 2);
        $rsl[-1]['merchant']['refund_total_commercant'] = round($rsl[-1]['merchant']['refund_total_commercant'], 2);
        $rsl[-1]['merchant']['refund_diff_commercant'] = round($rsl[-1]['merchant']['refund_diff_commercant'], 2);

        return $rsl;
    }

    /**	
     *	Retourne le contenu de la table adyen/order_payment trié par n° de commande.
     */
    public function getAdyenPaymentByMerchRef()
    {
        $collection = \Mage::getModel('adyen/order_payment')->getCollection();
        $collection->getSelect()->join('sales_flat_order', 'main_table.merchant_reference=sales_flat_order.increment_id');

        $ref = [];
        $cpt = 1;
        foreach ($collection as $fields) {
            $ref[$fields->getData('merchant_reference')][$cpt]['customer_firstname'] = $fields->getData('customer_firstname');
            $ref[$fields->getData('merchant_reference')][$cpt]['customer_lastname'] = $fields->getData('customer_lastname');
            $ref[$fields->getData('merchant_reference')][$cpt]['merchant_reference'] = $fields->getData('merchant_reference');
            $ref[$fields->getData('merchant_reference')][$cpt]['pspreference'] = $fields->getData('pspreference');
            $ref[$fields->getData('merchant_reference')][$cpt]['amount'] = $fields->getAmount();
            $ref[$fields->getData('merchant_reference')][$cpt]['total_refunded'] = $fields->getData('total_refunded');
            ++$cpt;
        }

        return $ref;
    }

    /**	
     *	Retourne le contenu de la table adyen/order_payment.
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

    /* Retourne la marge arriere par commercant */
    public function getMargin()
    {
        $data = [];
        $products = \Mage::getModel('catalog/product')->getCollection();
        $products->addAttributeToSelect('commercant')
                 ->addAttributeToSelect('name')
                 ->addAttributeToSelect('marge_arriere');

        foreach ($products as $product) {
            if (($product->getData('commercant') != null) && ($product->getData('marge_arriere') != '#DIV/0!')) {
                $data[$product->getAttributeText('commercant')]['nb_products'] = count($data[$product->getAttributeText('commercant')]['products']);
                $data[$product->getAttributeText('commercant')]['total_marge'] = array_sum($data[$product->getAttributeText('commercant')]['products']);
                $data[$product->getAttributeText('commercant')]['max_marge'] = 100 * max($data[$product->getAttributeText('commercant')]['products']);
                $data[$product->getAttributeText('commercant')]['min_marge'] = 100 * min($data[$product->getAttributeText('commercant')]['products']);
                $data[$product->getAttributeText('commercant')]['marge_moyenne'] = 100 * ($data[$product->getAttributeText('commercant')]['total_marge'] / $data[$product->getAttributeText('commercant')]['nb_products']);

                $data[$product->getAttributeText('commercant')]['products'][$product->getData('name')] = $product->getData('marge_arriere');
            }
        }

        ksort($data);

        return $data;
    }

    /* Affiche les infos relatifs à l'historique de la commande */
    public function getOrderHistory($order_id)
    {
        $order = \Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $history = $order->getStatusHistoryCollection();

        return $history;
    }

    /** ------- **/
    /** CUSTOMERS **/
    /** ------- **/

    /** Affiche les commandes par clients */
    public function getOrdersByCustomer($debut = null, $fin = null)
    {
        $data = [];
        $debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));

        $orders = \Mage::getModel('sales/order')->getCollection();

        $orders->getSelect()->join('sales_flat_invoice', 'sales_flat_invoice.order_id = main_table.entity_id');
        $orders->addFilterToMap('created_at', 'sales_flat_invoice.created_at');
        $orders->addFilterToMap('increment_id', 'sales_flat_invoice.increment_id');

        $orders->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin))
               ->addAttributeToSort('created_at', 'ASC');

        foreach ($orders as $order) {
            array_push($data, [
                'created_at' => $order->getData('created_at'),
                'numero_commande' => $order->getData('increment_id'),
                'nom_client' => $order->getData('customer_firstname').' '.$order->getData('customer_lastname'),
                'total_produits_HT' => round($order->getData('subtotal'), 2),
                'total_produits_TVA' => round($order->getData('tax_amount'), 2),
                'total_produits_TTC' => round($order->getData('subtotal_incl_tax'), 2),
                'frais_livraison_HT' => round($order->getData('shipping_amount'), 2),
                'frais_livraison_TVA' => round($order->getData('shipping_tax_amount'), 2),
                'frais_livraison_TTC' => round($order->getData('shipping_incl_tax'), 2),
                'discount' => round($order->getData('discount_amount'), 2),
                'total_commande_TTC' => round($order->getData('grand_total'), 2),
            ]);
        }

        return $data;
    }
}
