<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class RefundControllerTest extends AbstractControllerTest
{

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::indexAction()
	 */
	public function testIndexResponseStatus()
	{
		$this->client->request('GET', '/refund');
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

		$this->client->request('GET', "/refund/$this->orderId");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

		$this->client->request('GET', "/refund/-1/$this->from/$this->to");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundUploadAction()
	 */
	public function testRefundUploadResponseStatus()
	{
		$this->client->request('GET', "/refund/$this->orderId/upload/");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundInputAction()
	 */
	public function testRefundInputResponseStatus()
	{
		$this->client->request('GET', "/refund/$this->orderId/input/");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundDigestAction()
	 */
	public function testRefundDigestResponseStatus()
	{
		$this->client->request('GET', "/refund/$this->orderId/digest/");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundFinalAction()
	 */
	public function testRefundFinalResponseStatus()
	{
		$this->client->request('GET', "/refund/$this->orderId/final/");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test status code of Apdc\ApdcBundle\Controller\RefundController::refundClosureAction()
	 */
	public function testRefundClosureResponseStatus()
	{
		$this->client->request('GET', "/refund/$this->orderId/closure/");
		$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
	}

	/**
	 *	Test previous & next links of Apdc\ApdcBundle\Controller\RefundController::refundUploadAction()
	 */
	public function testRefundUploadNavigationLinks()
	{
		$crawler = $this->client->request('GET', "/refund/$this->orderId/upload/");

		$previous_to_index_link = $crawler->filter('a#previous_to_index')->link();
		$next_to_input_link = $crawler->filter('a#next_to_input')->link();

		if ($this->client->click($previous_to_index_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals('/refund', $this->client->getRequest()->getRequestUri());
		}

		if ($this->client->click($next_to_input_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/input/", $this->client->getRequest()->getRequestUri());
		}
	}

	/**
	 *	Test previous & next links of Apdc\ApdcBundle\Controller\RefundController::refundInputAction()
	 */
	public function testRefundInputNavigationLinks()
	{
		$crawler = $this->client->request('GET', "/refund/$this->orderId/input/");

		$previous_to_upload_link = $crawler->filter('a#previous_to_upload')->link();
		$next_to_digest_link = $crawler->filter('a#next_to_digest')->link();

		if ($this->client->click($previous_to_upload_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/upload/", $this->client->getRequest()->getRequestUri());
		}

		if ($this->client->click($next_to_digest_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/digest/", $this->client->getRequest()->getRequestUri());
		}
	}

	/**
	 *	Test previous & next links of Apdc\ApdcBundle\Controller\RefundController::refundDigestAction()
	 */
	public function testRefundDigestNavigationLinks()
	{
		$crawler = $this->client->request('GET', "/refund/$this->orderId/digest/");

		$previous_to_input_link = $crawler->filter('a#previous_to_input')->link();
		$next_to_final_link = ($crawler->filter('a#next_to_final')->count() > 0) ? $crawler->filter('a#next_to_final')->link() : null;
		$next_to_closure_link = ($crawler->filter('a#next_to_closure')->count() > 0) ? $crawler->filter('a#next_to_closure')->link() : null;
		

		if ($this->client->click($previous_to_input_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/input/", $this->client->getRequest()->getRequestUri());
		}

		if (!is_null($next_to_final_link) && $this->client->click($next_to_final_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/final/", $this->client->getRequest()->getRequestUri());
		}

		if (!is_null($next_to_closure_link) && $this->client->click($next_to_closure_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/closure/", $this->client->getRequest()->getRequestUri());
		}
	}

	/**
	 * 	Test previous & next links of Apdc\ApdcBundle\Controller\RefundController::refundFinalAction()
	 */
	public function testRefundFinalNavigationLinks()
	{
		$crawler = $this->client->request('GET', "/refund/$this->orderId/final/");

		$previous_to_digest_link = $crawler->filter('a#previous_to_digest')->link();
		$next_to_closure_form = $crawler->filter('button#form_submit')->form();

		if ($this->client->click($previous_to_digest_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/digest/", $this->client->getRequest()->getRequestUri());
		}

		if ($this->client->submit($next_to_closure_form)) {
			$this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
			
			$crawler = $this->client->followRedirect();
			$this->assertNotNull($crawler->getUri());
		}
	}

	/**
	 *	Test previous & next links of Apdc\ApdcBundle\Controller\RefundController::refundClosureAction()
	 */
	public function testRefundClosureNavigationLinks()
	{
		$crawler = $this->client->request('GET', "/refund/$this->orderId/closure/");

		$previous_to_digest_link = ($crawler->filter('a#previous_to_digest')->count() > 0) ? $crawler->filter('a#previous_to_digest')->link() : null;
		$previous_to_final_link = ($crawler->filter('a#previous_to_final')->count() > 0) ? $crawler->filter('a#previous_to_final')->link() : null;
		$next_to_index_link = $crawler->filter('a#next_to_index')->link();

		if (!is_null($previous_to_digest_link) && $this->client->click($previous_to_digest_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/digest/", $this->client->getRequest()->getRequestUri());
		}

		if (!is_null($previous_to_final_link) && $this->client->click($previous_to_final_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund/$this->orderId/final/", $this->client->getRequest()->getRequestUri());
		}

		if ($this->client->click($next_to_index_link)) {
			$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
			$this->assertEquals("/refund", $this->client->getRequest()->getRequestUri());
		}
	}

}