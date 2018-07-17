<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class StoresControllerTest extends AbstractControllerTest
{

	/**
	*	Test status code of Apdc\ApdcBundle\Controller\StoresController::indexAction()
	*/
	public function testIndexResponseStatus()
	{
		$this->client->request('GET', '/stores/');

		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	*	Test status code of Apdc\ApdcBundle\Controller\StoresController::storesAllAction()
	*/
	public function testStoresAllResponseStatus()
	{
		$this->client->request('GET', "/stores/$this->from/$this->to");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

		$this->client->request('GET', "/stores/$this->wrongFrom/$this->wrongTo");
		$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Process form of all StoresController actions
	 */
	private function processStoresForm($crawler)
	{
		$stores_all_form = $crawler->filter('button#from_to_Search')->form([
			'from_to[from]'	=> $this->from,
			'from_to[to]'	=> $this->to,
		]);

		$this->client->submit($stores_all_form);
		$this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

		$crawler = $this->client->followRedirect();
		$this->assertNotNull($crawler->getUri());
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\StoresController::indexAction()
	 */
	public function testIndexForm()
	{
		$crawler = $this->client->request('GET', '/stores/');

		$this->processStoresForm($crawler);
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\StoresController::storesAllAction()
	 */
	public function testStoresAllForm()
	{
		$crawler = $this->client->request('GET', "/stores/$this->from/$this->to");

		$this->processStoresForm($crawler);
	}
}