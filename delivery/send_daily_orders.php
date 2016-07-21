#!/usr/bin/php

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('CHEMIN_MODELE', dirname(__FILE__).'/models/');
define('CHEMIN_MAGE', dirname(__FILE__).'/../');
define('AMASTY_MW_DATE',date("Y-m-d", mktime(0, 0, 0, 1, 20, 2016)));
define('TODAY_DATE', date("Y-m-d"));

$GLOBALS['REFUND_ITEMS_INFO_ID_LIMIT']=2016000249;

include CHEMIN_MODELE.'magento.php';
connect_magento();

$lib_path = realpath(dirname(__FILE__) . '../lib');
set_include_path("$libpath:".get_include_path());

require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance();

function getCommercant() {
	$commercants = [];
    $categories = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*')->addIsActiveFilter()->addFieldToFilter('estcom_commercant', ['neq' => NULL]);
	foreach ($categories as $category) {
		$commercants[$category->getData('att_com_id')] = [
			'name'		=>	$category->getName(),
			'addr'		=>	$category->getAdresseCommercant(),
			'phone'		=>	$category->getTelephone(),
			'mobile'	=>	$category->getPortable(),
			'mail'		=>	$category->getMailContact()
		];
	}
	return ($commercants);
}

function getOrders($date = TODAY_DATE) {
//	$orders = Mage::getModel('mwddate_store')->getCollection();
//	$orders->getSelect()->join('mwddate', 'mwddate_store.ddate_id=mwddate.ddate_id', ['ddate' => 'mwddate.ddate', 'dtime' => 'mwddate.dtime']);
//	$orders->addFilterToMap('ddate', 'mwddate.ddate');
//	$orders->addFilterToMap('dtime', 'mwddate.dtimetext');
//	$order->addFieldToFilter('order_status', ['nin' => ['pending_payment','payment_review','holded','closed','canceled']]);
//	$order->addFieldToFilter('ddate', ['eq' => $date]);
//	$item = Mage::getModel('sales/order_item')->getCollection();
	$item = Mage::getModel('sales/order_item')->getCollection();
	
	print_r($item);
}

getOrders();

//foreach ($commercants as $commercant_id => $commercant_info) {
//	$commercants[$commercant_id]['orders'] = getOrders($commercant_id, $orders_date);
//} 

