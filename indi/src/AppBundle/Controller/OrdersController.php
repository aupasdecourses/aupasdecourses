<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

include_once 'Magento.php';

class OrdersController extends Controller
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

		$form_fromto->handleRequest($request);
		$form_id->handleRequest($request);

		if ($form_id->isValid()) {
			return $this->redirectToRoute('ordersOne', [
				'id' => $entity_id->id
			]);
		} else if ($form_fromto->isValid()) {
			return $this->redirectToRoute('ordersAll', [
				'from' => $entity_fromto->from,
				'to' => $entity_fromto->to
			]);
		}

		return $this->render('orders/index.html.twig', [
			'forms' => [
				$form_fromto->createView(),
				$form_id->createView()
			]
		]);
	}

	public function ordersOneAction(Request $request)
	{
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_fromto = new \AppBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\AppBundle\Form\FromTo::class, $entity_fromto, [
			'action' => $this->generateUrl('ordersIndex'),
		]);
		$entity_id = new \AppBundle\Entity\OrderId();
		$form_id = $this->createForm(\AppBundle\Form\OrderId::class, $entity_id, [
			'action' => $this->generateUrl('ordersIndex'),
		]);

		$id = $request->attributes->get('id');
		$form_id->get('id')->setData($id);

		return $this->render('orders/one.html.twig', [
			'forms' => [
				$form_fromto->createView(),
				$form_id->createView()
			],
			'orders' => $mage->getOrders(NULL, NULL, -1, $id)
		]);
	}

	public function ordersAllAction(Request $request)
	{
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_fromto = new \AppBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\AppBundle\Form\FromTo::class, $entity_fromto, [
			'action' => $this->generateUrl('ordersIndex'),
		]);
		$entity_id = new \AppBundle\Entity\OrderId();
		$form_id = $this->createForm(\AppBundle\Form\OrderId::class, $entity_id, [
			'action' => $this->generateUrl('ordersIndex'),
		]);

		$from = $request->attributes->get('from');
		$to = $request->attributes->get('to');

		$form_fromto->get('from')->setData($from);
		$form_fromto->get('to')->setData($to);

		return $this->render('orders/all.html.twig', [
			'forms' => [
				$form_fromto->createView(),
				$form_id->createView()
			],
			'orders' => $mage->getOrders($from, $to)
		]);
	}
}
