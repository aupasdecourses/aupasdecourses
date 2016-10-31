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
//		if (!$mage->isLogged())
//			// redirect to user login

		return $this->render('home/index.html.twig');
	}
}
