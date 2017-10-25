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
    private function comid_item($itemid, $ordered_items)
    {
        $commercant = null;
        foreach ($ordered_items as $itemId => $item) {
            if ($item->getProductId() == $itemid) {
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
        $data_order=array();

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
            $incrementid = $order->getIncrementId();
            $nom_client = $order->getCustomerName();
            $ddate=$order->getDdate();
            $hascreditmemos=$order->hasCreditmemos();
		
			$sum_shipping_HT	= $order->getData('shipping_amount');
			$sum_shipping_TVA	= $order->getData('shipping_tax_amount');
			$sum_shipping_TTC	= $order->getData('shipping_incl_tax');	// == HT + TVA
 
            //Create Invoices Items collection
            $invoices = $order->getInvoiceCollection();
            foreach ($invoices as $invoice) {
                $invoiced_items = $invoice->getAllItems();
            }

            //Get Credit Memo items
            if ($hascreditmemos) {
                $creditmemos = \Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToFilter('order_id', $order->getId());
                foreach ($creditmemos as $creditmemo) {
                    $credit_items = $creditmemo->getAllItems();
                }
            }

            //Process ordered_items
            foreach ($ordered_items as $item) {
                if($item->getProductType()!="bundle"){
                    $itcom=$item->getCommercant();
                    $do=$data_order[$incrementid][$itcom];
					$do['shop_id']=$list_commercant[$itcom]['shop_id'];
					$do['id_attribut_commercant']=$list_commercant[$itcom]['id_attribut_commercant'];
                    $do['order_shop_id'] = $incrementid.'-'.$do['shop_id'];
                    $do['billing_month']=$billing_month;
                    $do['customer_name']=$nom_client;
                    $do['increment_id'] = $incrementid;
                    $do['shop']=$list_commercant[$itcom]['name'];
                    $do['nb_products']+=floatval($item->getQtyOrdered());
                    $do['sum_items']+=floatval($item->getRowTotalInclTax());
                    $do['sum_items_HT']+=floatval($item->getRowTotal());
					$do['creation_date'] = date('d/m/Y', strtotime($order->getCreatedAt()));

					$do['sum_shipping_HT']	= floatval($sum_shipping_HT);
					$do['sum_shipping_TVA'] = floatval($sum_shipping_TVA);
					$do['sum_shipping_TTC'] = floatval($sum_shipping_TTC);

                    if (!is_null($ddate)) {
                        $do['delivery_date'] = date('d/m/Y', strtotime($ddate));
                    } else {
                        $do['delivery_date'] = $do['billing_month'] = 'Non Dispo';
                    }
                    $data_order[$incrementid][$itcom]=$do;
                }

            }

            //Add info from invoiced_items
            foreach ($invoiced_items as $item) {
                $itcom = $this->comid_item($item->getProductId(), $ordered_items);
                if ($itcom == null) {
                    $product = \Mage::getModel('catalog/product')->load($item->getProductID());
                    if($product->getTypeId()=="bundle"){
                        continue;
                    }
                }
                $do=$data_order[$incrementid][$itcom];

                $do['sum_items_invoice'] += floatval($item->getRowTotalInclTax());
                $do['sum_items_invoice_HT'] += floatval($item->getRowTotal());
                $TVApercent = ($do['sum_items_invoice'] - $do['sum_items_invoice_HT']) / $do['sum_items_invoice_HT'];
                $marge_arriere = $this->marge_item($item, $order);
                if ($hascreditmemos) {
                    foreach ($credit_items as $citem) {
                        if ($item->getProductID() == $citem->getProductID()) {
                            $do['sum_items_credit'] += floatval($citem->getRowTotalInclTax());
                            $do['sum_items_credit_HT'] += floatval($citem->getRowTotal())/(1+$TVAPercent);

                            $do['sum_commission_HT'] += (floatval($item->getRowTotal()) - floatval($citem->getRowTotal())) * floatval(str_replace(',', '.', $marge_arriere));
                        }
                    }
                    $creditinfo = $this->getRefunditemdata($item);
                    $prixclient = $creditinfo['prix_final'];
                    $prixcommercant = $creditinfo['prix_commercant'];

                    $prix_final = ($prixcommercant != null ? $prixcommercant : $prixclient);

                    $creditvalue = $creditinfo['prix_initial'] - $prix_final;

                    $do['sum_items_credit'] += floatval($creditvalue);
                    $do['sum_items_credit_HT'] += floatval($creditvalue) / (1 + $TVApercent);
                    $do['sum_commission_HT'] += (floatval($item->getRowTotal()) - floatval($creditvalue) / (1 + $TVApercent)) * floatval(str_replace(',', '.', $marge_arriere));
                } else {
                    $do['sum_items_credit']=$do['sum_items_credit_HT']=0;
                    $do['sum_commission_HT'] += floatval($item->getRowTotal()) * floatval(str_replace(',', '.', $marge_arriere));
                }

                //Process sum_ticket
                $do['sum_ticket_HT'] = $do['sum_items_HT'] - $do['sum_items_credit_HT'];
                $do['sum_ticket'] = $do['sum_items'] - $do['sum_items_credit'];
                $do['sum_due_HT'] = $do['sum_items_invoice_HT'] - $do['sum_items_credit_HT'] - $do['sum_commission_HT'];
				$do['sum_commission_TVA'] = ($do['sum_commission_HT'] * 0.2);

                $data_order[$incrementid][$itcom]=$do;
			}
       }//end foreach orders

        //Reformat and round numbers
        $data_details=array();
        foreach($data_order as $incrementid => $dat){
            foreach($dat as $itcom => $da){
                foreach($da as $key => $value){
                    if(is_float($value)){
                        $data_order[$incrementid][$itcom][$key] = round($value, FLOAT_NUMBER,   PHP_ROUND_HALF_UP);
                    }
                }
                $data_details[]=$da;
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
				$data_summary[$com['shop_id']]['id_attribut_commercant'] = $com['id_attribut_commercant'];
                $data_summary[$com['shop_id']]['shop'] = $com['name'];
                $data_summary[$com['shop_id']]['billing_month'] = $billing_month;
                $data_summary[$com['shop_id']]['created_at'] = $currentTimestamp;
            }

            foreach($data_details as $row){
                foreach ($data_summary_key as $key) {
                    if (!in_array($key, array('shop_id', 'shop'))) {
                        $data_summary[$row['shop_id']][$key] += $row[$key];
                    }
                }
            }

            //Remove shopid with 0 à facturer
            foreach($data_summary as $shopid => $dat){
                if($dat['sum_ticket_HT']==0){
                    unset($data_summary[$shopid]);
                }
            }

        }

        if ($type == 'all') {
            $return = array('details' => $data_details, 'summary' => $data_summary);
        } elseif ($type == 'details') {
            $return = $data_details;
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
            $discount = -floatval($order->getBaseDiscountAmount());
            if ($order->getCouponCode() != '') {
                $discount_coupon = -floatval($order->getBaseDiscountAmount());
            } else {
                $discount_coupon = 0;
            }
            $incrementid = $order->getIncrementId();
            $total_withship = $order->getGrandTotal();
            if($total_withship - $order->getShippingAmount() - + $order->getShippingTaxAmount()>0){
                $frais_livraison = $order->getShippingAmount() + $order->getShippingTaxAmount();
            }else{
                $frais_livraison = $order->getShippingAmount() + $order->getShippingTaxAmount() + $order->getShippingHiddenTaxAmount();
            }
            $total_withoutship = $total_withship - $frais_livraison + $discount;
            $ordered_items = $order->getAllItems();

            $missing_com_att_count=0;
            foreach($ordered_items as $oi){
                if($oi->getProductType()!="bundle"){
                    if($oi->getCommercant()==null || $oi->getCommercant()==""){
                        $missing_com_att_count+=1;
                    }
                }
            }

            $data[$incrementid]=[
                'status' => $status,
                'increment_id' => $incrementid,
                'total_commande' => $total_withship,
                'frais_livraison' => $frais_livraison,
                'total_produit' => $total_withoutship,
                'discount' => $discount,
                'discount_coupon' => $discount_coupon,
                'missing_com_att_count' => $missing_com_att_count
            ];
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
                'verif_nomissingcom' => false,
                'verif_totaux' => false,
                'display_button' => false,
                'sum_items_facturation' => 'NA',
                'sum_items_magento' => 'NA',
                'sum_order_magento' => 'NA',
                'sum_shipping_magento' => 'NA',
                'sum_discount_magento' => 'NA',
                'sum_discount_coupon_magento' => 'NA',
                'diff_facturation_magento' => 'NA',
                'status_ok_count' => 'NA',
                'status_nok_count' => 'NA',
                'status_processing_count' => 'NA',
                'missing_com_att_count' => 0,
                'order_total' => 'NA',
                'id_max' => 'NA',
                'id_min' => 'NA',
                'orders' => array(),
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
                'total_commande' => 0,
                'frais_livraison' => 0,
                'total_produit' => 0,
                'discount' => 0,
                'discount_coupon' => 0,
                'status_ok_count' => 0,
                'status_nok_count' => 0,
                'status_processing_count' => 0,
                'missing_com_att_count' => 0,
                'id_max' => 0,
                'id_min' => 999999999999999999999,
                'diff_facturation_magento' => 0,
                'orders' =>array(),
            );
            foreach ($data_magento as $id => $row) {
                if (in_array(strtolower($row['status']), [strtolower(\Mage_Sales_Model_Order::STATE_COMPLETE), strtolower(\Mage_Sales_Model_Order::STATE_CLOSED)])) {
                    
                    //add detailled table for debugging
                    $data_consolidated[$id]['increment_id']=$id;
                    $data_consolidated[$id]['total_commande']=floatval($row['total_commande']);
                    $data_consolidated[$id]['total_produit']=floatval($row['total_produit']);
                    $data_consolidated[$id]['frais_livraison']=floatval($row['frais_livraison']);
                    $data_consolidated[$id]['discount']=floatval($row['discount']);
                    $data_consolidated[$id]['discount_coupon']=floatval($row['discount_coupon']);

                    $result_data_magento['total_commande'] += $data_consolidated[$id]['total_commande'];
                    $result_data_magento['total_produit'] += $data_consolidated[$id]['total_produit'];
                    $result_data_magento['frais_livraison'] += $data_consolidated[$id]['frais_livraison'];
                    $result_data_magento['discount'] += $data_consolidated[$id]['discount'];
                    $result_data_magento['discount_coupon'] += $data_consolidated[$id]['discount_coupon'];
                    $result_data_magento['status_ok_count'] += 1;
                    if ($result_data_magento['id_max'] < $row['increment_id']) {
                        $result_data_magento['id_max'] = $row['increment_id'];
                    }
                    if ($result_data_magento['id_min'] > $row['increment_id']) {
                        $result_data_magento['id_min'] = $row['increment_id'];
                    }
                    $result_data_magento['orders'][]=$row['increment_id'];
                } elseif (strtolower($row['status']) == strtolower(\Mage_Sales_Model_Order::STATE_PROCESSING)) {
                    $result_data_magento['status_processing_count'] += 1;
                } else {
                    $result_data_magento['status_nok_count'] += 1;
                }

                $result_data_magento['missing_com_att_count'] += $row['missing_com_att_count'];

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
				'sum_commission_TVA' => 0,
				'sum_due_HT' => 0,
				'sum_shipping_HT' => 0,
				'sum_shipping_TVA' => 0,
				'sum_shipping_TTC' => 0,	
            ];

            foreach ($data_facturation as $row) {
                foreach ($result_data_facturation as $key => $value) {
                    $result_data_facturation[$key] += floatval($row[$key]);
                }
                //add detailled table for debugging - see above
                if(!isset($data_consolidated[$row['increment_id']]['sum_items'])){
                    $data_consolidated[$row['increment_id']]['sum_items']=$row['sum_items'];
                }else{
                    $data_consolidated[$row['increment_id']]['sum_items']+=$row['sum_items'];
                }

                $data_consolidated[$row['increment_id']]['diff']=round($data_consolidated[$row['increment_id']]['total_commande']-$data_consolidated[$row['increment_id']]['frais_livraison']+$data_consolidated[$row['increment_id']]['discount']-$data_consolidated[$row['increment_id']]['sum_items'], FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                
            }

            // 3 - Vérification aucune commande en processing
            if ($result_data_magento['status_processing_count'] != 0) {
                $result['verif_noprocessing'] = false;
            } else {
                $result['verif_noprocessing'] = true;
            }

            // 3 bis - Vérification pas de order items sans attributs commerçant
            if ($result_data_magento['missing_com_att_count'] != 0) {
                $result['verif_nomissingcom'] = false;
            } else {
                $result['verif_nomissingcom'] = true;
            }

            // 4 - Vérification totaux égaux
            $diff_facturation_magento = round($result_data_facturation['sum_items'] - $result_data_magento['total_produit'], FLOAT_NUMBER, PHP_ROUND_HALF_UP);
            if ($diff_facturation_magento == 0) {
                $result['verif_totaux'] = true;
            } else {
                $result['verif_totaux'] = false;
            }

            // 5 - Verif affichage bouton ou non
            if ($result['verif_totaux'] * $result['verif_noprocessing'] * $result['verif_noentry'] * $result['verif_mois'] * $result['verif_nomissingcom'] == 1) {
                $result['display_button'] = true;
            } else {
                $result['display_button'] = false;
            }

            $result = array_merge($result, [
                'sum_items_facturation' => $result_data_facturation['sum_items'],
                'sum_order_magento' => $result_data_magento['total_commande'],
                'sum_shipping_magento' => $result_data_magento['frais_livraison'],
                'sum_discount_magento' => $result_data_magento['discount'],
                'sum_discount_coupon_magento' => $result_data_magento['discount_coupon'],
                'sum_items_magento' => $result_data_magento['total_produit'] + $result_data_magento['discount'],
                'diff_facturation_magento' => $diff_facturation_magento,
                'status_ok_count' => $result_data_magento['status_ok_count'],
                'status_nok_count' => $result_data_magento['status_nok_count'],
                'status_processing_count' => $result_data_magento['status_processing_count'],
                'missing_com_att_count' => $result_data_magento['missing_com_att_count'],
                'order_total' => $result_data_magento['status_ok_count'] + $result_data_magento['status_nok_count'] + $result_data_magento['status_processing_count'],
                'id_max' => $result_data_magento['id_max'],
                'id_min' => $result_data_magento['id_min'],
                'orders' => $result_data_magento['orders'],
            ]);
        }
        return array('result'=>$result,'details'=>$data_consolidated);
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
	 * Surcharge avec ajout de @param string $fin
	 */
	public function getDataFactu($model, $debut, $fin)
	{
		$billing_month = date('01/m/Y', strtotime(str_replace('/', '-', $debut)));
		$model = \Mage::getModel('pmainguet_delivery/'.$model)->getCollection();
		$return = $model->addFieldToFilter('billing_month', ['from' => $billing_month, 'to' => $fin]);
		$return = $return->toArray();

		return $return['items'];
	}

	/**
	 * Surcharge avec ajout de @param string $id_attribut_commercant
	 */
	public function getDataFactuNoTimeLimit($model, $id_attribut_commercant)
	{
		$model = \Mage::getModel('pmainguet_delivery/'.$model)->getCollection();

		$return = $model->addFieldToFilter('id_attribut_commercant', $id_attribut_commercant);	
		$return = $return->toArray();

		return $return['items'];
	}

	public function getBillingDetailsByDeliveryDate($debut, $fin)
	{
		$billing_details = \Mage::getModel('pmainguet_delivery/indi_billingdetails')->getCollection();
		
		$return = $billing_details->addFieldToFilter('delivery_date', ['from' => $debut, 'to' => $fin]);
		$return = $return->toArray();

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
        $result['discount_shop_HT'] = floatval(str_replace(",",".",$data['discount_shop_HT']));
        $result['discount_shop_TVA_percent'] = $data['discount_shop_TVA_percent'] / 100;
        $result['comments_discount_shop'] = $data['comments_discount_shop'];
        $result['processing_fees_HT'] = floatval(str_replace(",",".",$data['processing_fees_HT']));
        $result['processing_fees_TVA_percent'] = $data['processing_fees_TVA_percent'] / 100;

        //Calcul du taux de TVA et TTC pour commission totale
        $result['sum_commission_TVA_percent'] = 0.2;
        $result['sum_commission_TVA'] = $result['sum_commission_HT'] * $result['sum_commission_TVA_percent'];
        $result['sum_commission'] = $result['sum_commission_HT'] + $result['sum_commission_TVA'];

        //Calcul TTC somme due
        $result['sum_due'] = $result['sum_ticket'] - $result['sum_commission'];

        //Calcul TVA et TTC Remise commerciale
        $result['discount_shop_TVA'] = $result['discount_shop_HT'] * $result['discount_shop_TVA_percent'];
        $result['discount_shop'] = $result['discount_shop_HT'] + $result['discount_shop_TVA'];

        //Calcul Virement
        $result['sum_virement'] = $result['sum_due'] + $result['discount_shop'];

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
        $result['date_finalized'] = \Varien_Date::toTimestamp(\Varien_Date::now());

        return $result;
    }

    public function sendBilling($bill, $filepath)
    {
        $bill = $bill['summary'][0];
        $shops = \Mage::getModel('apdc_commercant/shop')->getCollection();
        //$shops->addFieldtoSelect('id_shop', 'id_commercant', 'enabled', 'name');
        $shops->getSelect()->join('apdc_commercant', 'main_table.id_commercant=apdc_commercant.id_commercant', array('commercant_name' => 'apdc_commercant.name', 'id_contact_ceo', 'id_contact_billing'));
        $shops->getSelect()->join(array('contact_ceo' => 'apdc_commercant_contact'), 'apdc_commercant.id_contact_ceo=contact_ceo.id_contact', array('ceo_lastname' => 'contact_ceo.lastname', 'ceo_firstname' => 'contact_ceo.firstname', 'ceo_email' => 'contact_ceo.email'));
        $shops->getSelect()->join(array('contact_billing' => 'apdc_commercant_contact'), 'apdc_commercant.id_contact_billing=contact_billing.id_contact', array('billing_lastname' => 'contact_billing.lastname', 'billing_firstname' => 'contact_billing.firstname', 'billing_email' => 'contact_billing.email'));
        $data = $shops->addFieldToFilter('id_shop', $bill['shop_id'])->getFirstItem()->getData();
        $monthnumeric = str_replace('/', '-', $bill['billing_month']);
        $month = ucfirst(strftime('%B %G', strtotime(str_replace('/', '-', $bill['billing_month']))));
 
        $result['mails'][0] = $data['ceo_email'];
        if ($data['ceo_email'] != $data['billing_email']) {
            $result['mails'][1] = $data['billing_email'];
        }
        $result['subject'] = 'Au Pas De Courses - Facture et détails de '.$month;
        $result['mail_template'] = 'billing_shop_mail';
        $result['mail_vars'] = ['commercant' => $data['name'], 'month' => $month];
        $result['attachment'] = array('path' => $filepath, 'name' => 'Facture_'.$bill['increment_id'].$month_numeric.'.pdf');

        $model_summary = \Mage::getModel('pmainguet_delivery/indi_billingsummary')->getCollection();
        $data_summary = $model_summary->addFieldtoFilter('increment_id', $bill['increment_id'])->toArray();
        $result['date_sent']= \Varien_Date::toTimestamp(\Varien_Date::now());
        return $result;
	}

	/** Pour les payouts */
    public function getApdcBankFields()
    {
        $tab = [];

        $merchants = \Mage::getModel('apdc_commercant/commercant')->getCollection();

        $merchants->getSelect()->join('apdc_bank_information', 'main_table.id_bank_information = apdc_bank_information.id_bank_information');

        $merchants->getSelect()->join('apdc_commercant_contact', 'main_table.id_contact_billing = apdc_commercant_contact.id_contact');

        $merchants->getSelect()->join('apdc_shop', 'main_table.id_commercant = apdc_shop.id_commercant')->group('main_table.id_commercant');

        foreach ($merchants as $merchant) {
            $tab[$merchant->getData('name')] = [
                'id'						=> $merchant->getData('id_commercant'),
                'name'						=> $merchant->getData('name'),
                'ownerName'					=> $merchant->getData('owner_name'),
                'iban'						=> $merchant->getData('account_iban'),
                'shopperEmail'				=> $merchant->getData('email'),
				'shopperReference'			=> $merchant->getData('firstname').' - '.$merchant->getData('lastname'),
				'id_attribut_commercant'	=> $merchant->getData('id_attribut_commercant'),
            ];
        }

        return $tab;
    }

}
