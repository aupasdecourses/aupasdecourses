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
 * GardenMedia_Sponsorship_IndexController 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Core_Controller_Front_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * indexAction 
     * 
     * @return void
     */
    public function indexAction()
    {
        if (!$this->_getSession()->isLoggedIn() || !Mage::helper('gm_sponsorship')->isEnabled()) {
            $this->_redirect('customer/account');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('core/session');
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Sponsorship | Invitations'));
        $this->renderLayout();
    }

    /**
     * inviteAction 
     * 
     * @return void
     */
    public function inviteAction()
    {
        if ($this->_getSession()->isLoggedIn() || !Mage::helper('gm_sponsorship')->isEnabled()) {
            $this->_redirect('customer/account');
            return;
        }
        $params = $this->getRequest()->getParams();
        if (isset($params['sponsor_id'], $params['sponsor_code']) &&
            (int) $params['sponsor_id'] > 0 &&
            $params['sponsor_code'] != ''
        ) {
            $sponsor = Mage::getModel('customer/customer')->load((int)$params['sponsor_id']);
            if ($sponsor && $sponsor->getId()) {
                $sponsorCode = Mage::getModel('gm_sponsorship/sponsor')->load((int)$params['sponsor_id']);
                if ($sponsorCode && $sponsorCode->getSponsorCode() == $params['sponsor_code']) {
                    $sponsorData = new Varien_Object();
                    $sponsorData->setSponsor($sponsor);
                    $sponsorData->setSponsorCode($sponsorCode);
                    Mage::getSingleton('core/session')->setSponsorData($sponsorData);
                }
            }
        }
        $this->getResponse()->setRedirect('/');
    }

    public function godchildsAction()
    {
        if (!$this->_getSession()->isLoggedIn() || !Mage::helper('gm_sponsorship')->isEnabled()) {
            $this->_redirect('customer/account');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('core/session');
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Sponsorship | My Godchilds'));
        $this->renderLayout();
    }

    public function rewardsAction()
    {
        if (!$this->_getSession()->isLoggedIn() || !Mage::helper('gm_sponsorship')->isEnabled()) {
            $this->_redirect('customer/account');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('core/session');
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Sponsorship | My Rewards'));
        $this->renderLayout();
    }

    public function sendEmailAction()
    {
        $postData = $this->getRequest()->getPost();
        $customer = $this->_getSession()->getCustomer();

		$emailTemplate = Mage::getModel('core/email_template');
        $vars = array(
            'customer' => $customer,
            'sponsorCode' => Mage::helper('gm_sponsorship')->getSponsorCode($customer),
            'sponsorLink' => Mage::helper('gm_sponsorship')->getUniqueLink($customer),
            'message' => $postData['emailMessage'],
            'friendName' => $postData['friendName']
        );

        $templateId = Mage::getStoreConfig('gm_sponsorship/email/template');
        if (!$templateId) {
            $templateId = 'gm_sponsorship_email_template';
        }
        $sender = array(
            'name' => $customer->getName(),
            'email' => $customer->getEmail()
        );

		try{
            $emailTemplate->sendTransactional($templateId, $sender, $postData['sendTo'], $postData['friendName'], $vars);
            if ($emailTemplate->getSentSuccess()) {
                Mage::getSingleton('core/session')->addSuccess($this->__('Invitation has been sent successfully'));
            } else {
                Mage::getSingleton('core/session')->addError($this->__('An error has occured. Sending email is not possible. Please try later or contact the administrator'));
            }
		} catch(Exception $error) {
            Mage::getSingleton('core/session')->addError($error->getMessage());
	    }
        $this->_redirect('*/*/index');
    }
}
