<?php

class Apdc_Delivery_Model_Orders_Shop extends Mage_Sales_Model_Order_Item
{
    protected $_collection;
    protected $_shops;
    protected $_shopsids;

    public function __construct()
    {
        parent::__construct();

        $collection = $this->getCollection();
        $collection->addAttributeToSelect('commercant')
                    ->addAttributeToSelect('item_id')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('short_description')
                    ->addAttributeToSelect('qty_ordered')
                    ->addAttributeToSelect('price_incl_tax')
                    ->addAttributeToSelect('prix_kilo_site')
                    ->addAttributeToSelect('row_total_incl_tax')
                    ->addAttributeToSelect('produit_fragile')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('item_comment')
                    ->addAttributeToSelect('product_options')
                    ->addAttributeToSelect('product_type')
                    ->addFieldToFilter('main_table.product_type', array('nin' => array('bundle')));
        $collection->getSelect()->join(
            'sales_flat_order',
            'main_table.order_id=sales_flat_order.entity_id', [
                'entity_id' => 'sales_flat_order.entity_id',
                'increment_id' => 'sales_flat_order.increment_id',
                'status' => 'sales_flat_order.increment_id',
                'store_id' => 'sales_flat_order.store_id',
                'status' => 'sales_flat_order.status',
                'customer_email' => 'sales_flat_order.customer_email',
                'customer_id' => 'sales_flat_order.customer_id',
                'created_at' => 'sales_flat_order.created_at',
                'produit_equivalent' => 'sales_flat_order.produit_equivalent',
            ]
        )
        ->join(
            'mwddate_store',
            'sales_flat_order.entity_id=mwddate_store.sales_order_id',
            [
                'mwddate_store.ddate_id',
            ]
        )->join(
            'mwddate',
            'mwddate_store.ddate_id=mwddate.ddate_id',
            [
                'ddate' => 'mwddate.ddate',
            ]
        )->join(
            'mwdtime',
            'mwddate.dtime = mwdtime.dtime_id',
            [
                'dtime' => 'mwdtime.interval',
            ]
        )->join(
            [
                'order_attribute' => 'amasty_amorderattr_order_attribute',
            ],
            'order_attribute.order_id = sales_flat_order.entity_id',
            [
                'order_attribute.contactvoisin',
                'order_attribute.codeporte1',
                'order_attribute.codeporte2',
                'order_attribute.batiment',
                'order_attribute.etage',
                'order_attribute.telcontact',
                'order_attribute.infoscomplementaires',
            ]
        )->joinLeft(
            ['shipping_o_a' => $this->getCollection()->getTable('sales/order_address')],
            '(sales_flat_order.entity_id = shipping_o_a.parent_id AND shipping_o_a.address_type = "shipping")',
            [
                'o_firstname' => 'shipping_o_a.firstname',
                'o_lastname' => 'shipping_o_a.lastname',
                'o_telephone' => 'shipping_o_a.telephone',
                'o_street' => 'shipping_o_a.street',
                'o_zipcode' => 'shipping_o_a.postcode',
                'o_city' => 'shipping_o_a.city',
            ]
        );

        $collection->addFilterToMap('ddate', 'mwddate.ddate');
        $collection->addFilterToMap('commercant', 'sales_flat_order_item.commercant');
        $this->_collection = $collection->addFilterToMap('dtime', 'mwdtime.interval')
            ->addFieldToFilter('sales_flat_order.status', array('nin' => array('complete', 'pending_payment', 'payment_review', 'holded', 'closed', 'canceled')))
            ->addAttributeToSort('dtime', 'asc');

        $this->listShopsIds();
    }

    public function listShopsIds()
    {
        $collection = Mage::getModel('apdc_commercant/shop')->getCollection();
        $this->_shopsids = $collection->getColumnValues('id_attribut_commercant');
    }

