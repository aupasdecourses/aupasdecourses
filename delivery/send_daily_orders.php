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
/*
$lib_path = realpath(dirname(__FILE__) . '../lib');
set_include_path("$libpath:".get_include_path());

require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance();
 */
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

function getOrders(&$commercants, $date = TODAY_DATE) {
	$orderObjs = orders_fortheday($date);
	foreach ($orderObjs as $orderObj) {
		$order['id'] = $orderObj->getData('increment_id');
		$order['first_name'] = $orderObj->getShippingAddress()->getData('firstname');
		$order['last_name'] = $orderObj->getShippingAddress()->getData('lastname');
		$order['order_date'] = $orderObj->getData('created_at');
		$order['delivery_date'] = $orderObj->getData('ddate');
		$order['delivery_time'] = $orderObj->getData('dtime');
		$order['equivalent_replacement'] = $orderObj->getData('produit_equivalent');
		$order['Total quantité'] = 0;
		$order['Total prix'] = 0.0;
		$order['products'] = [];

		$oproducts = $orderObj->getAllItems();
		foreach ($oproducts as $oprod) {
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
			$prod_data['comment']	.= $oprod->getData('item_comment') . PHP_EOL;
			if (!isset($commercants[$oprod->getCommercant()]['orders'][$order['id']])) {
				$commercants[$oprod->getCommercant()]['orders'][$order['id']] = $order;
			}
			$commercants[$oprod->getCommercant()]['orders'][$order['id']]['products'][] = $prod_data;
			$commercants[$oprod->getCommercant()]['orders'][$order['id']]['Total quantité'] += $prod_data['quantite'];
			$commercants[$oprod->getCommercant()]['orders'][$order['id']]['Total prix'] += $prod_data['prix_total'];
		}
	}
}

$commercants = getCommercant();

$orders_date = date('Y-m-d', strtotime('2016-06-21'));

getOrders($commercants, $orders_date);

print_r($commercants);

class generatePdf {
	private			$_pdf; 

	private			$_font;
	private			$_font_bold;
	private			$_font_italic;

	private static	$_width = 842;
	private	static	$_height = 595;
	private			$_format;

	private			$_summary = [];
	private			$_summary_id = 0;

	private			$_summary_columnWidth;
	private			$_summary_columnOffset;
	private			$_summary_startColumnOffset = 0;
	private			$_summary_maxColumnOffset = 2;

	private			$_summary_lineHeight = 20;
	private			$_summary_startLineOffset = 9;
	private			$_summary_maxLineOffset;
	private			$_summary_lineOffset;

	private			$_order_template_first;
	private			$_order_template_second;
	private			$_orders = [];
	private			$_orders_id = -1;
	private			$_orders_lineHeight = 15;
	private			$_orders_startLineOffset_first = 13;
	private			$_orders_startLineOffset_second = 6;
	private			$_orders_maxLineOffset;
	private			$_orders_lineOffset;

	private			$_orders_count = 0;

	private			$_margin_horizontal = 50;
	private			$_margin_vertical = 50;

	private			$_finalized = false;

	public function __construct($commercant, $orders_date) {
		$this->_pdf = new Zend_Pdf();

		// Algorithm constants set ==>>
		$this->_font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$this->_font_bold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
		$this->_font_italic = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC);

		$this->_summary_columnWidth = (static::$_width - ($this->_margin_horizontal * 2)) / number_format($this->_summary_maxColumnOffset + 1, 2);
		$this->_summary_columnOffset = $this->_summary_startColumnOffset;
		$this->_summary_lineOffset = $this->_summary_startLineOffset;
		$this->_summary_maxLineOffset = (static::$_height / $this->_summary_lineHeight) - ($this->_margin_vertical / $this->_summary_lineHeight);

		$this->_orders_maxLineOffset = (static::$_height / $this->_orders_lineHeight) - ($this->_margin_vertical / $this->_orders_lineHeight);

		$this->_format = static::$_width . ':' . static::$_height . ':';
		// <<==

		// create summary first page and draw header ==>>
		$this->_summary[0] = $this->_pdf->newPage($this->_format);
		$this->_summary[0]->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
		$this->_summary[0]->setFont($this->_font, 16);
		$this->_summary[0]->drawText('Commandes AU PAS DE COURSES', $this->_margin_horizontal, static::$_height - ($this->_summary_lineHeight * 5));
		$this->_summary[0]->setFont($this->_font, 12);
		$this->_summary[0]->drawText("A {$commercant['name']} pour le {$orders_date}", $this->_margin_horizontal, static::$_height - ($this->_summary_lineHeight * 6));
		// <<==

		// create orders template page ==>>
		$this->_orders_template_first = $this->_pdf->newPage($this->_format);	

		$this->_orders_template_first->setFont($this->_font, 8);
		$this->_orders_template_first->drawText("Commande Au Pas De Courses - {$commercant['name']} pour le {$orders_date}", $this->_margin_horizontal, static::$_height - $this->_margin_vertical + 10);
		$this->_orders_template_first->setLineWidth(0.5);
		$this->_orders_template_first->drawLine($this->_margin_horizontal, static::$_height - $this->_margin_vertical, static::$_width - $this->_margin_horizontal, static::$_height - $this->_margin_vertical);

