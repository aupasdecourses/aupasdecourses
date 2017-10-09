<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountingController extends Controller
{
    public function indexAction(Request $request)
    {
		if(!$this->isGranted('ROLE_INDI_COMPTABILITE')) {
			return $this->redirectToRoute('root');
		}

		return $this->render('ApdcApdcBundle::accounting/index.html.twig', [
		]);
    }
}
