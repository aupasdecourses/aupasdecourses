<?php

//Ajout des champs mis à la main dans la table amasty_orderattach_field
$columns = [
    'upload' => 'Check upload step in Indi',
    'input' => 'Check input step in Indi',
    'digest' => 'Check digest step in Indi',
    'refund' => 'Check refund/final step in Indi',
    'refund_shipping' => 'Check if shipping is refunded in Indi',
];

foreach ($columns as $code => $label) {
    $data = [
        'code' => $code,
        'label' => $label,
        'type' => 'text',
        'show_on_grid' => 0,
        'is_enabled' => 1,
        'customer_visibility' => 'no',
        'status_backend' => 'all',
        'apply_to_each_product' => 0,
    ];

    Mage::getModel('amorderattach/field')->setData($data)->save();
    Mage::unregister('amorderattach_additional_data');
}

//Ajout des champs mis à la main dans la table amasty_orderattach_field
$entries = Mage::getModel('amorderattach/order_field')->getCollection();

foreach ($entries as $entry) {
    $merge = $entry->getData('commentaires_commande');
    $old = $entry->getData('commentaires_ticket');
    if ($old == '') {
        $new = $merge;
    } else {
        $new = $old.' / '.$merge;
    }

    $entry->setCommentairesTicket($new);
    $entry->save();
}
