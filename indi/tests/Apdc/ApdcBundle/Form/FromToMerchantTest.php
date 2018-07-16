<?php

namespace Tests\Apdc\ApdcBundle\Form;

class FromToMerchantTest extends AbstractFormTest
{
	/**
	 *	Test validity of Apdc\ApdcBundle\Form\FromToMerchant
	 */
	public function testFromToMerchantTypeValidity()
	{
		$fromToMerchantType = $this->factory->create($this->getFromToMerchantType(), $this->fromToMerchant);
		$fromToMerchantType->submit($this->data);

		$this->assertTrue($fromToMerchantType->isSynchronized());

		$children = $fromToMerchantType->createView()->children;

		foreach (array_keys($this->data) as $key) {
			if ($key === 'from' || $key === 'to' || $key === 'merchant' || $key === 'Search') {
				$this->assertArrayHasKey($key, $children);
			} else {
				$this->assertArrayNotHasKey($key, $children);
			}
		}
	}
}