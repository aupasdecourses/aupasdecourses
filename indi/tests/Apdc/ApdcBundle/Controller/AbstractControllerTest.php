<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractControllerTest extends WebTestCase
{
	protected $client;
	private $username = "sturquier";

	public function setUp()
	{
		$this->client = $this->createAuthorizedClient();
	}

	public function tearDown()
	{
		$this->client = null;
	}

	/**
	 * 	Bypass le formulaire de login pour effectuer les tests fonctionnels des controlleurs
	 *
	 * 	@return Client
	 */
	private function createAuthorizedClient()
	{
		$client = static::createClient();
		$container = $client->getContainer();
		$session = $container->get('session');

		$userManager = $container->get('fos_user.user_manager');
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