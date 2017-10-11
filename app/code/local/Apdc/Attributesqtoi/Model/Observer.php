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
		$quoteItem->setProduitFragile($product->getProduitFragile());
    }

}
