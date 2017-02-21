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
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromtoMerchant = new \Apdc\ApdcBundle\Entity\FromToMerchant();
		$form_fromtoMerchant = $this->createForm(\Apdc\ApdcBundle\Form\FromToMerchant::class, $entity_fromtoMerchant);

		$form_fromtoMerchant->handleRequest($request);

		if($form_fromtoMerchant->isSubmitted() && $form_fromtoMerchant->isValid()){
			if($entity_fromtoMerchant->merchant <> -1) {
				return $this->redirectToRoute('billingOne', [
					'id' => $entity_fromtoMerchant->merchant,
					'from' => $entity_fromtoMerchant->from,
					'to' => $entity_fromtoMerchant->to
				]);
			} else {
				return $this->redirectToRoute('billingAll', [
					'from'	=> $entity_fromtoMerchant->from,
					'to'	=> $entity_fromtoMerchant->to
				]);
			}
		}

		return $this->render('ApdcApdcBundle::billing/index.html.twig', [
			'forms' => [
				$form_fromtoMerchant->createView(),
			]
		]);
	}


	public function billingAllAction(Request $request, $from, $to)
	{
		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromtoMerchant = new \Apdc\ApdcBundle\Entity\FromToMerchant();
		$form_fromtoMerchant = $this->createForm(\Apdc\ApdcBundle\Form\FromToMerchant::class, $entity_fromtoMerchant,			[
			'action' => $this->generateUrl('billingIndex')
		]);

		/* STRTOTIME = EPIC PHP FUNCTION */
/*		if(!isset($from)&& !isset($to))
			return $this->redirectToRoute('billingIndex', [
				'from'	=> date('Y-m-d', strtotime('first day of this month')), 
				'to'	=> date('Y-m-d', strtotime('last day of this month'))
			]);
 */
		$form_fromtoMerchant->get('from')->setData($from);
		$form_fromtoMerchant->get('to')->setData($to);

		return $this->render('ApdcApdcBundle::billing/all.html.twig', [
			'forms'	=> [ 
				$form_fromtoMerchant->createView(), 
			],
			'stores' => $mage->getMerchantsOrdersByStore(-1, $from, $to)
		]);
	}

	
	public function payoutIndexAction()
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}
	
		$repository = $this->getDoctrine()->getManager()->getRepository('ApdcApdcBundle:Payout');
		$payout_list = $repository->findAll();

		return $this->render('ApdcApdcBundle::billing/payoutIndex.html.twig', [
			'payout_list'	=> $payout_list,
		]);
	}

	public function payoutSubmitAction(Request $request)
	{

		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$adyen = $this->container->get('apdc_apdc.adyen');
		$payout = new Payout();
		$form = $this->createForm(PayoutType::class, $payout);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
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
			'form' => $form->createView(),
			]);
	}
}
