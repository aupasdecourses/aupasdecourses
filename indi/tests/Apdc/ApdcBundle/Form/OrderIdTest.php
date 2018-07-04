<?php

namespace Tests\Apdc\ApdcBundle\Form;

class OrderIdTest extends AbstractFormTest
{
	/**
	 *	Test validity of Apdc\ApdcBundle\Form\OrderId
	 */
	public function testOrderIdTypeValidity()
	{
		$orderIdType = $this->factory->create($this->getOrderIdType(), $this->orderId);
		$orderIdType->submit($this->data);

		$this->assertTrue($orderIdType->isSynchronized());

		$children = $orderIdType->createView()->children;

		foreach (array_keys($this->data) as $key) {
			if ($key === 'id' || $key === 'Search') {
				$this->assertArrayHasKey($key, $children);
			} else {
				$this->assertArrayNotHasKey($key, $children);
			}
		}
	}
}