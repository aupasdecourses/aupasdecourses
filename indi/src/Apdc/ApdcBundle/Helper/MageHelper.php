<?php

namespace Apdc\ApdcBundle\Helper;

class MageHelper
{

	public function getMage()
	{
		$helper = $this->container->get('apdc_apdc.magento');
		return $helper;

	}
}
