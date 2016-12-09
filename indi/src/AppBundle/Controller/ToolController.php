<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Catalog;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


include_once 'Magento.php';

class ToolController extends Controller
{
	public function uploadAction(Request $request)
	{
		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		return $this->render('tool/upload.html.twig', [
			'user' => $_SESSION['delivery']['username'],
		]);
	}
}
