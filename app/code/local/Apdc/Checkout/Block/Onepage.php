<?php
/**
/ @author Pierre Mainguet
 */
class Apdc_Checkout_Block_Onepage extends MW_Ddate_Block_Onepage
{
    public function getSteps()
    {
        $steps = array();
        if (!$this->isCustomerLoggedIn()) {
            $steps['login'] = $this->getCheckout()->getStepData('login');
        }

        //Change steps order and remove "shipping" and "payment"
        $stepCodes = array('billing', 'shipping_method','ddate', 'payment');
        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }

        return $steps;
    }
}
