<?php

namespace Apdc\ApdcBundle\Controller;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//use Apdc\ApdcBundle\Helper\MageHelper;
//include_once 'Magento.php';

class DefaultController extends Controller
{
//	public $tmp = new MageHelper();
	
	private function getMage()
	{	
		$mage = $this->container->get('apdc_apdc.magento');
		return $mage;
	}
 
	public function indexAction(Request $request)
	{
	//	new MageHelper();
//		$mage=$tmp->getMage();

		$mage = $this->getMage();
//		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');
	 
		$entity_fromto = new \AppBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\AppBundle\Form\FromTo::class, $entity_fromto, [
			'action' => $this->generateUrl('ordersIndex')
		]);

		$entity_id = new \AppBundle\Entity\OrderId();
		$form_id = $this->createForm(\AppBundle\Form\OrderId::class, $entity_id, [
			'action' => $this->generateUrl('merchantsIndex')
		]);
/*
		$entity_fromtoMerchant = new \AppBundle\Entity\FromToMerchant();
		$form_fromtoMerchant = $this->createForm(\AppBundle\Form\FromToMerchant::class, $entity_fromtoMerchant, [
			'action' => $this->generateUrl('merchantsIndex')
		]);
 */
		$entity_from_picking = new\AppBundle\Entity\From();
		$form_from_picking = $this->createForm(\AppBundle\Form\From::class, $entity_from_picking, [
			'action' => $this->generateUrl('pickingIndex')
		]);

		$entity_from_shipping = new\AppBundle\Entity\From();
		$form_from_shipping = $this->createForm(\AppBundle\Form\From::class, $entity_from_shipping, [
			'action' => $this->generateUrl('shippingIndex')
		]);

		$form_fromto->handleRequest($request);
		$form_id->handleRequest($request);
//		$form_fromtoMerchant->handleRequest($request);
		$form_from_picking->handleRequest($request);
		$form_from_shipping->handleRequest($request);

		return $this->render('ApdcApdcBundle::home/index.html.twig', [
			'user'	=> $_SESSION['delivery']['username'],
			'orders' => [
				$form_fromto->createView(),
					$form_id->createView(),
				],
			'merchants' => [
//					$form_fromtoMerchant->createView(),
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
