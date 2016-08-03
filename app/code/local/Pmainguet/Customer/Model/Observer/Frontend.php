<?php
/** @category    APDC
 * @package     Pmainguet_Customer
 * @author      Pierre Mainguet <mainguetpierre@gmail.com>
 * @copyright   Copyright (c) 2016
 * All publicly accessible method names correspond to the event they observe.
 *
 */
class Pmainguet_Customer_Model_Observer_Frontend
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function customerRegisterSuccess($observer)
    {
        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');

        // This event occurs within Mage_Customer_AccountController::createPostAction
        // however it occurs before the controller sets it's own redirect settings.
        // Therefore we set this flag to true now, and then within the postdispatch
        // we'll redirect to our custom URL
        $session->setData('customer_register_success', true);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function controllerActionPostdispatchCreatePostAction($observer)
    {
        /* @var $controller Mage_Customer_AccountController */
        /* @var $session Mage_Customer_Model_Session */

        $session = Mage::getSingleton('customer/session');

        // We must test for a successful customer registration because they
        // could have failed registration despite reaching postdispatch
        // (for example: they used an existing email address)
        if ($session->getData('customer_register_success')) {
            $session->unsetData('customer_register_success');

            $url = Mage::getBaseUrl();
            $controller = $observer->getData('controller_action');
            Mage::getSingleton('core/session')->addSuccess('Votre compte a bien été créé. Bienvenue chez Au Pas De Courses !');
            $controller->getResponse()->setRedirect($url);
        }
    }
}
