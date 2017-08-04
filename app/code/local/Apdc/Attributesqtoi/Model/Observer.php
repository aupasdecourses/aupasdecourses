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
                $day = (date('w', $timestamp)==0)?7:date('w', $timestamp);
                $delivery_days = Mage::helper('apdc_catalog/product_available')->getDeliveryDays($quoteItem->getProduct());
                if(!in_array($day,$delivery_days)){
                    if(count($delivery_days)==0){
                        $orderItem->setDeliveryCheck('Non disponible cette semaine');
                    }elseif(count($delivery_days)<4){
                        $orderItem->setDeliveryCheck('Non disponible pour ce jour de livraison');
                    }
                }else{
                    $orderItem->setDeliveryCheck('OK');
                }
            }
        }catch(Exception $e){
            Mage::log('Error deliverycheck observer, see below',null,'deliverycheck.log');
            Mage::log($e->getMessage(),null,'deliverycheck.log');
        }
    }
}
