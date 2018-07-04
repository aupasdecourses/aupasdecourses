<?php

namespace Tests\Apdc\ApdcBundle\Form;

use Symfony\Component\Form\Test\TypeTestCase;

use Apdc\ApdcBundle\Entity\From;
use Apdc\ApdcBundle\Form\From as FromType;

class AbstractFormTest extends TypeTestCase
{

	protected $from;
	protected $data;

	protected function setUp()
	{
		parent::setUp();

		$this->from = new From();
		$this->data = [
			'from' 	=> '2018-01-01',
			'to'	=> '2018-01-31',
		];
	}

	protected function tearDown()
	{
		parent::tearDown();

		$this->from = null;
		$this->data = null;
	}

	protected function getFromType()
	{
		return FromType::class;
	}
}