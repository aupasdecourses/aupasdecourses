<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * GardenMedia_Sponsorship_Block_Dashboard 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Block_Dashboard extends Mage_Core_Block_Template
{

    /**
     * getSponsorCode 
     * 
     * @return string
     */
    public function getSponsorCode()
    {
        return Mage::helper('gm_sponsorship')->getSponsorCode(Mage::getSingleton('customer/session')->getCustomer());
    }

    /**
     * getBlockInfos 
     * 
     * @return string
     */
    public function getBlockInfos()
    {
        $blockId = Mage::getStoreConfig('gm_sponsorship/general/block_dashboard');
        if (!empty($blockId)) {
          return $this->getLayout()->createBlock('cms/block')->setBlockId($blockId)->toHtml();
        }
        return '';
    }

    public function getUniqueLink()
    {
        return Mage::helper('gm_sponsorship')->getUniqueLink(Mage::getSingleton('customer/session')->getCustomer());
    }

    public function getFacebookShareLink()
    {
        $appId = Mage::getStoreConfig('gm_sponsorship/facebook/app_id');
        $url = urlencode($this->getUniqueLink());
        $title = urlencode(Mage::getStoreConfig('gm_sponsorship/facebook/title'));
        $description = urlencode(sprintf(Mage::getStoreConfig('gm_sponsorship/facebook/description'), Mage::getSingleton('customer/session')->getCustomer()->getName()));
        $caption = urlencode(Mage::getStoreConfig('gm_sponsorship/facebook/caption'));
        $image = urlencode(Mage::getBaseUrl('media') . 'gm_sponsorship' . DS . Mage::getStoreConfig('gm_sponsorship/facebook/image'));

        return 'https://www.facebook.com/dialog/feed?app_id=' . $appId . '&link=' . $url . '&picture=' . $image . ' &name=' . $title . ' &description=' . $description . '&caption=' . $caption;
        
    }

    public function getTwitterShareLink()
    {
        $params = array(
            'text' => Mage::getStoreConfig('gm_sponsorship/twitter/text'),
            'url' => $this->getUniqueLink()
        );
        $hashtags = Mage::getStoreConfig('gm_sponsorship/twitter/hashtags');
        if ($hashtags != '') {
            $params['hashtags'] = str_replace(' ', '', $hashtags);
        }

        return 'https://twitter.com/intent/tweet?' . http_build_query($params, '', '&amp;');
    }
}
