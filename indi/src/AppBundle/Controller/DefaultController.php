<?php

namespace AppBundle\Controller;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

include_once 'Magento.php';

class DefaultController extends Controller
{
	public function indexAction(Request $request)
	{

		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');
	 
		$entity_fromto = new \AppBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\AppBundle\Form\FromTo::class, $entity_fromto);
		$entity_id = new \AppBundle\Entity\OrderId();
		$form_id = $this->createForm(\AppBundle\Form\OrderId::class, $entity_id);
		$entity_fromtoMerchant = new \AppBundle\Entity\FromToMerchant();
		$form_fromtoMerchant = $this->createForm(\AppBundle\Form\FromToMerchant::class, $entity_fromtoMerchant);
		$entity_from_picking = new\AppBundle\Entity\From();
		$form_from_picking = $this->createForm(\AppBundle\Form\From::class, $entity_from_picking);
		$entity_from_shipping = new\AppBundle\Entity\From();
		$form_from_shipping = $this->createForm(\AppBundle\Form\From::class, $entity_from_shipping);

		$form_fromto->handleRequest($request);
		$form_id->handleRequest($request);
		$form_fromtoMerchant->handleRequest($request);
		$form_from_picking->handleRequest($request);
		$form_from_shipping->handleRequest($request);

		if ($form_id->isValid()) {
			return $this->redirectToRoute('ordersOne', [
				'id' => $entity_id->id
			]);
		} else if ($form_fromto->isValid()) {
			return $this->redirectToRoute('ordersAll', [
				'from' => $entity_fromto->from,
				'to' => $entity_fromto->to
			]);
		} else if ($form_fromtoMerchant->isValid()) {
			if ($entity_fromtoMerchant->merchant <> -1) {
				return $this->redirectToRoute('merchantsOne', [
					'id' => $entity_fromtoMerchant->merchant,
					'from' => $entity_fromtoMerchant->from,
					'to' => $entity_fromtoMerchant->to
				]);
			} else {
				return $this->redirectToRoute('merchantsAll', [
					'from' => $entity_fromtoMerchant->from,
					'to' => $entity_fromtoMerchant->to
				]);
			}
		} else if ($form_from_picking->isValid()) {
			return $this->redirectToRoute('pickingAll', [
				'from' => $entity_from_picking->from
				]);
		} else if ($form_from_shipping->isValid()) {
			return $this->redirectToRoute('shippingAll', [
				'from' => $entity_from_shipping->from
				]);
		}

		return $this->render('home/index.html.twig', [
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
