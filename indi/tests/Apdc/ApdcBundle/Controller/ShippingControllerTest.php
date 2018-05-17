<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class ShippingControllerTest extends AbstractControllerTest
{
	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\ShippingController::indexAction()
	 */
	public function testIndexResponseStatus()
	{
		$this->client->request('GET', '/shipping/');

		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\ShippingController::shippingAllAction()
	 */
	public function testShippingAllResponseStatus()
	{
		$this->client->request('GET', "/shipping/$this->from");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

		$this->client->request('GET', "/shipping/$this->wrongFrom");
		$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
	}

	/**
	 * 	Process form of all ShippingController actions
	 */
	private function processShippingForm($crawler)
	{
		$shipping_all_form = $crawler->filter('button#from_Search')->form([
			'from[from]' => $this->from,
		]);

		$this->client->submit($shipping_all_form);
		$this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

		$crawler = $this->client->followRedirect();
		$this->assertNotNull($crawler->getUri());
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\ShippingController::indexAction()
	 */
	public function testIndexForm()
	{
		$crawler = $this->client->request('GET', '/shipping/');

		$this->processShippingForm($crawler);
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\ShippingController::shippingAllAction()
	 */
	public function testShippingAllForm()
	{
		$crawler = $this->client->request('GET', "/shipping/$this->from");

		$this->processShippingForm($crawler);
	}
}