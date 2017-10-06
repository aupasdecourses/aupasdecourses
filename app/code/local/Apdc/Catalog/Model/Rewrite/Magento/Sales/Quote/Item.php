<?php

class Apdc_Catalog_Model_Rewrite_Magento_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function checkData()
    {
        parent::checkData();

        $availability = Mage::helper('apdc_catalog/product_availability')->getAvailability($this->getProduct());
        if (!$availability['is_available']) {
            $this->setHasError(true);
            $this->setMessage($availability['message']);
            $this->getQuote()->setHasError(true)
                ->addMessage(Mage::helper('sales')->__('Certains produits ne sont pas disponible pour le créneau de livraison que vous avez sélectionné.'));
        }
        $this->setAvailability($availability);

        return $this;
    }
}
