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

//$orders = orders_fortheday(TODAY_DATE);
$orderObjs = orders_fortheday(date('Y-m-d', strtotime('2016-06-17')), 7);

$orders = [];
foreach ($orderObjs as $orderObj) {
	$data['Commande n°'] = $orderObj->getData('increment_id');
	$data['Prenom'] = $orderObj->getShippingAddress()->getData('firstname');
	$data['Nom'] = $orderObj->getShippingAddress()->getData('lastname');
	$data['Date prise de commande'] = $orderObj->getData('created_at');
	$data['Date de livraison'] = (TODAY_DATE <= AMASTY_MW_DATE) ? $orderObj->getData('delivery_date') : $orderObj->getData('ddate');
	$data['Heure de livraison'] = (TODAY_DATE <= AMASTY_MW_DATE) ? $orderObj->getData('delivery_time') : $orderObj->getData('dtime');
	$data['Remplacement pour produit équivalent possible'] = $orderObj->getData('produit_equivalent');
	$data['Total quantité'] = 0;
	$data['Total prix'] = 0.0;
	$data['Order products'] = [];
	
	$oproducts = $orderObj->getAllItems();
	foreach ($oproducts as $oprod) {
		$sprod = Mage::getModel('catalog/product')->load($oprod->getProduct()->getId());
		$prod_data = [
			'title'				=>	$oprod->getName(),
			'prix_kilo'			=>	($oprod->getPrixKiloSite() <> "") ? $oprod->getPrixKiloSite() : $sprod->getPrixKiloSite(),
			'quantite'			=>	round($oprod->getQtyOrdered(),0),
			'description'		=>	($oprod->getShortDescription() <> "") ? $oprod->getShortDescription() : $sprod->getShortDescription(),
			'prix_unitaire'		=>	round($oprod->getPriceInclTax(),2),
			'prix_total'		=>	round($oprod->getRowTotalInclTax(),2)
		];
		$prod_data['comment']	=	'';
		$opts = $oprod->getProductOptions()['options'];
		foreach ($opts as $opt) {
			$prod_data['comment']	.=	$opt['label'] . ' ' . $opt['value'] . PHP_EOL;
		}
		$prod_data['comment']	.= $oprod->getData('item_comment') . PHP_EOL;
		$data['Total quantité'] +=	$prod_data['quantite'];
		$data['Total prix']		+=	$prod_data['prix_total'];
		$data['Order products'][] = $prod_data;
	}
	$orders[] = $data;
}

print_r($orders);
