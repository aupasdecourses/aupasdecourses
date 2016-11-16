<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

		$attrNames = [];
		foreach ($order as $merchant_id => $data) {
			$name = preg_replace("/[^-a-zA-Z0-9:]| /", "_", $data['merchant']['name']);
			$attrNames[$merchant_id] = $name;
			$form_upload->add($name, FileType::class, [
				'required'    => false,
				'label' => $data['merchant']['name'],
				'attr'	=> [
					'class'	=> 'form-control'
					]
			]);
		}
		$form_upload->add('Upload', SubmitType::class);
		$form_upload = $form_upload->getForm();

		if (isset($_FILES['form'])) {
			foreach ($attrNames as $merchant_id => $name) {
				if (!$_FILES['form']['error'][$name] && $_FILES['form']['size'][$name] > 0) {
					$extentions;
					preg_match("/.*(\..*)$/", $_FILES['form']['name'][$name], $extention);
					$file = $id;
					if ($name <> 'All')
						$file .= "-{$merchant_id}";
					$file .= $extention[1];

					$tmp_file = $_FILES['form']['tmp_name'][$name];

					echo $tmp_file." -> ".$file.'<br />';
				}
			}
		}

		return $this->render('refund/upload.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'forms' => [ $form_upload->createView() ],
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
			'order' => $order
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
