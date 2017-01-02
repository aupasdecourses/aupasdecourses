<?php 

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class ShippingController extends Controller
{
	private function getMage()
	{
		$mage = $this->container->get('apdc_apdc.magento');
		return $mage;
	}

    public function indexAction(Request $request)
    {
		$mage = $this->getMage();
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
		$mage = $this->getMage();
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

