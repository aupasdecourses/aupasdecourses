<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class PickingControllerTest extends AbstractControllerTest
{
	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\PickingController::indexAction()
	 */
	public function testIndexResponseStatus()
	{
		$this->client->request('GET', '/picking/');

		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\PickingController::pickingAllAction()
	 */
	public function testPickingAllResponseStatus()
	{
		$this->client->request('GET', "/picking/$this->from");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

		$this->client->request('GET', "/picking/$this->wrongFrom");
		$this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
	}

	/**
	 * 	Process form of all PickingController actions
	 */
	private function processPickingForm($crawler)
	{
		$picking_all_form = $crawler->filter('button#from_Search')->form([
			'from[from]' => $this->from,
		]);

		$this->client->submit($picking_all_form);
		$this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

		$crawler = $this->client->followRedirect();
		$this->assertNotNull($crawler->getUri());
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\PickingController::indexAction()
	 */
	public function testIndexForm()
	{
		$crawler = $this->client->request('GET', '/picking/');

		$this->processPickingForm($crawler);
	}

	/**
	 *	Test form of Apdc\ApdcBundle\Controller\PickingController::pickingAllAction()
	 */
	public function testPickingAllForm()
	{
		$crawler = $this->client->request('GET', "/picking/$this->from");

		$this->processPickingForm($crawler);
	}
}