    public function getShops($getByStore = true)
    {
        $commercant = [];
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = 'SELECT main_table.*, apdc_commercant_contact.lastname AS m_lastname, apdc_commercant_contact.firstname AS m_firstname, apdc_commercant_contact.email AS m_email, apdc_commercant_contact.phone AS m_phone, apdc_commercant_contact_2.lastname AS e1_lastname, apdc_commercant_contact_2.firstname AS e1_firstname, apdc_commercant_contact_2.email AS e1_email, apdc_commercant_contact_2.phone AS e1_phone, apdc_commercant_contact_3.lastname AS e2_lastname, apdc_commercant_contact_3.firstname AS e2_firstname, apdc_commercant_contact_3.email AS e2_email, apdc_commercant_contact_3.phone AS e2_phone FROM apdc_shop AS main_table LEFT JOIN apdc_commercant_contact ON main_table.id_contact_manager=apdc_commercant_contact.id_contact LEFT JOIN apdc_commercant_contact AS apdc_commercant_contact_2 ON main_table.id_contact_employee=apdc_commercant_contact_2.id_contact LEFT JOIN apdc_commercant_contact AS apdc_commercant_contact_3 ON main_table.id_contact_employee_bis=apdc_commercant_contact_3.id_contact';
        $shops = $connection->fetchAll($sql);

        if ($getByStore) {
            $cat_array = Mage::helper('apdc_commercant')->getCategoriesArray();
            $S = Mage::helper('apdc_commercant')->getStoresArray();

            foreach ($shops as $shop) {
                $cats = explode(',', $shop['id_category']);
                foreach ($cats as $cat) {
                    $storeinfo = $S[explode('/', $cat_array[$cat])[1]];
                    if (!isset($commercants[$storeinfo['store_id']][$shop['id_attribut_commercant']])) {
                        $commercants[$storeinfo['store_id']][$shop['id_attribut_commercant']]['infos'] = $shop;
                        $commercants[$storeinfo['store_id']][$shop['id_attribut_commercant']]['orders'] = [];
                    }
                }
            }
        } else {
            foreach ($shops as $shop) {
                $commercants[$shop['id_attribut_commercant']]['infos'] = $shop;
                $commercants[$shop['id_attribut_commercant']]['orders'] = [];
            }
        }

        return $commercants;
    }

    private function filterQuery($dfrom, $dto, $orderId = -1)
    {
        $collection = $this->_collection;
        if ($orderId != -1) {
            $collection->addFieldToFilter('main_table.increment_id', ['eq' => $orderId]);
        } else {
            $collection->addAttributeToFilter('ddate', array(
                'from' => $dfrom,
                'to' => $dto,
            ));
        }
        $this->_collection = $collection;
    }

    //To group order items by order (only 1 item per order, just to get order list)
    private function aggregateByOrders()
    {
        $collection = $this->_collection;
        $collection->getSelect()->group('sales_flat_order.entity_id');
        $this->_collection = $collection;
    }

    private function OrderHeaderParsing($order)
    {
        $orderHeader = [];

        $orderHeader['increment_id'] = $order->getData('increment_id');
        $orderHeader['store'] = Mage::app()->getStore($order->getData('store_id'))->getName();
        $orderHeader['store_id'] = Mage::app()->getStore($order->getData('store_id'))->getId();
        $orderHeader['status'] = $order->getData('status');
        $orderHeader['customer_id'] = $order->getData('customer_id');
        $orderHeader['first_name'] = $order->getData('o_firstname');
        $orderHeader['last_name'] = $order->getData('o_lastname');
        $orderHeader['street'] = $order->getData('o_street');
        $orderHeader['zipcode'] = $order->getData('o_zipcode');
        $orderHeader['city'] = $order->getData('o_city');
        $orderHeader['phone'] = $order->getData('o_telephone');
        $orderHeader['mail'] = $order->getData('customer_email');
        $orderHeader['codeporte1'] = $order->getData('codeporte1');
        $orderHeader['codeporte2'] = $order->getData('codeporte2');
        $orderHeader['batiment'] = $order->getData('batiment');
        $orderHeader['etage'] = $order->getData('etage');
        $orderHeader['info'] = 'Porte 1:'.$order->getData('codeporte1').' | Porte 2: '.$order->getData('codeporte2').' | '.$order->getData('infoscomplementaires');
        $orderHeader['contact'] = $order->getData('contactvoisin');
        $orderHeader['contact_phone'] = $order->getData('telcontact');
        $orderHeader['order_date'] = $order->getData('created_at');
        $orderHeader['delivery_date'] = $order->getData('ddate');
        $orderHeader['delivery_time'] = $order->getData('dtime');
        $orderHeader['equivalent_replacement'] = $order->getData('produit_equivalent');
        $orderHeader['Total quantite'] = 0;
        $orderHeader['Total prix'] = 0.0;
        $orderHeader['products'] = [];

        return $orderHeader;
    }

