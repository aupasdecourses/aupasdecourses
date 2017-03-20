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

		$stat = $factu->stats_clients();

		return $this->render('ApdcApdcBundle::customer/statCustomer.html.twig', [
			'stat'			=> $stat,	
		]); 
	
	}

	public function loyaltyCustomerAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$factu = $this->container->get('apdc_apdc.billing');

		if (isset($_GET['date_debut'])) {
			$list				= $factu->get_list_orderid();
			$date_debut			= $_GET['date_debut'];
			$date_fin			= $_GET['date_fin'];
			$data_clients		= $factu->data_clients($date_debut, $date_fin);
		}
			return $this->render('ApdcApdcBundle::customer/loyaltyCustomer.html.twig', [
				'date_debut'			=> $date_debut,
				'date_fin'				=> $date_fin,	
				'data_clients'			=> $data_clients,
			]); 
	
	}

	public function statTicketAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		return $this->render('ApdcApdcBundle::customer/statTicket.html.twig'); 
	
	}
}
