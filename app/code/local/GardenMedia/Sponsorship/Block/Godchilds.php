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
 * GardenMedia_Sponsorship_Block_Godchilds 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Block_Godchilds extends Mage_Core_Block_Template
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
     * getGodchilds 
     * 
     * @return void
     */
    public function getGodchilds()
    {
        $ruleId = Mage::getStoreConfig('gm_sponsorship/rewards/salesrule_register');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $godchilds = Mage::getModel('customer/customer')->getCollection()
            ->addNameToSelect();
        $godchilds->getSelect()->join(
            array('godchilds' => $godchilds->getTable('gm_sponsorship/godchild')),
            'godchilds.godchild_id = e.entity_id and  godchilds.sponsor_id = ' . (int) $customer->getId(),
            array('*')
        );
        $godchilds->getSelect()->joinLeft(
            array('rewards' => $godchilds->getTable('gm_sponsorship/salesrule_coupon_customer')),
            'rewards.customer_owner_id = godchilds.godchild_id AND rewards.owner_type = "godchild"',
            array('has_rewards' => 'coupon_id')
        );
        $godchilds->getSelect()->join(
            array('coupon' => $godchilds->getTable('salesrule/coupon')),
            'coupon.coupon_id = rewards.coupon_id AND coupon.rule_id = ' . (int) $ruleId,
            array()
        );

        $godchilds->getSelect()->joinLeft(
            array('coupon_used' => $godchilds->getTable('salesrule/coupon_usage')),
            'coupon_used.customer_id = godchilds.godchild_id AND coupon_used.coupon_id = rewards.coupon_id AND coupon_used.times_used >= 1',
            array('is_used' => 'coupon_id')
        );


        return $godchilds;
    }

}
