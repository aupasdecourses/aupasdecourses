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
 * GardenMedia_Sponsorship_Block_Rewards 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Block_Rewards extends Mage_Core_Block_Template
{
    public function getRewards()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $rewards = Mage::getModel('customer/customer')->getCollection()
            ->addNameToSelect();

        $rewards->getSelect()->join(
            array('rewards' => $rewards->getTable('gm_sponsorship/salesrule_coupon_customer')),
            'rewards.customer_owner_id = ' . (int) $customer->getId() . ' AND rewards.customer_linked_id = e.entity_id',
            array('*')
        );

        $rewards->getSelect()->joinLeft(
            array('coupon_used' => $rewards->getTable('salesrule/coupon_usage')),
            'coupon_used.customer_id = rewards.customer_owner_id AND coupon_used.coupon_id = rewards.coupon_id AND coupon_used.times_used >= 1',
            array('is_used' => 'coupon_id')
        );

        $rewards->getSelect()->joinLeft(
            array('coupon' => $rewards->getTable('salesrule/coupon')),
            'coupon.coupon_id = rewards.coupon_id',
            array('coupon_code' => 'code')
        );

        return $rewards;
    }
}
