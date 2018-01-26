<?php

class Apdc_Catalog_Model_Api2_Product_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Rest_Admin_V1
{

	protected function _retrieveCollection()
	{
		return Mage::getResourceModel('catalog/product_collection');
	}
}