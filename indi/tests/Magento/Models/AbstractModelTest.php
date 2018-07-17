<?php

namespace Tests\Magento\Models;

include_once(realpath(dirname(__FILE__)) . '/../../../../app/Mage.php');
use PHPUnit\Framework\TestCase;

class AbstractModelTest extends TestCase
{
	protected $catalog_product_class;
	protected $amasty_order_field_class;
	protected $refund_items_class;

	public function setUp()
	{
		\Mage::app();

		$this->catalog_product_class 			= get_class(\Mage::getModel('catalog/product'));
		$this->amasty_order_field_class 		= get_class(\Mage::getModel('amorderattach/order_field'));
		$this->refund_items_class 				= get_class(\Mage::getModel('pmainguet_delivery/refund_items'));
	}

	public function tearDown()
	{
		$this->catalog_product_class 		= null;
		$this->amasty_order_field_class		= null;
		$this->refund_items_class 			= null;
	}
}