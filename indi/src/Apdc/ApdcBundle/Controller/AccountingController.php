<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountingController extends Controller
{

	public function customersOrdersAction(Request $request)
	{
		if(!$this->isGranted('ROLE_INDI_COMPTABILITE')) {
			return $this->redirectToRoute('root');
		}	

		$mage	= $this->container->get('apdc_apdc.magento');
		$bill	= $this->container->get('apdc_apdc.billing');

		if (isset($_GET['date_debut'])) {
			$date_debut = $_GET['date_debut'];
			$date_fin	= $bill->end_month($date_debut);
			$orders		= $mage->getOrdersByCustomer($date_debut, $date_fin);
		}

		return $this->render('ApdcApdcBundle::accounting/customers_orders.html.twig', [
			'orders'		=> $orders,
			'date_debut'	=> $date_debut,
			'date_fin'		=> $date_fin,
		]);
	}
}
