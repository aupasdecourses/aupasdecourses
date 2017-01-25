<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BillingController extends Controller
{
	public function indexAction(Request $request)
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto);

		$form_fromto->handleRequest($request);

		if($form_fromto->isSubmitted() && $form_fromto->isValid())
		{
			return $this->redirectToRoute('billingAll', [
				'from'	=> $entity_fromto->from,
				'to'	=> $entity_fromto->to
			]);
		}

		return $this->render('ApdcApdcBundle::billing/index.html.twig', [
			'forms'	=> [
				$form_fromto->createView()
			]
		]);
	}

	public function billingAllAction(Request $request, $from, $to)
	{
		$mage = $this->container->get('apdc_apdc.magento');

		return $this->render('ApdcApdcBundle::billing/all.html.twig', [
			'orders' => $mage->getOrders($from, $to)
		]);
	}
}
