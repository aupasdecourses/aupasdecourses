<?php

namespace Tests\Apdc\ApdcBundle\Services;

use Apdc\ApdcBundle\Services\Magento;
use PHPUnit\Framework\TestCase;

class MagentoTest extends TestCase
{
	private $magento;
	private $fakeOrders;
	private $from;
	private $to;
	private $merchantId;
	private $orderId;

	public function setUp()
	{
		$this->magento = new Magento();
	
		$this->fakeOrders = [
			'2018000001' => [
				'id' 			=> '2018000001',
				'first_name'	=> 'Donald',
				'last_name'		=> 'Duck',
				'products'		=> [],
			],
			'2018000002' => [
				'id'			=> '2018000002',
				'first_name'	=> 'Mickey',
				'last_name'		=> 'Mouse',
				'products'		=> [
					0 => [
						'nom'				=> 'Roti de Veau',
						'prix_unitaire'		=> 15.00,
						'quantite'			=> 2.0,
						'prix_total'		=> 30.00,
						'nom_commercant'	=> 'Boucherie Saint Médard',
					],
					1 => [
						'nom'				=> 'Demi Chaource',
						'prix_unitaire'		=> 5.0,
						'quantite'			=> 3.0,
						'prix_total'		=> 15.00,
						'nom_commercant'	=> 'Laiterie Gilbert',
					],
				],
			],
		];

		$this->from 		= "2000-01-01 00:00:00";
		$this->to 			= "3000-12-31 23:59:59";
		$this->merchantId	= -1;
		$this->orderId		= -1;
	}

	public function tearDown()
	{
		$this->magento = null;

		unset($this->fakeOrders, $this->from, $this->to, $this->merchantId, $this->orderId);
	}

	public function testGetOrders()
	{
		$orders = $this->magento->getOrders();
	}
}