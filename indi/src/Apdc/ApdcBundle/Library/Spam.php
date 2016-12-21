<?php

namespace Apdc\ApdcBundle\Library;

class Spam
{

	public function isSpam($text){
		return strlen($text) < 50;
	}
}
