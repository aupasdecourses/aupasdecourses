<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class FromTo extends \AppBundle\Entity\Model {
	protected $_from;
	protected $_to;

	public static function loadValidatorMetadata(ClassMetadata $metadata) {
		$metadata->addPropertyConstraint('_from', new Assert\Regex([
			'pattern' => '/\d{4}-\d{2}-\d{2}/'
		]));
		$metadata->addPropertyConstraint('_to', new Assert\Regex([
			'pattern' => '/\d{4}-\d{2}-\d{2}/'
		]));
	}
}
