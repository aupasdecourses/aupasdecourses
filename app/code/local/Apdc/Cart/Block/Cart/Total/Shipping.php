<?php
/* @author Pierre Mainguet*/
class Apdc_Cart_Block_Cart_Total_Shipping extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'apdccart/cart/total/shipping.phtml';

    /**
     * Check if shipping is free.
     *
     * @return bool
     */
    public function isFreeShipping()
    {
        return (float) $this->getTotal()->getValue() ? false : true;
    }

    public function getVAT()
    {
        $store = Mage::app()->getStore();
        $taxCalculation = Mage::getModel('tax/calculation');
        $request = $taxCalculation->getRateRequest(null, null, null, $store);
        $taxRateId = Mage::getStoreConfig('tax/classes/shipping_tax_class', $store);

        //taxRateId is the same model id as product tax classes, so you can do this:
        $percent = $taxCalculation->getRate($request->setProductClassId($taxRateId));

        return $percent;
    }
}
