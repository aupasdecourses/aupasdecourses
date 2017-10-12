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

		$accounting = $this->container->get('apdc_apdc.accounting');

		$orders = $accounting->getOrdersByCustomer();

		return $this->render('ApdcApdcBundle::accounting/customers_orders.html.twig', [
			'orders' => $orders,
		]);
    }
}
