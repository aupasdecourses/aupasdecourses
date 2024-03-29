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
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}

		$stats	= $this->container->get('apdc_apdc.stats');

		return $this->render('ApdcApdcBundle::stat/statCustomer.html.twig', [
			'stat'				=> $stats->getCustomerStatData(),
			'customer_url'		=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/customer/edit/id/'
		]); 
	
	}

	public function statCustomerNoOrderAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stats');

		return $this->render('ApdcApdcBundle::stat/statCustomerNoOrder.html.twig', [
			'stat'				=> $stats->getCustomerNoOrderStatData(),
			'customer_url'		=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/customer/edit/id/'
		]);
	}

	public function loyaltyCustomerAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stats');

		if (isset($_GET['date_debut'])) {
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
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}

		$stats = $this->container->get('apdc_apdc.stats');

		if (isset($_GET['date_debut']) && isset($_GET['date_fin'])) {
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


	public function noteOrderAction(Request $request)
	{
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}
		
		$stats = $this->container->get('apdc_apdc.stats');

		if (isset($_GET['date_debut'])) {
			$date_debut	= $_GET['date_debut'];
			$date_fin	= $stats->end_month($date_debut);
			$notes = $stats->getNotes($date_debut, $date_fin);
			$json_data = $stats->histogramme($date_debut, $date_fin);
		}

		return $this->render('ApdcApdcBundle::stat/noteOrder.html.twig', [
			'date_debut'	=> $date_debut,
			'date_fin'		=> $date_fin,
			'notes'			=> $notes,
			'json_data'		=> $json_data,
			'order_url'		=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/sales_order/view/order_id/',
			'customer_url'	=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/customer/edit/id/'
		]);
	}

	public function marginAction(Request $request) {
		if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');
		$margin = $mage->getMargin();

		return $this->render('ApdcApdcBundle::stat/margin.html.twig', [
			'margin' => $margin,
		]);
	}

	public function productEvolutionAction(Request $request)
    {   
        if (!$this->isGranted('ROLE_INDI_COMMUNICATION')) {
            return $this->redirectToRoute('root');
        }
    
        $stats = $this->container->get('apdc_apdc.stats');

        if (isset($_GET['date_debut'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin	= $_GET['date_fin'];
            $sku		= $_GET['sku'];

            $products = $stats->cleanUpProductEvolutionByDateAndSku($date_debut, $date_fin, $sku);
        }

        return $this->render('ApdcApdcBundle::stat/productEvolution.html.twig', [
            'products'          => $products,
            'date_debut'		=> $date_debut,
            'date_fin'			=> $date_fin,
            'sku'				=> $sku,
            'json_products'		=> json_encode($products),
        ]);
    }

    public function merchantProductPriceVariationAction()
    {
    	$stats = $this->container->get('apdc_apdc.stats');

    	if (isset($_GET['date_debut'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin	= $_GET['date_fin'];

            $prices = $stats->getMerchantProductPriceVariation($date_debut, $date_fin);
        }

    	return $this->render('ApdcApdcBundle::stat/merchantProductPriceVariation.html.twig', [
    		'prices'		=> $prices,
    		'date_debut'	=> $date_debut,
    		'date_fin'		=> $date_fin,
    	]);
    }

    public function merchantQuarterLocationAction()
    {
    	$stats = $this->container->get('apdc_apdc.stats');

    	$location = $stats->getMerchantQuarterLocation();

    	return $this->render('ApdcApdcBundle::stat/merchantQuarterLocation.html.twig', [
    		'location'	=> $location,
    	]);
    } 
}
