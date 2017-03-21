<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatController extends Controller
{
	public function statCustomerAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$stats	= $this->container->get('apdc_apdc.stats');
		$stat	= $stats->stats_clients();

		return $this->render('ApdcApdcBundle::stat/statCustomer.html.twig', [
			'stat'			=> $stat,	
		]); 
	
	}

	public function loyaltyCustomerAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stats');

		if (isset($_GET['date_debut'])) {
			$list				= $stats->get_list_orderid();
			$date_debut			= $_GET['date_debut'];
			$date_fin			= $_GET['date_fin'];
			$data_clients		= $stats->data_clients($date_debut, $date_fin);
		}
			return $this->render('ApdcApdcBundle::stat/loyaltyCustomer.html.twig', [
				'date_debut'			=> $date_debut,
				'date_fin'				=> $date_fin,	
				'data_clients'			=> $data_clients,
			]); 
	}

	public function statVoucherAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stats');

		if (isset($_GET['date_debut']) && isset($_GET['date_fin'])) {
			$list			= $stats->get_list_orderid();
			$date_debut		= $_GET['date_debut'];
			$date_fin		= $_GET['date_fin'];
			$data_coupon	= $stats->data_coupon($date_debut, $date_fin);
		}

		return $this->render('ApdcApdcBundle::stat/statVoucher.html.twig', [
			'date_debut'	=> $date_debut,
			'date_fin'		=> $date_fin,
			'data_coupon'	=> $data_coupon,
		]); 
	
	}


	public function rateOrderAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}
		
		$stats = $this->container->get('apdc_apdc.stats');

		if (isset($_GET['date_debut'])) {
			$date_debut	= $_GET['date_debut'];
			$date_fin	= $stats->end_month($date_debut);
		}

//		$notes = $stats->getNotes($date_debut, $date_fin);
		
		return $this->render('ApdcApdcBundle::stat/rateOrder.html.twig', [
			'date_debut'	=> $date_debut,
			'date_fin'		=> $date_fin,
//			'notes'			=> $notes,
		]);
	}
}
