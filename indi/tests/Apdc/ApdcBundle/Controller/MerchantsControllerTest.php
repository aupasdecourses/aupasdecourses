<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class MerchantsControllerTest extends AbstractControllerTest
{

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\MerchantsController::indexAction()	
	 */
	// public function testIndexResponseStatus()
	// {
	// 	$this->client->request('GET', '/merchants/');

	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\MerchantsController::merchantsOneAction()
	 */
	// public function testMerchantsOneResponseStatus()
	// {
	// 	$this->client->request('GET', "/merchants/$this->merchantId/$this->from/$this->to");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

	// 	$this->client->request('GET', "/merchants/-1/$this->from/$this->to");
	// 	$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

	// 	$this->client->request('GET', "/merchants/$this->merchantId/$this->wrongFrom/$this->wrongTo");
	// 	$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\MerchantsController::merchantsOneAction()
	 */
	// public function testMerchantsAllResponseStatus()
	// {
	// 	$this->client->request('GET', "/merchants/$this->from/$this->to");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

	// 	$this->client->request('GET', "/merchants/$this->wrongFrom/$this->wrongTo");
	// 	$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Process merchantsOne form of all MerchantsController actions
	 */
	private function processMerchantsOneForm($crawler)
	{
		$merchants_one_form = $crawler->filter('button#from_to_merchant_Search')->form([
			'from_to_merchant[from]' 		=> $this->from,
			'from_to_merchant[to]'			=> $this->to,
			'from_to_merchant[merchant]'	=> $this->merchantId,
		]);

		if ($this->client->submit($merchants_one_form)) {
			$this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

			$crawler = $this->client->followRedirect();
			$this->assertNotNull($crawler->getUri());
		}
	}

	/**
	 *	Process merchantsAll form of all MerchantsController actions
	 */
	private function processMerchantsAllForm($crawler)
	{
		$merchants_all_form = $crawler->filter('button#from_to_merchant_Search')->form([
			'from_to_merchant[from]' 		=> $this->from,
			'from_to_merchant[to]'			=> $this->to,
			'from_to_merchant[merchant]'	=> -1,
		]);

		if ($this->client->submit($merchants_all_form)) {
			$this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

			$crawler = $this->client->followRedirect();
			$this->assertNotNull($crawler->getUri());
		}
	}

	/**
	 *	Test forms of Apdc\ApdcBundle\Controller\MerchantsController::indexAction()
	 */
	public function testIndexForms()
	{
		$crawler = $this->client->request('GET', '/merchants/');

		$this->processMerchantsOneForm($crawler);
		$this->processMerchantsAllForm($crawler);
	}
}