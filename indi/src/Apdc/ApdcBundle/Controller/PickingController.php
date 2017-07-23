<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PickingController extends Controller
{
    public function indexAction(Request $request)
    {
		
		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}
		
		$mage = $this->container->get('apdc_apdc.magento');

		$entity_from	= new\Apdc\ApdcBundle\Entity\From();
		$form_from		= $this->createForm(\Apdc\ApdcBundle\Form\From::class, $entity_from);

		$form_from->handleRequest($request);

		if ($form_from->isSubmitted() && $form_from->isValid()) {
			return $this->redirectToRoute('pickingAll', [
				'from' => $entity_from->from
			]);
		}

		return $this->render('ApdcApdcBundle::picking/index.html.twig', [
			'forms' => [ $form_from->createView() ]
		]);
	}

    public function pickingAllAction(Request $request, $from)
    {
		
		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}
		
		$mage = $this->container->get('apdc_apdc.magento');

		$entity_from	= new \Apdc\ApdcBundle\Entity\From();
		$form_from		= $this->createForm(\Apdc\ApdcBundle\Form\From::class, $entity_from, [
				'action' => $this->generateUrl('pickingIndex'),
		]);

		$form_from->get('from')->setData($from);

		return $this->render('ApdcApdcBundle::picking/all.html.twig', [
				'forms'		=> [ $form_from->createView() ],
				'stores'	=> $mage->getMerchantsOrdersByStore(-1, $from)
		]);
    }
}
