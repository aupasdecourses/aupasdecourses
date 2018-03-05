<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Partner_Model_Partner 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Partner extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'apdc_partner';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'partner';

    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_partner/partner');
    }

    public function login($partnerKey, $signature)
    {
        $result = false;
        try {
            $this->loadByPartnerKey($partnerKey);
            print_r($signature);
            if (Mage::getModel('apdc_partner/authentication')->checkSignature($this, $signature)) {
                $result = true;
                if ($this->getIsActive() != '1') {
                    Mage::throwException(Mage::helper('adminhtml')->__('This account is inactive.'));
                }
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->unsetData();
            throw $e;
        }

        if (!$result) {
            $this->unsetData();
        }
        return $result;
    }

    public function loadByPartnerKey($partnerKey)
    {
        $this->setData($this->getResource()->loadByPartnerKey($partnerKey));
        return $this;
    }

    /**
     * getProductList 
     * 
     * @return string (json)
     */
    public function getProductList()
    {
        return Mage::getModel('apdc_partner/data_products')->getList();
    }
}
