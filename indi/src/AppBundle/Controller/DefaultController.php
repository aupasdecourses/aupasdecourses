<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//use Symfony\Component\HttpFoundation\Response;

//use AppBundle\Entity;

//use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\Form\Extension\Core\Type\IntergerType;

class DefaultController extends Controller
{
	public function indexAction(Request $request)
	{
		$id = new \AppBundle\Entity\OrderId();
		$form = $this->createForm(\AppBundle\Form\OrderIdType::class, $id);
//			->add('id', IntegerType::class)
//			->add('Search', SubmitType::class)
//			->getForm();

		return $this->render('home/index.html.twig', [
			'form'	=>	$form->createView()
		]);
	}
}
