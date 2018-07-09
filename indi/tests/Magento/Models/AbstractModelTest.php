<?php

namespace Tests\Magento\Models;

include_once(realpath(dirname(__FILE__)) . '/../../../../app/Mage.php');
use PHPUnit\Framework\TestCase;

class AbstractModelTest extends TestCase
{
	protected $catalog_product_class;

	public function setUp()
	{
		\Mage::app();

		$this->catalog_product_class = get_class(\Mage::getModel('catalog/product'));
	}

	public function tearDown()
	{
		$this->catalog_product_class = null;
	}
}