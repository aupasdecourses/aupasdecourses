<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

include_once 'Magento.php';

class PickingController extends Controller
{
    public function indexAction(Request $request)
    {
	$mage = \Magento::getInstance();
	if (!$mage->isLogged())
		return $this->redirectToRoute('userLogin');

	$entity_fromto = new\AppBundle\Entity\FromTo();
	$form_fromto = $this->createForm(\AppBundle\Form\FromTo::class, $entity_fromto);

	$entity_id = new \AppBundle\Entity\OrderId();
	$form_id = $this->createForm(\AppBundle\Form\OrderId::class, $entity_id);

	$form_fromto->handleRequest($request);
	$form_id->handleRequest($request);

	if ($form_id->isValid()) {
		return $this->redirectToRoute('pickingIndex', [
			'id' =>$entity_id->id
		]);
	} else if ($form_fromto->isValid()) {
		return $this->redirectToRoute('pickingAll', [
			'from' => $entity_fromto->from
		]);
	}	return $this->render('picking/index.html.twig', [
		'forms' => [
			$form_fromto->createView(),
			$form_id->createView()
		]
	]);
    }

    public function pickingAllAction(Request $request, $from)
    {
     		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_fromto = new \AppBundle\Entity\FromTo();
		$form_fromto = $this->createForm(\AppBundle\Form\FromTo::class, $entity_fromto, [
			'action' => $this->generateUrl('pickingIndex'),
		]);
		$entity_id = new \AppBundle\Entity\OrderId();
		$form_id = $this->createForm(\AppBundle\Form\OrderId::class, $entity_id, [
			'action' => $this->generateUrl('pickingIndex'),
		]);

		$form_fromto->get('from')->setData($from);

		return $this->render('picking/all.html.twig', [
			'forms' => [
				$form_fromto->createView(),
				$form_id->createView()
			],
			'orders' => $mage->getOrders($from)
		]);


    }
}

