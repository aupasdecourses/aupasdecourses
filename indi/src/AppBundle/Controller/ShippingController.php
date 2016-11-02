<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

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

		if ($form_from->isValid()) {
			return $this->redirectToRoute('shippingAll', [
				'from' => $entity_from->from
				]);
		}

		return $this->render('shipping/index.html.twig', [
			'forms' => [ $form_from->createView() ]
		]);
	}

    public function shippingAllAction(Request $request)
    {
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_from = new \AppBundle\Entity\From();
		$form_from = $this->createForm(\AppBundle\Form\From::class, $entity_from, [
				'action' => $this->generateUrl('pickingIndex'),
			]);

		$form_from->get('from')->setData($from);

		return $this->render('shipping/all.html.twig', [
				'forms' => [ $form_from->createView() ],
				'stores' => $mage->getMerchantsOrdersbyStore(-1, $from)
			]);
    }
}

