<?php

//Constante des fonctions
define('TAX_SERVICE', .2);
define('FLOAT_NUMBER', 2);

//=> FONCTIONS GENERALES

/*Get Date or Delivery Time Array => would be better to use model/table mwddate & amasty
/ Used in
/var/www/html/apdcdev/delivery/modules/commande/views/commande_commercant.phtml:
/var/www/html/apdcdev/delivery/modules/commande/views/liste_commande.phtml:
/var/www/html/apdcdev/delivery/modules/commande/views/liste_commande_client.phtml:
/var/www/html/apdcdev/delivery/modules/dispatch/views/listing.phtml
*/
function datetime_filter($render = 'date', $var = 'mwddate')
{
    try {
        $temp = array();
        if ($var == 'mwddate') {
            if ($render == 'date') {
                $temp = Mage::getSingleton('ddate/ddate')->getCollection()->addFieldToSelect('ddate')->getColumnValues('ddate');
                $temp = array_unique($temp);
            } elseif ($render == 'time') {
                $temp = Mage::getSingleton('ddate/dtime')->getCollection()->addFieldToSelect('dtime')->getColumnValues('dtime');
                $temp = array_unique($temp);
            }
        } else {
            if ($render == 'date') {
                $temp = Mage::getSingleton('amasty/amdeliverydate_deliverydate')->getCollection()->addFieldToSelect('date')->getColumnValues('date');
                $temp = array_unique($temp);
            } elseif ($render == 'time') {
                $temp = Mage::getSingleton('amasty/amdeliverydate_deliverydate')->getCollection()->addFieldToSelect('time')->getColumnValues('time');
                $temp = array_unique($temp);
            }
        }

        if ($render == 'date') {
            //Ajout de la date du jour
      $current_day = date('Y-m-d');
            if (!in_array($current_day, $temp)) {
                $temp[] = $current_day;
            };
            rsort($temp);
        }

        if ($render == 'time') {
            sort($temp);
        }

        return $temp;
    } catch (Exception $e) {
    }
}

/* Used in 
/var/www/html/apdcdev/delivery/models/magento.php:
/var/www/html/apdcdev/delivery/modules/facturation/view.php:
/var/www/html/apdcdev/delivery/modules/facturation/views/facturation.phtml:
*/

function end_month($date)
{
    $date = strtotime('+1 month', strtotime(str_replace('/', '-', $date)));
    $date = strtotime('-1 second', $date);
    $date = date('Y-m-d H:i:s', $date);

    return $date;
}

//Used in /var/www/html/apdcdev/delivery/modules/commande/views/commande_commercant.phtml
function produit_equivalent_label($order)
{
    $prodeq = $order->getData('produit_equivalent');
    if ($prodeq == 1) {
        return 'Oui';
    } else {
        return 'Non';
    }
}

//FONCTIONS MAGENTO

/////Connection à Magento => création d'un objet réutilisable par les autres fonctions
function connect_magento()
{
    include CHEMIN_MAGE.'app/Mage.php';
    umask(0);
    Mage::app();
}

/////Création des tables commercants, produits, statuts pour limiter les call à MAGENTO
/* Used in
list_rootcatid($displayby = 'name')
/var/www/html/apdcdev/delivery/modules/commande/views/liste_commande_client.phtml
*/

function list_stores($displayby = 'code')
{
    //Get all store except "accueil"

    $stores = [];
    $allStores = Mage::app()->getStores();
    foreach ($allStores as $_eachStoreId => $val) {
        $_storeId = Mage::app()->getStore($_eachStoreId)->getId();
        if ($displayby == 'code') {
            $_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
            $stores[$_storeId] = $_storeCode;
        } elseif ($displayby == 'name') {
            $_storeName = Mage::app()->getStore($_eachStoreId)->getName();
            $stores[$_storeId] = $_storeName;
        }
    }
    if (($key = array_search('Au Pas De Courses Accueil', $stores)) !== false || ($key = array_search('accueil', $stores)) !== false) {
        unset($stores[$key]);
    }

    return $stores;
}

/*Used in
/var/www/html/apdcdev/delivery/modules/commercant/views/list_commercant_order.phtml:
/var/www/html/apdcdev/delivery/modules/commercant/views/list_commercant_profil.phtml:
/var/www/html/apdcdev/delivery/modules/dispatch/views/listing.phtml
*/

