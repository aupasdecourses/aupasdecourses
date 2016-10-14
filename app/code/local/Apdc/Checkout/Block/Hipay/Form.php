<?php
/**
/ @author Pierre Mainguet
 */ 

class Apdc_Checkout_Block_HiPay_Form extends Mage_Payment_Block_Form
{
	protected function _construct()
	{
		$this->setTemplate('apdc_checkout/hipay/form.phtml');
		parent::_construct();
	}
}