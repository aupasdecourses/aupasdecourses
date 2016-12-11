<?php 
/*
* @author Pierre Mainguet
*/
class Apdc_Attributesqtoi_Model_Observer extends Varien_Object
{
    public function salesQuoteItemSetCustomAttribute($observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $quoteItem->setCommercant($product->getCommercant());
        $quoteItem->setMargeArriere($product->getMargeArriere());
        $quoteItem->setPrixKiloSite($product->getPrixKiloSite());
        $quoteItem->setShortDescription($product->getShortDescription());
    }

    public function deliverycheck($observer){
        try{
            $orderItem = $observer->getOrderItem();
            $quoteItem = $observer->getEvent()->getItem();
            $ddate=Mage::getSingleton('core/session')->getDdate();

            if(isset($ddate)){
                $timestamp = strtotime($ddate);
                $day = date('w', $timestamp);
                $helper=Mage::helper('apdc_checkout');
                $name=$helper->getCommercantname($quoteItem);
                $delivery_days=$helper->getCommercantcat($name);
                $delivery_days = str_replace(' ', '', $delivery_days);
                $delivery_days=explode(",", $delivery_days);
                $days=$helper->convertdaysinnb($delivery_days);
                if(!in_array($day,$days)){
                    if(count($days)==0){
                        $orderItem->setDeliveryCheck('Non disponible cette semaine');
                    }else{
                        $orderItem->setDeliveryCheck('Non disponible pour ce jour de livraison');
                    }
                }else{
                    $orderItem->setDeliveryCheck('OK');
                }
            }else{
                //$orderItem->setDeliveryCheck('Pas de date');
            }
        }catch(Exception $e){
            Mage::log('Error deliverycheck observer',null,'debug.log');
        }
    }
}