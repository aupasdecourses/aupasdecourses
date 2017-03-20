<?php

namespace Apdc\ApdcBundle\Services;

include '../../app/Mage.php';

define('TAX_SERVICE', .2);
define('FLOAT_NUMBER', 2);

class Billing
{

    public function __construct()
    {
        \Mage::app();
    }

	private function startsWith($haystack, $needle)
	{
		$length = strlen($needle);

		return substr($haystack, 0, $length) === $needle;
	}

	private function getOrderAttachments($order)
	{
		$attachments					= \Mage::getModel('amorderattach/order_field')->load($order->getId(), 'order_id');
		$commentaires_ticket			= '|*COM. TICKET*|'."\n".$attachments->getData('commentaires_ticket')."\n";
		$commentaires_interne			= '|*COM. INTERNE*|'."\n".$attachments->getData('commentaires_commande')."\n";
		$commentaires_fraislivraison	= '|*COM. FRAISLIV*|'."\n".$attachments->getData('commentaires_fraislivraison');
		$comments = $remboursement_client.$commentaires_ticket.$commentaires_interne.$commentaires_fraislivraison;

		return $comments;
	}

	private function getOrderComments($order)
	{
		$order_comments = '';
		foreach ($order->getAllStatusHistory() as $status) {
			$comment_status = $status->getData('status');
			$comment		= $status->getData('comment');
			if ($comment_status == 'processing' && $comment != null && $comment != '' && !startsWith($comment, 'Notification paiement Hipay') && !startsWith($comment, 'Le client a payé par Hipay avec succès')) {
				$order_comments .= '=> '.$comment."\n";
			}
		}

		return '|*ORDER HISTORY*|'."\n".$order_comments;
	}

	/** Récupère l'information commercant dans la table order */
	private function comid_item($item, $order)
	{
		$pid		= $item->getProductId();
		$items		= $order->getAllItems();
		$commercant = null;
		foreach ($items as $itemId => $item) {
			if ($item->getProductId() == $pid) {
				$commercant = $item->getCommercant();
			}
		}

		return $commercant;
	}

	/** Récupère l'information marge dans la table order */
	private function marge_item($item, $order)
	{
		$pid		= $item->getProductId();
		$items		= $order->getAllItems();
		$commercant = null;
		foreach ($items as $itemId => $item) {
			if ($item->getProductId() == $pid) {
				$commercant = $item->getMargeArriere();
			}
		}

		return $commercant;
	}

	private function getRefundorderdata($order, $output)
	{
		$refund_order	= \Mage::getModel('pmainguet_delivery/refund_order');
		$orders			= $refund_order->getCollection()->addFieldToFilter('order_id', array('in' => $order->getIncrementId()));
		$response = array();
		if ($output == 'comment') {
			$orderAttachment = $this->getOrderAttachments($order);
			// DOESNT WORK THERE
	//		$order_comments = $this->getOrderComments($order);
			if ((int) $order->getIncrementId() > $GLOBALS['REFUND_ITEMS_INFO_ID_LIMIT']) {
				foreach ($orders as $o) {
					$response[$o->getData('commercant')] .= $orderAttachment;
				}
			} else {
				$response = $orderAttachment;
			}
		} else {
			foreach ($orders as $o) {
				$response[$o->getData('commercant')] = $o->getData($output);
			}
		}

		return $response;
	}

	private function getRefunditemdata($item, $output)
	{
		$refund_items	= \Mage::getModel('pmainguet_delivery/refund_items');
		$item			= $refund_items->load($item->getOrderItemId(), 'order_item_id');
		$response		= $item->getData($output);

		return $response;
	}

	/** Version Delivery de getShops. */
	private function getShops($id = -1, $filter = 'none')
	{
		$return = [];
		$shops	= \Mage::getModel('apdc_commercant/shop')->getCollection();
		if ($id == -1) {
			if ($filter == 'none') {
				foreach ($shops as $shop) {
					$return[$shop->getIdAttributCommercant()] = $shop->getName();
				}
			} elseif ($filter == 'store') {
				$shops->getSelect()->join('catalog_category_entity', 'main_table.id_category=catalog_category_entity.entity_id', array('catalog_category_entity.path'));
				$shops->addFilterToMap('path', 'catalog_category_entity.path');
				foreach ($shops as $shop) {
					$storeid = explode('/', $shop->getPath())[1];
					$return[$storeid][$shop->getIdAttributCommercant()] = array(
						'name'		=> $shop->getName(),
						'adresse'	=> $shop->getStreet().' '.$shop->getPostcode().' '.$shop->getCity(),
						'telephone'	=> $shop->getPhone(),
					);
				}
			}
			arsort($return);
		} else {
			$data = $shops->addFieldToFilter('id_attribut_commercant', $id)->getFirstItem()->getData();
			$return['name']				= $data['name'];
			$return['adresse']			= $data['street'].' '.$data['postcode'].' '.$data['city'];
			$return['url_adresse']		= 'https://www.google.fr/maps/place/'.str_replace(' ', '+', $return['adresse']);
			$return['phone']			= $data['phone'];
			$return['website']			= $data['website'];
			$return['timetable']		= implode(',', $data['timetable']);
			$return['closing_periods']	= $data['closing_periods'];
			$return['delivery_days']	= 'Du Mardi au Vendredi';
			$return['mail_contact']		= \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_manager'])->getFirstItem()->getEmail();
			$return['mail_pro']			= \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_employee'])->getFirstItem()->getEmail();
			$return['mail_3']			= \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_employee_bis'])->getFirstItem()->getEmail();
		}

		return $return;
	}

