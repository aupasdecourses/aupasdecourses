<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class MerchantsControllerTest extends AbstractControllerTest
{

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\MerchantsController::indexAction()	
	 */
	public function testIndexResponseStatus()
	{
		$this->client->request('GET', '/merchants/');

		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\MerchantsController::merchantsOneAction()
	 */
	public function testMerchantsOneResponseStatus()
	{
		for ($i = 0; $i < count($this->lightMerchantsIds); $i++) { 
			$this->client->request('GET', "/merchants/{$this->lightMerchantsIds[$i]}/$this->from/$this->to");
			
			if ($this->lightMerchantsIds[$i] == -1) {
				$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
			} else {
				$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			}	
		}
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\MerchantsController::merchantsOneAction()
	 */
	public function testMerchantsAllResponseStatus()
	{
		$this->client->request('GET', "/merchants/$this->from/$this->to");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

		$this->client->request('GET', "/merchants/$this->wrongFrom/$this->wrongTo");
		$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Process form of all MerchantsController actions
	 */
	private function processMerchantsForm($crawler)
	{
		for ($i = 0; $i < count($this->lightMerchantsIds); $i++) { 
			$merchants_form = $crawler->filter('button#from_to_merchant_Search')->form([
				'from_to_merchant[from]'		=> $this->from,
				'from_to_merchant[to]'			=> $this->to,
				'from_to_merchant[merchant]'	=> $this->lightMerchantsIds[$i],
			]);

			$this->client->submit($merchants_form);

			$this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
			
			$crawler = $this->client->followRedirect();
			$this->assertNotNull($crawler->getUri());
		}
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\MerchantsController::indexAction()
	 */
	public function testIndexForm()
	{
		$crawler = $this->client->request('GET', '/merchants/');

		$this->processMerchantsForm($crawler);
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\MerchantsController::merchantsOneAction()
	 */
	public function testMerchantsOneForm()
	{
		for ($i = 0; $i < count($this->lightMerchantsIds); $i++) { 
			$crawler = $this->client->request('GET', "/merchants/{$this->lightMerchantsIds[$i]}/$this->from/$this->to");
			
			$this->processMerchantsForm($crawler);
		}
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\MerchantsController::merchantsAllAction()
	 */
	public function testMerchantsAllForm()
	{
		$crawler = $this->client->request('GET', "/merchants/$this->from/$this->to");

		$this->processMerchantsForm($crawler);
	}
}