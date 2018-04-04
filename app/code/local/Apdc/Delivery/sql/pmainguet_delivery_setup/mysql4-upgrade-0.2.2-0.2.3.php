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

$res = [];
// 1 - Create huge array[] containing all comments of the database
foreach ($orders as $order) {
    array_push($res, [
        'created_at'                => $order->getCreatedAt(),
        'commentaires_commercant'   => $order->getData('commentaires_commercant'),
        'commentaires_client'       => $order->getData('commentaires_client'),
        'order_id'                  => $order->getIncrementId(),
    ]);
}

// 2 - Fix when comment merchant AND customer for the same order
foreach ($res as $r) {
    if (!empty($r['commentaires_commercant']) && (!empty($r['commentaires_client']))) {
        $tmp[] = [
            'created_at'    => $r['created_at'],
            'updated_at'    => '',
            'author'        => 'Au Pas De Courses',
            'comment_type'  => 'type_comment_commercant_non_visible',
            'comment_text'  => $r['commentaires_commercant'],
            'order_id'      => $r['order_id'],
            'merchant_id'   => -1,
        ];

        $res[] = [
            'created_at'    => $r['created_at'],
            'updated_at'    => '',
            'author'        => 'Au Pas De Courses',
            'comment_type'  => 'type_comment_client_non_visible',
            'comment_text'  => $r['commentaires_client'],
            'order_id'      => $r['order_id'],
            'merchant_id'   => -1,
        ];
    }
}

foreach ($res as $k => &$v) {
    if (!empty($v['commentaires_commercant']) && (!empty($v['commentaires_client']))) {
        unset($res[$k]);
    }
}

// 3 - Clean up
foreach ($res as &$r) {
    if (count($r) == 4) {
        $r['updated_at']    = '';
        $r['author']        = 'Au Pas De Courses';
        $r['merchant_id']   = -1;
                
        if (empty($r['commentaires_client'])) {
            $r['comment_type'] = 'type_comment_commercant_non_visible';
            $r['comment_text'] = $r['commentaires_commercant'];
        }
                
        if (empty($r['commentaires_commercant'])) {
            $r['comment_type'] = 'type_comment_client_non_visible';
            $r['comment_text'] = $r['commentaires_client'];
        }

        unset($r['commentaires_commercant'], $r['commentaires_client']);
    }
}

// 4 - Fill indi_commenthistory only if empty table. Prevents from spamming mysqlupgrade
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