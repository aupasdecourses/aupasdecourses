<?php

namespace Apdc\ApdcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrdersController extends Controller
{
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_INDI_DISPATCH')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');

        $entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
        $form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto);
        $entity_id = new \Apdc\ApdcBundle\Entity\OrderId();
        $form_id = $this->createForm(\Apdc\ApdcBundle\Form\OrderId::class, $entity_id);

        $form_fromto->handleRequest($request);
        $form_id->handleRequest($request);

        if ($form_id->isSubmitted() && $form_id->isValid()) {
            return $this->redirectToRoute('ordersOne', [
                'id' => $entity_id->id,
            ]);
        } elseif ($form_fromto->isValid()) {
            return $this->redirectToRoute('ordersAll', [
                'from' => $entity_fromto->from,
                'to' => $entity_fromto->to,
            ]);
        }

        return $this->render('ApdcApdcBundle::orders/index.html.twig', [
            'forms' => [
                $form_fromto->createView(),
                $form_id->createView(),
            ],
        ]);
    }

    public function ordersOneAction(Request $request, $id)
    {
        if (!$this->isGranted('ROLE_INDI_DISPATCH')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');

        $entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
        $form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto, [
            'action' => $this->generateUrl('ordersIndex'),
        ]);
        $entity_id = new \Apdc\ApdcBundle\Entity\OrderId();
        $form_id = $this->createForm(\Apdc\ApdcBundle\Form\OrderId::class, $entity_id, [
            'action' => $this->generateUrl('ordersIndex'),
        ]);
        $form_id->get('id')->setData($id);

        return $this->render('ApdcApdcBundle::orders/one.html.twig', [
            'forms' => [
                $form_fromto->createView(),
                $form_id->createView(),
            ],
            'orders' => $mage->getOrders(null, null, -1, $id),
        ]);
    }

    public function ordersAllAction(Request $request, $from, $to)
    {
        if (!$this->isGranted('ROLE_INDI_DISPATCH')) {
            return $this->redirectToRoute('root');
        }

        $mage = $this->container->get('apdc_apdc.magento');

        $entity_fromto = new \Apdc\ApdcBundle\Entity\FromTo();
        $form_fromto = $this->createForm(\Apdc\ApdcBundle\Form\FromTo::class, $entity_fromto, [
            'action' => $this->generateUrl('ordersIndex'),
        ]);
        $entity_id = new \Apdc\ApdcBundle\Entity\OrderId();
        $form_id = $this->createForm(\Apdc\ApdcBundle\Form\OrderId::class, $entity_id, [
            'action' => $this->generateUrl('ordersIndex'),
        ]);

        $form_fromto->get('from')->setData($from);
        $form_fromto->get('to')->setData($to);

        return $this->render('ApdcApdcBundle::orders/all.html.twig', [
            'forms' => [
                $form_fromto->createView(),
                $form_id->createView(),
            ],
            'orders' => $mage->getOrders($from, $to),
        ]);
    }
}
