<?php 

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

include_once "Magento.php";

class ShippingController extends Controller
{
    public function indexAction(Request $request)
    {
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_from = new\AppBundle\Entity\From();
		$form_from = $this->createForm(\AppBundle\Form\From::class, $entity_from);

		$form_from->handleRequest($request);

		if ($form_from->isSubmitted() && $form_from->isValid()) {
			return $this->redirectToRoute('shippingAll', [
				'from' => $entity_from->from
				]);
		}

		return $this->render('ApdcApdcBundle::shipping/index.html.twig', [
			'user'	=> $_SESSION['delivery']['username'],
			'forms' => [ $form_from->createView() ]
		]);
	}

    public function shippingAllAction(Request $request, $from)
    {
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_from = new \AppBundle\Entity\From();
		$form_from = $this->createForm(\AppBundle\Form\From::class, $entity_from, [
				'action' => $this->generateUrl('shippingIndex'),
			]);

		$form_from->get('from')->setData($from);

		return $this->render('ApdcApdcBundle::shipping/all.html.twig', [
			'user'	=> $_SESSION['delivery']['username'],
				'forms' => [ $form_from->createView() ],
				'stores' => $mage->getOrdersByStore($from)
			]);
    }
}

