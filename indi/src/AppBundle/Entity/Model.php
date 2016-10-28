<?php

namespace AppBundle\Entity;

class Model {
	public function __get($key) {
		$var_name = "_{$key}";
		if (isset($this->$var_name))
			return ($this->$var_name);
		return (NULL);
	}

	public function __set($key, $value) {
		$var_name = "_{$key}";
		$this->$var_name = $value;
	}
}
