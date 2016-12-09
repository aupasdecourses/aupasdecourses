<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Catalog;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\FileType;


include_once 'Magento.php';

class ToolController extends Controller
{
	public function uploadAction(Request $request)
	{
		$catalog = array('' => '');

		$mage = \Magento::getInstance();
		if(!$mage->isLogged())
			return $this->redirectToRoute('userLogin');

		
		$form = $this->createFormBuilder($catalog)
			->add('File', FileType::class)
			->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();
		}

		return $this->render('tool/upload.html.twig', [
			'user' => $_SESSION['delivery']['username'],
			'form' => $form->createView(),
		]);
	}
}