function list_rootcatid($displayby = 'name')
{
    $stores = list_stores('name');
    $rootcatid = [];
    foreach ($stores as $id => $name) {
        $current = Mage::app()->setCurrentStore($id);
        $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
        if ($displayby == 'name') {
            $rootcatid[$rootCategoryId] = $name;
        } elseif ($displayby == 'id') {
            $rootcatid[$rootCategoryId] = $id;
        }
    }

    return $rootcatid;
}

//LISTE COMMERCANT PAR STORE ET ID ATTRIBUT COMMERCANT

/*Used in
/var/www/html/apdcdev/delivery/modules/commande/views/commande_commercant.phtml:
/var/www/html/apdcdev/delivery/modules/commercant/views/list_commercant_order.phtml:
/var/www/html/apdcdev/delivery/modules/commercant/views/list_commercant_profil.phtml:
/var/www/html/apdcdev/delivery/modules/dispatch/views/listing.phtml:
/var/www/html/apdcdev/delivery/send_daily_orders.php:
/var/www/html/apdcdev/delivery/uploadpdf.php
/data_facturation_products()
*/

function getShops($id = -1, $filter = 'none')
{
    $return = [];
    $shops = Mage::getModel('apdc_commercant/shop')->getCollection();

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
                    'name' => $shop->getName(),
                    'adresse' => $shop->getStreet().' '.$shop->getPostcode().' '.$shop->getCity(),
                    'telephone' => $shop->getPhone(),
                );
            }
        }
        arsort($return);
    } else {
        $data = $shops->addFieldToFilter('id_attribut_commercant',$id)->getFirstItem()->getData();
        $return['name'] = $data['name'];
        $return['adresse'] = $data['street'].' '.$data['postcode'].' '.$data['city'];
        $return['url_adresse'] = 'https://www.google.fr/maps/place/'.str_replace(' ', '+', $return['adresse']);
        $return['phone'] = $data['phone'];
        $return['website'] = $data['website'];
        $return['timetable'] = implode(',', $data['timetable']);
        $return['closing_periods'] = $data['closing_periods'];
        $return['delivery_days'] = 'Du Mardi au Vendredi';
        $return['mail_contact'] = Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_manager'])->getFirstItem()->getEmail();
        $return['mail_pro'] = Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_employee'])->getFirstItem()->getEmail();
        $return['mail_3'] = Mage::getModel('apdc_commercant/contact')->getCollection()->addFieldToFilter('id_contact', $data['id_contact_employee_bis'])->getFirstItem()->getEmail();
    }
    return $return;
}

//=>FONCTIONS COMMANDES PAR COMMERCANTS/SHOPS

