<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
 
	public function indexAction(Request $request)
	{
		$mage = $this->container->get('apdc_apdc.magento');
		
		//$mage->getUsers();
		//$mage->getRoles();

//		if($mage->getCurrentUser() == 'sturquier')
//			echo'vous etes sturquier';	
	
	
	
	


		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');
	 
		$entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto, [
			'action' => $this->generateUrl('ordersIndex')
		]);

		$entity_id = new \Apdc\ApdcBundle\Entity\OrderId();
		$form_id = $this->createForm(\Apdc\ApdcBundle\Form\OrderId::class, $entity_id, [
			'action' => $this->generateUrl('merchantsIndex')
		]);

		$entity_fromtoMerchant = new \Apdc\ApdcBundle\Entity\FromToMerchant();
		$form_fromtoMerchant = $this->createForm(\Apdc\ApdcBundle\Form\FromToMerchant::class, $entity_fromtoMerchant, [
			'action' => $this->generateUrl('merchantsIndex')
		]);
 
		$entity_from_picking = new\Apdc\ApdcBundle\Entity\From();
		$form_from_picking = $this->createForm(\Apdc\ApdcBundle\Form\From::class, $entity_from_picking, [
			'action' => $this->generateUrl('pickingIndex')
		]);

		$entity_from_shipping = new\Apdc\ApdcBundle\Entity\From();
		$form_from_shipping = $this->createForm(\Apdc\ApdcBundle\Form\From::class, $entity_from_shipping, [
			'action' => $this->generateUrl('shippingIndex')
		]);

		$form_fromto->handleRequest($request);
		$form_id->handleRequest($request);
		$form_fromtoMerchant->handleRequest($request);
		$form_from_picking->handleRequest($request);
		$form_from_shipping->handleRequest($request);

		return $this->render('ApdcApdcBundle::home/index.html.twig', [
			'user'	=> $_SESSION['delivery']['username'],
			'orders' => [
				$form_fromto->createView(),
					$form_id->createView(),
				],
			'merchants' => [
					$form_fromtoMerchant->createView(),
				],
			'picking' => [
					$form_from_picking->createView(),
				],
			'shipping' => [
					$form_from_shipping->createView()
				]
			]);
	}
}
