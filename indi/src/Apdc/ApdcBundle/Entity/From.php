<?php

namespace Apdc\ApdcBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class From extends \Apdc\ApdcBundle\Entity\Model
{
	protected $_from;

	public static function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addPropertyConstraint('_from', new Assert\Regex([
			'pattern' => '/\d{4}-\d{2}-\d{2}/'
		]));
	}
}
