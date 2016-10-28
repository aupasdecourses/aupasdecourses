<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
	public function indexAction(Request $request)
	{
		$id = new \AppBundle\Entity\OrderId();
		$form = $this->createForm(\AppBundle\Form\OrderIdType::class, $id);

$form->handleRequest($request);

		return $this->render('home/index.html.twig', [
			'form'	=>	$form->createView()
		]);
	}
}
