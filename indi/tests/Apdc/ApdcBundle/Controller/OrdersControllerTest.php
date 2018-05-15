<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class OrdersControllerTest extends AbstractControllerTest
{

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\OrdersController::indexAction()
	 */
	public function testIndexResponseStatus()
	{
		$this->client->request('GET', '/orders/');

		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\OrdersController::ordersOneAction()
	 */
	public function testOrdersOneResponseStatus()
	{
		$orderId = 2018000001;

		$this->client->request('GET', "/orders/$orderId");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

		$this->client->request('GET', '/orders/foo');
		$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\OrdersController::ordersAllAction()
	 */
	public function testOrdersAllResponseStatus()
	{
		$wrongFrom 	= "01-01-2000";
		$wrongTo 	= "31-12-3000"; 

		$this->client->request('GET', "/orders/$this->from/$this->to");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	
		$this->client->request('GET', "/orders/$wrongFrom/$wrongTo");
		$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test forms of Apdc\ApdcBundle\Controller\OrdersController::indexAction()
	 */
	public function testIndexForms()
	{
		$orderId = 20180000001;
		$crawler = $this->client->request('GET', '/orders/');

		$orders_one_form = $crawler->filter('button#order_id_Search')->form([
			'order_id[id]'	=> $orderId,
		]);

		$orders_all_form = $crawler->filter('button#from_to_Search')->form([
			'from_to[from]' => $this->from,
			'from_to[to]'	=> $this->to,
		], 'POST');

		if ($this->client->submit($orders_one_form) || $this->client->submit($orders_all_form)) {
			$this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
			
			$crawler = $this->client->followRedirect();
			$this->assertNotNull($crawler->getUri());
		}	
	}
}