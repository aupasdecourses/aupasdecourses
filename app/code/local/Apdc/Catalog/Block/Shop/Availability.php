<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Block_Shop_Availability 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Shop_Availability extends Mage_Core_Block_Template
{

    protected $shopInfo = null;

    /**
     * getShopMessage
     * 
     * @return string
     */
    public function getShopMessage()
    {
        $shopMessage = '';
        $shopInfo = $this->getShopInfo();
        if (isset($shopInfo['is_closed'])) {
            $shopMessage = $shopInfo['is_closed']['message'];
        } else if (Mage::getSingleton('core/session')->getDdate()) {
            $timestamp = strtotime(Mage::getSingleton('core/session')->getDdate());
            $date = date('Y-m-d', $timestamp);
            if (isset($shopInfo['availability']) && 
                isset($shopInfo['availability'][$date]) &&
                $shopInfo['availability'][$date]['message_info'] != ''
            ) {
                $shopMessage = $shopInfo['availability'][$date]['message_info'];
            }
        }
        if ($shopMessage == '' && isset($shopInfo['next_closed'])) {
            $shopMessage = $shopInfo['next_closed']['message'];
        }

        return $shopMessage;
    }

    /**
     * setAvailability 
     * 
     * @param array $availability availability 
     * 
     * @return this
     */
    public function setShopInfo($shopInfo)
    {
        $this->shopInfo = $shopInfo;
        return $this;
    }

    /**
     * getShopIdByCategory
     *
     * @return int | null
     */
    public function getShopIdByCategory()
    {
        if (Mage::registry('current_category')) {
            $path = explode('/', Mage::registry('current_category')->getPath());
            if (isset($path[3])) {
                $comcatid = $path[3];

                $shop = Mage::getSingleton('apdc_commercant/shop')->getCollection()->addCategoryFilter($comcatid)->getFirstItem();
                if ($shop && $shop->getId()) {
                    return $shop->getId();
                }
            }
        }
        return null;
    }

    /**
     * getShopIdByProduct
     * 
     * @param Mage_Catalog_Model_Product $product product 
     * 
     * @return array
     */
    public function getShopIdByProduct(Mage_Catalog_Model_Product $product)
    {
        if ($product->getCommercant()) {
            $shop = Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldtoFilter('id_attribut_commercant',$product->getCommercant())->getFirstItem();
            if ($shop && $shop->getId()) {
                return $shop->getId();
            }
        }
        return null;
    }

    /**
     * getShopInfo 
     * 
     * @return array
     */
    protected function getShopInfo()
    {
        if (is_null($this->shopInfo)) {
            $shopId = null;
            $this->shopInfo = [];
            $product = $this->getProduct();
            if($this->getRequest()->getControllerName()=='category'){
                $shopId = $this->getShopIdByCategory();
            } else if ($product && $product->getId()) {
                $shopId = $this->getShopIdByProduct($product);
            } else if (Mage::registry('current_product')) {
                $shopId = $this->getShopIdByProduct(Mage::registry('current_product'));
            }
            if ($shopId) {
                $this->shopInfo = Mage::helper('apdc_commercant')->getInfoShop($shopId);
            }
        }
        return $this->shopInfo;
    }

    /**
     * getWeekDays 
     *
     * @param boolean $short : display short days or not
     * 
     * @return array
     */
    public function getWeekDays($short=true)
    {
        return Mage::helper('apdc_commercant')->getWeekDays($short);
    }
}
