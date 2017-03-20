<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CustomerController extends Controller
{
	public function statCustomerAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		//$stats	= $this->container->get('apdc_apdc.stats');
		$factu	= $this->container->get('apdc_apdc.billing');

		$list = $factu->get_list_orderid();
		$data = $factu->stats_clients();

		return $this->render('ApdcApdcBundle::customer/statCustomer.html.twig', [
			'orderid_array' => $list,
			'data'			=> $data,	
		]); 
	
	}

	public function loyaltyCustomerAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		return $this->render('ApdcApdcBundle::customer/loyaltyCustomer.html.twig'); 
	
	}

	public function statTicketAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		return $this->render('ApdcApdcBundle::customer/statTicket.html.twig'); 
	
	}
}
