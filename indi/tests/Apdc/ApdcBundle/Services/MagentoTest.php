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

		$this->from 		= "2018-05-01 00:00:00";
		$this->to 			= "2018-05-31 23:59:59";
		$this->merchantId 	= 72; // Maree 17
		$this->orderId 		= 2018000173;
	}

	public function tearDown()
	{
		$this->magento = null;

		unset($this->fakeOrders, $this->from, $this->to, $this->merchantId, $this->orderId);
	}

	/**
	 * 	Test return type of Apdc\ApdcBundle\Services\Magento::getOrders()
	 */
	public function testGetOrdersReturnType()
	{
		$orders = $this->magento->getOrders($this->from, $this->to);
		
		$this->assertInternalType('array', $orders);
	}

	/**
	 * 	Test return type of Apdc\ApdcBundle\Services\Magento::getMerchantsByStore()
	 */
	public function testGetMerchantsByStoreReturnType()
	{
		$allMerchants 		= $this->magento->getMerchantsByStore();
		$specificMerchant	= $this->magento->getMerchantsByStore($this->merchantId);

		$this->assertInternalType('array', $allMerchants);
		$this->assertInternalType('array', $specificMerchant);
	}

	/**
	 * 	Test return type of Apdc\ApdcBundle\Services\Magento::getOrdersByStore()
	 */
	public function testGetOrdersByStoreReturnType()
	{
		$orders = $this->magento->getOrdersByStore();

		$this->assertInternalType('array', $orders);
	}

	/**
	 * 	Test return type of Apdc\ApdcBundle\Services\Magento::getOrdersByStoreByMerchants()
	 */
	public function testGetOrdersByStoreByMerchantsReturnType()
	{
		$orders = $this->magento->getOrdersByStoreByMerchants(-1, $this->from, $this->to);
		
		$this->assertInternalType('array', $orders);
	}

	/**
	 * 	Test return type of Apdc\ApdcBundle\Services\Magento::getMerchants()
	 */
	public function testGetMerchantsReturnType()
	{
		$allMerchants 		= $this->magento->getMerchants();
		$specificMerchant 	= $this->magento->getMerchants($this->merchantId);

		$this->assertInternalType('array', $allMerchants);
		$this->assertInternalType('array', $specificMerchant);
	}

	/**
	 * 	Test return type of Apdc\ApdcBundle\Services\Magento::getMerchantsOrders()
	 */
	public function testGetMerchantsOrdersReturnType()
	{
		$orders = $this->magento->getMerchantsOrders($this->merchantId);
		
		$this->assertInternalType('array', $orders);
	}

	/**
	 *	Test return type of Apdc\ApdcBundle\Services\Magento::getOrderByMerchants()
	 */
	public function testGetOrderByMerchantsReturnType()
	{
		$order = $this->magento->getOrderByMerchants($this->orderId);

		$this->assertInternalType('array', $order);
	}

	/**
	 *	Test return type of Apdc\ApdcBundle\Services\Magento::getRefunds()
	 */
	public function testGetRefundsReturnType()
	{
		$refunds = $this->magento->getRefunds($this->orderId);

		$this->assertInternalType('array', $refunds);
	}
}