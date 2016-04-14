<?php
/**
 * @author Pierre Mainguet
 * @copyright Copyright (c) 2016 Pierre Mainguet - mainguetpierre@gmail.com
 * @package Pmainguet_Delivery
 */
class Pmainguet_Delivery_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function check_amorderattach($orderid){
		  $field=Mage::getModel('amorderattach/order_field');
		  //Check if entity exists in database
		  $check=$field->getCollection()->addFieldToFilter('order_id', $orderid)->getFirstItem()->getId();
		  if($check==NULL){
		     $field->setData('order_id',intval($orderid));
		  }else{
		     $field->load($orderid, 'order_id');
		  }

		  return $field;
	}

	public function json_unicodechar($json){
   		return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", $json);
	}

}