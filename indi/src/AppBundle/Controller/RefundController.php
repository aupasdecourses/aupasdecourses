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

		if ($form_from->isValid())
			return $this->redirectToRoute('refundIndex', [ 'from' => $entity_from->from ]);
		if (isset($from))
			$orders = $mage->getOrders($from);
		else
			$orders = $mage->getOrders();
		 
		return $this->render('refund/index.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'forms' => [ $form_from->createView() ],
			'orders' => $orders
		]);

	}

	public function refundUploadAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$order = $mage->getOrderByMerchants($id);

		return $this->render('refund/upload.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'order' => $order
		]);		

	}

	public function refundAttachmentAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$order = $mage->getOrderByMerchants($id);

		return $this->render('refund/attachment.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'order' => $order,
			'stores' => $mage->getMerchantsOrdersByStore(-1)
		]);
	}

	public function refundResumeAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$order = $mage->getOrderByMerchants($id);

		return $this->render('refund/resume.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'order' => $order
		]);
	}
}
