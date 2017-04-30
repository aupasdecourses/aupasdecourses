<?php

namespace Apdc\ApdcBundle\Services;

include_once '../../app/Mage.php';

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
        $attachments = \Mage::getModel('amorderattach/order_field')->load($order->getId(), 'order_id');
        $commentaires_ticket = '|*COM. TICKET*|'."\n".$attachments->getData('commentaires_ticket')."\n";
        $commentaires_interne = '|*COM. INTERNE*|'."\n".$attachments->getData('commentaires_commande')."\n";
        $commentaires_fraislivraison = '|*COM. FRAISLIV*|'."\n".$attachments->getData('commentaires_fraislivraison');
        $comments = $remboursement_client.$commentaires_ticket.$commentaires_interne.$commentaires_fraislivraison;

        return $comments;
    }

    private function getOrderComments($order)
    {
        $order_comments = '';
        foreach ($order->getAllStatusHistory() as $status) {
            $comment_status = $status->getData('status');
            $comment = $status->getData('comment');
            if ($comment_status == 'processing' && $comment != null && $comment != '' && !startsWith($comment, 'Notification paiement Hipay') && !startsWith($comment, 'Le client a payé par Hipay avec succès')) {
                $order_comments .= '=> '.$comment."\n";
            }
        }

        return '|*ORDER HISTORY*|'."\n".$order_comments;
    }

    /** Récupère l'information commercant dans la table order */
    private function comid_item($item, $order)
    {
        $pid = $item->getProductId();
        $items = $order->getAllItems();
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
        $pid = $item->getProductId();
        $items = $order->getAllItems();
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
        $refund_order = \Mage::getModel('pmainguet_delivery/refund_order');
        $orders = $refund_order->getCollection()->addFieldToFilter('order_id', array('in' => $order->getIncrementId()));
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

    private function getRefunditemdata($item)
    {
        $refund_items = \Mage::getModel('pmainguet_delivery/refund_items');
        $item = $refund_items->load($item->getOrderItemId(), 'order_item_id');
        $response = $item->getData();

        return $response;
    }

    /** Version Delivery de getShops. */
    private function getShops($id = -1, $filter = 'none')
    {
        $return = [];
        $shops = \Mage::getModel('apdc_commercant/shop')->getCollection();
        if ($id == -1) {
            if ($filter == 'none') {
                foreach ($shops as $shop) {
                    $return[$shop->getIdAttributCommercant()] = array(
                        'name' => $shop->getName(),
                        'adresse' => $shop->getStreet().' '.$shop->getPostcode().' '.$shop->getCity(),
                        'telephone' => $shop->getPhone(),
                        'shop_id' => $shop->getIdShop(),
                        'id_attribut_commercant' => $shop->getIdAttributCommercant(),
                    );
                }
            } elseif ($filter == 'store') {
                $shops->getSelect()->join('catalog_category_entity', 'main_table.id_category=catalog_category_entity.entity_id', array('catalog_category_entity.path'));
                $shops->addFilterToMap('path', 'catalog_category_entity.path');
                foreach ($shops as $shop) {
                    $storeid = explode('/', $shop->getPath())[1];
                    $return[$storeid][$shop->getIdAttributCommercant()] = array(
                        'name' => $shop->getName(),
                        'adresse' => $shop->getStreet().' '.$shop->getPostcode().' '.$shop->getCity(),
                        'telephone' => $shop->getPhone(),
                        'shop_id' => $shop->getIdShop(),
                        'id_attribut_commercant' => $shop->getIdAttributCommercant(),
                    );
                }
            }
            arsort($return);
        } else {
            $data = $shops->addFieldToFilter('id_attribut_commercant', $id)->getFirstItem()->getData();
            $return['name'] = $data['name'];
            $return['adresse'] = $data['street'].' '.$data['postcode'].' '.$data['city'];
            $return['url_adresse'] = 'https://www.google.fr/maps/place/'.str_replace(' ', '+', $return['adresse']);
            $return['phone'] = $data['phone'];
            $return['website'] = $data['website'];
            $return['timetable'] = implode(',', $data['timetable']);
            $return['closing_periods'] = $data['closing_periods'];
            $return['delivery_days'] = 'Du Mardi au Vendredi';
            $return['mail_contact'] = \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_manager'])->getFirstItem()->getEmail();
            $return['mail_pro'] = \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_employee'])->getFirstItem()->getEmail();
            $return['mail_3'] = \Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_employee_bis'])->getFirstItem()->getEmail();
            $return['shop_id'] = $data['id_shop'];
            $return['id_commercant'] = $data['id_commercant'];
            $return['id_attribut_commercant'] = $data['id_attribut_commercant'];
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

    /**
     * [data_facturation description].
     *
     * @param string $debut [description]
     * @param string $fin   [description]
     * @param string $type  [description]
     *
     * @return array [description]
     */
    public function data_facturation($debut, $fin, $type = 'all')
    {
        $data = [];

        //current time
        $currentTime = \Varien_Date::now();
        $currentTimestamp = \Varien_Date::toTimestamp($currentTime);

        //$debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));
        $debut = date('Y-m-d', strtotime(str_replace('/', '-', $debut)));
        $billing_month = date('01/m/Y', strtotime(str_replace('/', '-', $debut)));

        $list_commercant = $this->getShops();
        $orders = \Mage::getModel('sales/order')->getCollection()
            //->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
            ->addAttributeToFilter('status', array('in' => array(\Mage_Sales_Model_Order::STATE_COMPLETE, \Mage_Sales_Model_Order::STATE_CLOSED)));
            //->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin));

        $orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id = mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
        $orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id = mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));
        $orders->addFilterToMap('ddate', 'mwddate.ddate');
        $orders->addAttributeToFilter('ddate', array(
                'from' => $debut,
                'to' => $fin,
            ));

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
                                    $creditinfo = $this->getRefunditemdata($item);
                                    $prixclient = $creditinfo['prix_final'];
                                    $prixcommercant = $creditinfo['prix_commercant'];

                                    $prix_final = ($prixcommercant != null ? $prixcommercant : $prixclient);

                                    $creditvalue = $creditinfo['prix_initial'] - $prix_final;

                                    $sum_items_credit += floatval($creditvalue);
                                    $sum_items_credit_HT += floatval($creditvalue) / (1 + $TVApercent);
                                    $sum_commission_HT += (floatval($item->getRowTotal()) - floatval($creditvalue) / (1 + $TVApercent)) * floatval(str_replace(',', '.', $marge_arriere));
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
                            $date_livraison = $billing_month = 'Non Dispo';
                        }
                        if ($parentid == null) {
                            $parentid = $order->getIncrementId();
                        }
                        $incrementid = $order->getIncrementId();
                        $order_shop_id = $incrementid.'-'.$com['shop_id'];
                        $nom_client = $order->getCustomerName();
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
                            //$sum_commission = round($sum_commission_HT * (1 + TAX_SERVICE), FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                            $sum_commission_HT = round($sum_commission_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                            //$sum_commission_TVA = round($sum_commission_HT * TAX_SERVICE, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                        } else {
                            //$sum_commission = 0;
                            $sum_commission_HT = 0;
                            //$sum_commission_TVA = 0;
                        }
                        if ($order->hasInvoices()) {
                            //$sum_due = round($sum_items_invoice - $sum_items_credit - $sum_commission_HT * (1 + TAX_SERVICE), FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                            $sum_due_HT = round($sum_items_invoice_HT - $sum_items_credit_HT - $sum_commission_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                            //$sum_due_TVA = round($sum_items_invoice - $sum_items_invoice_HT - ($sum_items_credit - $sum_items_credit_HT) - $sum_commission_HT * TAX_SERVICE, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                        } else {
                            //$sum_due = 0;
                            $sum_due_HT = 0;
                            //$sum_due_TVA = 0;
                        }
                        if ((int) $order->getIncrementId() > $GLOBALS['REFUND_ITEMS_INFO_ID_LIMIT']) {
                            $creditcom = $credit_comments[$com['name']];
                        } else {
                            $creditcom = $credit_comments;
                        }
                        array_push($data, [
                            'order_shop_id' => $order_shop_id,
                            'creation_date' => $date_creation,
                            'delivery_date' => $date_livraison,
                            'billing_month' => $billing_month,
                            'increment_id' => $incrementid,
                            'customer_name' => $nom_client,
                            'shop_id' => intval($com['shop_id']),
                            'shop' => $com['name'],
                            'id_billing' => '',
                            'sum_items_HT' => $sum_items_HT,
                            'sum_items' => $sum_items,
                            'sum_items_credit_HT' => $sum_items_credit_HT,
                            'sum_items_credit' => $sum_items_credit,
                            'sum_ticket_HT' => $sum_items_HT - $sum_items_credit_HT,
                            'sum_ticket' => $sum_items - $sum_items_credit,
                            'sum_commission_HT' => $sum_commission_HT,
                            //'sum_commission' => $sum_commission,
                            'sum_due_HT' => $sum_due_HT,
                            //'sum_due' => $sum_due,
                        ]);
                    }
                }
            }
        }

    //indi_billing_summary calculation

        if ($type != 'details') {
            $data_summary = array();
            $data_summary_key = array(
                'sum_items_HT',
                'sum_items',
                'sum_items_credit_HT',
                'sum_items_credit',
                'sum_ticket_HT',
                'sum_ticket',
                'sum_commission_HT',
                'sum_commission_TVA_percent',
                'sum_commission_TVA',
                'sum_commission',
                'sum_due_HT',
                'sum_due',
                );

            foreach ($list_commercant as $id => $com) {
                foreach ($data_summary_key as $key) {
                    $data_summary[$com['shop_id']][$key] = 0;
                }
                $data_summary[$com['shop_id']]['shop_id'] = $com['shop_id'];
                $data_summary[$com['shop_id']]['shop'] = $com['name'];
                $data_summary[$com['shop_id']]['billing_month'] = $billing_month;
                $data_summary[$com['shop_id']]['created_at'] = $currentTimestamp;
            }

            foreach ($data as $row) {
                foreach ($data_summary_key as $key) {
                    if (!in_array($key, array('shop_id', 'shop'))) {
                        $data_summary[$row['shop_id']][$key] += $row[$key];
                    }
                }
            }
        }

        if ($type == 'all') {
            $return = array('details' => $data, 'summary' => $data_summary);
        } elseif ($type == 'details') {
            $return = $data;
        } elseif ($type == 'summary') {
            $return = $data_summary;
        }

        return $return;
    }

    public function data_clients_light($debut, $fin)
    {
        $data = [];
        $debut = date('Y-m-d', strtotime(str_replace('/', '-', $debut)));

        $orders = \Mage::getModel('sales/order')->getCollection()
            ->addAttributeToSort('increment_id', 'DESC');
        $orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id = mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
        $orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id = mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));
        $orders->addFilterToMap('ddate', 'mwddate.ddate');
        $orders->addAttributeToFilter('ddate', array(
                'from' => $debut,
                'to' => $fin,
            ));

        foreach ($orders as $order) {
            $status = $order->getStatusLabel();
            if ($order->getCouponCode() != '') {
                $discount = -floatval($order->getBaseDiscountAmount());
            } else {
                $discount = 0;
            }
            $incrementid = $order->getIncrementId();
            $total_withship = $order->getGrandTotal();
            $frais_livraison = $order->getShippingAmount() + $order->getShippingTaxAmount();
            $total_withoutship = $total_withship - $frais_livraison;
            array_push($data, [
                'status' => $status,
                'increment_id' => $incrementid,
                'total_produit' => $total_withoutship,
                'discount' => $discount,
            ]);
        }

        return $data;
    }

    /**
     * @param  string debut date de début 
     * @param  string fin date de fin
     *
     * @return array tableau de données de vérification
     *               sum_items_facturation total des produits commandés via Facturation
     *               sum_items_magento total des produits commandés via Magento (intégrant discount)
     *               status_ok_count nbe de commandes cloturées (complete ou closed)
     *               status_nok_count nbe de commandes non cloturées (complete ou closed)
     */
    public function data_facturation_verif($debut, $fin)
    {
        $result = array();

        // 1 - verify that there is no entry for billing month in database
        $begin_month = date('01/m/Y', strtotime(str_replace('/', '-', $debut)));
        $count_details = \Mage::getModel('pmainguet_delivery/indi_billingdetails')->getCollection()->addFieldToFilter('billing_month', $begin_month)->getSize();
        $count_summary = \Mage::getModel('pmainguet_delivery/indi_billingsummary')->getCollection()->addFieldToFilter('billing_month', $begin_month)->getSize();
        if ($count_details == 0 && $count_summary == 0) {
            $result['verif_noentry'] = true;
        } else {
            $result['verif_noentry'] = false;
        }

        if (!$result['verif_noentry']) {
            $result = [
                'verif_mois' => false,
                'verif_noentry' => false,
                'verif_noprocessing' => false,
                'verif_totaux' => false,
                'display_button' => false,
                'sum_items_facturation' => 'NA',
                'sum_items_magento' => 'NA',
                'diff_facturation_magento' => 'NA',
                'status_ok_count' => 'NA',
                'status_nok_count' => 'NA',
                'status_processing_count' => 'NA',
                'order_total' => 'NA',
                'id_max' => 'NA',
                'id_min' => 'NA',
            ];
        } else {

            // 2 - verify that current month > month chosen for billing
            $current_month = date('01/m/Y', time());
            $billing_month = date('01/m/Y', strtotime(str_replace('/', '-', $debut)));
            if (strtotime($current_month) - strtotime($billing_month) > 0) {
                $result['verif_mois'] = true;
            } else {
                $result['verif_mois'] = false;
            }

            //get Magento total products & coupons
            $data_magento = $this->data_clients_light($debut, $fin);
            $result_data_magento = array(
                'total_produit' => 0,
                'discount' => 0,
                'status_ok_count' => 0,
                'status_nok_count' => 0,
                'status_processing_count' => 0,
                'id_max' => 0,
                'id_min' => 999999999999999999999,
                'diff_facturation_magento' => 0,
            );
            foreach ($data_magento as $row) {
                if (in_array(strtolower($row['status']), [strtolower(\Mage_Sales_Model_Order::STATE_COMPLETE), strtolower(\Mage_Sales_Model_Order::STATE_CLOSED)])) {
                    $result_data_magento['total_produit'] += floatval($row['total_produit']);
                    $result_data_magento['discount'] += floatval($row['discount']);
                    $result_data_magento['status_ok_count'] += 1;
                    if ($result_data_magento['id_max'] < $row['increment_id']) {
                        $result_data_magento['id_max'] = $row['increment_id'];
                    }
                    if ($result_data_magento['id_min'] > $row['increment_id']) {
                        $result_data_magento['id_min'] = $row['increment_id'];
                    }
                } elseif (strtolower($row['status']) == strtolower(\Mage_Sales_Model_Order::STATE_PROCESSING)) {
                    $result_data_magento['status_processing_count'] += 1;
                } else {
                    $result_data_magento['status_nok_count'] += 1;
                }
            }

            //get data_facturation total
            $data_facturation = $this->data_facturation($debut, $fin, 'details');
            $result_data_facturation = [
                'sum_items_HT' => 0,
                'sum_items' => 0,
                'sum_items_credit_HT' => 0,
                'sum_items_credit' => 0,
                'remboursements' => 0,
                'sum_ticket_HT' => 0,
                'sum_ticket' => 0,
                'sum_commission_HT' => 0,
                //'sum_commission' => 0,
                'sum_due_HT' => 0,
                //'sum_due' => 0,
            ];
            foreach ($data_facturation as $row) {
                foreach ($result_data_facturation as $key => $value) {
                    $result_data_facturation[$key] += floatval($row[$key]);
                }
            }

            // 3 - Vérification aucune commande en processing
            if ($result_data_magento['status_processing_count'] != 0) {
                $result['verif_noprocessing'] = false;
            } else {
                $result['verif_noprocessing'] = true;
            }

            // 4 - Vérification totaux égaux
            if (round($result_data_facturation['sum_items'] - $result_data_magento['total_produit'] - $result_data_magento['discount'], FLOAT_NUMBER, PHP_ROUND_HALF_UP) == 0) {
                $result['verif_totaux'] = true;
            } else {
                $result['verif_totaux'] = false;
            }

            // 5 - Verif affichage bouton ou non
            if ($result['verif_totaux'] * $result['verif_noprocessing'] * $result['verif_noentry'] * $result['verif_mois'] == 1) {
                $result['display_button'] = true;
            } else {
                $result['display_button'] = false;
            }

            $result = array_merge($result, [
                'sum_items_facturation' => $result_data_facturation['sum_items'],
                'sum_items_magento' => $result_data_magento['total_produit'] + $result_data_magento['discount'],
                'diff_facturation_magento' => round($result_data_facturation['sum_items'] - $result_data_magento['total_produit'] - $result_data_magento['discount'], FLOAT_NUMBER, PHP_ROUND_HALF_UP),
                'status_ok_count' => $result_data_magento['status_ok_count'],
                'status_nok_count' => $result_data_magento['status_nok_count'],
                'status_processing_count' => $result_data_magento['status_processing_count'],
                'order_total' => $result_data_magento['status_ok_count'] + $result_data_magento['status_nok_count'] + $result_data_magento['status_processing_count'],
                'id_max' => $result_data_magento['id_max'],
                'id_min' => $result_data_magento['id_min'],
            ]);
        }

        return $result;
    }

    public function updateDataBillingId($debut)
    {
        $billing_month = date('01/m/Y', strtotime(str_replace('/', '-', $debut)));

        $model_summary = \Mage::getModel('pmainguet_delivery/indi_billingsummary')->getCollection();
        $data_summary = $model_summary->addFieldtoFilter('billing_month', $billing_month)->toArray();

        $array = array();
        foreach ($data_summary['items'] as $row) {
            $array[$row['shop_id']] = $row['increment_id'];
        }

        $model_details = \Mage::getModel('pmainguet_delivery/indi_billingdetails')->getCollection();
        $data_details = $model_details->addFieldtoFilter('billing_month', $billing_month)->toArray();

        $update = $data_details['items'];

        foreach ($update as $id => $row) {
            $shop_id = $update[$id]['shop_id'];
            $update[$id]['id_billing'] = $array[$shop_id];
        }

        return $update;
    }

    /**
     * [getDataFacturation description].
     *
     * @param string $model [description]
     * @param string $debut [description]
     *
     * @return array [description]
     */
    public function getDataFacturation($model, $debut)
    {
        $billing_month = date('01/m/Y', strtotime(str_replace('/', '-', $debut)));
        $model = \Mage::getModel('pmainguet_delivery/'.$model)->getCollection();
        $return = $model->addFieldtoFilter('billing_month', $billing_month)->toArray();

        return $return['items'];
    }

    /**
     * [getOneBilling description].
     *
     * @param [type] $increment_id [description]
     * @param [type] $shop         [description]
     *
     * @return [type] [description]
     */
    public function getOneBilling($increment_id)
    {
        $model_details = \Mage::getModel('pmainguet_delivery/indi_billingdetails')->getCollection();
        $data_details = $model_details->addFieldtoFilter('id_billing', $increment_id)->toArray();

        $model_summary = \Mage::getModel('pmainguet_delivery/indi_billingsummary')->getCollection();
        $data_summary = $model_summary->addFieldtoFilter('increment_id', $increment_id)->toArray();

        $return = [
            'increment_id' => $increment_id,
            'details' => $data_details['items'],
            'summary' => $data_summary['items'],
        ];

        return $return;
    }

    public function finalizeFacturation($data, $id)
    {
        $model_summary = \Mage::getModel('pmainguet_delivery/indi_billingsummary')->getCollection();
        $data_summary = $model_summary->addFieldtoFilter('increment_id', $id)->toArray();

        $result = $data_summary['items'][0];

        //Get Form inputs
        $result['discount_shop_HT'] = $data['discount_shop_HT'];
        $result['discount_shop_TVA_percent'] = $data['discount_shop_TVA_percent'];
        $result['comments_discount_shop'] = $data['comments_discount_shop'];
        $result['processing_fees_HT'] = $data['processing_fees_HT'];
        $result['processing_fees_TVA_percent'] = $data['processing_fees_TVA_percent'];

        //Calcul du taux de TVA et TTC pour commission totale
        $result['sum_commission_TVA_percent'] = 0.2;
        $result['sum_commission_TVA'] = $result['sum_commission_HT'] * $result['sum_commission_TVA_percent'];
        $result['sum_commission'] = $result['sum_commission_HT'] + $result['sum_commission_TVA'];

        //Calcul TTC somme due
        $result['sum_due'] = $result['sum_ticket'] - $result['sum_commission'];

        //Calcul TVA et TTC Remise commerciale
        $result['discount_shop_TVA'] = $result['discount_shop_HT'] * $result['discount_shop_TVA_percent'];
        $result['discount_shop'] = $result['discount_shop_HT'] + $result['discount_shop_TVA'];

        //Calcul TVA et TTC Processing fees
        $result['processing_fees_TVA'] = $result['processing_fees_HT'] * $result['processing_fees_TVA_percent'];
        $result['processing_fees'] = $result['processing_fees_HT'] + $result['processing_fees_TVA'];

        //Calcul Total Facture (Commission - Remise Commerciale + Frais HiPay)
        $result['sum_billing_HT'] = $result['sum_commission_HT'] - $result['discount_shop_HT'] + $result['processing_fees_HT'];
        $result['sum_billing_TVA'] = $result['sum_commission_TVA'] - $result['discount_shop_TVA'] + $result['processing_fees_TVA'];
        $result['sum_billing'] = $result['sum_commission'] - $result['discount_shop'] + $result['processing_fees'];

        //Calcul Total Virement Banque (Somme Due + Remise Commerciale - Frais Bancaire)
        $result['sum_payout'] = $result['sum_due'] + $result['discount_shop'] - $result['processing_fees'];

        //current time
        $result['date_finalized']= \Varien_Date::toTimestamp(\Varien_Date::now());

        return $result;
    }

}