//COMMANDES (OBJETS) PAR COMMERCANTS
//Used only in function orders_fortheday()*/
function all_orders($var = 'mwddate', $commercantId = 'all')
{
    try {
        $orders = Mage::getModel('sales/order')->getCollection();
        //N'affiche que les commandes de moins de 3 mois
        $from_date = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') - 3, date('d'), date('Y')));

        //Ajout de MWDDate (pour les commandes après le 12 janvier environ)
        if ($var == 'mwddate') {
            $orders->getSelect()->join('mwddate_store', 'main_table.entity_id=mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
            $orders->getSelect()->join('mwddate', 'mwddate_store.ddate_id=mwddate.ddate_id', array('ddate' => 'mwddate.ddate', 'dtime' => 'mwddate.dtimetext'));
            $orders->getSelect()->join(array('order_attribute' => 'amasty_amorderattr_order_attribute'), 'order_attribute.order_id = main_table.entity_id', array('produit_equivalent' => 'order_attribute.produit_equivalent', 'contactvoisin' => 'order_attribute.contactvoisin', 'codeporte1' => 'order_attribute.codeporte1', 'codeporte2' => 'order_attribute.codeporte2', 'batiment' => 'order_attribute.batiment', 'etage' => 'order_attribute.etage', 'telcontact' => 'order_attribute.telcontact', 'infocomplementaires' => 'order_attribute.infoscomplementaires'));
            $orders->addFilterToMap('ddate', 'mwddate.ddate');
            $orders->addFilterToMap('dtime', 'mwddate.dtimetext')
            ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
            ->addAttributeToSort('dtime', 'asc');
            $orders->addAttributeToFilter('main_table.created_at', array(
                            'from' => $from_date,
                    ));

        //Amasty Delivery Date
        } elseif ($var = '') {
            $orders->getSelect()->join(array('delivery_date' => 'amasty_amdeliverydate_deliverydate'), 'delivery_date.order_id = main_table.entity_id', array('*', 'delivery_date' => 'delivery_date.date', 'delivery_time' => 'delivery_date.time'))->order('delivery_date', 'ASC');
            $orders->getSelect()->join(array('order_attribute' => 'amasty_amorderattr_order_attribute'), 'order_attribute.order_id = main_table.entity_id', array('produit_equivalent' => 'order_attribute.produit_equivalent', 'contactvoisin' => 'order_attribute.contactvoisin', 'codeporte1' => 'order_attribute.codeporte1', 'codeporte2' => 'order_attribute.codeporte2', 'batiment' => 'order_attribute.batiment', 'etage' => 'order_attribute.etage', 'telcontact' => 'order_attribute.telcontact', 'infocomplementaires' => 'order_attribute.infoscomplementaires'));
            $orders->addFilterToMap('delivery_date', 'delivery_date.date');
            $orders->addFilterToMap('delivery_time', 'delivery_date.time')
            ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
            ->addAttributeToSort('delivery_time', 'asc');
            $orders->addAttributeToFilter('main_table.created_at', array(
                            'from' => $from_date,
                    ));
        }

        if ($commercantId != 'all') {
            $orders->getSelect()->join(
                   array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                'order_item.order_id = main_table.entity_id'
            )->where('order_item.commercant='.strval($commercantId))->group('order_item.order_id');
        }
    } catch (Exception $e) {
    }

    return $orders;
}

//Get orders for one commercant for a specific date
/* Used in
/var/www/html/apdcdev/delivery/modules/commande/views/commande_commercant.phtml:
/var/www/html/apdcdev/delivery/modules/commande/views/liste_commande.phtml:
/var/www/html/apdcdev/delivery/modules/commande/views/liste_commande_client.phtml:
/var/www/html/apdcdev/delivery/modules/dispatch/views/listing.phtml:
/var/www/html/apdcdev/delivery/send_daily_orders.php
*/

function orders_fortheday($date, $commercantId = 'all', $var = 'mwddate')
{
    //$date need to be of format 2016-02-23
    $orders = all_orders($var, $commercantId);
    $d = explode('-', $date);
    $date = date('Y-m-d H:i:s', mktime(0, 0, 0, intval($d[1]), intval($d[2]), intval($d[0])));
    if ($var == 'mwddate') {
        $orders->addAttributeToFilter('ddate', array(
            'in' => $date,
        ));
    } else {
        $orders->addAttributeToFilter('delivery_date', array(
            'in' => $date,
        ));
    }

    return $orders;
}

//=>FONCTIONS COMMANDES GENERALES

//Get list of order ids
/* Used in
/var/www/html/apdcdev/delivery/modules/clients/views/clients_coupon.phtml:
/var/www/html/apdcdev/delivery/modules/clients/views/clients_fidelity.phtml:
/var/www/html/apdcdev/delivery/modules/clients/views/clients_stat.phtml:
/var/www/html/apdcdev/delivery/modules/facturation/view.php:
/var/www/html/apdcdev/delivery/modules/facturation/views/facturation.phtml:
/var/www/html/apdcdev/delivery/modules/remboursement/view.php
*/

function get_list_orderid()
{
    $orders = Mage::getResourceModel('sales/order_collection')
        ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
        ->addAttributeToSelect('increment_id')
        ->addAttributeToSelect('created_at')
        ->setOrder('increment_id', 'asc');

    $array_orderid = array();

    foreach ($orders as $order) {
        $id = $order->getIncrementId();
        $date = date('d/m/Y', strtotime($order->getCreatedAt()));
        $array_orderid[$id] = $date;
    }

    return $array_orderid;
}

//=> FONCTIONS ATTACHMENTS/COMMENTS

//Get Order Attachments
/*Used in 
function getRelevantComments($order)
function getRefundorderdata($order, $output)
*/
function getOrderAttachments($order)
{
    $attachments = Mage::getModel('amorderattach/order_field')->load($order->getId(), 'order_id');
    //$remboursement_client = '|*REMBOURSEMENTS*|</br>'.$attachments->getData('remboursements').'</br>';
    $commentaires_ticket = '|*COM. TICKET*|</br>'.$attachments->getData('commentaires_ticket').'</br>';
    $commentaires_interne = '|*COM. INTERNE*|</br>'.$attachments->getData('commentaires_commande').'</br>';
    $commentaires_fraislivraison = '|*COM. FRAISLIV*|</br>'.$attachments->getData('commentaires_fraislivraison');

    $comments = $remboursement_client.$commentaires_ticket.$commentaires_interne.$commentaires_fraislivraison;

    return $comments;
}

/*Check if a string start with something
/ Used only in
function getOrderComments($order)
*/

function startsWith($haystack, $needle)
{
    $length = strlen($needle);

    return substr($haystack, 0, $length) === $needle;
}

//Get Order Comments
/*Used in 
function getRelevantComments($order)
function getRefundorderdata($order, $output)
*/
function getOrderComments($order)
{
    $order_comments = '';
    foreach ($order->getAllStatusHistory() as $status) {
        $comment_status = $status->getData('status');
        $comment = $status->getData('comment');
        if ($comment_status == 'processing' && $comment != null && $comment != '' && !startsWith($comment, 'Notification paiement Hipay') && !startsWith($comment, 'Le client a payé par Hipay avec succès')) {
            $order_comments .= '=> '.$comment.'<br/>';
        }
    }

    return '|*ORDER HISTORY*|</br>'.$order_comments;
}

//Used in data_clients()
function getRelevantComments($order)
{
    $orderAttachment = getOrderAttachments($order);
    //$order_comments = getOrderComments($order);

    return $orderAttachment.$order_comments;
}

//=> FONCTIONS FACTURATION

//Récupère l'information commercant dans la table order
/*Used in function data_facturation_products() */
function comid_item($item, $order)
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

//Récupère l'information marge dans la table order
/*Used in function data_facturation_products() */
function marge_item($item, $order)
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

//Used in data_facturation_products()
function getRefundorderdata($order, $output)
{
    $refund_order = Mage::getModel('pmainguet_delivery/refund_order');
    $orders = $refund_order->getCollection()->addFieldToFilter('order_id', array('in' => $order->getIncrementId()));
    $response = array();

    if ($output == 'comment') {
        $orderAttachment = getOrderAttachments($order);
        $order_comments = getOrderComments($order);
        if ((int) $order->getIncrementId() > $GLOBALS['REFUND_ITEMS_INFO_ID_LIMIT']) {
            foreach ($orders as $o) {
                //$response[$o->getData('commercant')]= $o->getData($output);
                $response[$o->getData('commercant')] .= $orderAttachment;
                //$response[$o->getData('commercant')].=$order_comments;
            }
        } else {
            $response = $orderAttachment;
            //$response.=$order_comments;
        }
    } else {
        foreach ($orders as $o) {
            $response[$o->getData('commercant')] = $o->getData($output);
        }
    }

    return $response;
}

//Used in data_facturation_products()
function getRefunditemdata($item, $output)
{
    $refund_items = Mage::getModel('pmainguet_delivery/refund_items');
    $item = $refund_items->load($item->getOrderItemId(), 'order_item_id');
    $response = $item->getData($output);

    return $response;
}

//Liste des données de facturation
/*Used in
/var/www/html/apdcdev/delivery/modules/facturation/view.php
*/

function data_facturation_products($debut, $fin)
{
    $data = [];

  /* Format dates */

  $debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));

    $list_commercant = getShops();

    $orders = Mage::getModel('sales/order')->getCollection()
    ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
    ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
    ->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin));

    //Get info on delivery date
    $orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id=mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
    $orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id=mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));

    foreach ($orders as $order) {

 //In case of modified order, find the id of the order made by the client
        //     $parentid=$order->getData('relation_parent_real_id');
        // if ($parentid!=NULL) {
        //         $firstparent=false;
        //         while(!$firstparent) {
        //             $temp=Mage::getModel("sales/order")->loadByIncrementId($parentid);
        //             $temp_parentid=$temp->getData('relation_parent_real_id');
        //             if ($temp_parentid==NULL) {$firstparent=true;}else{$parentid=$temp_parentid;}
        //         }
        // }

        //Ordered Items
        // if ($parentid!=NULL) {
        //     $ordered_items=Mage::getModel("sales/order")->loadByIncrementId($parentid)->getAllVisibleItems();
        // }else{
        $ordered_items = $order->getAllItems();
        $credit_comments = getRefundorderdata($order, 'comment');
        // }

        //Invoiced Items
        if ($order->hasInvoices()) {
            $invoices = $order->getInvoiceCollection();
        };
        foreach ($invoices as $invoice) {
            $invoiced_items = $invoice->getAllItems();
        }

        //Calcul du nombre de commerçant concernés par commande

    foreach ($list_commercant as $id => $com) {
        //Order Totals
          $nb_products = 0;
        $sum_items = 0;
        $sum_items_HT = 0;
        foreach ($ordered_items as $item) {
            //récupère l'information 'commerçant' dans sales_flat_order_item pour les commandes après 11-06-2015
            if ($item->getCommercant() !== null) {
                if ($item->getData('commercant') == $id) {
                    $nb_products += floatval($item->getQtyOrdered());
                    $sum_items += floatval($item->getRowTotalInclTax());
                    $sum_items_HT += floatval($item->getRowTotal());
                }
            } else {
                //Calcul pour les commandes avant le 11-06-2015
              $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                if ($product->getCategoryIds()[2] == $id) {
                    $nb_products += floatval($item->getQtyOrdered());
                    $sum_items += floatval($item->getRowTotalInclTax());
                    $sum_items_HT += floatval($item->getRowTotal());
                }
            }
        }

          //Invoice Totals & Commission Totals
          if ($order->hasInvoices()) {
              //Credit Memo Totals
              if ($order->hasCreditmemos()) {
                  //Retrieve Credit Memo Items
                  if ($order->hasCreditmemos()) {
                      $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToFilter('order_id', $order->getId());
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

                //Pour éviter de calculer deux fois la commission si avoir
                $com_done = false;

                //récupère l'information 'commerçant' dans sales_flat_order_item pour les commandes après 11-06-2015
                $commercant_id = comid_item($item, $order);

                  if ($commercant_id !== null) {
                      if ($commercant_id == $id) {
                          $sum_items_invoice += floatval($item->getRowTotalInclTax());
                          $sum_items_invoice_HT += floatval($item->getRowTotal());
                          $TVApercent = ($sum_items_invoice - $sum_items_invoice_HT) / $sum_items_invoice_HT;

                      //On récupère l'info marge arriere dans la table order
                      $marge_arriere = marge_item($item, $order);

                          if ($order->hasCreditmemos()) {
                              //Compute Credit Memos total for current item (if one exist for individual items => old orders)
                          foreach ($credit_items as $citem) {
                              if ($item->getProductID() == $citem->getProductID()) {
                                  $sum_items_credit += floatval($citem->getRowTotalInclTax());
                                  $sum_items_credit_HT += floatval($citem->getRowTotal());
                                  $sum_commission_HT += (floatval($item->getRowTotal()) - floatval($citem->getRowTotal())) * floatval(str_replace(',', '.', $marge_arriere));
                                  $com_done = true;
                              }
                          }

                          //Compute credit memos from Delivery Refund Orders (new orders)
                          $creditdata = getRefunditemdata($item, 'diffprixfinal');
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
                      //Calcul pour les commandes avant le 11-06-2015
                    $product = Mage::getModel('catalog/product')->load($item->getProductID());

                      if ($product->getCategoryIds()[2] == $id) {
                          $sum_items_invoice += floatval($item->getRowTotalInclTax());
                          $sum_items_invoice_HT += floatval($item->getRowTotal());

                        //Compute Credit Memos total for current item
                        if ($order->hasCreditmemos()) {
                            foreach ($credit_items as $citem) {
                                if ($item->getProductID() == $citem->getProductID()) {
                                    $cproduct = Mage::getModel('catalog/product')->load($citem->getProductID());
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
                  //Parent ID

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
                //Orders
                $sum_items = round($sum_items, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                  $sum_items_HT = round($sum_items_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                  $sum_items_TVA = round($sum_items - $sum_items_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                //Invoice
                if ($order->hasInvoices()) {
                    $sum_items_invoice = round($sum_items_invoice, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                    $sum_items_invoice_HT = round($sum_items_invoice_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                    $sum_items_invoice_TVA = round($sum_items_invoice - $sum_items_invoice_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                } else {
                    $sum_items_invoice = $sum_items_invoice_HT = $sum_items_invoice_TVA = 0;
                }
                //Credit Memo
                if ($order->hasCreditMemos()) {
                    $sum_items_credit = round($sum_items_credit, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                    $sum_items_credit_HT = round($sum_items_credit_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                    $sum_items_credit_TVA = round($sum_items_credit - $sum_items_credit_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                } else {
                    $sum_items_credit = $sum_items_credit_HT = $sum_items_credit_TVA = 0;
                }

                //Commission
                if ($order->hasInvoices()) {
                    $sum_commission = round($sum_commission_HT * (1 + TAX_SERVICE), FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                    $sum_commission_HT = round($sum_commission_HT, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                    $sum_commission_TVA = round($sum_commission_HT * TAX_SERVICE, FLOAT_NUMBER, PHP_ROUND_HALF_UP);
                } else {
                    $sum_commission = $sum_commission_HT = $sum_commission_TVA = 0;
                }
                //Versement commerçant
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
                  //'sum_items_TVA'=>$sum_items_TVA,
                  'sum_items_credit' => $sum_items_credit,
                  'sum_items_credit_HT' => $sum_items_credit_HT,
                  //'sum_items_credit_TVA'=>$sum_items_credit_TVA,
                  'remboursements' => $creditcom,
                  'sum_ticket' => $sum_items - $sum_items_credit,
                  'sum_ticket_HT' => $sum_items_HT - $sum_items_credit_HT,
                  'sum_commission' => $sum_commission,
                  'sum_commission_HT' => $sum_commission_HT,
                  //'sum_commission_TVA'=>$sum_commission_TVA,
                  'sum_versement' => $sum_versement,
                  'sum_versement_HT' => $sum_versement_HT,
                  //'sum_versement_TVA'=>$sum_versement_TVA
                ]);
              }
          }
    }
    }

    return $data;
}

//Used in /var/www/html/apdcdev/delivery/modules/commande/views/validate.php
function validate_item($order_id, $comment)
{
    $magento = connect_magento();
    $soap = $magento[0];
    $session_id = $magento[1];
    $result = $soap->call($session_id, 'sales_order.addComment', array('orderIncrementId' => $order_id, 'status' => 'valid_produit_commercant', 'comment' => $comment));
}

//=> FONCTIONS STATISTIQUES

//Used in /var/www/html/apdcdev/delivery/modules/clients/views/clients_fidelity.phtml
function data_clients($debut, $fin)
{
    $data = [];

  /* Format dates */
  $debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));
    $fin = date('Y-m-d H:i:s', strtotime('-1 second', strtotime('+1 day', strtotime(str_replace('/', '-', $fin)))));

    $orders = Mage::getModel('sales/order')->getCollection()
    ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
    ->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin))
    //->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
    ->addAttributeToSort('increment_id', 'DESC');

  //Get info on delivery date
  $orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id=mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
    $orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id=mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));

    foreach ($orders as $order) {
        $status = $order->getStatusLabel();
        $date_commande = date('d/m/Y', strtotime($order->getCreatedAt()));

        if (!is_null($order->getDdate())) {
            $date_livraison = date('d/m/Y', strtotime($order->getDdate()));
        } else {
            $date_livraison = 'Non Dispo';
        }

        $incrementid = $order->getIncrementId();
        $nom_client = $order->getCustomerName().' '.$order->getCustomerId();
        $coupon = $order->getCouponCode();
        $total_withship = $order->getGrandTotal();
        $frais_livraison = $order->getShippingAmount() + $order->getShippingTaxAmount();
        $total_withoutship = $total_withship - $frais_livraison;
        $comments = getRelevantComments($order);

        array_push($data, [
            'status' => $status,
            'date_commande' => $date_commande,
            'date_livraison' => $date_livraison,
            'increment_id' => $incrementid,
            'nom_client' => $nom_client,
            'Total Produit' => $total_withoutship,
            'Frais livraison' => $frais_livraison,
            'Total' => $total_withship,
            'Coupon Code' => $coupon,
            'Commentaires' => $comments,
        ]);
    }

    return $data;
}

//Refactoring surement possible avec data_facturation
//Used in /var/www/html/apdcdev/delivery/modules/clients/views/clients_coupon.phtml
function data_coupon($debut, $fin)
{
    $data = [];

  /* Format dates */
  $debut = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $debut)));
    $fin = date('Y-m-d H:i:s', strtotime('-1 second', strtotime('+1 day', strtotime(str_replace('/', '-', $fin)))));

    $orders = Mage::getModel('sales/order')->getCollection()
    ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
    ->addAttributeToFilter('created_at', array('from' => $debut, 'to' => $fin))
    ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
    ->addAttributeToSort('increment_id', 'DESC');

  //Get info on delivery date
  // $orders->getSelect()->joinLeft('mwddate_store', 'main_table.entity_id=mwddate_store.sales_order_id', array('mwddate_store.ddate_id'));
  //   $orders->getSelect()->joinLeft('mwddate', 'mwddate_store.ddate_id=mwddate.ddate_id', array('ddate' => 'mwddate.ddate'));

    foreach ($orders as $order) {
        array_push($data, [
            'increment_id' => $order->getIncrementId(),
            'quartier' => $order->getStoreName(),
            'Coupon Code' => $order->getCouponCode(),
        ]);

        arsort($data);
    }

    $data_conso = [];
    foreach ($data as $row) {
        if ($row['Coupon Code']) {
            $data_conso[$row['Coupon Code']][] = $row['increment_id'].' - '.$row['quartier'];
        }
    }

    return $data_conso;
}

/* Used only in
function stats_clients()
*/

function array_columns($array, $column_name)
{
    return array_map(
        function ($element) use ($column_name) {
            return $element[$column_name];
        },
        $array
    );
}

//Used in /var/www/html/apdcdev/delivery/modules/clients/views/clients_stat.phtml
function stats_clients()
{
    $data = [];

    $orders = Mage::getModel('sales/order')->getCollection()
    ->addFieldToFilter('status', array('nin' => $GLOBALS['ORDER_STATUS_NODISPLAY']))
    ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE));
    $orders->getSelect()->columns('COUNT(*) AS nb_order')
    ->columns('SUM(base_grand_total) AS amount_total')
    ->columns('MAX(updated_at) AS last_order')
    ->group('customer_id');

    foreach ($orders as $order) {
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

        $dataadd = Mage::getModel('sales/order_address')->load($order->getShippingAddressId());
        $address = $dataadd->getStreet()[0].' '.$dataadd->getPostcode().' '.$dataadd->getCity();

        $datelo=new DateTime($order->getLastOrder());

        array_push($data, [
            'Nom Client' => $order->getCustomerName(),
            'Nb Commande' => $order->getNbOrder(),
            'Total' => round($order->getAmountTotal(), FLOAT_NUMBER, PHP_ROUND_HALF_UP),
            'Dernière commande' => $datelo->format('d/m/Y'),
            // 'Mail client' => $order->getCustomerEmail(),
            'Rue' => $dataadd->getStreet()[0],
            'Code Postal' => $dataadd->getPostcode(),
            'Date Inscription' => Mage::helper('core')->formatDate($customer->getCreatedAt(), 'short', false),
            'Créé dans' => $customer->getCreatedIn(),
        ]);
    }

    //Add customer who never ordered
    $customers = Mage::getModel('customer/customer')
    ->getCollection()
    ->addAttributeToSelect('*');

    foreach ($customers as $customer) {
        $key = array_search($customer->getEmail(), array_columns($data, 'Mail client'));

        if ($key == false) {
            array_push($data, [
                'Nom Client' => $customer->getFirstname().' '.$customer->getLastname(),
                'Nb Commande' => 0,
                'Total' => 0,
                'Dernière commande' => 'NA',
                // 'Mail client' => $customer->getEmail(),
                'Rue' => "NA",
                'Code Postal' => "NA",
                'Date Inscription' => Mage::helper('core')->formatDate($customer->getCreatedAt(), 'short', false),
                'Créé dans' => $customer->getCreatedIn(),
            ]);
        }
    }

    return $data;
}

//=>FONCTION FOR FUTURE ORDER RATING MODULE

//used in histogramme
function getNotes()
{
    $notationClient = Mage::getSingleton('pmainguet_emailclient/notation')->getCollection();
    $result = array();
    foreach ($notationClient as    $n) {
        $note = $n->getNote();
        $orderid = $n->getOrderId();
        $result[$orderid] = $note;
    }

    return $result;
}

function histogramme()
{
    $notes = getNotes();
    $result = array();

    foreach ($notes as $id => $n) {
        if (!array_key_exists($n, $result)) {
            $result[$n] = 1;
        } else {
            $result[$n] += 1;
        }
    }

    return json_encode($result);
}
