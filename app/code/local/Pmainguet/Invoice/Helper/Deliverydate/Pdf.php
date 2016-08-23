<?php
/**
* @author Amasty Team
* @copyright Copyright (c) Amasty (http://www.amasty.com)
* @package Amasty_Deliverydate
*/
class Pmainguet_Invoice_Helper_Deliverydate_Pdf extends Amasty_Deliverydate_Helper_Pdf
{
    // public function addDeliverydate(&$page, $obj, $control)
    // {

    //     $lineheight=14;

    //     if ($obj instanceof Mage_Sales_Model_Order) {
    //         $shipment = null;
    //         $order = $obj;
    //         $currentStore = $order->getStoreId();
    //         $fields = Mage::helper('amdeliverydate')->whatShow('invoice_pdf', $currentStore, 'include');
    //     } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
    //         $shipment = $obj;
    //         $order = $shipment->getOrder();
    //         $currentStore = $order->getStoreId();
    //         $fields = Mage::helper('amdeliverydate')->whatShow('shipment_pdf', $currentStore, 'include');
    //     }
        
    //     if (!Mage::getStoreConfig('amdeliverydate/general/enabled', $currentStore)) {
    //         return ;
    //     }
        
    //     if (!is_array($fields) || empty($fields)) {
    //         return ;
    //     }
        
    //     $deliveryDate = Mage::getModel('amdeliverydate/deliverydate');
    //     $deliveryDate->load($order->getId(), 'order_id');
        
    //     $list = array();
    //     foreach ($fields as $field) {
    //         $value = $deliveryDate->getData($field);
    //         if ('date' == $field) {
    //             $label = $this->__('Delivery Date');
    //             if ('0000-00-00' != $value) {
    //                 $value = date(Mage::helper('amdeliverydate')->getPhpFormat($currentStore), strtotime($value));
    //             } else {
    //                 $value = '';
    //             }
    //         } elseif ('time' == $field) {
    //             $label = $this->__('Delivery Time Interval');
    //         } elseif ('comment' == $field) {
    //             $label = $this->__('Delivery Comments');
    //             $value = htmlentities(preg_replace('/\$/','\\\$', $value), ENT_COMPAT, "UTF-8");
    //             $text = str_replace(array("\r\n", "\n", "\r"), '~~~', $value);
    //             $value = array();
    //             foreach (explode('~~~', $text) as $str) {
    //                 foreach (Mage::helper('core/string')->str_split($str, 120, true, true) as $part) {
    //                     if (empty($part)) {
    //                         continue;
    //                     }
    //                     $value[] = $part;
    //                 }
    //             }
    //         }
    //         if (is_array($value)) {
    //             $list[$label] = $value;
    //         } elseif ($value) {
    //             $list[$label] = $value;
    //         }
    //     }
        
    //     if (empty($list)) {
    //         return ;
    //     }
        
    //     foreach ($list as $label => $value) {
    //         if (is_array($value)) {
    //             $page->drawText($label . ': ', 45, $control->y, 'UTF-8');
    //             foreach ($value as $str) {
    //                 $page->drawText($str, 200, $control->y, 'UTF-8');
    //                 $control->y -= $lineheight;
    //             }
    //         } else {
    //             $page->drawText($label . ': ', 45, $control->y, 'UTF-8');
    //             $page->drawText($value, 200, $control->y, 'UTF-8');
    //             $control->y -= $lineheight;
    //         }
    //     }
        
    //     $control->y -= $lineheight;
    // }
}