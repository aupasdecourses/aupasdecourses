<?php

namespace Apdc\ApdcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Apdc\ApdcBundle\Entity\IndiRefund;
use Apdc\ApdcBundle\Form\IndiRefundType;

class RefundController extends Controller
{
    const    ERROR = 0;
    const    SUCCESS = 1;
    const    WARNING = 2;

    public function indexAction(Request $request, $id, $from, $to)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');
		$mistral = $this->container->get('apdc_apdc.mistral');
		$session = $request->getSession();

        $entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
        $form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto);
		$form_fromto->handleRequest($request);

		$entity_id = new \Apdc\ApdcBundle\Entity\OrderId();
		$form_id = $this->createForm(\Apdc\ApdcBundle\Form\OrderId::class, $entity_id);
		$form_id->handleRequest($request);

		if ($form_fromto->isValid()) {
			return $this->redirectToRoute('refundIndex', [
				'from' => $entity_fromto->from,
				'to' => $entity_fromto->to,
				'id' => -1,
            ]);
		} elseif ($form_id->isSubmitted() && $form_id->isValid()) {
			return $this->redirectToRoute('refundIndex', [
				'id' => $entity_id->id,
			]);
		}

        $form_fromto->get('from')->setData($from);
        $form_fromto->get('to')->setData($to);
		$form_id->get('id')->setData($id);

		$orders = $mage->getOrders($from, $to, -1, $id);

		// MISTRAL RETARDS DE LIVRAISON
		$mistral_late_orders_data = array('message' => 'Retards de picking/shipping');
		$mistral_late_orders_form = $this->createFormBuilder($mistral_late_orders_data)
			->add('submit', SubmitType::class, [
				'label'	=> 'Verifier les retards',
				'attr'	=> [
					'class'	=> 'btn btn-lg btn-info',
				],
			])
			->getForm();
		$mistral_late_orders_form->handleRequest($request);

		if ($mistral_late_orders_form->isSubmitted() && $mistral_late_orders_form->isValid()) {

			$neighborhood = $mistral->getApdcNeighborhood();
			$mistral_results = [];

			// Construct results[]
			foreach ($neighborhood as $neigh) {
				foreach ($orders as $order_id => $order) {
					if ($neigh['store_id'] == $order['store_id']) {
						$mistral_results[$order_id] = [
							'partner_ref'			=> $neigh['partner_ref'],
							'merchant_id'			=> [],
							'real_hour_picking'		=> '',
							'slot_start_picking'	=> '',
							'slot_end_picking'		=> '',
							'real_hour_shipping'	=> '',
							'slot_start_shipping'	=> '',
							'slot_end_shipping'		=> '',
						];

						foreach ($order['products'] as $product) {
							$mistral_results[$order_id]['merchant_id'][] = $product['commercant_id'];
						}

						$mistral_results[$order_id]['merchant_id'] = array_unique($mistral_results[$order_id]['merchant_id']);
					}
				}
			}
		
			// Store mistral data in tmp[]
			foreach ($mistral_results as $order_id => $mistral_data) {
				foreach ($mistral_data['merchant_id'] as $key => $merch_id) {
					$tmp[$order_id][$merch_id] = $mistral->getOrderWarehouse($mistral_data['partner_ref'], $order_id, $merch_id); 
				}
			}

			// Clean up tmp[]
			foreach ($tmp as $order_id => $order_data) {
				foreach ($order_data as $merch_id => $mistral_result) {
					if (is_numeric($merch_id)) {
						if (isset($mistral_result['Message'])) { // Commande inconnue
							unset($tmp[$order_id]);
						} 
						if ($mistral_result['StatusCode'] == 'EA' || $mistral_result['StatusCode'] == 'EEC') { // En acheminement ou en enlevement
							unset($tmp[$order_id][$merch_id]);
						}
					}
				}

				if(empty($tmp[$order_id])) { // manque d'infos Mistral
					unset($tmp[$order_id]);
				}
			}

			// Store mistral hours in mistral_results[]
			foreach ($mistral_results as $order_id => $data) {
				foreach ($tmp as $o_id => $order_data) {
					foreach ($order_data as $merch_id => $result) {
						if ($order_id == $o_id) {
							$mistral_results[$order_id]['real_hour_picking']	= $result['Pick']['RealHour'];
							$mistral_results[$order_id]['slot_start_picking']	= $result['Pick']['SlotStart'];
							$mistral_results[$order_id]['slot_end_picking']		= $result['Pick']['SlotEnd'];
							$mistral_results[$order_id]['real_hour_shipping']	= $result['Delivery']['RealHour'];
							$mistral_results[$order_id]['slot_start_shipping']	= $result['Delivery']['SlotStart'];
							$mistral_results[$order_id]['slot_end_shipping']	= $result['Delivery']['SlotEnd'];
						}
					}
				}

				unset($mistral_results[$order_id]['partner_ref'], $mistral_results[$order_id]['merchant_id']);
			}

			unset($tmp);

			// Update DDB
			foreach ($mistral_results as $order_id => $data) {
				$mage->updateEntryToMistralDelivery(
					['order_id' => $order_id],
					['real_hour_picking' => date('H:i', strtotime($data['real_hour_picking'])), 
					'slot_start_picking' => date('H:i', strtotime($data['slot_start_picking'])), 
					'slot_end_picking' => date('H:i', strtotime($data['slot_end_picking'])),
					'real_hour_shipping' => date('H:i', strtotime($data['real_hour_shipping'])),
					'slot_start_shipping' => date('H:i', strtotime($data['slot_start_shipping'])),
					'slot_end_shipping' => date('H:i', strtotime($data['slot_end_shipping']))
				]);
			}

			$session->getFlashBag()->add('success', 'Statuts Mistral mis a jour');
			return $this->redirectToRoute('refundIndex', [
				'id' => $id, 
				'from' => $from, 
				'to' => $to
			]);
		}

		return $this->render('ApdcApdcBundle::refund/index.html.twig', [
			'forms' => [
				$form_fromto->createView(),
				$form_id->createView()
			],
			'orders' => $orders,
			'mistral_late_orders_form'	=> $mistral_late_orders_form->createView(),
			'mistral_hours'	=> $mage->getMistralDelivery(),
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
                    $dir_files[-1]['path'] = $ticket_folder.'/'.$dir_entry;
                    $dir_files[-1]['url'] = $ticket_url.'/'.$dir_entry;
                } else {
                    $dir_files[$sp[1]]['path'] = $ticket_folder.'/'.$dir_entry;
                    $dir_files[$sp[1]]['url'] = $ticket_url.'/'.$dir_entry;
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

        $mage = $this->container->get('apdc_apdc.magento');
        $mistral = $this->container->get('apdc_apdc.mistral');
        $session = $request->getSession();

        $order = $mage->getOrderByMerchants($id);

        $entity_upload = new \Apdc\ApdcBundle\Entity\Upload();
        $form_upload = $this->createFormBuilder($entity_upload);

        $rsl;
        self::check_upload_status($id, $order, $rsl);

        $attrNames = [];
        foreach ($order as $merchant_id => $data) {
            $name = preg_replace('/[^-a-zA-Z0-9:]| /', '_', $data['merchant']['name']);
            if ($name != '') {
                $attrNames[$merchant_id] = $name;
                $class = ' '.((in_array($merchant_id, $rsl)) ? 'success' : 'error');
                $form_upload->add($name, FileType::class, [
                    'required' => false,
                    'label' => $data['merchant']['name'],
                    'attr' => [
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
                            \Mage::log("cannot create ".$folder);
                            \Mage::log($e->getMessage());
                        }
                    }
                    if (file_exists($folder)) {
                        $file = "{$folder}/{$id}";
                        $filename .= ";{$id}/{$id}";
                        if ($name != 'All') {
                            $file .= "-{$merchant_id}";
                            $filename .= "-{$merchant_id}";
						}
                        //$file .= $extentions[1];
                        //$filename .= $extentions[1];
						$file .= ".jpeg";
						$filename .= ".jpeg";
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
                $session->getFlashBag()->add('error', 'L\'image n\'a pas été uploadée');

                return $this->redirectToRoute('refundUpload', ['id' => $id]);
            }
        }

        // MISTRAL AUTO UPLOAD
        $mistral_data = array('message' => 'Upload via Mistral');
        $mistral_form = $this->createFormBuilder($mistral_data)
            ->add('Mistral tickets upload', SubmitType::class, array(
                'label' => 'Upload tickets',
                'attr' => array(
                    'class' => 'btn btn-info', ), ))
            ->getForm();
        $mistral_form->handleRequest($request);

        if ($mistral_form->isSubmitted() && $mistral_form->isValid()) {

			// 1 - Construct results[]
			$results = $mistral->constructMistralImgsResults($order, $id);	

            // 2 - Store Mistral images in temp[]	
            $temp = [];
            foreach ($results as $merchant_id => $result) {
                if (is_numeric($merchant_id)) {
                    try {
                        $temp[$merchant_id] = $mistral->getPictures($results['partner_ref'], $results['order_id'], $merchant_id);
                    } catch (Exception $e) {
                        $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de la récupération via Mistral. Veuillez reessayer');

                        return $this->redirectToRoute('refundUpload', ['id' => $id]);
                    }
                }
			}

            // 3 - Store images in results[]
			$results = $mistral->storeMistralImgsResults($temp, $results);

            // 4 - Convert base64 into real img, add imgs into media/attachments & update DBB
            foreach ($results as $merchant_id => $result) {
                if (is_numeric($merchant_id)) {
                    $mistral->convert_base64_to_img($result['base64_string'], $result['image_type'], $results['order_id'], $merchant_id);
                }
            }

            $diff = array_diff_assoc($temp, $results);
			
			if (!empty($diff)) {
				$intersect = array_intersect_assoc($order, $diff);
                foreach ($intersect as $merchant_id => $merchant) {
                    $session->getFlashBag()->add('error', 'Image non uploadée pour '.$merchant['merchant']['name'].'. Veuillez l\'uploader manuellement ');
                }

                return $this->redirectToRoute('refundUpload', ['id' => $id]);
			} else {
				$mage->updateEntryToOrderField(
					['order_id'	=> $results['order_mid']],
					['upload'	=> 'done', 'ticket_commercant' => $results['ticket_com']]
				);	

                $session->getFlashBag()->add('success', 'Images uploadées via Mistral');

                return $this->redirectToRoute('refundInput', ['id' => $id]);
            }
        }

        return $this->render('ApdcApdcBundle::refund/upload.html.twig', [
            'forms' => [$form_upload->createView()],
            'order' => $order,
            'id' => $id,
			'mistral_form' => $mistral_form->createView(),
			'mistral_hours' => $mage->getMistralDelivery(),
        ]);
    }

    public function refundInputAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');
        $session = $request->getSession();

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

        $commentaire_client = $order[-1]['order']['commentaire_client'];
        $commentaire_commercant = $order[-1]['order']['commentaire_commercant'];
        $total = $order[-1]['merchant']['total'];
        $order_mid = $order[-1]['order']['mid'];
        $input_status = $order[-1]['order']['input'];
        $customer_name = $order[-1]['order']['first_name'].' '.$order[-1]['order']['last_name'];
        unset($order[-1]);

        $entity_input = new \Apdc\ApdcBundle\Entity\Input();
        $form_input = $this->createFormBuilder($entity_input);
        $form_input->setAction($this->generateUrl('refundInput', array('id' => $id)));
        $form_input = $form_input->getForm();
        //$form_input_token = $this->get('security.csrf.token_manager')->getToken($form_input->getName())->getValue();
        /* Voir ce qu'est input_status*/
        if (isset($_POST['submit']) /*&& ($form_input_token == $_POST['form']['_token'])*/ /*&& $input_status == 'none'*/) {
            $rsl_table = [];
            foreach ($order as $merchant_id => $o_data) {
                foreach ($o_data['products'] as $product_id => $p_data) {
                    if (isset($_POST['form'][$product_id])) {
                        $rsl_table[$product_id] = [
                            'order_item_id' => $product_id,
                            'item_name' => $p_data['nom'],
                            'commercant' => $o_data['name'],
                            'commercant_id' => $o_data['merchant']['id'],
                            'order_id' => $p_data['order_id'],
                            'prix_initial' => $p_data['prix_total'],
                            'prix_final' => doubleval($_POST['form'][$product_id]['ticket']),
                            'diffprixfinal' => $p_data['prix_total'] - doubleval($_POST['form'][$product_id]['ticket']),
                            'prix_commercant' => doubleval($_POST['form'][$product_id]['ticket-commercant']),
                            'diffprixcommercant' => $p_data['prix_total'] - doubleval($_POST['form'][$product_id]['ticket-commercant']),
                            'comment' => $_POST['form'][$product_id]['comment'],
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
                    ['order_id' => $order_mid],
                    ['refund_shipping' => $_POST['form']['refund_shipping'],
                    'input' => 'done',
                    'commentaires_fraislivraison' => $_POST['form']['commentaire_client'],
                    'commentaires_ticket' => $_POST['form']['commentaire_commercant'], ]
                );
                $session->getFlashBag()->add('success', 'Information enregistrée avec succès');

                return $this->redirectToRoute('refundInput', ['id' => $id]);
            } catch (Exception $e) {
                $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de l\'enregistrement.');
            }
        }

        return $this->render('ApdcApdcBundle::refund/input.html.twig', [
            'form' => $form_input->createView(),
            'order' => $order,
            'total' => $total,
            'id' => $id,
            'refund_shipping' => $mage->checkRefundShipping($order_mid),
            'commentaire_client' => $commentaire_client,
            'commentaire_commercant' => $commentaire_commercant,
			'customer_name' => $customer_name,
			'mistral_hours' => $mage->getMistralDelivery(),
        ]);
    }

    public function refundDigestAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');
        $session = $request->getSession();
		$order = $mage->getRefunds($id);

        $total = $order[-1]['merchant']['total'];
        $refund_total = $order[-1]['merchant']['refund_total'];
        $refund_diff = $order[-1]['merchant']['refund_diff'];
        $refund_shipping_amount = $order[-1]['order']['refund_shipping_amount'];
        $order_mid = $order[-1]['order']['mid'];
        $order_header = $order[-1]['order'];
        unset($order[-1]);

        $refund_full = $mage->getRefundfull($refund_diff, $refund_shipping_amount);

        $files = $this->getUploadedFiles($id);
        foreach ($order as $merchant_id => $merchant_part) {
            if (isset($files[$merchant_id])) {
                $order[$merchant_id]['merchant']['ticket'] = $files[$merchant_id]['url'];
            } else {
                $order[$merchant_id]['merchant']['ticket'] = $files[-1]['url'];
            }
        }
        ksort($order);

        $entity_submit = new \Apdc\ApdcBundle\Entity\Model();
        $form_submit = $this->createFormBuilder($entity_submit);
        $form_submit->setAction($this->generateUrl('refundDigest', array('id' => $id)));
        $form_submit = $form_submit->getForm();

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

                    if ($refund_full != 0) {
                        $mage->updateEntryToOrderField(['order_id' => $order_mid], ['digest' => 'done']);
                    } else {
                        $mage->updateEntryToOrderField(['order_id' => $order_mid], [
                            'digest' => 'done',
                            'refund' => 'no_refund',
                        ]);
                    }
                } catch (\Exception $e) {
                    $session->getFlashBag()->add('error', 'Magento: '.$e->getMessage());
                }
            }
        }

        return $this->render('ApdcApdcBundle::refund/digest.html.twig', [
            'total' => $total,
            'refund_total' => $refund_total,
            'refund_diff' => $refund_diff,
            'refund_shipping' => $refund_shipping_amount,
            'refund_full' => $refund_full,
            'order_header' => $order_header,
            'order' => $order,
            'id' => $id,
            'show_creditmemo' => $mage->checkdisplaybutton($id, 'creditmemo'),
			'forms' => [$form_submit->createView()],
			'mistral_hours' => $mage->getMistralDelivery(),
        ]);
    }

    public function refundFinalAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $adyen = $this->container->get('apdc_apdc.adyen');
        $mage = $this->container->get('apdc_apdc.magento');
        $session = $request->getSession();

        $order = $mage->getRefunds($id);
        $orders = $mage->getAdyenPaymentByPsp();

        $refund_diff = $order[-1]['merchant']['refund_diff'];
        $order_mid = $order[-1]['order']['mid'];

        $refund = new IndiRefund();
        $form = $this->createForm(IndiRefundType::class, $refund);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $value = $form['value']->getData();
                $originalReference = $form['originalReference']->getData();

                $adyen->refund($value, $originalReference);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $mage->updateEntryToOrderField(['order_id' => $order_mid], ['refund' => 'done_with_adyen']);

            $session->getFlashBag()->add('success', 'Remboursement via Adyen effectué.');

            return $this->redirectToRoute('refundClosure', ['id' => $id]);
		}

		// Si Hipay, MAJ BDD lorsqu'on clique sur le bouton "cloturer >>"
		$data_hipay_refund = array('message' => 'Cloturer >>');
		$hipay_refund_form = $this->createFormBuilder($data_hipay_refund)
			->add('submit', SubmitType::class, array(
				'label'	=> 'Cloturer >>',
				'attr' => array(
					'style'	=> 'margin-left:70px;',
				),
			))
			->getForm();
		$hipay_refund_form->handleRequest($request);

		if ($hipay_refund_form->isSubmitted() && $hipay_refund_form->isValid()) {
			$mage->updateEntryToOrderField(['order_id' => $order_mid], ['refund' => 'done_with_hipay']);
			
			return $this->redirectToRoute('refundClosure', ['id' => $id]);
		}

        return $this->render('ApdcApdcBundle::refund/final.html.twig', [
            'form' => $form->createView(),
            'refund_diff' => $refund_diff,
            'id' => $id,
			'orders' => $orders,
			'order'	=> $order,
			'hipay_refund_form' => $hipay_refund_form->createView(),
			'mistral_hours' => $mage->getMistralDelivery(),
        ]);
    }

    public function refundClosureAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');
        $order_history = $mage->getOrderHistory($id);
        $session = $request->getSession();
        $order = $mage->getRefunds($id);

        $refund_diff = $order[-1]['merchant']['refund_diff'];
        $refund_shipping_amount = $order[-1]['order']['refund_shipping_amount'];
        $order_mid = $order[-1]['order']['mid'];

        $entity_submit = new \Apdc\ApdcBundle\Entity\Model();
        $form_submit = $this->createFormBuilder($entity_submit);
        $form_submit->setAction($this->generateUrl('refundClosure', array('id' => $id)));
        $form_submit = $form_submit->getForm();

        if ($request->isMethod('POST')) {
            $form_submit->handleRequest($request);
            if (!is_null($_POST['close'])) {
                try {
                    //Close order
                    $close = $mage->setCloseStatus($id);
                } catch (Exception $e) {
                    $session->getFlashBag()->add('error', 'Magento: '.$e->getMessage());
                }

                $mage->updateEntryToOrderField(['order_id' => $order_mid], ['closure' => 'done']);

                $session->getFlashBag()->add('success', 'Commande cloturée avec succès!');

                return $this->redirectToRoute('refundClosure', ['id' => $id]);
            }
        }

        return $this->render('ApdcApdcBundle::refund/closure.html.twig', [
            'order_history' => $order_history,
            'id' => $id,
            'refund_full' => $mage->getRefundfull($refund_diff, $refund_shipping_amount),
            'show_close' => $mage->checkdisplaybutton($id, 'close'),
			'forms' => [$form_submit->createView()],
			'mistral_hours' => $mage->getMistralDelivery(),

        ]);
    }

    public function refundPostClosureIndexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');
        $orders = $mage->getAdyenPaymentByMerchRef();

        return $this->render('ApdcApdcBundle::refund/post_closure_index.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function refundPostClosureFormAction(Request $request, $psp)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $adyen = $this->container->get('apdc_apdc.adyen');
        $mage = $this->container->get('apdc_apdc.magento');

        $orders = $mage->getAdyenPaymentByPsp();

        $refund = new IndiRefund();
        $form = $this->createForm(IndiRefundType::class, $refund);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $value = $form['value']->getData();
                $originalReference = $form['originalReference']->getData();

                $adyen->refund($value, $originalReference);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $this->get('session')->getFlashBag()->add('notice', 'Remboursement effectué !');

            return $this->redirectToRoute('refundPostClosureIndex');
        }

        return $this->render('ApdcApdcBundle::refund/post_closure_form.html.twig', [
            'form' => $form->createView(),
            'psp' => $psp,
            'orders' => $orders,
        ]);
    }
}
