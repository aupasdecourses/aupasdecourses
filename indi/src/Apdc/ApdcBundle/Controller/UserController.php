<?php 

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

	public function indexAction(Request $request)
	{
		return $this->redirectToRoute('userLogin', []);
	}

	public function loginAction(Request $request) 
	{
		$mage = $this->container->get('apdc_apdc.magento');
		$entity_login = new \Apdc\ApdcBundle\Entity\Login();
		$form_login = $this->createForm(\Apdc\ApdcBundle\Form\Login::class, $entity_login);
		$form_login->handleRequest($request);

		if ($mage->isLogged())
			return $this->redirectToRoute('root');
		else {
			if ($form_login->isSubmitted() && $form_login->isValid()) {
				if ($mage->login($entity_login->username, $entity_login->password))
					return $this->redirectToRoute('root');
			}

			return $this->render('ApdcApdcBundle::login/index.html.twig', [
				'forms' => [ $form_login->createView() ]
			]);
		}
	}

	public function logoutAction(Request $request) 
	{
		$mage = $this->container->get('apdc_apdc.magento');
		$mage->logout();
		return $this->redirectToRoute('root');
	}
}

