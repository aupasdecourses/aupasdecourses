<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BillingController extends Controller
{
	public function indexAction(Request $request, $from, $to)
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto);

		$form_fromto->handleRequest($request);

		if($form_fromto->isValid())
			return $this->redirectToRoute('billingIndex', [
				'from'	=> $entity_fromto->from,
				'to'	=> $entity_fromto->to
			]);

		/* STRTOTIME = EPIC PHP FUNCTION */
		if(!isset($from)&& !isset($to))
			return $this->redirectToRoute('billingIndex', [
				'from'	=> date('Y-m-d', strtotime('first day of this month')), 
				'to'	=> date('Y-m-d', strtotime('last day of this month'))
			]);

		$form_fromto->get('from')->setData($from);
		$form_fromto->get('to')->setData($to);

		$orders = $mage->getOrders($from, $to);	

		return $this->render('ApdcApdcBundle::billing/index.html.twig', [
			'forms'	=> [ $form_fromto->createView() ],
			'orders' => $orders
		]);
	}
}
