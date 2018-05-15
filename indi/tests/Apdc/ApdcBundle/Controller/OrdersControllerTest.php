<?php

namespace Tests\Apdc\ApdcBundle\Controller;

class OrdersControllerTest extends AbstractControllerTest
{

	/**
	 *	Test of Apdc\ApdcBundle\Controller\OrdersController::indexAction()
	 */
	public function testIndex()
	{
		$crawler = $this->client->request('GET', '/orders/');

		var_dump($crawler);
	}

	/**
	 *	Test of Apdc\ApdcBundle\Controller\OrdersController::ordersOneAction()
	 */
	public function testOrdersOne()
	{

	}

	/**
	 *	Test of Apdc\ApdcBundle\Controller\OrdersController::ordersAllAction()
	 */
	public function testOrdersAll()
	{

	}
}