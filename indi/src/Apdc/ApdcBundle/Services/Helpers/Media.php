<?php

namespace Apdc\ApdcBundle\Services\Helpers;

trait Media
{

	public function mediaPath()
    {
        return realpath(__DIR__.'/../../../../../media');
    }

    public function mediaUrl()
    {
        return \Mage::getBaseUrl('media');
    }

}