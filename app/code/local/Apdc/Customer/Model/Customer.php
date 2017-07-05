<?php
/**
 *
 * @category    Apdc
 * @package     Apdc Customer
 * @copyright  Copyright (c) 2017 Au Pas De Courses
 */

/**
 * Apdc Customer model
 *
 * @category    Apdc
 * @package     Apdc_Customer
 * @author      Pierre Mainguet <pierre@aupasdecourses.com>
 */
class Apdc_Customer_Model_Customer extends Mage_Customer_Model_Customer
{
	/**
     * Send email with reset password confirmation link
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordResetConfirmationEmail()
    {
        $storeId = Mage::app()->getStore()->getId();
        // If current store is landing page, get store where customer has been created
        if(!($storeId) || $storeId == 2){
        	$storeId = $this->getStore()->getId();
        	if($storeId==0){
        		$storeId=1;
        	}
        }

        $this->_sendEmailTemplate(self::XML_PATH_FORGOT_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('customer' => $this), $storeId);

        return $this;
    }

    /**
     * getNeighborhoodUrl 
     * 
     * @return string
     */
    public function getNeighborhoodUrl()
    {
        $neighborhood = $this->getNeighborhood();
        if ($neighborhood && $neighborhood->getId()) {
            return $neighborhood->getStoreUrl();
        }
        return Mage::app()->getWebsite($this->getWebsiteId())->getDefaultStore()->getBaseUrl();
    }

    /**
     * getNeighborhood 
     * 
     * @return Apdc_Neighborhood_Model_Neighborhood
     */
    public function getNeighborhood()
    {
        $neighborhood = Mage::getModel('apdc_neighborhood/neighborhood');
        if ($this->getCustomerNeighborhood()) {
            $neighborhood = $neighborhood->load($this->getCustomerNeighborhood());
        }
        return $neighborhood;
    }

    /**
     * needToChooseNeighborhood 
     * 
     * @return boolean
     */
    public function needToChooseNeighborhood()
    {
        if ((int)$this->getCustomerNeighborhood() > 0) {
            return false;
        } else {
            $websites = Mage::getModel('core/website')->getCollection()->addFieldToFilter('is_default', 1);
            $website = $websites->getFirstItem();
            if ($this->getWebsiteId() != $website->getId()) {
                return false;
            }
        }
        return true;
    }

}
