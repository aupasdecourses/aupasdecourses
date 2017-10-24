<?php

namespace Apdc\ApdcBundle\Services\Helpers;

trait Media
{

	public function mediaPath()
    {
        return \Mage::getBaseDir('media');
    }

    public function mediaUrl()
    {
        return \Mage::getBaseUrl('media');
    }

}