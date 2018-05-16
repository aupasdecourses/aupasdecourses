<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractControllerTest extends WebTestCase
{
	// Change in prod
	private $username = "sturquier";

	protected $client;
	protected $from;
	protected $to;
	protected $wrongFrom;
	protected $wrongTo;
	protected $orderId;
	protected $merchantId;

	public function setUp()
	{
		$this->client 		= $this->createAuthorizedClient();
		$this->from 		= "2000-01-01";
		$this->to 			= "3000-12-31";
		$this->wrongFrom 	= "01-01-2000";
		$this->wrongTo		= "31-12-3000";
		$this->orderId		= 2018000001;
		$this->merchantId	= 7; // Boucherie des Moines
	}

	public function tearDown()
	{
		$this->client 		= null;
		$this->from 		= null;
		$this->to 			= null;
		$this->wrongFrom 	= null;
		$this->wrongTo 		= null;
		$this->orderId		= null;
		$this->merchantId	= null;
	}

	/**
	 * 	Bypass le formulaire de login pour effectuer les tests fonctionnels des controlleurs
	 *
	 * 	@return Client
	 */
	private function createAuthorizedClient()
	{
		$client 	= static::createClient();
		$container 	= $client->getContainer();
		$session 	= $container->get('session');

		$userManager  = $container->get('fos_user.user_manager');
		$loginManager = $container->get('fos_user.security.login_manager');
		$firewallName = $container->getParameter('fos_user.firewall_name');

		// Login
		$user = $userManager->findUserBy(['username' => $this->username]);
		$loginManager->loginUser($firewallName, $user);

		// Save login token into the session & put it in a cookie
		$container->get('session')->set('_security_' . $firewallName,
		serialize($container->get('security.token_storage')->getToken()));
		$container->get('session')->save();
		$client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

		return $client;
	}
}