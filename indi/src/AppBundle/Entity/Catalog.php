<?php

namespace AppBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;

class Catalog 
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}

	public function onUpload(PostPersistEvent $event)
	{
		$request = $event->getRequest();
		$gallery = $request->get('gallery');

	}
}
