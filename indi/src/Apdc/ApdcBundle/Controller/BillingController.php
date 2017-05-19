<?php

namespace Apdc\ApdcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Apdc\ApdcBundle\Entity\Payout;
use Apdc\ApdcBundle\Form\PayoutType;

class BillingController extends Controller
{
    public function indexAction(Request $request)
    {
    }

    public function verifAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
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
                'display_button' => false,
                'sum_items_facturation' => 'NA',
                'sum_items_magento' => 'NA',
                'sum_order_magento' => 'NA',
                'sum_shipping_magento' => 'NA',
                'sum_discount_magento' => 'NA',
                'diff_facturation_magento' => 'NA',
                'status_ok_count' => 'NA',
                'status_nok_count' => 'NA',
                'status_processing_count' => 'NA',
                'order_total' => 'NA',
                'id_max' => 'NA',
                'id_min' => 'NA',
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
            'verif' => $verif,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
        ]);
    }

    public function detailsAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $factu = $this->container->get('apdc_apdc.billing');

        $session = $request->getSession();

        if (isset($_GET['date_debut'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin = $factu->end_month($date_debut);
            $bill = $factu->getDataFacturation('indi_billingdetails', $date_debut);
        }

        $check_date = (strtotime(str_replace('/', '-', $date_debut)) < strtotime('2017/01/01')) ? 1 : 0;

        return $this->render('ApdcApdcBundle::billing/details.html.twig', [
            'bill' => $bill,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'check_date' => $check_date,
        ]);
    }

    public function summaryAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $factu = $this->container->get('apdc_apdc.billing');

        $session = $request->getSession();

        if (isset($_GET['date_debut'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin = $factu->end_month($date_debut);
            $summary = $factu->getDataFacturation('indi_billingsummary', $date_debut);
        }

        $check_date = (strtotime(str_replace('/', '-', $date_debut)) < strtotime('2017/01/01')) ? 1 : 0;

        return $this->render('ApdcApdcBundle::billing/summary.html.twig', [
            'summary' => $summary,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'check_date' => $check_date,
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
        $file_path=$billing_path.$id.'.pdf';

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
                        $data=$factu->sendBilling($bill,$file_path);
                        $return=$pdfbilling->send($data);
                        if($return){
                            $mage->updateEntryToBillingSummary(['increment_id' => $id], array('date_sent' => $data['date_sent']));
                            foreach($data['mails'] as $m){
                                $session->getFlashBag()->add('success', 'PDF envoyé avec succès à '.$data['mail_vars']['commercant'].": ".$m.' !');
                            }
                        }else{
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

        return $this->render('ApdcApdcBundle::billing/one.html.twig', [
            'forms' => [$form_id->createView()],
            'billing_form' => $form_billing->createView(),
            'form_payout' => $form_download->createView(),
            'form_download' => $form_download->createView(),
            'form_send' => $form_download->createView(),
            'bill' => $bill,
            'check_date' => $check_date,
            'check_file' => $check_file,
        ]);
    }

    public function payoutIndexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');

        $entity_prepayout = new \Apdc\ApdcBundle\Entity\PayoutChoice();
        $form_prepayout = $this->createForm(\Apdc\ApdcBundle\Form\PayoutChoiceType::class, $entity_prepayout);
        $form_prepayout->handleRequest($request);

        $choice = $form_prepayout['choice']->getData();

        if ($form_prepayout->isSubmitted() && $form_prepayout->isValid()) {
            return $this->redirectToRoute('billingPayoutSubmit', [
                'choice' => $choice,
            ]);
        }

        $repository = $this->getDoctrine()->getManager()->getRepository('ApdcApdcBundle:Payout');
        $payout_list = $repository->findAll();

        return $this->render('ApdcApdcBundle::billing/payoutIndex.html.twig', [
            'payout_list' => $payout_list,
            'form_prepayout' => $form_prepayout->createView(),
        ]);
    }

    public function payoutSubmitAction(Request $request, $choice)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');
        $merchants = $mage->getApdcBankFields();

        $session = $request->getSession();

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

        return $this->render('ApdcApdcBundle::billing/payoutSubmit.html.twig', [
            'form' => $form->createView(),
            'merchants' => $merchants,
            'choice' => $choice,
        ]);
    }
}
