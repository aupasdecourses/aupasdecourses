<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

include_once 'Magento.php';

class DefaultController extends Controller
{
	public function indexAction(Request $request)
	{

		$mage = \Magento::getInstance();
		if (!$mage->isLogged())
			return $this->redirectToRoute('userLogin');
	 
		return $this->render('home/index.html.twig', [
			'user'	=> $_SESSION['delivery']['username']
		]);
	}
}
