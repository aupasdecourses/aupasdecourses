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
		//		if (!$mage->isLogged())
		//			// redirect to user login

		$entity_fromto = new \AppBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\AppBundle\Form\FromToType::class, $entity_fromto);
		$entity_id = new \AppBundle\Entity\OrderId();
		$form_id = $this->createForm(\AppBundle\Form\OrderIdType::class, $entity_id);

		$form_fromto->handleRequest($request);
		$form_id->handleRequest($request);

		if ($form_id->isValid()) {
			$y = $form_id->getData();
			echo 'yolo';
			print_r($entity_id->getid());
			echo 'swag';
			//$url = $this->generateUrl('ordersOne', ['id' => $entity_id->getid()]);
			//echo "id: ===>|$url|<===";
			die('=== ID ===');
			//$this->redirect($url);
		} else if ($form_fromto->isValid()) {
			$url = $this->generateUrl('ordersAll',
				['from' => $entity_fromto->getfrom()],
			UrlGeneratorInterface::ABSOLUTE_URL);
			echo "from: ===>|$url|<===";
			die('=== FROM ===');
			//$this->redirect($url);
		}
//		$url = $this->generateUrl('ordersAll', ['from' => '0000-00-00'/*$entity_fromto->getfrom()*/]);
//			echo "===>|$url|<===".PHP_EOL;

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
