<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BillingController extends Controller
{
	public function indexAction(Request $request)
	{
		if(!$this->isGranted('ROLE_ADMIN'))
		{
			return $this->redirectToRoute('root');
		}
	}
}
