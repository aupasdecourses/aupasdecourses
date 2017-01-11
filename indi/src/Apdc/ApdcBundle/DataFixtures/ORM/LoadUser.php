<?php

namespace Apdc\ApdcBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Apdc\ApdcBundle\Entity\User;

class LoadUser implements FixtureInterface
{
	public function load(ObjectManager $manager)
	{
		/* Add new users in this array */
		$listNames = array('sturquier', 'pmainguet', 'livreur1', 'lpr1', 'lpr2');
		$listLivreursTest = array('livr3','livr4');
		foreach($listNames as $name)
		{
			$user = new User;

			/**!! Username & password sont identiques !!**/
			$user->setUsername($name);
			$user->setPassword($name);

			$user->setSalt('');

			/* On définit le role_user qui est le role de base */
			$user->setRoles(array('ROLE_ADMIN'));

			$manager->persist($user);
		}

		foreach($listLivreursTest as $livr)
		{
			$userL = new User;
			$userL->setUsername($livr);
			$userL->setPassword($livr);
			$userL->setSalt('');
			$userL->setEmail('livrTest@gmail.com');
			$userL->setRoles(array('ROLE_LIVREUR'));
			$manager->persist($userL);
		}

		$manager->flush();
	}
}
