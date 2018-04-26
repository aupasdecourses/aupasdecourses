<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
require_once(Mage::getModuleDir('controllers','Mage_Customer').DS.'AccountController.php');
/**
 * Apdc_Neighborhood_IndexController 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Core_Controller_Front_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * saveAction 
     * Set new neighborhood for customer
     * 
     * @return void
     */
    public function saveAction()
    {
        $postcode = $this->getRequest()->getParam('postcode', null);
        if ($postcode) {
            $neighborhood = Mage::helper('apdc_neighborhood')->getNeighborhoodByPostcode($postcode);
            if ($neighborhood) {
                $customer = $this->_getSession()->getCustomer();
                if ($customer && $customer->getId() && $neighborhood && $neighborhood->getId()) {
                    $oldNeighborhoodId = $customer->getCustomerNeighborhood();
                    $customer->setCustomerNeighborhood($neighborhood->getId())
                        ->save();

                    if (Mage::getStoreConfig('apdc_neighborhood/notifications/is_active')) {
                        $oldNeighborhood = Mage::app()->getStore($oldNeighborhoodId);
                        Mage::helper('apdc_neighborhood')->sendChangeNeighborhoodAdminNotification($customer, $oldNeighborhood, $neighborhood);
                    }
                }
                $neighborhood->setPostcode($postcode);
                Mage::helper('apdc_neighborhood')->setNeighborhood($neighborhood);
                Mage::getSingleton('core/session')->addSuccess(
                    Mage::helper('apdc_neighborhood')->__('Your neighborhood is now %s', $neighborhood->getName())
                );
                return $this->_redirectUrl($neighborhood->getUrl());
            }
            Mage::getSingleton('core/session')->addError('Désolé, nous ne livrons pas encore à votre adresse.');
        } else {
            Mage::getSingleton('core/session')->addError('Le code postal doit être renseigné');
        }
        return $this->_redirectUrl(Mage::app()->getStore()->getUrl());
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
