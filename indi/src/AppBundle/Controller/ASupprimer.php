<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//use Symfony\Component\HttpFoundation\Response;

//use AppBundle\Entity;

class DefaultController extends Controller
{
	public function indexAction(Request $request)
	{
		$id = new \AppBundle\Entity\OrderId();
		$form = $this->createForm(\AppBundle\Form\OrderIdType::class, $id);

		return $this->render('merchants/all.html.twig', [
			'form'	=>	$form->createView()
		]);
	}
}
