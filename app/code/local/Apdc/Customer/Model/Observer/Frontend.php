<?php
/** @category    APDC
 * @author      Pierre Mainguet <mainguetpierre@gmail.com>
 * Redirect logged customer to homepage
 */
class Apdc_Customer_Model_Observer_Frontend
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

            $customer = $session->getCustomer();
            if ($customer->getCustomerNeighborhood() > 0) {
                $neighborhood = Mage::app()->getStore((int)$customer->getCustomerNeighborhood());
                if ($neighborhood && $neighborhood->getId()) {
                    $url = $neighborhood->getBaseUrl();
                }
            } else {
                $url = Mage::getBaseUrl();
            }
            $controller = $observer->getData('controller_action');
            Mage::getSingleton('core/session')->addSuccess('Votre compte a bien été créé. Bienvenue chez Au Pas De Courses !');
			if(Mage::app()->getRequest()->getPost('success_url') && Mage::app()->getRequest()->getPost('success_url') != '') {
				$controller->getResponse()->setRedirect(Mage::app()->getRequest()->getPost('success_url'));
			}
			else {
				$controller->getResponse()->setRedirect($url);
			}
        }
    }

    public function customerRegistrationAllowed(Varien_Event_Observer $observer)
    {
        if (Mage::app()->getWebsite()->getCode() == 'apdc_main') {
            $observer->getResult()->setIsAllowed(false);
        }
    }

    public function customerLoggedIn(Varien_Event_Observer $observer)
    {
        if (Mage::app()->getWebsite()->getCode() == 'apdc_main') {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = $observer->getCustomer();
            $storeUrl = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getBaseUrl();
            if ($customer->getCustomerNeighborhood()) {
                $neighborhood = Mage::app()->getStore((int)$customer->getCustomerNeighborhood());
                if ($neighborhood && $neighborhood->getId()) {
                    $storeUrl = $neighborhood->getBaseUrl();
                }
            }
        } else {
            $storeUrl = Mage::app()->getWebsite()->getDefaultStore()->getBaseUrl();
        }
        Mage::getSingleton('customer/session')->setBeforeAuthUrl($storeUrl);
    }
	
	public function customerLogout(Varien_Event_Observer $observer)
	{
		$observer->getControllerAction()
			->setRedirectWithCookieCheck('http://apdc.touchfordiffusion.com/');
	}
	
}
