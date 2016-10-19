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
 * GardenMedia_Sponsorship_Helper_Data 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Core_Helper_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $sponsorCode = null;

    /**
     * getSponsorCode 
     * 
     * @param Mage_Customer_Model_Customer $customer : Magento customer entity
     * 
     * @return string
     */
    public function getSponsorCode($customer)
    {
        if (is_null($this->sponsorCode)) {
            $sponsor = Mage::getModel('gm_sponsorship/sponsor')->load($customer->getId());
            if ($sponsor && $sponsor->getId() > 0) {
                $this->sponsorCode = $sponsor->getSponsorCode();
            } else {
                try {
                    $sponsorCode = Mage::getSingleton('gm_sponsorship/couponCode')->generateSponsorCode();
                    if ($sponsorCode) {
                        $sponsor->setSponsorId($customer->getId())
                            ->setSponsorCode($sponsorCode)
                            ->save();

                        $this->sponsorCode = $sponsorCode;
                    }
                } catch(Exception $e) {
                    Mage::getSingleton('core/session')->addError(Mage::helper('gm_sponsorship')->__($e->getMessage())); 
                }
            }
        }

        return $this->sponsorCode;
    }

    /**
     * isEnabled
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) Mage::getStoreConfig('gm_sponsorship/general/active');
    }

    /**
     * getUniqueLink 
     * 
     * @param Mage_Customer_Model_Customer $customer : customer 
     * 
     * @return string
     */
    public function getUniqueLink($customer)
    {
        $url = Mage::getUrl('gm_sponsorship/index/invite', array('sponsor_id' => $customer->getId(), 'sponsor_code' => $this->getSponsorCode($customer)));
        $shortUrl = Mage::helper('gm_sponsorship/shortUrl')->shorten($url);
        if ($shortUrl) {
            return $shortUrl;
        }
        return $url;
    }

    /**
     * getTotalAmountOrdered 
     * 
     * @param Mage_Customer_Model_Customer $customer     : customer 
     * 
     * @return boolean
     */
    public function getTotalAmountOrdered($customer)
    {
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('main_table.customer_id', $customer->getId())
            ->addFieldToFilter(
                'main_table.state',
                array(
                    'in' => array(
                        Mage_Sales_Model_Order::STATE_PROCESSING,
                        Mage_Sales_Model_Order::STATE_COMPLETE
                    )
                )
            )
            ->addFieldToSelect('customer_id');

        $orders->getSelect()->join(
            array('invoice' => $orders->getTable('sales/invoice')),
            'invoice.order_id = main_table.entity_id',
            array(
                'invoice_id' => 'entity_id',
                'order_id' => 'order_id'
            )
        );

        $orders->getSelect()->columns('IFNULL(SUM(main_table.base_total_invoiced), 0) as total_invoiced');
        $orders->getSelect()->columns('IFNULL(SUM(main_table.base_total_refunded), 0) as total_refunded');
        $orders->getSelect()->order('invoice.entity_id DESC');

        return $orders;
    }

    /**
     * createReward 
     * 
     * @param Mage_Customer_Model_Customer $customer  : customer 
     * @param int                          $sponsorId : sponsor Id
     * 
     * @return void
     */
    public function createReward(Mage_Customer_Model_Customer $customer, $sponsorId)
    {
        try {
            $couponCode = Mage::getModel('gm_sponsorship/couponCode');
            $sponsorCode = $couponCode->generateRewardCode($sponsorId, $customer->getId(), 'sponsor');
            $godchildCode = $couponCode->generateRewardCode($customer->getId(), $sponsorId, 'godchild');

            $templateSponsorId = Mage::getStoreConfig('gm_sponsorship/rewards/template_sponsor');
            if (!$templateSponsorId) {
                $templateSponsorId = 'gm_sponsorship_rewards_template_sponsor';
            }

            $templateGodchildId = Mage::getStoreConfig('gm_sponsorship/rewards/template_godchild');
            if (!$templateGodchildId) {
                $templateGodchildId = 'gm_sponsorship_rewards_template_godchild';
            }

            $sponsor = Mage::getModel('customer/customer')->load($sponsorId);
            $vars = array(
                'sponsor' => $sponsor,
                'godchild' => $customer,
                'couponCode' => $sponsorCode
            );
            $this->sendRewardEmail($templateSponsorId, $sponsor->getEmail(), $sponsor->getName(), $vars);

            $vars['couponCode'] = $godchildCode;
            $this->sendRewardEmail($templateGodchildId, $customer->getEmail(), $customer->getName(), $vars);


        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * sendRewardEmail 
     * 
     * @param int|string $templateId : templateId 
     * @param string     $emailTo    : emailTo 
     * @param string     $nameTo     : nameTo 
     * @param array      $vars       : vars 
     * 
     * @return void
     */
    protected function sendRewardEmail($templateId, $emailTo, $nameTo, $vars)
    {
        $sender = array(
            'name' => Mage::getStoreConfig('trans_email/ident_general/name'),
            'email' => Mage::getStoreConfig('trans_email/ident_general/email')
        );
		$emailTemplate = Mage::getModel('core/email_template');
        $emailTemplate->sendTransactional($templateId, $sender, $emailTo, $nameTo, $vars);
        if (!$emailTemplate->getSentSuccess()) {
            Mage::throwException('Impossible d\'envoyer l\'email avec le coupon de réduction : ' . $vars['couponCode']);
        }
    }

}
