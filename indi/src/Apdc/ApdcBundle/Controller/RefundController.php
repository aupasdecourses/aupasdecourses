<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

use Apdc\ApdcBundle\Entity\Refund;
use Apdc\ApdcBundle\Form\RefundType;

class RefundController extends Controller
{
	const	ERROR = 0;
	const	SUCCESS = 1;
	const	WARNING = 2;

	public function indexAction(Request $request, $from)
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$entity_from = new\Apdc\ApdcBundle\Entity\From();
		$form_from = $this->createForm(\Apdc\ApdcBundle\Form\From::class, $entity_from);
		
		$form_from->handleRequest($request);

		if ($form_from->isValid())
			return $this->redirectToRoute('refundIndex', [ 'from' => $entity_from->from ]);
		if (!isset($from))
			return $this->redirectToRoute('refundIndex', [ 'from' => date('Y-m-d', strtotime('-1 day')) ]);

		$form_from->get('from')->setData($from);

		$orders = $mage->getOrders($from);

		return $this->render('ApdcApdcBundle::refund/index.html.twig', [
			'forms' => [ $form_from->createView() ],
			'orders' => $orders
		]);
	}

	private function check_upload_status($id, $order, &$rsl = []) {
		
		$mage = $this->container->get('apdc_apdc.magento');

		$ticket_folder = $mage->mediaPath().'/attachments/'.$id;
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

	private function getUploadedFiles($id) {
		$dir_files = [];
		
		$mage = $this->container->get('apdc_apdc.magento');

		$ticket_folder = $mage->mediaPath().'/attachments/'.$id;
		$ticket_url = $mage->mediaUrl().'attachments/'.$id;
		if (($dir = opendir($ticket_folder))) {
			while (($dir_entry = readdir($dir)) <> false) {
				if ($dir_entry == '.' || $dir_entry == '..')
					continue ;
				preg_match("/{$id}-?([0-9]*)\.(.*)/", $dir_entry, $sp);
				if ($sp[1] == '') {
					$dir_files[-1]['path'] = $ticket_folder.'/'.$dir_entry;
					$dir_files[-1]['url'] = $ticket_url.'/'.$dir_entry;
				} else {
					$dir_files[$sp[1]]['path'] = $ticket_folder.'/'.$dir_entry;
					$dir_files[$sp[1]]['url'] = $ticket_url.'/'.$dir_entry;
				}
			}
			closedir($dir);
		}
		return ($dir_files);
	}

	public function refundUploadAction(Request $request, $id)
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$order = $mage->getOrderByMerchants($id);

		$entity_upload = new \Apdc\ApdcBundle\Entity\Upload();
		$form_upload = $this->createFormBuilder($entity_upload);

		$rsl;
		self::check_upload_status($id, $order, $rsl);

		$attrNames = [];
		foreach ($order as $merchant_id => $data) {
			$name = preg_replace("/[^-a-zA-Z0-9:]| /", "_", $data['merchant']['name']);
			$attrNames[$merchant_id] = $name;
			$class = ' '.((in_array($merchant_id, $rsl)) ? 'success' : 'error');
			$form_upload->add($name, FileType::class, [
				'required'  => false,
				'label'		=> $data['merchant']['name'],
				'attr'		=> [
					'class'		=> "form-control{$class}",
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
					$folder = $mage->mediaPath().'/attachments/'.$id;
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
			if ($err <> self::ERROR) {
				if ($err == self::SUCCESS)
					$status = 'done';
				else
					$status = 'joker';
				$mage->updateEntryToOrderField([ 'order_id' => $order[-1]['order']['mid']], [ 'upload' => $status]);
				return $this->redirectToRoute('refundInput', [ 'id' => $id ]);
			} else {
				return $this->redirectToRoute('refundUpload', [ 'id' => $id ]);
			}
		}

		return $this->render('ApdcApdcBundle::refund/upload.html.twig', [
			'forms' => [ $form_upload->createView() ],
			'order' => $order
		]);		
	}

	public function refundInputAction(Request $request, $id)
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$order = $mage->getRefunds($id);

		$files = $this->getUploadedFiles($id);

		foreach ($order as $merchant_id => $merchant_part) {
			if (isset($files[$merchant_id]))
				$order[$merchant_id]['merchant']['ticket'] = $files[$merchant_id]['url'];
			else
				$order[$merchant_id]['merchant']['ticket'] = $files[-1]['url'];
		}
		ksort($order);

		$total = $order[-1]['merchant']['total'];
		$order_mid = $order[-1]['order']['mid'];
		$input_status = $order[-1]['order']['input'];
		unset($order[-1]);

		$entity_input = new \Apdc\ApdcBundle\Entity\Input();
		$form_input = $this->createFormBuilder($entity_input);

		$form_input = $form_input->getForm();
		//$form_input_token = $this->get('security.csrf.token_manager')->getToken($form_input->getName())->getValue();

		if (isset($_POST['submit']) /*&& ($form_input_token == $_POST['form']['_token'])*/ && $input_status == 'none') {
			$rsl_table = [];
			foreach ($order as $merchant_id => $o_data) {
				foreach ($o_data['products'] as $product_id => $p_data) {
					if (isset($_POST['form'][$product_id])) {
						$rsl_table[$product_id] = [
							'order_item_id'	=> $product_id,
							'item_name'		=> $p_data['nom'],
							'commercant'	=> $o_data['name'],
							'commercant_id'	=> $o_data['merchant']['id'],
							'order_id'		=> $p_data['order_id'],
							'prix_initial'	=> $p_data['prix_total'],
							'prix_final'	=> doubleval($_POST['form'][$product_id]['ticket']),
							'diffprixfinal'	=> $p_data['prix_total'] - doubleval($_POST['form'][$product_id]['ticket']),
							'comment'		=> $_POST['form'][$product_id]['comment'],
						];
						unset($_POST['form'][$product_id]);
					}
				}
			}
			
			foreach ($rsl_table as $product_id => $data) {
				$mage->updateEntryToRefundItem(['order_item_id' => $product_id], $data);
			}

			if ($_POST['submit'] == 'next') {
				return $this->redirectToRoute('refundDigest', [ 'id' => $id ]);
			} else {
				return $this->redirectToRoute('refundInput', [ 'id' => $id ]);
			}
		}

		return $this->render('ApdcApdcBundle::refund/input.html.twig', [
			'form' => $form_input->createView(),
			'order' => $order,
			'total' => $total,
			'order_id' => $id,
		]);
	}
	
	public function refundDigestAction(Request $request, $id)
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$order = $mage->getRefunds($id);

		$total = $order[-1]['merchant']['total'];
		$refund_total = $order[-1]['merchant']['refund_total'];
		$refund_diff = $order[-1]['merchant']['refund_diff'];
		$order_header = $order[-1]['order'];
		unset ($order[-1]);

		$files = $this->getUploadedFiles($id);
		foreach ($order as $merchant_id => $merchant_part) {
			if (isset($files[$merchant_id]))
				$order[$merchant_id]['merchant']['ticket'] = $files[$merchant_id]['url'];
			else
				$order[$merchant_id]['merchant']['ticket'] = $files[-1]['url'];
		}
		ksort($order);

		$entity_submit = new \Apdc\ApdcBundle\Entity\Model();
		$form_submit = $this->createForm(\Apdc\ApdcBundle\Form\Submit::class, $entity_submit);

		$form_submit->handleRequest($request);
		$msg = '';
		if ($form_submit->isSubmitted()) {
			$mage->updateEntryToOrderField([ 'order_id' => $order_mid ], [ 'input' => 'none' ]); // to be changed to done

			try {
		//		$mage->processcreditAction($id, $order);
		//		$adyen = new \Adyen();
		//		$adyen->refund('AuPasDeCoursesFR', $refund_diff, $order_header['pspreference'], "{id-R}");

		//		send mail
			} catch (\Exception $e) {
				$msg = $e->getMessage();
			}
		}

		return $this->render('ApdcApdcBundle::refund/digest.html.twig', [
			'total' => $total,
			'msg' => $msg,
			'refund_total' => $refund_total,
			'refund_diff' => $refund_diff,
			'order_header' => $order_header,
			'order' => $order,
			'order_id' => $id,
			'forms' => [ $form_submit->createView() ],
		]);
	}

	public function refundAdyenIndexAction(Request $request)
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');
		$orders = $mage->getAdyenOrderPaymentTable();

		return $this->render('ApdcApdcBundle::refund/adyenIndex.html.twig', [
			'orders' => $orders,
			]);

	}

	public function refundAdyenFormAction(Request $request, $id)
	{
		$adyen = $this->container->get('apdc_apdc.adyen');
		$logs = $this->container->get('apdc_apdc.adyenlogs');
		
		$refund = new Refund();
		$form = $this->createForm(RefundType::class, $refund);

		if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
			try{
			$value					= $form["value"]->getData();
			$originalReference		= $form["originalReference"]->getData();

			$adyen->refund($value, $originalReference);
			} catch (Exception $e){
				echo $e->getMessage();	
			}
			return $this->redirectToRoute('refundAdyenIndex');
		}
		return $this->render('ApdcApdcBundle::refund/adyenForm.html.twig', [
			'form' => $form->createView(),
		]);	
	}
}
