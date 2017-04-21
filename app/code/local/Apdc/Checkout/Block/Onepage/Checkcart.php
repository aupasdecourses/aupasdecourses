<?php

class Apdc_Checkout_Block_Onepage_Checkcart extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData("checkcart", array(
            "label"     => Mage::helper("checkout")->__("Check cart"),
            "is_show"   => $this->isShow()
        ));
        parent::_construct();
    }
	
	public function isShow() {
		return true;
	}
	
}