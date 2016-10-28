<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
	public function indexAction(Request $request)
	{
		$id = new \AppBundle\Entity\FromToMerchant();
		$form = $this->createForm(\AppBundle\Form\FromToMerchantType::class, $id, ['yolo' => 'swag']);

		$form->handleRequest($request);

		return $this->render('home/index.html.twig', [
			'form'	=>	$form->createView()
		]);
	}
}
