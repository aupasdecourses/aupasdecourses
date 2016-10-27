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
		$form = $this->createForm(new \AppBundle\Form\OrderIdType(), $id);

		return $this->render('home/index.html.twig', [
			'form'	=>	$form->createView()
		]);
	}
}
