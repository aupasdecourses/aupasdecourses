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

		$entity_fromto	= new \Apdc\ApdcBundle\Entity\FromTo();
		$form_fromto	= $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto);

		$form_fromto->handleRequest($request);

		if ($form_fromto->isSubmitted() && $form_fromto->isValid()) {
			return $this->redirectToRoute('storesAll', [
				'from'	=> $entity_fromto->from,
				'to'	=> $entity_fromto->to
			]);
		}

		return $this->render('ApdcApdcBundle::stores/index.html.twig', [
			'forms' => [
				$form_fromto->createView(),
			]
		]);
    }

    public function storesAllAction(Request $request, $from, $to)
    {
	
		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}
		
		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromto	= new \Apdc\ApdcBundle\Entity\FromTo();
		$form_fromto	= $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto, [
			'action' => $this->generateUrl('storesIndex')
		]);

		$form_fromto->get('from')->setData($from);
		$form_fromto->get('to')->setData($to);

		return $this->render('ApdcApdcBundle::stores/all.html.twig', [
			'forms' => [
				$form_fromto->createView(),
			],
			'stores' 		=> $mage->getMerchantsOrdersByStore(-1, $from, $to),
			'shop_url'		=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/commercant_shop/edit/id_shop/',
			'manager_url'	=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/commercant_contact/edit/id_contact/',

		]);
    }
}
