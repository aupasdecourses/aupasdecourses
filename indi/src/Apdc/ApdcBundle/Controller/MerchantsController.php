<?php 

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

include_once 'Magento.php';

class MerchantsController extends Controller
{
    public function indexAction(Request $request)
    {
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_fromtoMerchant = new \AppBundle\Entity\FromToMerchant();
		$form_fromtoMerchant = $this->createForm(\AppBundle\Form\FromToMerchant::class, $entity_fromtoMerchant);

		$form_fromtoMerchant->handleRequest($request);

		if ($form_fromtoMerchant->isSubmitted() && $form_fromtoMerchant->isValid()) {
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
		}

		return $this->render('merchants/index.html.twig', [
			'user'	=> $_SESSION['delivery']['username'],
			'forms' => [
				$form_fromtoMerchant->createView(),
			]
		]);
    }

    public function merchantsOneAction(Request $request, $id, $from, $to)
    {
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_fromtoMerchant = new \AppBundle\Entity\FromToMerchant();
		$form_fromtoMerchant = $this->createForm(\AppBundle\Form\FromToMerchant::class, $entity_fromtoMerchant, [
			'action' => $this->generateUrl('merchantsIndex')
		]);

		$form_fromtoMerchant->get('from')->setData($from);
		$form_fromtoMerchant->get('to')->setData($to);
		$form_fromtoMerchant->get('merchant')->setData($id);

		return $this->render('merchants/one.html.twig', [
			'user'	=> $_SESSION['delivery']['username'],
			'forms' => [
				$form_fromtoMerchant->createView(),
			],
			'merchants' => $mage->getMerchantsOrders($id, $from, $to)
		]);
    }
    
    public function merchantsAllAction(Request $request, $from, $to)
    {
		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		$entity_fromtoMerchant = new \AppBundle\Entity\FromToMerchant();
		$form_fromtoMerchant = $this->createForm(\AppBundle\Form\FromToMerchant::class, $entity_fromtoMerchant, [
			'action' => $this->generateUrl('merchantsIndex')
		]);

		$form_fromtoMerchant->get('from')->setData($from);
		$form_fromtoMerchant->get('to')->setData($to);

		return $this->render('merchants/all.html.twig', [
			'user'	=> $_SESSION['delivery']['username'],
			'forms' => [
				$form_fromtoMerchant->createView(),
			],
			'stores' => $mage->getMerchantsOrdersByStore(-1, $from, $to)
		]);
    }
}

