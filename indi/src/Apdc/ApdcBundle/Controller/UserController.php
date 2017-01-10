<?php 

namespace Apdc\ApdcBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\SecurityContext;

class UserController extends Controller
{
	public function loginAction(Request $request) 
	{
		$mage = $this->container->get('apdc_apdc.magento');

		/* Si déja identifié */
		if($this->isGranted('IS_AUTHENTIFICATED_REMEMBERED'))
		{
			return $this->redirectToRoute('root');
		}

		$authentificationUtils = $this->get('security.authentication_utils');
		/* Handle errors */
		return $this->render('ApdcApdcBundle::login/index.html.twig', array(
			'last_username' => $authentificationUtils->getLastUsername(),
			'error'         => $authentificationUtils->getLastAuthenticationError(),
		));
	}
}

