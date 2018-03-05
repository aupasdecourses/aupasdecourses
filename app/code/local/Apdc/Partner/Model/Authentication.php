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
 * Apdc_Partner_Model_Authentication 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Authentication extends Mage_Core_Model_Abstract
{
    /**
     * validateRequest
     * 
     * @param Mage_Core_Controller_Request_Http $request request 
     * 
     * @return boolean
     */
    public function validateRequest($request)
    {
        if (!$request->getHeader('Authorization')) {
            return false;
        } else if (!$request->isPost()) {
            return false;
        }
        return true;
    }

    /**
     * checkSignature
     * 
     * @param Apdc_Partner_Model_Partner $partner partner 
     * @param string $signature signature 
     * 
     * @return boolean
     */
    public function checkSignature($partner, $signature)
    {
        $data = $partner->getPartnerKey() . $partner->getPartnerSecret() . date('Y-m-d');
        $partnerSignature = hash_hmac('sha256', $data, $partner->getEmail(), true);
        if (hash_equals($partnerSignature, base64_decode($signature))) {
            return true;
        }
        return false;
    }

}
