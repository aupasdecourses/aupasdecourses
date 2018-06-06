<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class RefundControllerTest extends AbstractControllerTest
{

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::indexAction()
	 */
	// public function testIndexResponseStatus()
	// {
	// 	$this->client->request('GET', '/refund');
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

	// 	$this->client->request('GET', "/refund/$this->orderId");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

	// 	$this->client->request('GET', "/refund/-1/$this->from/$this->to");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundUploadAction()
	 */
	// public function testRefundUploadResponseStatus()
	// {
	// 	$this->client->request('GET', "/refund/$this->orderId/upload/");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundInputAction()
	 */
	// public function testRefundInputResponseStatus()
	// {
	// 	$this->client->request('GET', "/refund/$this->orderId/input/");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundDigestAction()
	 */
	// public function testRefundDigestResponseStatus()
	// {
	// 	$this->client->request('GET', "/refund/$this->orderId/digest/");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundFinalAction()
	 */
	// public function testRefundFinalResponseStatus()
	// {
	// 	$this->client->request('GET', "/refund/$this->orderId/final/");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundClosureAction()
	 */
	// public function testRefundClosureResponseStatus()
	// {
	// 	$this->client->request('GET', "/refund/$this->orderId/closure/");
	// 	$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	// }

	/**
	 *	Test previous & next links of Apdc\ApdcBundle\Controller\RefundController::refundUploadAction()
	 */
	public function testRefundUploadNavigationLinks()
	{
		$crawler = $this->client->request('GET', "/refund/$this->orderId/upload/");

		$previous_link = $crawler->filter('a#previous')->link();
		$next_link = $crawler->filter('a#next')->link();

		if ($this->client->click($previous_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals('/refund', $this->client->getRequest()->getRequestUri());
		}

		if ($this->client->click($next_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/input/", $this->client->getRequest()->getRequestUri());
		}
	}

}