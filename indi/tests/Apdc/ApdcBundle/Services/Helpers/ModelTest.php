<?php

namespace Tests\Apdc\ApdcBundle\Services\Helpers;

use Apdc\ApdcBundle\Services\Magento;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
	private $magento;

	private $catalogProductClass;
	private $catalogProductData;

	public function setUp()
	{
		$this->magento = new Magento();

		$this->catalogProductClass = get_class(\Mage::getModel('catalog/product'));
		$this->catalogProductData = [
			'getEntityId' 	=> 99999,
			'getOptions'	=> 0,
			'getSku'		=> 'AAA01-ABC-1234',
		];
	}

	public function tearDown()
	{
		$this->magento 				= null;

		$this->catalogProductClass 	= null;
		$this->catalogProductData 	= null;
	}

	/**
	 *	Test mocking catalog/product table
	 */
	public function testCatalogProductMock()
	{
		$product = $this->createMock($this->catalogProductClass);
		
		foreach ($this->catalogProductData as $key => $value) {
			$product->method($key)->willReturn($value);
		}

		$this->assertInternalType("int", $product->getEntityId());
		$this->assertTrue($product->getOptions() == false);
		$this->assertEquals('AAA01-ABC-1234', $product->getSku());
	}
}