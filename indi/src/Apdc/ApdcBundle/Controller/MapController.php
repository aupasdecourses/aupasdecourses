<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class MapController extends Controller 
{
	public function merchantsAction()
	{
		return $this->render('ApdcApdcBundle::map/merchants.html.twig');
	}

	public function customersAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('root');
		}

		$stats	= $this->container->get('apdc_apdc.stats');


//		$json_data = $stats->addLatLongAndJsonEncode();
//		$fs = new Filesystem();
//		if($fs->exists('../web/json/clients.json')) {
//			$fs->dumpFile('../web/json/clients.json', $json_data);
//		}


		// update entry to geocode customers
		
		
		return $this->render('ApdcApdcBundle::map/customers.html.twig',
			[
//				'json_data'	=> $json_data,
		]);
	}
}	
