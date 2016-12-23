<?php 

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

//include_once 'Magento.php';

class PickingController extends Controller
{
	private function getMage(){
		$mage = $this->container->get('apdc_apdc.magento');
		return $mage;
	}

    public function indexAction(Request $request)
    {
	//	$mage = \Magento::getInstance();
		$mage = $this->getMage();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_from = new\AppBundle\Entity\From();
		$form_from = $this->createForm(\AppBundle\Form\From::class, $entity_from);

		$form_from->handleRequest($request);

		if ($form_from->isSubmitted() && $form_from->isValid()) {
			return $this->redirectToRoute('pickingAll', [
				'from' => $entity_from->from
				]);
		}

		return $this->render('ApdcApdcBundle::picking/index.html.twig', [
			'user'	=> $_SESSION['delivery']['username'],
			'forms' => [ $form_from->createView() ]
		]);
	}

    public function pickingAllAction(Request $request, $from)
    {
	//	$mage = \Magento::getInstance();
		$mage = $this->getMage();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_from = new \AppBundle\Entity\From();
		$form_from = $this->createForm(\AppBundle\Form\From::class, $entity_from, [
				'action' => $this->generateUrl('pickingIndex'),
			]);

		$form_from->get('from')->setData($from);

		return $this->render('ApdcApdcBundle::picking/all.html.twig', [
				'user'	=> $_SESSION['delivery']['username'],
				'forms' => [ $form_from->createView() ],
				'stores' => $mage->getMerchantsOrdersByStore(-1, $from)
			]);
    }
}

