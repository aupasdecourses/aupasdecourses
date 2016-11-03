<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

include_once 'Magento.php';

class UserController extends Controller
{
    public function indexAction(Request $request)
    {
		return $this->redirectToRoute('userLogin', []);
    }

	public function loginAction(Request $request) {
		$entity_login = new \AppBundle\Entity\Login();
		$form_login = $this->createForm(\AppBundle\Form\Login::class, $entity_login);
		$form_login->handleRequest($request);

		$mage = \Magento::getInstance();
		if ($mage->isLogged())
			return $this->redirectToRoute('root');
		else {
			if ($form_login->isValid()) {
				if ($mage->login($entity_login->username, $entity_login->password))
					return $this->redirectToRoute('root');
			}

			return $this->render('login/index.html.twig', [
				'forms' => [$form_login->createView()]
			]);
		}
	}

	public function logoutAction(Request $request) {
		$mage = \Magento::getInstance();
		$mage->logout();
		return $this->redirectToRoute('root');
	}
}

