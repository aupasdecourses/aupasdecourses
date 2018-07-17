<?php

namespace Tests\Magento\Models;

class CatalogProductTest extends AbstractModelTest
{
	private $catalog_product_data;

	public function setUp()
	{
		parent::setUp();
		$this->catalog_product_data = [
			[
				'entity_id' 		=> 12345,
				'entity_type_id'	=> 4,
				'attribute_set_id'	=> 4,
				'type_id'			=> 'simple',
				'sku'				=> 'AAA01-ABC-1234',
				'has_options'		=> 0,
				'required_options'	=> 0,
				'created_at'		=> '2018-01-01 00:00:00',
				'updated_at'		=> '2018-01-01 12:45:57',
			],
			[
				'entity_id' 		=> 56789,
				'entity_type_id'	=> 4,
				'attribute_set_id'	=> 4,
				'type_id'			=> 'simple',
				'sku'				=> 'BBB05-DEF-5678',
				'has_options'		=> 1,
				'required_options'	=> 0,
				'created_at'		=> '2018-02-10 15:55:23',
				'updated_at'		=> '2018-02-10 16:12:45',
			]
		];
	}

	public function tearDown()
	{
		$this->catalog_product_data = null;
	}

	/**
	 *	Mock catalog/product table
	 */
	public function testCatalogProductMock()
	{
		$products = $this->createMock($this->catalog_product_class);
		
		$products->method('getCollection')->willReturn($this->catalog_product_data);

		foreach ($products->getCollection() as $product) {
			$this->assertInternalType('int', $product['entity_id']);
			$this->assertInternalType('int', $product['entity_type_id']);
			$this->assertInternalType('int', $product['attribute_set_id']);
			$this->assertEquals('simple', $product['type_id']);
			$this->assertRegExp("/^[A-Za-z0-9]{5}-[A-Za-z]{3}-[0-9]{4}/", $product['sku']);
			$this->assertInternalType('int', $product['has_options']);
			$this->assertInternalType('int', $product['required_options']);
			$this->assertRegExp("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/", $product['created_at']);
			$this->assertRegExp("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/", $product['updated_at']);
		}
	}
}