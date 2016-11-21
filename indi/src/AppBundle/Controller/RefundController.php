<?php

namespace AppBundle\Controller;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\FileType;

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
		if (!isset($from))
			return $this->redirectToRoute('refundIndex', [ 'from' => date('Y-m-d') ]);

		$form_from->get('from')->setData($from);

		$orders = $mage->getOrders($from);
		 
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

		$entity_upload = new \AppBundle\Entity\Upload();
		$form_upload = $this->createFormBuilder($entity_upload);

		foreach ($order as $merchant_id => $data) {
			$form_upload->add(preg_replace("/[^-a-zA-Z0-9:]| /", "_", $data['merchant']['name']), FileType::class, [
				'label' => [ "Yolo"/*$data['merchant']['name']*/ ],
				'attr'	=> [
					'class'	=> 'form-control'
					]
			]);
			// << =====
		}

		return $this->render('refund/upload.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'forms' => $form_upload->getForm()->createView(),
			'order' => $order,
			'order_id' => $id
		]);		
	}

	public function refundAttachmentAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$order = $mage->getOrderByMerchants($id);
		$total = $order[-1]['merchant']['total'];
		unset($order[-1]);
 
		return $this->render('refund/attachment.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'order' => $order,
			'total' => $total,
			'order_id' => $id,
			'refunds' => $mage->getRefunds($id)
		]);
	}

	public function refundResumeAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$order = $mage->getOrderByMerchants($id);
		$total = $order[-1]['merchant']['total'];
		unset($order[-1]);

		return $this->render('refund/resume.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'order' => $order,
			'total' => $total,
			'order_id' => $id
		]);
	}
}