		$this->_orders_template_first->drawLine($this->_margin_horizontal, $this->_margin_vertical, static::$_width - $this->_margin_horizontal, $this->_margin_vertical);
		$this->_orders_template_first->setFont($this->_font, 8);
		$this->_orders_template_first->drawText("Genere le: " . date('r'), $this->_margin_horizontal, $this->_margin_vertical - 10);

		$this->_orders_template_second = clone $this->_orders_template_first;

		// <<==
	}

	private function _finalizePdf() {
		$page_id = 1;
		$page_count = count($this->_summary) + count($this->_orders);
		$this->_summary[0]->drawText('Nombre de commandes: ' . $this->_orders_count, $this->_margin_horizontal, static::$_height - ($this->_summary_lineHeight * 8));
		foreach ($this->_summary as $page) {
			$page->setFont($this->_font, 8);
			$page->drawText("page {$page_id}/{$page_count}", static::$_width - ($this->_margin_horizontal * 2), $this->_margin_vertical - 10);
			$page_id++;
			$this->_pdf->pages[] = $page;
		}
		foreach ($this->_orders as $page) {
			$page->setFont($this->_font, 8);
			$page->drawText("page {$page_id}/{$page_count}", static::$_width - ($this->_margin_horizontal * 2), $this->_margin_vertical - 10);
			$page_id++;
			$this->_pdf->pages[] = $page;
		}
	}

	private function addOrderToSummary($order) {
		if (!($this->_summary_lineOffset < $this->_summary_maxLineOffset)) { // add column to current summary
			if ($this->_summary_columnOffset < $this->_summary_maxColumnOffset) {
				$this->_summary_columnOffset++;
			} else { // add page to summary
				$this->_summary_startLineOffset = 5;
				$this->_summary_columnOffset = $this->_summary_startColumnOffset;
				$this->_summary_id++;
				$this->_summary[$this->_summary_id] = $this->_pdf->newPage($this->_format);
				$this->_summary[$this->_summary_id]->setFont($this->_font, 12);
			}
			// reinitialyse line offset to start position
			$this->_summary_lineOffset = $this->_summary_startLineOffset;
		}
		// add current order to summary
		$this->_summary[$this->_summary_id]->drawText("Commande n°" . ++$this->_orders_count . ": {$order['id']}", $this->_margin_horizontal + ($this->_summary_columnWidth * $this->_summary_columnOffset), static::$_height - ($this->_summary_lineHeight * $this->_summary_lineOffset++));
	}

	private static function _orders_table_header($page) {
	}

	public function addOrder($order) {
		$this->addOrderToSummary($order);
		$this->_orders_id++;
		$this->_orders[$this->_orders_id] = clone $this->_orders_template_first;

		$this->_orders[$this->_orders_id]->setFont($this->_font, 16);
		$this->_orders[$this->_orders_id]->drawText("Commande n°  {$order['id']}", $this->_margin_horizontal, static::$_height - ($this->_orders_lineHeight * 5));
		$this->_orders[$this->_orders_id]->setFont($this->_font, 12);
		$this->_orders[$this->_orders_id]->drawText("Nom du client: {$order['first_name']} {$order['last_name']}", $this->_margin_horizontal, static::$_height - ($this->_orders_lineHeight * 6));
		$this->_orders[$this->_orders_id]->drawText("Date de Prise de Commande: {$order['order_date']}", $this->_margin_horizontal, static::$_height - ($this->_orders_lineHeight * 7));
		$this->_orders[$this->_orders_id]->drawText("Date de Livraison: {$order['delivery_date']}", $this->_margin_horizontal, static::$_height - ($this->_orders_lineHeight * 8));
		$this->_orders[$this->_orders_id]->drawText("Creneau de Livraison: {$order['delivery_time']}", $this->_margin_horizontal, static::$_height - ($this->_orders_lineHeight * 9));
		$this->_orders[$this->_orders_id]->drawText("Remplacement equivalent: " . (($order['equivalent_replacement']) ? "oui" : "non"), $this->_margin_horizontal, static::$_height - ($this->_orders_lineHeight * 10));
		$this->_orders[$this->_orders_id]->drawText("Liste des produits commandes: ", $this->_margin_horizontal, static::$_height - ($this->_orders_lineHeight * 11));

		$this->_orders_lineOffset = $this->_orders_startLineOffset_first;
		$this->_orders[$this->_orders_id]->drawText("==================", $this->_margin_horizontal, static::$_height - ($this->_orders_lineHeight * $this->_orders_lineOffset++));
		// create table
	}

	public function save($filename) {
		if (!$this->finalized) {
			$this->_finalizePdf();
		}
		$this->_pdf->save($filename);
	}

	public function send($from, $to) {
		if (!$this->finalized)
			$this->_finalizePdf();
	}
}

$test = new generatePdf($commercants['7'], $orders_date);
foreach ($commercants['7']['orders'] as $order) {
	$test->addOrder($order);
}
$test->save('test.pdf');
