<?php

namespace AppBundle\Controller;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

include_once 'Magento.php';

class RefundController extends Controller
{
	const	ERROR = 0;
	const	SUCCESS = 1;
	const	WARNING = 2;

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
			return $this->redirectToRoute('refundIndex', [ 'from' => date('Y-m-d', strtotime('-1 day')) ]);

		$form_from->get('from')->setData($from);

		$orders = $mage->getOrders($from);
		 
		return $this->render('refund/index.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'forms' => [ $form_from->createView() ],
			'orders' => $orders
		]);
	}

	private static function check_upload_status($id, $order, &$rsl = []) {
		$ticket_folder = \Magento::mediapath().'/attachments/'.$id;
		if (!($dir = opendir($ticket_folder)))
			return self::ERROR;
		$dir_files = [];
		while (($dir_entry = readdir($dir)) <> false) {
			if ($dir_entry == '.' || $dir_entry == '..')
				continue ;
			if (preg_match("/(.*)(\..*)$/", $dir_entry, $file_name))
				$dir_files[] = $file_name[1];
		}
		closedir($dir);

		$rsl = [];
		$err = self::SUCCESS;
		foreach ($order as $merchant_id => $osef) {
			if ($merchant_id == -1 && in_array("{$id}", $dir_files)) {
				$err = self::WARNING;
			} else if (!in_array("{$id}-{$merchant_id}", $dir_files)) {
				if ($merchant_id <> -1)
					$err = ($err <> self::WARNING) ? self::ERROR : $err;
				continue ;
			}
			$rsl[] = $merchant_id;
		}

		return $err;
	}

	public function refundUploadAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$order = $mage->getOrderByMerchants($id);

		$entity_upload = new \AppBundle\Entity\Upload();
		$form_upload = $this->createFormBuilder($entity_upload);

		$rsl;
		self::check_upload_status($id, $order, $rsl);

		$attrNames = [];
		foreach ($order as $merchant_id => $data) {
			$name = preg_replace("/[^-a-zA-Z0-9:]| /", "_", $data['merchant']['name']);
			$attrNames[$merchant_id] = $name;
			$class = ' '.((in_array($merchant_id, $rsl)) ? 'success' : 'error');
			$form_upload->add($name, FileType::class, [
				'required'    => false,
				'label' => $data['merchant']['name'],
				'attr'	=> [
					'class'	=> "form-control{$class}",
					]
			]);
		}

		$form_upload->add('Upload', SubmitType::class);
		$form_upload = $form_upload->getForm();

		if (isset($_FILES['form'])) {
			foreach ($attrNames as $merchant_id => $name) {
				if (!$_FILES['form']['error'][$name] && $_FILES['form']['size'][$name] > 0) {
					$extentions;
					preg_match("/.*(\..*)$/", $_FILES['form']['name'][$name], $extentions);
					$tmp_file = $_FILES['form']['tmp_name'][$name];
					$folder = \Magento::mediapath().'/attachments/'.$id;
					if (!file_exists($folder))
						mkdir($folder);
					if (file_exists($folder)) {
						$file = "{$folder}/{$id}";
						if ($name <> 'All')
							$file .= "-{$merchant_id}";
						$file .= $extentions[1];
						copy($tmp_file, $file);
					}
				}
			}
			$err = self::check_upload_status($id, $order);
			if ($err <> self::ERROR)
				return $this->redirectToRoute('refundAttachment', [ 'id' => $id ]);
			else
				return $this->redirectToRoute('refundUpload', [ 'id' => $id ]);
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

	public function refundDigestAction(Request $request, $id)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$order = $mage->getOrderByMerchants($id);

		return $this->render('refund/digest.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'order' => $order
		]);
	}
}
