<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

include_once 'Magento.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

class OrdersController extends Controller
{
	public function indexAction(Request $request)
	{
		$mage = \Magento::getInstance();
		//		if (!$mage->isLogged())
		//			// redirect to user login

		$entity_fromto = new \AppBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\AppBundle\Form\FromToType::class, $entity_fromto);
		$entity_id = new \AppBundle\Entity\OrderId();
		$form_id = $this->createForm(\AppBundle\Form\OrderIdType::class, $entity_id);

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
		return new Response('ORDER ONE CONTROLLER');
	}

	public function ordersAllAction(Request $request)
	{
		return new Response('ORDER ALL CONTROLLER');
	}
}
