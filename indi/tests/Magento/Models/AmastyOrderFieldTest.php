<?php

namespace Tests\Magento\Models;

class AmastyOrderFieldTest extends AbstractModelTest
{
	private $amasty_order_field_data;

	public function setUp()
	{
		parent::setUp();
		$this->amasty_order_field_data = [
			[
				'entity_id'					=> 12345,
				'order_id'					=> 4567,
				'ticket_commercant'			=> '2018000001/2018000001.jpeg',
				'remboursements'			=> null,
				'commentaires_commercant'	=> 'Comment commercant commande 001',
				'commentaires_client'		=> 'Comment client commande 001',
				'upload'					=> 'joker',
				'input'						=> 'done',
				'digest'					=> 'done',
				'refund'					=> 'no_refund',
				'refund_shipping'			=> null,
				'closure'					=> 'done',
			],
			[
				'entity_id'					=> 98765,
				'order_id'					=> 6789,
				'ticket_commercant'			=> '2018000002/2018000002.jpeg',
				'remboursements'			=> 'Manque de 5€ pour Foo.<br />Excès de 3€ pour Bar',
				'commentaires_commercant'	=> null,
				'commentaires_client'		=> null,
				'upload'					=> 'done',
				'input'						=> 'done',
				'digest'					=> 'done',
				'refund'					=> 'done_with_hipay',
				'refund_shipping'			=> true,
				'closure'					=> 'done',
			]
		];
	}

	public function tearDown()
	{
		$this->amasty_order_field_data = null;
	}

	/**
	 * 	Mock amorderattach/order_field table
	 */
	public function testSalesOrderMock()
	{
		$orders = $this->createMock($this->amasty_order_field_class);

		$orders->method('getCollection')->willReturn($this->amasty_order_field_data);
	
		foreach ($orders->getCollection() as $order) {
			$this->assertInternalType('int', $order['entity_id']);
			$this->assertInternalType('int', $order['order_id']);
			$this->assertRegExp("/^[0-9]{9}/", $order['ticket_commercant']);
			$this->assertTrue(is_string($order['remboursements']) || is_null($order['remboursements']));
			$this->assertTrue(is_string($order['commentaires_commercant']) || is_null($order['commentaires_commercant']));
			$this->assertTrue(is_string($order['commentaires_client']) || is_null($order['commentaires_client']));
			$this->assertInternalType('string', $order['upload']);
			$this->assertEquals('done', $order['input']);
			$this->assertEquals('done', $order['digest']);
			$this->assertInternalType('string', $order['refund']);
			$this->assertTrue($order['refund_shipping'] || is_null($order['refund_shipping']));
			$this->assertEquals('done', $order['closure']);
		}
	}
}