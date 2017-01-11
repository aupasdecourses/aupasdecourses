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
 * GardenMedia_Sponsorship_Model_Customer_Form 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Customer_Model_Form
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Model_Customer_Form extends Mage_Customer_Model_Form
{
    /**
     * Extract data from request and return associative data array
     *
     * @param Zend_Controller_Request_Http $request
     * @param string $scope the request scope
     * @param boolean $scopeOnly search value only in scope or search value in global too
     * @return array
     */
    public function extractData(Zend_Controller_Request_Http $request, $scope = null, $scopeOnly = true)
    {
        $data = parent::extractData($request, $scope, $scopeOnly);

        $sponsorCode = $request->getParam('sponsor_code', null);
        if ($sponsorCode) {
            $data['sponsor_code'] = $sponsorCode;
        }
        return $data;
    }

    /**
     * Validate data array and return true or array of errors
     *
     * @param array $data
     * @return boolean|array
     */
    public function validateData(array $data)
    {
        $errors = parent::validateData($data);
        if (!is_array($errors)) {
            $errors = array();
        }

        if (isset($data['sponsor_code'])) {
            $sponsorCode = trim($data['sponsor_code']);
            if (!empty($sponsorCode)) {
                $sponsor = Mage::getModel('gm_sponsorship/sponsor')->load($sponsorCode, 'sponsor_code');
                if (!$sponsor || !$sponsor->getId()) {
                    $errors[] = Mage::helper('gm_sponsorship')->__('Sponsor Code is not valid');
                }
            }
        }
        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }
}
