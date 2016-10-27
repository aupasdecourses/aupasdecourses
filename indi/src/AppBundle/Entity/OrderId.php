<?php

namespace AppBundle\Entity;

class OrderId {
	protected $_id;

	public function getId() {
		return $this->_id;
	}

	public function setId($id) {
		$this->_id = $id;
	}
}
