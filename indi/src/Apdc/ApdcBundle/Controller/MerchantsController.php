<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MerchantsController extends Controller
{
    public function indexAction(Request $request)
    {
		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromtoMerchant	= new \Apdc\ApdcBundle\Entity\FromToMerchant();
		$form_fromtoMerchant	= $this->createForm(\Apdc\ApdcBundle\Form\FromToMerchant::class, $entity_fromtoMerchant);

		$form_fromtoMerchant->handleRequest($request);

		if ($form_fromtoMerchant->isSubmitted() && $form_fromtoMerchant->isValid()) {
			if ($entity_fromtoMerchant->merchant <> -1) {
				return $this->redirectToRoute('merchantsOne', [
					'id'	=> $entity_fromtoMerchant->merchant,
					'from'	=> $entity_fromtoMerchant->from,
					'to'	=> $entity_fromtoMerchant->to
				]);
			} else {
				return $this->redirectToRoute('merchantsAll', [
					'from'	=> $entity_fromtoMerchant->from,
					'to'	=> $entity_fromtoMerchant->to
				]);
			}
		}

		return $this->render('ApdcApdcBundle::merchants/index.html.twig', [
			'forms' => [
				$form_fromtoMerchant->createView(),
			]
		]);
    }

    public function merchantsOneAction(Request $request, $id, $from, $to)
    {
   		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}

		$mage = $this->container->get('apdc_apdc.magento');

		$session	= $request->getSession();
		$pdforder	= $this->container->get('apdc_apdc.pdforder');
		
		$entity_fromtoMerchant	= new \Apdc\ApdcBundle\Entity\FromToMerchant();
		$form_fromtoMerchant	= $this->createForm(\Apdc\ApdcBundle\Form\FromToMerchant::class, $entity_fromtoMerchant, [
			'action' => $this->generateUrl('merchantsIndex')
		]);

		$form_fromtoMerchant->get('from')->setData($from);
		$form_fromtoMerchant->get('to')->setData($to);
		$form_fromtoMerchant->get('merchant')->setData($id);

		$merchants = $mage->getMerchantsOrders($id, $from, $to);


		/* Generation PDF commercant */
		$defaultDataPDF = array('message' => 'Send');
		$formPDF = $this->createFormBuilder($defaultDataPDF)
				->add("Send", SubmitType::class,array('label'=>'Envoyer PDF','attr'=>array('class'=>'btn btn-lg btn-info')))
				->getForm();
		$formPDF->handleRequest($request);
		$datePDF = date('Y-m-d');

		if ($formPDF->isSubmitted() && $formPDF->isValid()) {
			foreach ($merchants as $store_name => $mercs) {
				foreach ($mercs as $merchant_id => $merchant) {

					$pdforder->setDate($datePDF);
					$pdforder->setName($merchant['name']);
					$pdforder->setOrderTemplate();
				//	$pdforder->setMails($merchant['mailc']); ///// ATTENTION /////
					
					foreach ($merchant['orders'] as $order) {
						$pdforder->addOrder($order);
					}

					$pdforder->save($merchant['name'].'_'.$datePDF); // A sauvegarder autre part que dans indi/web 
				}
			}

	//		$pdforder->send();

	//		$session->getFlashBag()->add('success', 'PDF bien envoyé');
	//		return $this->redirectToRoute('merchantsOne', [
	//				'id'	=> $id,
	//				'from'	=> $from,
	//				'to'	=> $to
	//			]);
		}
		/* FIN PDF */


		return $this->render('ApdcApdcBundle::merchants/one.html.twig', [
			'forms' => [
				$form_fromtoMerchant->createView(),
			],
			'merchants' => $merchants,
			'formPDF' 	=> $formPDF->createView(),
		]);
    }

    public function merchantsAllAction(Request $request, $from, $to)
    {
	
		if(!$this->isGranted('ROLE_INDI_DISPATCH')) {
			return $this->redirectToRoute('root');
		}
		
		$mage = $this->container->get('apdc_apdc.magento');

		$entity_fromtoMerchant	= new \Apdc\ApdcBundle\Entity\FromToMerchant();
		$form_fromtoMerchant	= $this->createForm(\Apdc\ApdcBundle\Form\FromToMerchant::class, $entity_fromtoMerchant, [
			'action' => $this->generateUrl('merchantsIndex')
		]);

		$form_fromtoMerchant->get('from')->setData($from);
		$form_fromtoMerchant->get('to')->setData($to);

		return $this->render('ApdcApdcBundle::merchants/all.html.twig', [
			'forms' => [
				$form_fromtoMerchant->createView(),
			],
			'merchants' => $mage->getMerchantsOrdersByMerchants(-1, $from, $to),
			'shop_url'		=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/commercant_shop/edit/id_shop/',
			'manager_url'	=> \Mage::getBaseUrl().'../index.php/admin/petitcommisadmin/commercant_contact/edit/id_contact/',

		]);
    }

}