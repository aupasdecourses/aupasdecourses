<?php

namespace Tests\Apdc\ApdcBundle\Controller;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

include_once(realpath(dirname(__FILE__)) . '/../../../../../app/Mage.php');

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
	protected $merchantIds;
	protected $lightMerchantsIds;

	/**
	 *	Create array[] containing all id_attribut_commercant
	 *
	 * 	@return $merchantIds array[] 
	 */
	private function createMerchantIdsTable()
	{
		$merchantIds = [0 => -1];
		$shops = \Mage::getModel('apdc_commercant/shop')->getCollection();

		foreach ($shops as $shop) {
			array_push($merchantIds, (int) $shop->getData('id_attribut_commercant'));
		}

		shuffle($merchantIds);

		return $merchantIds;
	}

	public function setUp()
	{
		\Mage::app();

		$this->client 				= $this->createAuthorizedClient();
		$this->from 				= "2000-01-01";
		$this->to 					= "3000-12-31";
		$this->wrongFrom 			= "01-01-2000";
		$this->wrongTo				= "31-12-3000";
		$this->orderId				= 2018000173; // Use order_id which exists in prod AND in dev. It can be 2 different customers.
		$this->merchantIds			= $this->createMerchantIdsTable(); // Too heavy
		$this->lightMerchantsIds	= array_slice($this->merchantIds, 0, 3); // Better use 3 merchants
	}

	public function tearDown()
	{
		$this->client 				= null;
		$this->from 				= null;
		$this->to 					= null;
		$this->wrongFrom 			= null;
		$this->wrongTo 				= null;
		$this->orderId				= null;
		$this->merchantIds			= null;
		$this->lightMerchantsIds	= null;
	}

	/**
	 * 	Bypass Indi login form to execute controllers functionnals tests
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