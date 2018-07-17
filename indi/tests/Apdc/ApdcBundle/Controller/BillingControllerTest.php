<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class BillingControllerTest extends AbstractControllerTest
{

	/**
	 * Test status code of Apdc\ApdcBundle\Controller\BillingController::indexAction()
	 */
	public function testIndexResponseStatus()
	{
		$this->client->request('GET', '/billing/');
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test status code of Apdc\ApdcBundle\Controller\BillingController::verifAction()
	 */
	public function testVerifResponseStatus()
	{
		$this->client->request('GET', '/billing/verif');
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test status code of Apdc\ApdcBundle\Controller\BillingController::detailsAction()
	 */
	public function testDetailsResponseStatus()
	{
		$this->client->request('GET', '/billing/details');
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test status code of Apdc\ApdcBundle\Controller\BillingController::summaryAction()
	 */
	public function testSummaryResponseStatus()
	{
		$this->client->request('GET', '/billing/summary');
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test status code of Apdc\ApdcBundle\Controller\BillingController::billingOneAction()
	 */
	public function testBillingOneResponseStatus()
	{
		$this->client->request('GET', "/billing/$this->billingComId");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}
}