    private function ProductParsing($product)
    {
        $prod_data = [
            'nom' => $product->getName(),
                'prix_kilo' => $product->getPrixKiloSite(),
                'quantite' => round($product->getQtyOrdered(), 0),
                'description' => $product->getShortDescription(),
                'prix_unitaire' => round($product->getPriceInclTax(), 2),
                'prix_total' => round($product->getRowTotalInclTax(), 2),
                'commercant_id' => $product->getCommercant(),
            ];
        $prod_data['comment'] = '';
        if (array_key_exists('options', $product->getProductOptions())) {
            $options = $product->getProductOptions()['options'];
            foreach ($options as $option) {
                $prod_data['comment'] .= $option['label'].': '.$option['value'].' | ';
            }
        }
        $prod_data['comment'] .= $product->getData('item_comment');

        return $prod_data;
    }

    public function checkItem($commercant)
    {
        return in_array($commercant, $this->_shopsids);
    }

    public function getShopsOrdersAction($dfrom = null, $dto = null, $getByStore = true)
    {
        if (!isset($dfrom)) {
            $dfrom = date('Y-m-d');
        }
        if (!isset($dto)) {
            $dto = $dfrom;
        }
        $dfrom .=  ' 00:00:00';
        $dto .=  ' 00:00:00';

        $shops = $this->getShops($getByStore);
        $this->filterQuery($dfrom, $dto);

        $items = $this->_collection;
        foreach ($items as $item) {
            if ($this->checkItem($item->getCommercant())) {
                $orderHeader = $this->OrderHeaderParsing($item);

                if ($getByStore) {
                    if (!isset($shops[$orderHeader['store_id']][$item['commercant']]['orders'][$orderHeader['increment_id']])) {
                        $shops[$orderHeader['store_id']][$item['commercant']]['orders'][$orderHeader['increment_id']] = $orderHeader;
                    }
                    $item = $item->toArray(
                        array('commercant', 'item_id', 'qty_ordered', 'row_total_incl_tax', 'produit_fragile', 'name', 'short_description', 'price_incl_tax', 'prix_kilo_site', 'item_comment','product_options','product_type','produit_fragile')
                    );
                    $shops[$orderHeader['store_id']][$item['commercant']]['orders'][$orderHeader['increment_id']]['products'][] = $item;
                    $shops[$orderHeader['store_id']][$item['commercant']]['orders'][$orderHeader['increment_id']]['Total quantite'] += round($item['qty_ordered'], 0);
                    $shops[$orderHeader['store_id']][$item['commercant']]['orders'][$orderHeader['increment_id']]['Total prix'] += round($item['row_total_incl_tax'], 2);
                } else {
                    if (!isset($shops[$item['commercant']]['orders'][$orderHeader['increment_id']])) {
                        $shops[$item['commercant']]['orders'][$orderHeader['increment_id']] = $orderHeader;
                    }
                    $item = $item->toArray(
                        array('commercant', 'item_id', 'qty_ordered', 'row_total_incl_tax', 'produit_fragile', 'name', 'short_description', 'price_incl_tax', 'prix_kilo_site', 'item_comment','product_options','product_type','produit_fragile')
                    );
                    $options=unserialize($item['product_options']);
                    if(isset($options['options'])){
                        foreach($options['options'] as $o){
                            $item['item_options'][]=array("label"=>$o['label'],"value"=>$o['print_value']);
                        }
                    }
                    $shops[$item['commercant']]['orders'][$orderHeader['increment_id']]['products'][] = $item;
                    $shops[$item['commercant']]['orders'][$orderHeader['increment_id']]['Total quantite'] += round($item['qty_ordered'], 0);
                    $shops[$item['commercant']]['orders'][$orderHeader['increment_id']]['Total prix'] += round($item['row_total_incl_tax'], 2);
                }
            } else {
                $error[] = [
                    'increment_id' => $item->getIncrementId(),
                    'sku' => $item->getSku(),
                    'id_attribut_commercant' => $item->getCommercant(),
                ];
                continue;
            }
        }

        if (isset($error)) {
            Mage::getModel('apdcadmin/mail')->warnErrorItemCommercant($error);
        }

        return $shops;
    }
}
