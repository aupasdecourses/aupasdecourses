<?php
/**
 * @author Pierre Mainguet
 * @copyright Copyright (c) 2017 Pierre Mainguet - mainguetpierre@gmail.com
 * @package Apdc Notation
 */
class Apdc_Notation_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function noteExists($orderId){
	    $notationClient = Mage::getSingleton('apdc_notation/notation');
	   	$notationClient->load($orderId, 'order_id');

	    if ($notationClient->getId()) {
	        return true;
	    }
	    return false;
	}
}