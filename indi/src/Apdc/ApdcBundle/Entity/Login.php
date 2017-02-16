<?php

namespace Apdc\ApdcBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Login extends \Apdc\ApdcBundle\Entity\Model {
	protected $_username;
	protected $_password;

	public static function loadValidatorMetadata(ClassMetadata $metadata) {
		$metadata->addPropertyConstraint('_username', new Assert\Regex([
			'pattern' => '/.*/'
		]));
		$metadata->addPropertyConstraint('_password', new Assert\Regex([
			'pattern' => '/.*/'
		]));
	}
}
