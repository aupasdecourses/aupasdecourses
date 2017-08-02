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
        if ($this->_getSession()->isLoggedIn()) {
            $id = $this->getRequest()->getParam('id', null);
            if ($id) {
                $neighborhood = Mage::getModel('apdc_neighborhood/neighborhood')->load((int)$id);
                $customer = $this->_getSession()->getCustomer();
                if ($customer && $customer->getId() && $neighborhood && $neighborhood->getId()) {
                    $oldNeighborhoodId = $customer->getCustomerNeighborhood();
                    $customer->setCustomerNeighborhood($neighborhood->getId())
                        ->save();

                    if (Mage::getStoreConfig('apdc_neighborhood/notifications/is_active')) {
                        $oldNeighborhood = Mage::getModel('apdc_neighborhood/neighborhood');
                        if ($oldNeighborhoodId) {
                            $oldNeighborhood = $oldNeighborhood->load($oldNeighborhoodId);
                        }
                        Mage::helper('apdc_neighborhood')->sendChangeNeighborhoodAdminNotification($customer, $oldNeighborhood, $neighborhood);
                    }
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('apdc_neighborhood')->__('Your neighborhood is now %s', $neighborhood->getName()));
                    return $this->_redirectUrl($neighborhood->getStoreUrl());
                }
            }
        }
        $this->norouteAction();
        return;
    }

    /**
     * ajaxIUnderstoodAction 
     * Used to set NeighborhoodIUnderstood flag to not display the new_neighborhood informations anymore
     * 
     * @return void
     */
    public function ajaxIUnderstoodAction()
    {
        if ($this->getRequest()->isAjax()) {
            $iunderstood = $this->getRequest()->getParam('iunderstood');
            if ($iunderstood) {
                $this->_getSession()->setNeighborhoodIUnderstood(true);
                return 'ok';
            }
            return 'error';
        }

        $this->norouteAction();
        return;
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
