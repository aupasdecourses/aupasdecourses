<?php

namespace Apdc\ApdcBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class OrderId extends \Apdc\ApdcBundle\Entity\Model {
	protected $_id;

	public static function loadValidatorMetadata(ClassMetadata $metadata) {
		$metadata->addPropertyConstraint('_id', new Assert\Regex(array(
			'pattern' => '/\d+/',
		)));
	}
}
