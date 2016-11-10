<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
include_once 'Magento.php';

class RefundController extends Controller
{
	public function indexAction(Request $request, $from)
	{
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_from = new\AppBundle\Entity\From();
		$form_from = $this->createForm(\AppBundle\Form\From::class, $entity_from);
		
		$form_from->handleRequest($request);
		
		if ($form_from->isValid()) {
			return $this->redirectToRoute('refundAll', [
				'from' => $entity_from->from
			]);
		}

		return $this->render('refund/index.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'forms' => [ 
				$form_from->createView(),
			]
		]);

	}

	public function refundUploadAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		return $this->render('refund/one.html.twig', [
			'user' => $_SESSION['delivery']['username']		
		]);		

	}

	public function refundAttachmentAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		return $this->render('refund/input.html.twig', [
			'user' => $_SESSION['delivery']['username']
		]);
	}

	public function refundResumeAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		return $this->render('refund/summary.html.twig', [
			'user' => $_SESSION['delivery']['username']
		]);
	}
}
