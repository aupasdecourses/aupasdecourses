<?php

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ToolController extends Controller
{

	private function getMage(){
		$mage = $this->container->get('apdc_apdc.magento');
		return $mage;
	}

	public function productAction(Request $request)
	{
		$mage = $this->getMage();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		return $this->render('ApdcApdcBundle::tool/product.html.twig', [
			'user' => $_SESSION['delivery']['username'],
		]);
	}

	public function merchantAction(Request $request)
	{
		$mage = $this->getMage();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		return $this->render('ApdcApdcBundle::tool/merchant.html.twig', [
			'user' => $_SESSION['delivery']['username'],
		]);
	}

	public function categoryAction(Request $request)
	{
		$mage = $this->getMage();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		return $this->render('ApdcApdcBundle::tool/category.html.twig', [
			'user' => $_SESSION['delivery']['username'],
		]);
	}
}
