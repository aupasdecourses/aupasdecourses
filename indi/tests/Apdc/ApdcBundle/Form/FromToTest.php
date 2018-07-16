<?php

namespace Tests\Apdc\ApdcBundle\Form;

class FromToTest extends AbstractFormTest
{
	/**
	 *	Test validity of Apdc\ApdcBundle\Form\FromTo
	 */
	public function testFromToTypeValidity()
	{
		$fromToType = $this->factory->create($this->getFromToType(), $this->fromTo);
		$fromToType->submit($this->data);

		$this->assertTrue($fromToType->isSynchronized());

		$children = $fromToType->createView()->children;

		foreach (array_keys($this->data) as $key) {
			if ($key === 'from' || $key === 'to' || $key === 'Search') {
				$this->assertArrayHasKey($key, $children);
			} else {
				$this->assertArrayNotHasKey($key, $children);
			}
		}
	}
}