<?php

namespace Apdc\ApdcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Apdc\ApdcBundle\Entity\Refund;
use Apdc\ApdcBundle\Form\RefundType;

class RefundController extends Controller
{
    const    ERROR		= 0;
    const    SUCCESS	= 1;
    const    WARNING	= 2;

    public function indexAction(Request $request, $from)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');

        $entity_from	= new\Apdc\ApdcBundle\Entity\From();
        $form_from		= $this->createForm(\Apdc\ApdcBundle\Form\From::class, $entity_from);

        $form_from->handleRequest($request);

        if ($form_from->isValid()) {
            return $this->redirectToRoute('refundIndex', ['from' => $entity_from->from]);
        }
        if (!isset($from)) {
            return $this->redirectToRoute('refundIndex', ['from' => date('Y-m-d', strtotime('now'))]);
        }

        $form_from->get('from')->setData($from);

        $orders = $mage->getOrders($from);

        return $this->render('ApdcApdcBundle::refund/index.html.twig', [
            'forms'		=> [$form_from->createView()],
            'orders'	=> $orders,
        ]);
    }

    //A déplacer dans Service Magento ou trait associé
    private function check_upload_status($id, $order, &$rsl = [])
    {
        $mage = $this->container->get('apdc_apdc.magento');

        $ticket_folder = $mage->mediaPath().'/attachments/'.$id;
        if (!($dir = opendir($ticket_folder))) {
            return self::ERROR;
        }
        $dir_files = [];
        while (($dir_entry = readdir($dir)) != false) {
            if ($dir_entry == '.' || $dir_entry == '..') {
                continue;
            }
            if (preg_match("/(.*)(\..*)$/", $dir_entry, $file_name)) {
                $dir_files[] = $file_name[1];
            }
        }
        closedir($dir);

        $rsl = [];
        $err = self::SUCCESS;
        foreach ($order as $merchant_id => $osef) {
            if ($merchant_id == -1 && in_array("{$id}", $dir_files)) {
                $err = self::WARNING;
            } elseif (!in_array("{$id}-{$merchant_id}", $dir_files)) {
                if ($merchant_id != -1) {
                    $err = ($err != self::WARNING) ? self::ERROR : $err;
                }
                continue;
            }
            $rsl[] = $merchant_id;
        }

        return $err;
    }

    //A déplacer dans Service Magento ou trait associé
    private function getUploadedFiles($id)
    {
        $dir_files = [];

        $mage = $this->container->get('apdc_apdc.magento');

        $ticket_folder = $mage->mediaPath().'/attachments/'.$id;
        $ticket_url = $mage->mediaUrl().'attachments/'.$id;
        if (($dir = opendir($ticket_folder))) {
            while (($dir_entry = readdir($dir)) != false) {
                if ($dir_entry == '.' || $dir_entry == '..') {
                    continue;
                }
                preg_match("/{$id}-?([0-9]*)\.(.*)/", $dir_entry, $sp);
                if ($sp[1] == '') {
                    $dir_files[-1]['path']	= $ticket_folder.'/'.$dir_entry;
                    $dir_files[-1]['url']	= $ticket_url.'/'.$dir_entry;
                } else {
                    $dir_files[$sp[1]]['path']	= $ticket_folder.'/'.$dir_entry;
                    $dir_files[$sp[1]]['url']	= $ticket_url.'/'.$dir_entry;
                }
            }
            closedir($dir);
        }

        return $dir_files;
    }

    public function refundUploadAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage		= $this->container->get('apdc_apdc.magento');
        $session	= $request->getSession();

        $order		= $mage->getOrderByMerchants($id);

        $entity_upload	= new \Apdc\ApdcBundle\Entity\Upload();
        $form_upload	= $this->createFormBuilder($entity_upload);

        $rsl;
        self::check_upload_status($id, $order, $rsl);

        $attrNames = [];
        foreach ($order as $merchant_id => $data) {
            $name = preg_replace('/[^-a-zA-Z0-9:]| /', '_', $data['merchant']['name']);
            if($name<>""){
                $attrNames[$merchant_id] = $name;
                $class = ' '.((in_array($merchant_id, $rsl)) ? 'success' : 'error');
                $form_upload->add($name, FileType::class, [
                    'required'	=> false,
                    'label'		=> $data['merchant']['name'],
    				'attr'		=>
    				[
    					'class' => "form-control{$class}",
                    ],
                ]);
            }
        }

		$form_upload->add('Upload', SubmitType::class);
        $form_upload->setAction($this->generateUrl('refundUpload', array('id' => $id)));
        $form_upload = $form_upload->getForm();
        if (isset($_FILES['form'])) {
            foreach ($attrNames as $merchant_id => $name) {
                if (!$_FILES['form']['error'][$name] && $_FILES['form']['size'][$name] > 0) {
                    $extentions;
                    preg_match("/.*(\..*)$/", $_FILES['form']['name'][$name], $extentions);
                    $tmp_file = $_FILES['form']['tmp_name'][$name];
                    $folder = $mage->mediaPath().'/attachments/'.$id;
                    if (!file_exists($folder)) {
                        try {
                            $oldmask = umask(0);
                            mkdir($folder, 0777, true);
                            umask($oldmask);
                        } catch (Exception $e) {
                        }
                    }
                    if (file_exists($folder)) {
                        $file = "{$folder}/{$id}";
                        $filename .= ";{$id}/{$id}";
                        if ($name != 'All') {
                            $file .= "-{$merchant_id}";
                            $filename .= "-{$merchant_id}";
                        }
                        $file .= $extentions[1];
                        $filename .= $extentions[1];
                        try {
                            copy($tmp_file, $file);
                        } catch (Exception $e) {
                        }
                    }
                }
                ++$i;
            }
            $err = self::check_upload_status($id, $order);
            if ($err != self::ERROR) {
                if ($err == self::SUCCESS) {
                    $status = 'done';
                } else {
                    $status = 'joker';
                }
                $mage->updateEntryToOrderField(
                    ['order_id' => $order[-1]['order']['mid']],
                    ['upload' => $status,
                    'ticket_commercant' => substr($filename, 1), ]
                );
                $session->getFlashBag()->add('success', 'Image uploadée avec succès');

                return $this->redirectToRoute('refundInput', ['id' => $id]);
            } else {
                $session->getFlashBag()->add('error', 'L\'image n\'a pas été uploadée :-(');

                return $this->redirectToRoute('refundUpload', ['id' => $id]);
            }
        }

        return $this->render('ApdcApdcBundle::refund/upload.html.twig', [
            'forms' => [$form_upload->createView()],
            'order' => $order,
            'id'	=> $id,
        ]);
    }

    public function refundInputAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage		= $this->container->get('apdc_apdc.magento');
        $session	= $request->getSession();

        $order = $mage->getRefunds($id);
        $files = $this->getUploadedFiles($id);

        foreach ($order as $merchant_id => $merchant_part) {
            if (isset($files[$merchant_id])) {
                $order[$merchant_id]['merchant']['ticket'] = $files[$merchant_id]['url'];
            } else {
                $order[$merchant_id]['merchant']['ticket'] = $files[-1]['url'];
            }
        }
        ksort($order);

        $commentaire_client		= $order[-1]['order']['commentaire_client'];
        $commentaire_commercant = $order[-1]['order']['commentaire_commercant'];
        $total					= $order[-1]['merchant']['total'];
        $order_mid				= $order[-1]['order']['mid'];
        $input_status			= $order[-1]['order']['input'];
        $customer_name          = $order[-1]['order']['first_name'].' '.$order[-1]['order']['last_name'];
        unset($order[-1]);

        $entity_input	= new \Apdc\ApdcBundle\Entity\Input();
        $form_input		= $this->createFormBuilder($entity_input);
        $form_input->setAction($this->generateUrl('refundInput', array('id' => $id)));
        $form_input		= $form_input->getForm();
        //$form_input_token = $this->get('security.csrf.token_manager')->getToken($form_input->getName())->getValue();
        /* Voir ce qu'est input_status*/
        if (isset($_POST['submit']) /*&& ($form_input_token == $_POST['form']['_token'])*/ /*&& $input_status == 'none'*/) {
            $rsl_table = [];
            foreach ($order as $merchant_id => $o_data) {
                foreach ($o_data['products'] as $product_id => $p_data) {
                    if (isset($_POST['form'][$product_id])) {
                        $rsl_table[$product_id] = [
                            'order_item_id'			=> $product_id,
                            'item_name'				=> $p_data['nom'],
                            'commercant'			=> $o_data['name'],
                            'commercant_id'			=> $o_data['merchant']['id'],
                            'order_id'				=> $p_data['order_id'],
                            'prix_initial'			=> $p_data['prix_total'],
                            'prix_final'			=> doubleval($_POST['form'][$product_id]['ticket']),
                            'diffprixfinal'			=> $p_data['prix_total'] - doubleval($_POST['form'][$product_id]['ticket']),
							'prix_commercant'		=> doubleval($_POST['form'][$product_id]['ticket-commercant']),
							'diffprixcommercant'	=> $p_data['prix_total'] - doubleval($_POST['form'][$product_id]['ticket-commercant']),
                            'comment'				=> $_POST['form'][$product_id]['comment'],
                        ];
                        unset($_POST['form'][$product_id]);
                    }
                }
            }

            try {
                foreach ($rsl_table as $product_id => $data) {
                    $mage->updateEntryToRefundItem(['order_item_id' => $product_id], $data);
                }
                $mage->updateEntryToOrderField(
                    ['order_id'						=> $order_mid],
                    ['refund_shipping'				=> $_POST['form']['refund_shipping'],
                    'input'							=> 'done',
                    'commentaires_fraislivraison'	=> $_POST['form']['commentaire_client'],
                    'commentaires_ticket'			=> $_POST['form']['commentaire_commercant']]
                );
                $session->getFlashBag()->add('success', 'Information enregistrée avec succès');

                return $this->redirectToRoute('refundInput', ['id' => $id]);
            } catch (Exception $e) {
                $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de l\'enregistrement.');
            }
        }

        return $this->render('ApdcApdcBundle::refund/input.html.twig', [
            'form'						=> $form_input->createView(),
            'order'						=> $order,
            'total'						=> $total,
            'id'						=> $id,
            'refund_shipping'			=> $mage->checkRefundShipping($order_mid),
            'commentaire_client'		=> $commentaire_client,
            'commentaire_commercant'	=> $commentaire_commercant,
            'customer_name'             => $customer_name,
        ]);
    }

    public function refundDigestAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage		= $this->container->get('apdc_apdc.magento');
        $session	= $request->getSession();
        $order		= $mage->getRefunds($id);

        $total					= $order[-1]['merchant']['total'];
        $refund_total			= $order[-1]['merchant']['refund_total'];
        $refund_diff			= $order[-1]['merchant']['refund_diff'];
        $refund_shipping_amount = $order[-1]['order']['refund_shipping_amount'];
        $order_mid				= $order[-1]['order']['mid'];
        $order_header			= $order[-1]['order'];
        unset($order[-1]);

        $files = $this->getUploadedFiles($id);
        foreach ($order as $merchant_id => $merchant_part) {
            if (isset($files[$merchant_id])) {
                $order[$merchant_id]['merchant']['ticket'] = $files[$merchant_id]['url'];
            } else {
                $order[$merchant_id]['merchant']['ticket'] = $files[-1]['url'];
            }
        }
        ksort($order);

        $entity_submit	= new \Apdc\ApdcBundle\Entity\Model();
        $form_submit	= $this->createFormBuilder($entity_submit);
        $form_submit->setAction($this->generateUrl('refundDigest', array('id' => $id)));
        $form_submit	= $form_submit->getForm();

        if ($request->isMethod('POST')) {
            $form_submit->handleRequest($request);
            if (!is_null($_POST['creditmemo'])) {
                try {
                    //create invoice
                    $invoice = $mage->createinvoice($id);
                    if ($invoice) {
                        $session->getFlashBag()->add('success', 'Facture créée.');
                    } else {
                        $session->getFlashBag()->add('warning', 'Facture non créée/déjà existante.');
                    }

                    //process credit
                    $comment = $mage->processcreditAction($id, $order);
                    if ($refund_shipping_amount != 0) {
                        $mage->processcreditshipping($id, $refund_shipping_amount);
                    }
                    $mail_creditmemo = $mage->sendCreditMemoMail($id, $comment, $refund_diff, $refund_shipping_amount);

                    if ($mail_creditmemo) {
                        $session->getFlashBag()->add('success', 'Mail de remboursement & cloture envoyé avec succès!');
                    } else {
                        $session->getFlashBag()->add('error', 'Erreur lors de l\'envoi du mail de remboursement et cloture.');
                    }
                    $mage->updateEntryToOrderField(['order_id' => $order_mid], ['digest' => 'done']);
                } catch (\Exception $e) {
                    $session->getFlashBag()->add('error', 'Magento: '.$e->getMessage());
                }
            }
        }

        return $this->render('ApdcApdcBundle::refund/digest.html.twig', [
            'total'				=> $total,
            'refund_total'		=> $refund_total,
            'refund_diff'		=> $refund_diff,
            'refund_shipping'	=> $refund_shipping_amount,
            'refund_full'		=> $mage->getRefundfull($refund_diff, $refund_shipping_amount),
            'order_header'		=> $order_header,
            'order'				=> $order,
            'id'				=> $id,
            'show_creditmemo'	=> $mage->checkdisplaybutton($id, 'creditmemo'),
            'forms'				=> [$form_submit->createView()],
        ]);
    }

    public function refundFinalAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $adyen	= $this->container->get('apdc_apdc.adyen');
        $mage	= $this->container->get('apdc_apdc.magento');
		$session = $request->getSession();

        $order		= $mage->getRefunds($id);
        $orders		= $mage->getAdyenPaymentByPsp();
        $event_data = $mage->getAdyenQueueFields();

        $refund_diff	= $order[-1]['merchant']['refund_diff'];
        $order_mid		= $order[-1]['order']['mid'];

        $refund = new Refund();
        $form	= $this->createForm(RefundType::class, $refund);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $value = $form['value']->getData();
                $originalReference = $form['originalReference']->getData();

                $adyen->refund($value, $originalReference);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $mage->updateEntryToOrderField(['order_id' => $order_mid], ['refund' => 'done']);

			$session->getFlashBag()->add('success', 'Remboursement via Adyen effectué.');
            return $this->redirectToRoute('refundClosure', ['id' => $id]);
        }

        return $this->render('ApdcApdcBundle::refund/final.html.twig', [
            'form'			=> $form->createView(),
            'refund_diff'	=> $refund_diff,
            'id'			=> $id,
            'orders'		=> $orders,
            'event_data'	=> $event_data,
        ]);
	}

	public function refundClosureAction(Request $request, $id)
	{
		if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
		}
		
		$mage			= $this->container->get('apdc_apdc.magento');
		$order_history	= $mage->getOrderHistory($id);
		$session		= $request->getSession();
		$order			= $mage->getRefunds($id);

		$refund_diff	= $order[-1]['merchant']['refund_diff'];
		$refund_shipping_amount = $order[-1]['order']['refund_shipping_amount'];

		$entity_submit	= new \Apdc\ApdcBundle\Entity\Model();
        $form_submit	= $this->createFormBuilder($entity_submit);
        $form_submit->setAction($this->generateUrl('refundClosure', array('id' => $id)));
        $form_submit	= $form_submit->getForm();

		if ($request->isMethod('POST')) {
            $form_submit->handleRequest($request);
			if (!is_null($_POST['close'])) {
				try {
					//Close order
					$close = $mage->setCloseStatus($id);
					$session->getFlashBag()->add('success', 'Commande cloturée avec succès!');
				} catch (Exception $e) {
					$session->getFlashBag()->add('error', 'Magento: '.$e->getMessage());
				}
			}
		}		

		return $this->render('ApdcApdcBundle::refund/closure.html.twig', [
			'order_history' => $order_history,
			'id'			=> $id,
			'refund_full'	=> $mage->getRefundfull($refund_diff, $refund_shipping_amount),
			'show_close'	=> $mage->checkdisplaybutton($id, 'close'),
			'forms'			=> [$form_submit->createView()],

		]);
	}

    public function refundAdyenIndexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage	= $this->container->get('apdc_apdc.magento');
        $orders = $mage->getAdyenOrderPaymentTable();

        return $this->render('ApdcApdcBundle::refund/adyenIndex.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function refundAdyenFormAction(Request $request, $psp)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $adyen	= $this->container->get('apdc_apdc.adyen');
        $mage	= $this->container->get('apdc_apdc.magento');

		$orders		= $mage->getAdyenPaymentByPsp();
        $event_data = $mage->getAdyenQueueFields();

        $refund = new Refund();
        $form	= $this->createForm(RefundType::class, $refund);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $value = $form['value']->getData();
                $originalReference = $form['originalReference']->getData();

                $adyen->refund($value, $originalReference);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $this->get('session')->getFlashBag()->add('notice', 'Remboursement effectué !');
            return $this->redirectToRoute('refundAdyenIndex');
        }

        return $this->render('ApdcApdcBundle::refund/adyenForm.html.twig', [
            'form'			=> $form->createView(),
            'psp'			=> $psp,
            'orders'		=> $orders,
            'event_data'	=> $event_data,
        ]);
    }


    /* Pour l'instant, simple var_dump. A integrer à refund upload images */
    public function mistralTicketImgAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mistral = $this->container->get('apdc_apdc.mistral');

        $dataMistral = array('message' => 'Dump img ticket');        
        $formMistral = $this->createFormBuilder($dataMistral)
            ->add("Dump", SubmitType::class,array('label'=>'Dump img ticket','attr'=>array('class'=>'btn btn-lg btn-danger','style'=>'float:right')))
            ->getForm();
        $formMistral->handleRequest($request);

        if ($formMistral->isSubmitted() && $formMistral->isValid()) {
            $mistral->getPictures();
        }

        return $this->render('ApdcApdcBundle::refund/mistralTicketImg.html.twig', [
            'formMistral' => $formMistral->createView(),
        ]);
    }
}
