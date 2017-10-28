<?php

class Apdc_Catalog_Model_Rewrite_Magento_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function checkData()
    {
        parent::checkData();

        $availability = Mage::helper('apdc_catalog/product_availability')->getAvailability($this->getProduct());
        if (!$availability['is_available_for_sale']) {
            $this->setHasError(true);
            $this->setMessage($availability['message']);
            if (!$availability['can_order']) {
                $message = Mage::helper('sales')->__('Certains produits ne peuvent pas être commandés aujourd\'hui');
            } else {
                $message = Mage::helper('sales')->__('Certains produits ne sont pas disponible pour le créneau de livraison que vous avez sélectionné.');
            }
            $this->getQuote()->setHasError(true)
                ->addMessage($message);
        }
        $this->setAvailability($availability);

        return $this;
    }
}
