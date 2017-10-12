<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountingController extends Controller
{
    public function customersIndexAction(Request $request)
    {
		if(!$this->isGranted('ROLE_INDI_COMPTABILITE')) {
			return $this->redirectToRoute('root');
		}

		$entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto);

		$form_fromto->handleRequest($request);

		if ($form_fromto->isSubmitted() && $form_fromto->isValid()) {
			return $this->redirectToRoute('accountingCustomersAll', [
				'from'	=> $entity_fromto->from,
				'to'	=> $entity_fromto->to
			]);
		}

		return $this->render('ApdcApdcBundle::accounting/customers_orders_index.html.twig', [
			'forms' => [
				$form_fromto->createView(),
			]
		]);
	}

	public function customersAllAction(Request $request, $from, $to)
	{
		if(!$this->isGranted('ROLE_INDI_COMPTABILITE')) {
			return $this->redirectToRoute('root');
		}	

		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto, [
			'action'	=> $this->generateUrl('accountingCustomersIndex'),
		]);

		$form_fromto->get('from')->setData($from);
		$form_fromto->get('to')->setData($to);

		return $this->render('ApdcApdcBundle::accounting/customers_orders_all.html.twig', [
			'forms'		=> [ $form_fromto->createView()],
			'orders'	=> $mage->getOrdersByCustomer($from, $to)
		]);
	}
}