/*

function getOrders($commercants, $date = TODAY_DATE) {
	$orderObjs = orders_fortheday($date);
	print_r($orderObjs);
	$orders = [];
	foreach ($orderObjs as $orderObj) {
		$data['id'] = $orderObj->getData('increment_id');
		$data['first_name'] = $orderObj->getShippingAddress()->getData('firstname');
		$data['last_name'] = $orderObj->getShippingAddress()->getData('lastname');
		$data['order_date'] = $orderObj->getData('created_at');
		$data['delivery_date'] = (TODAY_DATE <= AMASTY_MW_DATE) ? $orderObj->getData('delivery_date') : $orderObj->getData('ddate');
		$data['delivery_time'] = (TODAY_DATE <= AMASTY_MW_DATE) ? $orderObj->getData('delivery_time') : $orderObj->getData('dtime');
		$data['equivalent_replacement'] = $orderObj->getData('produit_equivalent');
		$data['Total quantité'] = 0;
		$data['Total prix'] = 0.0;
		$data['Order products'] = [];

		$oproducts = $orderObj->getAllItems();
		foreach ($oproducts as $oprod) {
			if ($oprod->getCommercant() == $id) {
				$sprod = Mage::getModel('catalog/product')->load($oprod->getProduct()->getId());
				$prod_data = [
					'title'			=>	$oprod->getName(),
					'prix_kilo'		=>	($oprod->getPrixKiloSite() <> "") ? $oprod->getPrixKiloSite() : $sprod->getPrixKiloSite(),
					'quantite'		=>	round($oprod->getQtyOrdered(), 0),
					'description'	=>	($oprod->getShortDescription() <> "") ? $oprod->getShortDescription() : $sprod->getShortDescription(),
					'prix_unitaire'	=>	round($oprod->getPriceInclTax(),2),
					'prix_total'	=>	round($oprod->getRowTotalInclTax(),2)
					];
				$prod_data['comment'] = '';
				$opts = $oprod->getProductOptions()['options'];
				foreach ($opts as $opt) {
					$prod_data['comment'] .= $opt['label'] . ' ' . $opt['value'] . PHP_EOL;
				}
				$prod_data['comment']		.= $oprod->getData('item_comment') . PHP_EOL;
				$data['Total quantité']		+= $prod_data['quantite'];
				$data['Total prix']			+= $prod_data['prix_total'];
				$data['Order products'][]	= $prod_data;
			}
		}
		$orders[] = $data;
	}
	return ($orders);
}

$commercants = getCommercant();

$orders_date = date('Y-m-d', strtotime('2016-06-21'));

getOrders($commercants, $orders_date);

function generate_Pdf($commercant, $orders_date) {
	$lineHeight_summary = 20;
	$lineOffset_summary = 5;
	$lineHeight_order = 15;
	$lineOffset_order = 5;
	$alignLeft = 50;
	
	print_r($commercant);	// Debug

	$pdf = new Zend_Pdf();

	//	Orders_Summary	==>>
	$orders_summary = $pdf->newPage(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
	$pdf->pages[] = $orders_summary;
	$width = $orders_summary->getWidth();
	$height = $orders_summary->getHeight();

	$orders_summary->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
	$orders_summary->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 16);
	$orders_summary->drawText('Commandes AU PAS DE COURSES', $alignLeft, $height - ($lineHeight_summary * $lineOffset_summary++));
	$orders_summary->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
	$orders_summary->drawText("A {$commercant['name']} pour le {$orders_date}", $alignLeft, $height - ($lineHeight_summary * $lineOffset_summary++));
	$lineOffset_summary++;
	$orders_summary->drawText('Nombre de commandes: ' . count($commercant['orders']), $alignLeft, $height - ($lineHeight_summary * $lineOffset_summary++));
	//	<<==	Orders_Summary

	$order_count = 0;
	foreach ($commercant['orders'] as $order) {
		$orders_summary->drawText("Commande n°" . ++$order_count . ": {$order['id']}", $alignLeft, $height - ($lineHeight_summary * $lineOffset_summary++));
	//	Order	==>>
		$orders_page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
		$pdf->pages[] = $orders_page;

		$orders_page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
		$orders_page->drawText("Commande Au Pas De Courses - {$commercant['name']} pour le {$orders_date}", $alignLeft, $height - 45);
		$orders_page->setLineWidth(0.5);
		$orders_page->drawLine($alignLeft, $height - 50, $width - $alignLeft, $height - 50);

		$orders_page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 16);
		$orders_page->drawText("Commande n°  {$order['id']}", $alignLeft, $height - ($lineHeight_order * $lineOffset_order++));
		$orders_page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
		$orders_page->drawText("Nom du client: {$order['first_name']} {$order['last_name']}", $alignLeft, $height - ($lineHeight_order * $lineOffset_order++));
		$orders_page->drawText("Date de Prise de Commande: {$order['order_date']}", $alignLeft, $height - ($lineHeight_order * $lineOffset_order++));
		$orders_page->drawText("Date de Livraison: {$order['delivery_date']}", $alignLeft, $height - ($lineHeight_order * $lineOffset_order++));
		$orders_page->drawText("Creneau de Livraison: {$order['delivery_time']}", $alignLeft, $height - ($lineHeight_order * $lineOffset_order++));
		$orders_page->drawText("Remplacement equivalent: " . (($order['equivalent_replacement']) ? "oui" : "non"), $alignLeft, $height - ($lineHeight_order * $lineOffset_order++));
		$orders_page->drawText("Liste des produits commandes: ", $alignLeft, $height - ($lineHeight_order * $lineOffset_order++));

		//	<<===

		$orders_page->drawLine($alignLeft, 50, $width - $alignLeft, 50);
		$orders_page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
		$orders_page->drawText("Genere le: " . date('r'), $alignLeft, 40);
	//	<<==	Order
	}
	return ($pdf);
}

*/

//generate_Pdf($commercants['7'], $orders_date)->save('test.pdf');

