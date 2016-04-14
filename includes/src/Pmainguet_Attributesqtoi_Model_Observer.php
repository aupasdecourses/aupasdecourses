<?php 

class Pmainguet_Attributesqtoi_Model_Observer extends Varien_Object
{
    public function salesQuoteItemSetCustomAttribute($observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $quoteItem->setCommercant($product->getCommercant());
        $quoteItem->setMargeArriere($product->getMargeArriere());
        $quoteItem->setCommercantId($product->getCategoryIds()[2]);
        $quoteItem->setPrixKiloSite($product->getPrixKiloSite());
    }
}