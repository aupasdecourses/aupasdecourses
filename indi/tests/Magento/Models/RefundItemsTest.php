<?php

namespace Tests\Magento\Models;

class RefundItemsTest extends AbstractModelTest
{
	private $refund_items_data;

	public function setUp()
	{
		parent::setUp();
		$this->refund_items_data = [
			[
				'refund_item_id'		=> 22473,
				'order_item_id'			=> 194,
				'item_name'				=> 'Roti de Veau',
				'prix_initial'			=> 28.9900,
				'prix_final'			=> 25.9900,
				'diffprixfinal'			=> 3.0000,
				'prix_commercant'		=> 25.99,
				'diffprixcommercant'	=> 3.0,
			],
			[
				'refund_item_id'		=> 22483,
				'order_item_id'			=> 201,
				'item_name'				=> 'Domaine de Cardon - Bourgogne 2015 - Rouge',
				'prix_initial'			=> 31.9800,
				'prix_final'			=> 31.9500,
				'diffprixfinal'			=> 0.0300,
				'prix_commercant'		=> 31.95,
				'diffprixcommercant'	=> 0.03,
			]
		];
	}

	public function tearDown()
	{
		$this->refund_items_data = null;
	}

	/**
	 *	Mock pmainguet_delivery/refund_items table
	 */
	public function testRefundItemsMock()
	{
		$items = $this->createMock($this->refund_items_class);

		$items->method('getCollection')->willReturn($this->refund_items_data);

		foreach ($items->getCollection() as $item) {
			$this->assertInternalType('int', $item['refund_item_id']);
			$this->assertInternalType('int', $item['order_item_id']);
			
			$this->assertInternalType('string', $item['item_name']);
			$this->assertNotNull($item['item_name']);

			$this->assertInternalType('float', $item['prix_initial']);
			$this->assertNotNull($item['prix_initial']);

			$this->assertInternalType('float', $item['prix_final']);
			$this->assertNotNull($item['prix_final']);

			$this->assertInternalType('float', $item['diffprixfinal']);
			$this->assertNotNull($item['diffprixfinal']);

			$this->assertInternalType('float', $item['prix_commercant']);
			$this->assertNotNull($item['prix_commercant']);

			$this->assertInternalType('float', $item['diffprixcommercant']);
			$this->assertNotNull($item['diffprixcommercant']);
		}
	}
}