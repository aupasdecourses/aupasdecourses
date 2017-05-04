<?php

class Apdc_Delivery_Model_Resource_Geocode_Customers_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
	protected function _construct()
	{
		$this->_init('pmainguet_delivery/geocode_customers');
	}
}
