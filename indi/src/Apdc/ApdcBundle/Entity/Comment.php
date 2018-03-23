<?php

namespace Apdc\ApdcBundle\Entity;

class Comment
{
	private $id;

	private $type;

	public function getId()
	{
		return $this->id;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
	}
}