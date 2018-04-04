<?php

// THIS SCRIPT IS USED TO FILL "INDI_COMMENTHISTORY" TABLE
// RUN ONLY ONCE

set_time_limit(0);

$orders = Mage::getModel('sales/order')->getCollection();
$orders->addFieldToFilter('main_table.status', ['nin' => ['pending_payment', 'payment_review', 'holded', 'canceled']]);
        
$orders->getSelect()->joinLeft(['amasty_order_field' => 'amasty_amorderattach_order_field'], 'amasty_order_field.order_id = main_table.entity_id', [
    'commentaires_commercant' => 'amasty_order_field.commentaires_commercant',
    'commentaires_client' => 'amasty_order_field.commentaires_client',       
]);
        
$orders->addFieldToFilter(['commentaires_commercant', 'commentaires_client'], [['neq' => ''], ['neq' => '']]);

$tmp = [];
// 1 - Create huge array[] containing all comments of the database
foreach ($orders as $order) {
    array_push($tmp, [
        'created_at'                => $order->getCreatedAt(),
        'commentaires_commercant'   => $order->getData('commentaires_commercant'),
        'commentaires_client'       => $order->getData('commentaires_client'),
        'order_id'                  => $order->getIncrementId(),
    ]);
}

$res = [];
// 2 - Clean up & store in res[]
foreach ($tmp as $t) {
    $res[] = [
        'created_at'    => $t['created_at'],
        'updated_at'    => '',
        'author'        => 'Au Pas De Courses',
        'comment_type'  => 'mixed_non_visible',
        'comment_text'  => $t['commentaires_commercant'] . " <br/> " . $t['commentaires_client'],
        'order_id'      => $t['order_id'],
        'merchant_id'   => -1,
    ];
}

// 3 - Fill indi_commenthistory only if empty table. Prevents from spamming mysqlupgrade
$model = Mage::getModel('pmainguet_delivery/indi_commenthistory');

if ($model->getCollection()->getSize() === 0) {
    try {
        foreach ($res as $r) {
            $model->setData([
                'created_at'    => $r['created_at'],
                'updated_at'    => $r['updated_at'],
                'author'        => $r['author'],
                'comment_type'  => $r['comment_type'],
                'comment_text'  => $r['comment_text'],
                'order_id'      => $r['order_id'],
                'merchant_id'   => $r['merchant_id'],
            ])->save();
        }
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}

// 4 - Also add comment_types
$type_model = Mage::getModel('pmainguet_delivery/indi_commenttype');

$types = [
    0 => ['type' => 'merchant_non_visible', 'label' => 'Commentaire commercant interne'],
    1 => ['type' => 'customer_non_visible', 'label' => 'Commentaire client interne'],
    2 => ['type' => 'mixed_non_visible',    'label' => 'Commentaire mix interne'],
];

if ($type_model->getCollection()->getSize() === 0) {
    try {
        foreach ($types as $t) {
            $type_model->setData([
                'type'      => $t['type'],
                'label'     => $t['label'],
            ])->save();
        }
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}