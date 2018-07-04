<?php

namespace Tests\Apdc\ApdcBundle\Form;

class FromTest extends AbstractFormTest
{
	/**
	 *	Test validity of Apdc\ApdcBundle\Form\From
	 */
	public function testFromTypeValidity()
	{
		$fromType = $this->factory->create($this->getFromType(), $this->from);
		$fromType->submit($this->data);

		// Check if form compiles
		$this->assertTrue($fromType->isSynchronized());

		$children = $fromType->createView()->children;

		// Check the creation of the form with valid/invalid data
		foreach (array_keys($this->data) as $key) {
			if ($key === 'from') {
				$this->assertArrayHasKey($key, $children);
			} else {
				$this->assertArrayNotHasKey($key, $children);
			}
		}
	}
}