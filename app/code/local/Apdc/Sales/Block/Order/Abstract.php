<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Sales
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Sales_Block_Order_Abstract 
 * 
 * @category Apdc
 * @package  Sales
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Sales_Block_Order_Abstract extends Mage_Core_Block_Template
{
    protected $websiteIds = [];
    protected $neighborhoods = [];

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    /**
     * getReorderUrl 
     * 
     * @param Mage_Sales_Model_Order $order          : order 
     * @param int                    $neighborhoodId : neighborhoodId 
     * 
     * @return string
     */
    public function getReorderUrl($order, $neighborhoodId=null)
    {
        $params = [
            'order_id' => $order->getId()
        ];
        if ($neighborhoodId) {
            $params['neighborhood_id'] = $neighborhoodId;
        }
        return $this->getUrl('sales/order/reorder', $params);
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * getNeighborhood 
     * 
     * @param Mage_Sales_Model_Order $order order 
     * 
     * @return string
     */
    public function getNeighborhoodName($order)
    {
        return Mage::app()->getStore($order->getStoreId())->getName();
    }

    /**
     * getWebsiteIdByStoreId 
     * 
     * @param int $storeId storeId 
     * 
     * @return int
     */
    protected function getWebsiteIdByStoreId($storeId)
    {
        if (!isset($this->websiteIds[$storeId])) {
            $this->websiteIds[$storeId] = Mage::getModel('core/store')->load($storeId)->getWebsite()->getId();
        }
        return $this->websiteIds[$storeId];
    }

    /**
     * isSameNeighborhoodAsCurrent 
     * 
     * @param Mage_Sales_Model_Order $order order 
     * 
     * @return boolean
     */
    public function isSameNeighborhoodAsCurrent($order)
    {
        $currentNeighborhood = Mage::helper('apdc_neighborhood')->getCurrentNeighborhood();
        if ($currentNeighborhood && $order->getStoreId() == $currentNeighborhood->getId()) {
            return true;
        }
        return false;
    }

}
