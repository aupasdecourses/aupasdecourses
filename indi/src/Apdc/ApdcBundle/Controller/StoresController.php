<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StoresController extends Controller
{
    public function indexAction(Request $request)
    {
		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}
		
		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromtoMerchant	= new \Apdc\ApdcBundle\Entity\FromToMerchant();
		$form_fromtoMerchant	= $this->createForm(\Apdc\ApdcBundle\Form\FromToMerchant::class, $entity_fromtoMerchant);

		$form_fromtoMerchant->handleRequest($request);

		if ($form_fromtoMerchant->isSubmitted() && $form_fromtoMerchant->isValid()) {
			return $this->redirectToRoute('storesAll', [
				'from'	=> $entity_fromtoMerchant->from,
				'to'	=> $entity_fromtoMerchant->to
			]);
		}

		return $this->render('ApdcApdcBundle::stores/index.html.twig', [
			'forms' => [
				$form_fromtoMerchant->createView(),
			]
		]);
    }

    public function storesAllAction(Request $request, $from, $to)
    {
	
		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}
		
		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromtoMerchant	= new \Apdc\ApdcBundle\Entity\FromToMerchant();
		$form_fromtoMerchant	= $this->createForm(\Apdc\ApdcBundle\Form\FromToMerchant::class, $entity_fromtoMerchant, [
			'action' => $this->generateUrl('storesIndex')
		]);

		$form_fromtoMerchant->get('from')->setData($from);
		$form_fromtoMerchant->get('to')->setData($to);

		return $this->render('ApdcApdcBundle::stores/all.html.twig', [
			'forms' => [
				$form_fromtoMerchant->createView(),
			],
			'stores' 		=> $mage->getMerchantsOrdersByStore(-1, $from, $to),
			'shop_url'		=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/commercant_shop/edit/id_shop/',
			'manager_url'	=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/commercant_contact/edit/id_contact/',

		]);
    }
}
