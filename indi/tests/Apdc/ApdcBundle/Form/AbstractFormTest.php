<?php

namespace Tests\Apdc\ApdcBundle\Form;

use Symfony\Component\Form\Test\TypeTestCase;

use Apdc\ApdcBundle\Entity\OrderId;
use Apdc\ApdcBundle\Entity\From;
use Apdc\ApdcBundle\Entity\FromTo;
use Apdc\ApdcBundle\Entity\FromToMerchant;

use Apdc\ApdcBundle\Form\OrderId as OrderIdType;
use Apdc\ApdcBundle\Form\From as FromType;
use Apdc\ApdcBundle\Form\FromTo as FromToType;
use Apdc\ApdcBundle\Form\FromToMerchant as FromToMerchantType;

class AbstractFormTest extends TypeTestCase
{

	protected $orderId;
	protected $from;
	protected $fromTo;
	protected $fromToMerchant;

	protected $data;

	protected function setUp()
	{
		parent::setUp();

		$this->orderId 			= new OrderId();
		$this->from 			= new From();
		$this->fromTo 			= new FromTo();
		$this->fromToMerchant 	= new FromToMerchant();

		$this->data = [
			'id'		=> '2018000001',
			'from' 		=> '2018-01-01',
			'to'		=> '2018-01-31',
			'merchant' 	=> [
				'Au Verger Fleuri' 			=> 1391,
				'Boucherie des Moines'		=> 7,
				'Fromagerie Cantin'			=> 1383,
				'Poissonnerie Collachot'	=> 1740
			],
			'Search'	=> '',
		];
	}

	protected function tearDown()
	{
		parent::tearDown();

		$this->orderId 			= null;
		$this->from 			= null;
		$this->fromTo 			= null;
		$this->fromToMerchant 	= null;

		$this->data = null;
	}

	protected function getOrderIdType()
	{
		return OrderIdType::class;
	}

	protected function getFromType()
	{
		return FromType::class;
	}

	protected function getFromToType()
	{
		return FromToType::class;
	}

	protected function getFromToMerchantType()
	{
		return FromToMerchantType::class;
	}
}