<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Apdc\ApdcBundle\Entity\Payout;
use Apdc\ApdcBundle\Form\PayoutType;

class BillingController extends Controller
{
	public function indexAction(Request $request)
	{
        if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$factu = $this->container->get('apdc_apdc.billing');

		if(isset($_GET['date_debut'])) {
			$list		= $factu->get_list_orderid();
			$date_debut = $_GET['date_debut'];
			$date_fin	= $factu->end_month($date_debut);
			$bill		= $factu->data_facturation_products($date_debut, $date_fin, "creation");
		}

		return $this->render('ApdcApdcBundle::billing/index.html.twig', [
			'bill'			=> $bill,
			'date_debut'	=> $date_debut,
			'date_fin'		=> $date_fin,
		]);
	}


	public function payoutIndexAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$entity_prepayout = new \Apdc\ApdcBundle\Entity\PayoutChoice();
		$form_prepayout	= $this->createForm(\Apdc\ApdcBundle\Form\PayoutChoiceType::class, $entity_prepayout);
		$form_prepayout->handleRequest($request);

		$choice = $form_prepayout['choice']->getData();

		if ($form_prepayout->isSubmitted() && $form_prepayout->isValid()) {
			return $this->redirectToRoute('billingPayoutSubmit', [
				'choice'	=> $choice,
			]);
		}

		$repository		= $this->getDoctrine()->getManager()->getRepository('ApdcApdcBundle:Payout');
		$payout_list	= $repository->findAll();

		return $this->render('ApdcApdcBundle::billing/payoutIndex.html.twig', [
			'payout_list'		=> $payout_list,
			'form_prepayout'	=> $form_prepayout->createView(),
		]);
	}

	public function payoutSubmitAction(Request $request, $choice)
	{

		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');
		$merchants = $mage->getApdcBankFields();


		$adyen	= $this->container->get('apdc_apdc.adyen');
		$payout = new Payout();
		$form	= $this->createForm(PayoutType::class, $payout);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($payout);
			$em->flush();
 
		
			try {
				$value				= $form['value']->getData();
				$iban				= $form['iban']->getData();
				$ownerName			= $form['ownerName']->getData();
				$reference			= $form['reference']->getData();
				$shopperEmail		= $form['shopperEmail']->getData();
				$shopperReference	= $form['shopperReference']->getData();

				$adyen->payout($value, $iban, $ownerName, $reference, $shopperEmail, $shopperReference);
			} catch (Exception $e) {
				echo $e->getMessage();
			}
 
			return $this->redirectToRoute('billingPayoutIndex');
		}

		return $this->render('ApdcApdcBundle::billing/payoutSubmit.html.twig',
			[
				'form'		=> $form->createView(),
				'merchants'	=> $merchants,
				'choice'	=> $choice,
			]
		);
	}
}
