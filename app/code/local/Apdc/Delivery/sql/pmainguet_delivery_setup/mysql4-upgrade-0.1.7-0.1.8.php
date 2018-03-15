<?php

// THIS SCRIPT IS USED TO FILL "REFUND_PRICEVARIATION" TABLE
// RUN ONLY ONCE !!

set_time_limit(0);

$orders = Mage::getModel('sales/order')->getCollection();
$orders->getSelect()->join('mwddate_store','main_table.entity_id=mwddate_store.sales_order_id', array('mwddate_store.ddate_id',));
$orders->getSelect()->join('mwddate','mwddate_store.ddate_id=mwddate.ddate_id',array('ddate' => 'mwddate.ddate','dtime' => 'mwddate.dtime',));

$tmp = [];
$cpt = 0;
// 1 - Create huge array tmp[]. Contains every orders of the database
foreach ($orders as $order) {
    $products = Mage::getModel('sales/order_item')->getCollection();
    $products->addFieldToFilter('order_id', ['eq' => $order->getEntityId()]);
    $products->getSelect()->joinLeft(['refund' => Mage::getSingleton('core/resource')->getTableName('pmainguet_delivery/refund_items')], 'refund.order_item_id = main_table.item_id', [
        'refund_diff_commercant' => 'refund.diffprixcommercant',
    ]);
    $products->getSelect()->joinLeft(['shop' => Mage::getSingleton('core/resource')->getTableName('apdc_shop')], 'shop.id_attribut_commercant = main_table.commercant', [
        'merchant_id'   => 'shop.id_shop','merchant_name' => 'shop.name',
    ]);

    foreach ($products as $product) {
        $tmp[$cpt] = [
            'order_id'                  => $order->getIncrementId(),
            'order_date'                => $order->getCreatedAt(),
            'delivery_date'             => $order->getDdate(),  
            'merchant_id'               => $product->getData('merchant_id'),
            'merchant_name'             => $product->getData('merchant_name'),
            'refund_diff_commercant'    => (float) $product->getData('refund_diff_commercant'),
        ];

        $cpt++;
    }
}

unset($orders);

$ordered_tmp = [];
// 2 - Create smaller array ordered_tmp[]. Ordered by order ID & merchant ID
foreach ($tmp as $t) {
    $ordered_tmp[$t['order_id']][$t['merchant_id']]['order_id']                 = $t['order_id'];
    $ordered_tmp[$t['order_id']][$t['merchant_id']]['order_date']               = $t['order_date'];
    $ordered_tmp[$t['order_id']][$t['merchant_id']]['delivery_date']            = $t['delivery_date'];
    $ordered_tmp[$t['order_id']][$t['merchant_id']]['merchant_id']              = $t['merchant_id'];
    $ordered_tmp[$t['order_id']][$t['merchant_id']]['merchant_name']            = $t['merchant_name'];
    // PLZ DONT TRY THIS AT HOME
    @$ordered_tmp[$t['order_id']][$t['merchant_id']]['refund_diff_commercant'] += $t['refund_diff_commercant'];
    $ordered_tmp[$t['order_id']][$t['merchant_id']]['excess']                   = 0;
    $ordered_tmp[$t['order_id']][$t['merchant_id']]['lack']                     = 0;
}

unset($tmp);

$results = [];
$cp = 0;
// 3 - Create results array[]. Will be used to fill refund_pricevariation table
foreach ($ordered_tmp as $order_id => $order) {
    foreach ($order as $merchant_id => $o) {
        if ((float) $o['refund_diff_commercant'] < 0) { $o['excess'] += (float) $o['refund_diff_commercant']; }
        if ((float) $o['refund_diff_commercant'] > 0) { $o['lack'] += (float) $o['refund_diff_commercant']; }
        $results[$cp] = [
            'order_id'              => $o['order_id'],
            'merchant'              => $o['merchant_name'],
            'merchant_id'           => $o['merchant_id'],
            'merchant_excess'       => $o['excess'],
            'merchant_lack'         => $o['lack'],
            'order_date'            => $o['order_date'],
            'delivery_date'         => $o['delivery_date'],
        ];

        $cp++;
    }
}

// 4 - Fill refund_pricevariation only if empty table. Prevents from spamming mysqlupgrade

$model = Mage::getModel('pmainguet_delivery/refund_pricevariation');

if ($model->getCollection()->getSize() === 0) {
    try {
        foreach ($results as $r) {
            $model->setData([
                'order_id'          => $r['order_id'],
                'merchant'          => $r['merchant'],
                'merchant_id'       => $r['merchant_id'],
                'merchant_excess'   => $r['merchant_excess'],
                'merchant_lack'     => $r['merchant_lack'],
                'order_date'        => $r['order_date'],
                'delivery_date'     => $r['delivery_date'],
            ])->save();
        }
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}