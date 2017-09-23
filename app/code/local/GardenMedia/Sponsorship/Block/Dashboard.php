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
        if ($this->customerIsSponsor()) {
            $blockId = Mage::getStoreConfig('gm_sponsorship/general/block_dashboard');
        } else {
            $blockId = Mage::getStoreConfig('gm_sponsorship/general/block_become_sponsor_dashboard');
        }
        if (!empty($blockId)) {
          return $this->getLayout()->createBlock('cms/block')->setBlockId($blockId)->toHtml();
        }
        return '';
    }

    /**
     * getUniqueLink 
     * 
     * @return string
     */
    public function getUniqueLink()
    {
        return Mage::helper('gm_sponsorship')->getUniqueLink(Mage::getSingleton('customer/session')->getCustomer());
    }

    /**
     * getFacebookShareLink 
     * 
     * @return string
     */
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

    /**
     * getTwitterShareLink 
     * 
     * @return string
     */
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

    /**
     * customerIsSponsor 
     * 
     * @return bool
     */
    public function customerIsSponsor()
    {
        if (!$this->firstOrderEnabled() || ($this->firstOrderEnabled() && $this->hasAlreadyOrdered())) {
            return true;
        }
        return false;
    }

    /**
     * firstOrderEnabled 
     * 
     * @return bool
     */
    public function firstOrderEnabled()
    {
        return (bool) Mage::getStoreConfig('gm_sponsorship/general/become_sponsor_is_active');
    }

    /**
     * hasAlreadyOrdered 
     * 
     * @return bool
     */
    public function hasAlreadyOrdered()
    {
        $invoices = Mage::getModel('sales/order_invoice')->getCollection();
        $invoices->getSelect()->join(
            array('orders' => $invoices->getTable('sales/order')),
            'orders.entity_id = main_table.order_id and orders.customer_id = ' . Mage::getSingleton('customer/session')->getId(),
            array()
        );
        $invoices->getSelect()->limit(1);

        return (bool) $invoices->count();
    }
}
