<?php

// Move all "merchant_bill_comment" from "indi_billing_summary" table
// Into "indi_commenthistory" table

set_time_limit(0);

$bills = Mage::getModel('pmainguet_delivery/indi_billingsummary')->getCollection();

$tmp = [];

foreach ($bills as $bill) {
	if (!empty($bill->getData('merchant_bill_comment'))) {
		array_push($tmp, [
			'created_at'			=> $bill->getData('created_at'),
			'updated_at'			=> '',
			'author'				=> 'Au Pas De Courses',
			'comment_type'			=> 'merchant_bill_not_visible',
			'comment_text'			=> $bill->getData('merchant_bill_comment'),
			'order_id'				=> 0,
			'merchant_id'			=> $bill->getData('id_attribut_commercant'),
			'associated_order_id'	=> 0,
		]);
	}
}

$model = Mage::getModel('pmainguet_delivery/indi_commenthistory');

try {
	foreach ($tmp as $t) {
		$model->setData([
			'created_at'			=> $t['created_at'],
			'updated_at'			=> $t['updated_at'],
			'author'				=> $t['author'],
			'comment_type'			=> $t['comment_type'],
			'comment_text'			=> $t['comment_text'],
			'order_id'				=> $t['order_id'],
			'merchant_id'			=> $t['merchant_id'],
			'associated_order_id'	=> $t['associated_order_id'],
		])->save();
	}
} catch (\Exception $e) {
	echo $e->getMessage();
}