<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MapController extends Controller 
{
	public function merchantsAction()
	{
		return $this->render('ApdcApdcBundle::map/merchants.html.twig');
	}

	public function customersAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$stats	= $this->container->get('apdc_apdc.stats');
		$mage	= $this->container->get('apdc_apdc.magento');

		$json_data_for_map		= $stats->getCustomerMapData();


		/* Ajout des new customers dans table geocode_customers */
		$entity_submit_new_customers = new \Apdc\ApdcBundle\Entity\Model();
		$form_new_customers = $this->createFormBuilder($entity_submit_new_customers);
		$form_new_customers = $form_new_customers->getForm();

		if ($request->isMethod('POST')) {
			$form_new_customers->handleRequest($request);
			$new_customers_to_add	= $stats->addLatAndLong();
			echo'<pre>';
			print_R($new_customers_to_add);
			echo'<pre>';
		
		}

		return $this->render('ApdcApdcBundle::map/customers.html.twig',
			[
				'json_data'				=> $json_data_for_map,
				'form_new_customers'	=> $form_new_customers->createView(),
		]);
	}
}	
