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
 * Apdc_Catalog_Block_Product_Availability_Message 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Product_Availability_Message extends Mage_Core_Block_Template
{
    /**
     * @var array
     */
    protected $shopHolidays = [];

    /**
     * getAvailability 
     * 
     * @return array
     */
    public function getAvailability()
    {
        $product = $this->getProduct();
        return Mage::helper('apdc_catalog/product_availability')->getAvailability($product);
    }

    /**
     * getSpecificMessages 
     * 
     * @return '';
     */
    public function getSpecificMessages()
    {
        $availability = $this->getAvailability();
        if (!$availability['is_available']) {
            switch($availability['product_availability']) {
            case 4:
                return $this->getShopHolidaysSpecificMessage();
            }
        } else if (!$availability['can_order']) {
            return $this->getCanOrderDaysMessage($availability['can_order_days']);
        }
        return '';
    }

    /**
     * getShopHolidaysSpecificMessage 
     * 
     * @return string
     */
    protected function getShopHolidaysSpecificMessage()
    {
        if (Mage::getSingleton('core/session')->getDdate()) {
            $product = $this->getProduct();
            $date = Mage::getSingleton('core/session')->getDdate();
            if (!isset($this->shopHolidays[$product->getCommercant() . '_' . $date])) {
                $message = '';
                $shopInfo = Mage::helper('apdc_commercant')->getInfoShopByCommercantId($product->getCommercant());
                if (!empty($shopInfo)) {
                    $message = (isset($shopInfo['availability'][$date]) ? $shopInfo['availability'][$date]['message_info'] : '');
                }
                $this->shopHolidays[$product->getCommercant() . '_' . $date] = $message;
            }
            return $this->shopHolidays[$product->getCommercant() . '_' . $date];
        }
        return '';
    }

    /**
     * getCanOrderDaysMessage 
     * 
     * @param string $canOrderDays canOrderDays 
     * 
     * @return string
     */
    protected function getCanOrderDaysMessage($canOrderDays)
    {
        $canOrderDays = explode(',', $canOrderDays);
        $orderDays = [];
        foreach ($canOrderDays as $dayId) {
            $orderDays[] = Mage::getSingleton('apdc_catalog/source_product_days')->getOptionLabel($dayId);
        }
        if (!empty($orderDays)) {
            if (count($orderDays) > 1) {
                return Mage::helper('apdc_catalog')->__('Vous pouvez commander ce produit les : %s', implode(', ', $orderDays));
            } else {
                return Mage::helper('apdc_catalog')->__('Vous pouvez commander ce produit uniquement le : %s', implode(', ', $orderDays));
            }
        }
        return '';
    }
}
