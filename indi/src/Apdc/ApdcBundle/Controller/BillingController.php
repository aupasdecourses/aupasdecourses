<?php

namespace Apdc\ApdcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Apdc\ApdcBundle\Entity\Payout;
use Apdc\ApdcBundle\Form\PayoutType;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BillingController extends Controller
{
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_GESTION')) {
            return $this->redirectToRoute('root');
        }

        $factu = $this->container->get('apdc_apdc.billing');

        $result = [];
        if (isset($_GET['date_debut'])) {
            $date_debut     = $_GET['date_debut'];
            $today          = date('Y-m-d H:i:s');
            $date_fin       = date('t-m-Y', strtotime($today));
            $summary        = $factu->getDataFactu('indi_billingsummary', $date_debut, $date_fin);


            foreach ($summary as $sum) {
                
                $result[$sum['shop']]['date_payout'] = $sum['date_payout'];
                $result[$sum['shop']]['shop'] = $sum['shop'];
                $result[$sum['shop']]['sum_items'] += $sum['sum_items'];
                $result[$sum['shop']]['sum_due'] += $sum['sum_due'];
                $result[$sum['shop']]['sum_payout'] += $sum['sum_payout'];
            }


            $debut = date_create(str_replace('/', '-', $date_debut));
            $fin = date_create(str_replace('/', '-', $date_fin));
            $intervalM = date_diff($fin, $debut)->m+1;
            $intervalY = date_diff($fin, $debut)->y;
            $interval = ($intervalY * 12) + ($intervalM);
        } else {
            $interval = 0;
        }

        return $this->render('ApdcApdcBundle::billing/index.html.twig', [
            'result'            => $result,
            'date_debut'        => $date_debut,
            'date_fin'          => $date_fin,
            'months_diff'       => $interval,
        ]);
    }

    public function verifAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $factu = $this->container->get('apdc_apdc.billing');
        $mage = $this->container->get('apdc_apdc.magento');
        $session = $request->getSession();

        if (isset($_GET['date_debut'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin = $factu->end_month($date_debut);
            if (!isset($_POST['submit'])) {
                $verif = $factu->data_facturation_verif($date_debut, $date_fin);
            }
        } else {
            $verif = [
                'verif_mois' => false,
                'verif_noentry' => false,
                'verif_noprocessing' => false,
                'verif_totaux' => false,
                'verif_nomissingcom' => false,
                'missing_com_att_count' => 0,
                'display_button' => false,
                'sum_items_facturation' => 'NA',
                'sum_items_magento' => 'NA',
                'sum_order_magento' => 'NA',
                'sum_shipping_magento' => 'NA',
                'sum_discount_magento' => 'NA',
                'sum_discount_coupon_magento' => 'NA',
                'diff_facturation_magento' => 'NA',
                'status_ok_count' => 'NA',
                'status_nok_count' => 'NA',
                'status_processing_count' => 'NA',
                'order_total' => 'NA',
                'id_max' => 'NA',
                'id_min' => 'NA',
                'orders' => array(),
            ];
        }

        $entity_input = new \Apdc\ApdcBundle\Entity\Input();
        $form_input = $this->createFormBuilder($entity_input);
        $form_input->setAction($this->generateUrl('billingVerif', ['date_debut' => $date_debut]));
        $form_input = $form_input->getForm();

        if (isset($_POST['submit'])) {
            try {
                $bill = $factu->data_facturation($date_debut, $date_fin, 'all');
                foreach ($bill['details'] as $row) {
                    $mage->updateEntryToBillingDetails(['order_shop_id' => $row['order_shop_id']], $row);
                }
                foreach ($bill['summary'] as $row) {
                    $mage->updateEntryToBillingSummary(['increment_id' => $row['increment_id']], $row);
                }
                $update = $factu->updateDataBillingId($date_debut);
                foreach ($update as $row) {
                    $mage->updateEntryToBillingDetails(['order_shop_id' => $row['order_shop_id']], $row);
                }

                $session->getFlashBag()->add('success', 'Information enregistrée avec succès dans indi_billing_summary');
                $session->getFlashBag()->add('success', 'Information enregistrée avec succès dans indi_billing_details');

                return $this->redirectToRoute('billingSummary', ['date_debut' => $date_debut]);
            } catch (Exception $e) {
                $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de l\'enregistrement.');
            }
        }

        return $this->render('ApdcApdcBundle::billing/verif.html.twig', [
            'form' => $form_input->createView(),
            'verif' => $verif['result'],
            'details' => $verif['details'],
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
        ]);
    }

    public function detailsAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $factu = $this->container->get('apdc_apdc.billing');

        $session = $request->getSession();

        $defaultDataCSV = array('message' => 'Export');
        $formCSV = $this->createFormBuilder($defaultDataCSV)
            ->add("Exporter", SubmitType::class,array('label'=>'Exporter CSV','attr'=>array('class'=>'btn btn-lg btn-success','style'=>'float:right')))
            ->getForm();
        $formCSV->handleRequest($request);

        if (isset($_GET['date_debut'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin = $factu->end_month($date_debut);
            $bill = $factu->getDataFacturation('indi_billingdetails', $date_debut);

            /***** Export CSV des commandes facturées *****/
            if ($formCSV->isSubmitted() && $formCSV->isValid()) {
                $response = new StreamedResponse();
                $response->setCallback(function() use($bill) {
                    $handle = fopen('php://output', 'w+');

                    fputcsv($handle, array(
                        'Date creation',
                        'Date livraison',
                        '#Commande',
                        '#Facture',
                        'Nom client',
                        'Commercant',
                        'Commande Client HT',
                        'Commande Client TTC',
                        'Avoir HT',
                        'Avoir TTC',
                        'Valeur Ticket HT',
                        'Valeur Ticket TTC',
                        'Commission APDC (HT)',
                        'Versement Commercant (HT)'
                    ),';');

                    foreach ($bill as $order) {
                        fputcsv($handle, array(
                            $order['creation_date'],
                            $order['delivery_date'],
                            $order['increment_id'],
                            $order['id_billing'],
                            $order['customer_name'],
                            $order['shop'],
                            $order['sum_items_HT'],
                            $order['sum_items'],
                            $order['sum_items_credit_HT'],
                            $order['sum_items_credit'],
                            $order['sum_ticket_HT'],
                            $order['sum_ticket'],
                            $order['sum_commission_HT'],
                            $order['sum_due_HT']
                        ),';');
                    }

                    fclose($handle);
                });

                $response->setStatusCode(200);
                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition','attachment; filename="commandes-facturees"'.$date_debut.'".csv"');
      
                return $response;
            } // Fin export CSV 
        }


        $check_date = (strtotime(str_replace('/', '-', $date_debut)) < strtotime('2017/01/01')) ? 1 : 0;

        return $this->render('ApdcApdcBundle::billing/details.html.twig', [
            'bill' => $bill,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'check_date' => $check_date,
            'formCSV' => $formCSV->createView(),
        ]);
    }

    public function summaryAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $factu = $this->container->get('apdc_apdc.billing');

        $session = $request->getSession();

        $defaultDataCSV = array('message' => 'Export');
        $formCSV = $this->createFormBuilder($defaultDataCSV)
            ->add("Exporter", SubmitType::class,array('label'=>'Exporter CSV','attr'=>array('class'=>'btn btn-lg btn-success','style'=>'float:right')))
            ->getForm();
        $formCSV->handleRequest($request);

        if (isset($_GET['date_debut'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin = $factu->end_month($date_debut);
            $summary = $factu->getDataFacturation('indi_billingsummary', $date_debut);


      

            /***** Export CSV des factures commercants *****/
            if ($formCSV->isSubmitted() && $formCSV->isValid()) {
                $response = new StreamedResponse();
                $response->setCallback(function() use($summary) {
                    $handle = fopen('php://output', 'w+');

                    fputcsv($handle, array(
                        '#Facture',
                        'Magasins',
                        'Commande (TTC)',
                        'Ticket TTC',
                        'Ticket HT',
                        'Commission TTC',
                        'Commission HT',
                        'Total Commercant TTC',
                        'Total Commercant HT',
                        'Remise TTC',
                        'Remise HT',
                        'Frais bancaires TTC',
                        'Frais bancaires HT',
                        'Virement (TTC)'
                    ),';');

                    foreach ($summary as $order) {
                        fputcsv($handle, array(
                            $order['increment_id'],
                            $order['shop'],
                            $order['sum_items'],
                            $order['sum_ticket'],
                            $order['sum_ticket_HT'],
                            $order['sum_commission'],
                            $order['sum_commission_HT'],
                            $order['sum_due'],
                            $order['sum_due_HT'],
                            $order['discount_shop'],
                            $order['discount_shop_HT'],
                            $order['processing_fees'],
                            $order['processing_fees_HT'],
                            $order['sum_payout']
                        ),';');
                    }

                    fclose($handle);
                });

                $response->setStatusCode(200);
                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition','attachment; filename="facturation"'.$date_debut.'".csv"');
      
                return $response;
            } // Fin export CSV 
        }

        $check_date = (strtotime(str_replace('/', '-', $date_debut)) < strtotime('2017/01/01')) ? 1 : 0;

        return $this->render('ApdcApdcBundle::billing/summary.html.twig', [
            'summary' => $summary,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'check_date' => $check_date,
            'formCSV'   => $formCSV->createView(),
        ]);
    }

    public function billingOneAction(Request $request, $id)
    {
        $factu = $this->container->get('apdc_apdc.billing');
        $mage = $this->container->get('apdc_apdc.magento');
        $pdfbilling = $this->container->get('apdc_apdc.pdfbilling');
        $session = $request->getSession();
        $billing_path = $this->get('kernel')->getRootDir().'/../web/docs/billing/';

        //Select Billing id form
        $entity_id = new \Apdc\ApdcBundle\Entity\OrderId();
        $form_id = $this->createForm(\Apdc\ApdcBundle\Form\BillingId::class, $entity_id, [
            'action' => $this->generateUrl('billingOne', ['id' => $id]),
        ]);
        $form_id->get('id')->setData($id);

        //Billing Finalization Input Form
        $entity_billing = new \Apdc\ApdcBundle\Entity\Billing();
        $form_billing = $this->createForm(\Apdc\ApdcBundle\Form\BillingForm::class, $entity_billing, [
            'action' => $this->generateUrl('billingOne', ['id' => $id]),
        ]);

        $data_billing_form = $request->get('billing_form');
        if ($data_billing_form != null) {
            $data_billing_form['id_billing'] = $id;
            $result = $factu->finalizeFacturation($data_billing_form, $id);
            $mage->updateEntryToBillingSummary(['increment_id' => $data_billing_form['id_billing']], $result);
        }

        //Payout form
        $entity_input = new \Apdc\ApdcBundle\Entity\Input();
        $form_payout = $this->createFormBuilder($entity_input);
        $form_payout->setAction($this->generateUrl('billingOne', array('id' => $id)));
        $form_payout = $form_payout->getForm();

        //Download form
        $entity_input = new \Apdc\ApdcBundle\Entity\Input();
        $form_download = $this->createFormBuilder($entity_input);
        $form_download->setAction($this->generateUrl('billingOne', array('id' => $id)));
        $form_download = $form_download->getForm();

        //Send form
        $entity_input = new \Apdc\ApdcBundle\Entity\Input();
        $form_send = $this->createFormBuilder($entity_input);
        $form_send->setAction($this->generateUrl('billingOne', array('id' => $id)));
        $form_send = $form_send->getForm();

        $bill = $factu->getOneBilling($id);
        $file_path = $billing_path.$id.'.pdf';

        if (isset($_POST['submit'])) {
            switch ($_POST['submit']) {
                case 'payout':
                    try {
                        $mage->updateEntryToBillingSummary(['increment_id' => $id], ['date_payout' => \Varien_Date::toTimestamp(\Varien_Date::now())]);
                        $session->getFlashBag()->add('success', 'Payout checké avec succès!');

                        return $this->redirectToRoute('billingOne', ['id' => $id]);
                        break;
                    } catch (Exception $e) {
                        $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de l\'enregistrement du payout.');
                    }
                case 'download':
                    try {
                        $pdfbilling->printBillingShop($bill);
                        $pdfbilling->save($billing_path.$id.'.pdf');
                        $session->getFlashBag()->add('success', 'PDF généré avec succès!');

                        return $this->redirectToRoute('billingOne', ['id' => $id]);
                    } catch (Exception $e) {
                        $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de la génération du PDF.');
                    }
                    break;
                case 'send':
                    try {
                        $pdfbilling->printBillingShop($bill);
                        $pdfbilling->save($file_path);
                        $data = $factu->sendBilling($bill, $file_path);
                        $return = $pdfbilling->send($data);
                        if ($return) {
                            $mage->updateEntryToBillingSummary(['increment_id' => $id], array('date_sent' => $data['date_sent']));
                            foreach ($data['mails'] as $m) {
                                $session->getFlashBag()->add('success', 'PDF envoyé avec succès à '.$data['mail_vars']['commercant'].': '.$m.' !');
                            }
                        } else {
                            $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de la génération du PDF.');
                        }

                        return $this->redirectToRoute('billingOne', ['id' => $id]);
                    } catch (Exception $e) {
                        $session->getFlashBag()->add('error', 'Une erreur s\'est produite lors de la génération du PDF.');
                    }
                    break;
            }
        }

        $check_date = (strtotime(str_replace('/', '-', $bill['summary'][0]['billing_month'])) < strtotime('2017/01/01')) ? 1 : 0;

        $check_file = file_exists($billing_path.$id.'.pdf');


		$data_payout = array('message' => 'Redirection vers formulaire de virement commercant');
		$payout_form = $this->createFormBuilder($data_payout)
			->add('Payout', SubmitType::class, array(
				'label'	=> 'Virement',
				'attr'	=> array(
					'class' => 'btn btn-warning btn-lg',
					'style'	=> 'float:right; margin-top:-60px;')))
				->getForm();
		
		$payout_form->handleRequest($request);

		if ($payout_form->isSubmitted() && $payout_form->isValid()) {
			$session->set('increment_id', $bill['increment_id']);
			$session->set('sum_payout', $bill['summary'][0]['sum_payout']);
			return $this->redirectToRoute('billingPayoutSubmit', ['choice' => $bill['summary'][0]['shop_id']]);
		}

		// NE PAS CONFONDRE FORM_PAYOUT ET PAYOUT_FORM
        return $this->render('ApdcApdcBundle::billing/one.html.twig', [
            'forms' => [$form_id->createView()],
            'billing_form' => $form_billing->createView(),
            'form_payout' => $form_download->createView(),
            'form_download' => $form_download->createView(),
            'form_send' => $form_download->createView(),
            'bill' => $bill,
            'check_date' => $check_date,
			'check_file' => $check_file,
			'payout_form' => $payout_form->createView(),
        ]);
    }

    public function payoutIndexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');
		$bill = $this->container->get('apdc_apdc.billing');

		$merchants = $bill->getApdcBankFields();
		$choices = [];

		foreach ($merchants as $key => $value) {
			$choices[$value['name']] = $value['id_attribut_commercant'];
		}
		ksort($choices);

		$data = array('message' => 'Liste deroulante de choix de commercant');
		$choice_form = $this->createFormBuilder($data)
			->add('Choice', ChoiceType::class, array(
				'label'		=> 'Magasin',
				'choices'	=> $choices,
				'required'	=> true
			))
			->add('Submit', SubmitType::class, array(
				'label'	=> 'Continuer'
			))
			->getForm();
		
		$choice_form->handleRequest($request);

		if ($choice_form->isSubmitted() && $choice_form->isValid()) {
			return $this->redirectToRoute('billingPayoutSubmit', [
				'id'	=> $choice_form['Choice']->getData(),
			]);
		}

        $repository = $this->getDoctrine()->getManager()->getRepository('ApdcApdcBundle:Payout');
        $payout_list = $repository->findAll();

        return $this->render('ApdcApdcBundle::billing/payout_index.html.twig', [
            'payout_list' => $payout_list,
            'choice_form' => $choice_form->createView(),
        ]);
    }

    public function payoutSubmitAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $bill = $this->container->get('apdc_apdc.billing');
        $merchants = $bill->getApdcBankFields();

		$session = $request->getSession();
		$increment_id	= $session->get('increment_id');
		$sum_payout		= $session->get('sum_payout');

        $adyen = $this->container->get('apdc_apdc.adyen');
        $payout = new Payout();
        $form = $this->createForm(PayoutType::class, $payout);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($payout);
            $em->flush();

            try {
                $value = $form['value']->getData();
                $iban = $form['iban']->getData();
                $ownerName = $form['ownerName']->getData();
                $reference = $form['reference']->getData();
                $shopperEmail = $form['shopperEmail']->getData();
                $shopperReference = $form['shopperReference']->getData();

                $adyen->payout($value, $iban, $ownerName, $reference, $shopperEmail, $shopperReference);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $session->getFlashBag()->add('success', 'Payout effectué avec succès');

            return $this->redirectToRoute('billingPayoutIndex');
        }

        return $this->render('ApdcApdcBundle::billing/payout_submit.html.twig', [
            'form'			=> $form->createView(),
            'merchants'		=> $merchants,
			'id'			=> $id,
			'increment_id'	=> $increment_id,
			'sum_payout'	=> $sum_payout,
        ]);
    }
}