	public function end_month($date)
	{
		$date = strtotime('+1 month', strtotime(str_replace('/', '-', $date)));
		$date = strtotime('-1 second', $date);
		$date = date('Y-m-d H:i:s', $date);

		return $date;
	}

	public function get_list_orderid()
	{
		$orders = \Mage::getResourceModel('sales/order_collection')
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToSelect('increment_id')
			->addAttributeToSelect('created_at')
			->setOrder('increment_id', 'asc');
		$array_orderid = array();
		foreach ($orders as $order) {
			$id					= $order->getIncrementId();
			$date				= date('d/m/Y', strtotime($order->getCreatedAt()));
			$array_orderid[$id] = $date;
		}

		return $array_orderid;
	}

	public function data_facturation_products($debut, $fin)
	{
		$data = [];
		$debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));
		$list_commercant = $this->getShops();
		$orders = \Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
			->addAttributeToFilter('status', array('eq' => \Mage_Sales_Model_Order::STATE_COMPLETE))
			->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin));

		$orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id = mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
		$orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id = mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));

		foreach ($orders as $order) {
				$ordered_items = $order->getAllItems();
				$credit_comments = $this->getRefundorderdata($order, 'comment');

			if ($order->hasInvoices()) {
				$invoices = $order->getInvoiceCollection();
			}

			foreach ($invoices as $invoice) {
				$invoiced_items = $invoice->getAllItems();
			}
			foreach ($list_commercant as $id => $com) {
				$nb_products = 0;
				$sum_items = 0;
				$sum_items_HT = 0;
				foreach ($ordered_items as $item) {
					if ($item->getCommercant() !== null) {
						if ($item->getData('commercant') == $id) {
							$nb_products += floatval($item->getQtyOrdered());
							$sum_items += floatval($item->getRowTotalInclTax());
							$sum_items_HT += floatval($item->getRowTotal());
						}
					} else {
						$product = \Mage::getModel('catalog/product')->load($item->getProduct()->getId());
						if ($product->getCategoryIds()[2] == $id) {
							$nb_products += floatval($item->getQtyOrdered());
							$sum_items += floatval($item->getRowTotalInclTax());
							$sum_items_HT += floatval($item->getRowTotal());
						}
					}
				}
			 	if ($order->hasInvoices()) {
					if ($order->hasCreditmemos()) {
						if ($order->hasCreditmemos()) {
							$creditmemos = \Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToFilter('order_id', $order->getId());

							foreach ($creditmemos as $creditmemo) {
								$credit_items = $creditmemo->getAllItems();
							}
						}
					}
					$sum_items_invoice = 0;
					$sum_items_invoice_HT = 0;
					$sum_items_credit = 0;
					$sum_items_credit_HT = 0;
					$sum_commission_HT = 0;
					foreach ($invoiced_items as $item) {
						$com_done = false;
						$commercant_id = $this->comid_item($item, $order);
						if ($commercant_id !== null) {
							if ($commercant_id == $id) {
								$sum_items_invoice += floatval($item->getRowTotalInclTax());
								$sum_items_invoice_HT += floatval($item->getRowTotal());
								$TVApercent = ($sum_items_invoice - $sum_items_invoice_HT) / $sum_items_invoice_HT;
								$marge_arriere = $this->marge_item($item, $order);
								if ($order->hasCreditmemos()) {
									foreach ($credit_items as $citem) {
										if ($item->getProductID() == $citem->getProductID()) {
											$sum_items_credit += floatval($citem->getRowTotalInclTax());
											$sum_items_credit_HT += floatval($citem->getRowTotal());
											$sum_commission_HT += (floatval($item->getRowTotal()) - floatval($citem->getRowTotal())) * floatval(str_replace(',', '.', $marge_arriere));
											$com_done = true;
										}
									}
									$creditdata = $this->getRefunditemdata($item, 'diffprixfinal');
									$sum_items_credit += floatval($creditdata);
									$sum_items_credit_HT += floatval($creditdata) / (1 + $TVApercent);
									$sum_commission_HT += (floatval($item->getRowTotal()) - floatval($creditdata) / (1 + $TVApercent)) * floatval(str_replace(',', '.', $marge_arriere));
									$sum_items_credit_TVA = $sum_items_credit_HT * $TVApercent;
									$com_done = true;
								}
								if (!$com_done) {
									$sum_commission_HT += floatval($item->getRowTotal()) * floatval(str_replace(',', '.', $marge_arriere));
								}
							}
						} else {
							$product = \Mage::getModel('catalog/product')->load($item->getProductID());
							if ($product->getCategoryIds()[2] == $id) {
								$sum_items_invoice += floatval($item->getRowTotalInclTax());
								$sum_items_invoice_HT += floatval($item->getRowTotal());
								if ($order->hasCreditmemos()) {
									foreach ($credit_items as $citem) {
										if ($item->getProductID() == $citem->getProductID()) {
											$cproduct = \Mage::getModel('catalog/product')->load($citem->getProductID());
											$sum_items_credit += floatval($citem->getRowTotalInclTax());
											$sum_items_credit_HT += floatval($citem->getRowTotal());
											$sum_commission_HT += (floatval($item->getRowTotal()) - floatval($citem->getRowTotal())) * floatval(str_replace(',', '.', $product->getData('marge_arriere')));
											$com_done = true;
										}
									}
								}
								if (!$com_done) {
									$sum_commission_HT += floatval($item->getRowTotal()) * floatval(str_replace(',', '.', $product->getData('marge_arriere')));
								}
							}
						}
					}

					if ($sum_items != 0 || ($sum_items_invoice != 0 && $order->hasInvoices())) {
						$date_creation = date('d/m/Y', strtotime($order->getCreatedAt()));
						if (!is_null($order->getDdate())) {
							$date_livraison = date('d/m/Y', strtotime($order->getDdate()));
						} else {
							$date_livraison = 'Non Dispo';
						}
						if ($parentid == null) {
							$parentid = $order->getIncrementId();
						}
						$incrementid = $order->getIncrementId();
						$nom_client = $order->getCustomerName();
						$com;
						$sum_items = round($sum_items, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						$sum_items_HT = round($sum_items_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						$sum_items_TVA = round($sum_items - $sum_items_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						if ($order->hasInvoices()) {
							$sum_items_invoice = round($sum_items_invoice, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_items_invoice_HT = round($sum_items_invoice_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_items_invoice_TVA = round($sum_items_invoice - $sum_items_invoice_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						} else {
							$sum_items_invoice = $sum_items_invoice_HT = $sum_items_invoice_TVA = 0;
						}
						if ($order->hasCreditMemos()) {
							$sum_items_credit = round($sum_items_credit, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_items_credit_HT = round($sum_items_credit_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_items_credit_TVA = round($sum_items_credit - $sum_items_credit_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						} else {
							$sum_items_credit = $sum_items_credit_HT = $sum_items_credit_TVA = 0;
						}
						if ($order->hasInvoices()) {
							$sum_commission = round($sum_commission_HT * (1 + TAX_SERVICE), FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_commission_HT = round($sum_commission_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_commission_TVA = round($sum_commission_HT * TAX_SERVICE, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						} else {
							$sum_commission = $sum_commission_HT = $sum_commission_TVA = 0;
						}
						if ($order->hasInvoices()) {
							$sum_versement = round($sum_items_invoice - $sum_items_credit - $sum_commission_HT * (1 + TAX_SERVICE), FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_versement_HT = round($sum_items_invoice_HT - $sum_items_credit_HT - $sum_commission_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
							$sum_versement_TVA = round($sum_items_invoice - $sum_items_invoice_HT - ($sum_items_credit - $sum_items_credit_HT) - $sum_commission_HT * TAX_SERVICE, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
						} else {
							$sum_versement = $sum_versement_HT = $sum_versement_TVA = 0;
						}
						if ((int) $order->getIncrementId() > $GLOBALS['REFUND_ITEMS_INFO_ID_LIMIT']) {
							$creditcom = $credit_comments[$com];
						} else {
							$creditcom = $credit_comments;
						}
						array_push($data, [
							'date_creation' => $date_creation,
							'date_livraison' => $date_livraison,
							'increment_id' => $incrementid,
							'nom_client' => $nom_client,
							'commercant' => $com,
							'sum_items' => $sum_items,
							'sum_items_HT' => $sum_items_HT,
							'sum_items_credit' => $sum_items_credit,
							'sum_items_credit_HT' => $sum_items_credit_HT,
							'remboursements' => $creditcom,
							'sum_ticket' => $sum_items - $sum_items_credit,
							'sum_ticket_HT' => $sum_items_HT - $sum_items_credit_HT,
							'sum_commission' => $sum_commission,
							'sum_commission_HT' => $sum_commission_HT,
							'sum_versement' => $sum_versement,
							'sum_versement_HT' => $sum_versement_HT,
						]);
					}
				}
			}
		}

		return $data;
	}
}
