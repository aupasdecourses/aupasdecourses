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
 * GardenMedia_Sponsorship_Model_CouponCode 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Model_CouponCode extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $resource = Mage::getResourceModel('salesrule/coupon');
        $this->setGeneratorResource($resource);
    }
    /**
     * generateSponsorCode 
     * 
     * @return string
     */
    public function generateSponsorCode()
    {
        $length = (Mage::getStoreConfig('gm_sponsorship/sponsor/code_length') > 0 ? Mage::getStoreConfig('gm_sponsorship/sponsor/code_length') : 12);
        $data = array(
            'qty' => 1,
            'length' => $length,
            'format' => Mage::getStoreConfig('gm_sponsorship/sponsor/code_format'),
            'prefix' => Mage::getStoreConfig('gm_sponsorship/sponsor/code_prefix'),
            'suffix' => Mage::getStoreConfig('gm_sponsorship/sponsor/code_suffix'),
            'dash' => (int)Mage::getStoreConfig('gm_sponsorship/sponsor/code_dash'),
        );
        $resource = Mage::getResourceModel('gm_sponsorship/sponsor');
        $this->setGeneratorResource($resource);
        return $this->generateCouponCode($data);
    }

    /**
     * generateRewardCode 
     * 
     * @param int    $customerId        : customerId 
     * @param int    $sponsorCustomerId : sponsorCustomerId 
     * @param string $ownerType         : ownerType 
     * 
     * @return string
     */
    public function generateRewardCode($customerId, $sponsorCustomerId, $ownerType)
    {
        $ruleId = Mage::getStoreConfig('gm_sponsorship/rewards/salesrule');
        $length = (Mage::getStoreConfig('gm_sponsorship/rewards/code_length') > 0 ? Mage::getStoreConfig('gm_sponsorship/rewards/code_length') : 12);
        $data = array(
            'qty' => 1,
            'length' => $length,
            'format' => Mage::getStoreConfig('gm_sponsorship/rewards/code_format'),
            'prefix' => Mage::getStoreConfig('gm_sponsorship/rewards/code_prefix'),
            'suffix' => Mage::getStoreConfig('gm_sponsorship/rewards/code_suffix'),
            'dash' => (int)Mage::getStoreConfig('gm_sponsorship/rewards/code_dash'),
            'customer_owner_id' => $customerId,
            'customer_linked_id' => $sponsorCustomerId,
            'owner_type' => $ownerType,
            'rule_id' => $ruleId
        );
        return $this->generateCouponCode($data, true);
    }

    /**
     * generateCouponCode 
     * 
     * @param array   $data       : data 
     * @param boolean $saveCoupon : saveCoupon in DB
     * 
     * @return string|null
     */
    public function generateCouponCode($data, $saveCoupon=false)
    {
        $coupon = null;
        $generator = Mage::getSingleton('salesrule/coupon_massgenerator');
        $generator->setData($data);

        $size = $generator->getQty();
        $attempt = 0;
        $maxProbability = Mage_SalesRule_Model_Coupon_Massgenerator::MAX_PROBABILITY_OF_GUESSING; $maxAttempts = Mage_SalesRule_Model_Coupon_Massgenerator::MAX_GENERATE_ATTEMPTS;

        $chars = count(Mage::helper('salesrule/coupon')->getCharset($generator->getFormat()));
        $length = (int) $generator->getLength();
        $maxCodes = pow($chars, $length);
        $probability = $size / $maxCodes;
        //increase the length of Code if probability is low
        if ($probability > $maxProbability) {
            do {
                $length++;
                $maxCodes = pow($chars, $length);
                $probability = $size / $maxCodes;
            } while ($probability > $maxProbability);
            $generator->setLength($length);
        }

        do {
            if ($attempt >= $maxAttempts) {
                Mage::throwException(Mage::helper('salesrule')->__('Unable to create requested Coupon Qty. Please check settings and try again.'));
            }
            $couponCode = $generator->generateCode();
            $attempt++;
        } while ($this->getGeneratorResource()->exists($couponCode));

        if ($saveCoupon) {
            $now = $this->getGeneratorResource()->formatDate(
                Mage::getSingleton('core/date')->gmtTimestamp()
            );
            $coupon = Mage::getModel('salesrule/coupon')
                ->setRuleId($generator->getRuleId())
                ->setUsageLimit(1)
                ->setUsagePerCustomer(1)
                ->setCreatedAt($now)
                ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($couponCode)
                ->setCustomerUnique(true)
                ->save();

            $couponCustomer = Mage::getModel('gm_sponsorship/salesrule_coupon_customer')
                ->setCustomerOwnerId($generator->getCustomerOwnerId())
                ->setCustomerLinkedId($generator->getCustomerLinkedId())
                ->setOwnerType($generator->getOwnerType())
                ->setCouponId($coupon->getId())
                ->save();
        }

        return $couponCode;
    }

}
