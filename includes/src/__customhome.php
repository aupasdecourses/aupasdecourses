<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Magethrow
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Domainpolicy
{
    /**
     * X-Frame-Options allow (header is absent)
     */
    const FRAME_POLICY_ALLOW = 1;

    /**
     * X-Frame-Options SAMEORIGIN
     */
    const FRAME_POLICY_ORIGIN = 2;

    /**
     * Path to backend domain policy settings
     */
    const XML_DOMAIN_POLICY_BACKEND = 'admin/security/domain_policy_backend';

    /**
     * Path to frontend domain policy settings
     */
    const XML_DOMAIN_POLICY_FRONTEND = 'admin/security/domain_policy_frontend';

    /**
     * Current store
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    public function __construct($options = array())
    {
        $this->_store = isset($options['store']) ? $options['store'] : Mage::app()->getStore();
    }

    /**
     * Add X-Frame-Options header to response, depends on config settings
     *
     * @var Varien_Object $observer
     * @return $this
     */
    public function addDomainPolicyHeader($observer)
    {
        /** @var Mage_Core_Controller->getCurrentAreaDomainPolicy_Varien_Action $action */
        $action = $observer->getControllerAction();
        $policy = null;

        if ('adminhtml' == $action->getLayout()->getArea()) {
            $policy = $this->getBackendPolicy();
        } elseif('frontend' == $action->getLayout()->getArea()) {
            $policy = $this->getFrontendPolicy();
        }

        if ($policy) {
            /** @var Mage_Core_Controller_Response_Http $response */
            $response = $action->getResponse();
            $response->setHeader('X-Frame-Options', $policy, true);
        }

        return $this;
    }

    /**
     * Get backend policy
     *
     * @return string|null
     */
    public function getBackendPolicy()
    {
        return $this->_getDomainPolicyByCode((int)(string)$this->_store->getConfig(self::XML_DOMAIN_POLICY_BACKEND));
    }

    /**
     * Get frontend policy
     *
     * @return string|null
     */
    public function getFrontendPolicy()
    {
        return $this->_getDomainPolicyByCode((int)(string)$this->_store->getConfig(self::XML_DOMAIN_POLICY_FRONTEND));
    }



    /**
     * Return string representation for policy code
     *
     * @param $policyCode
     * @return string|null
     */
    protected function _getDomainPolicyByCode($policyCode)
    {
        switch($policyCode) {
            case self::FRAME_POLICY_ALLOW:
                $policy = null;
                break;
            default:
                $policy = 'SAMEORIGIN';
        }

        return $policy;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log data helper
 */
class Mage_Log_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_LOG_ENABLED = 'system/log/enable_log';

    /**
     * @var Mage_Log_Helper_Data
     */
    protected $_logLevel;

    public function __construct(array $data = array())
    {
        $this->_logLevel = isset($data['log_level']) ? $data['log_level']
            : intval(Mage::getStoreConfig(self::XML_PATH_LOG_ENABLED));
    }

    /**
     * Are visitor should be logged
     *
     * @return bool
     */
    public function isVisitorLogEnabled()
    {
        return $this->_logLevel == Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel::LOG_LEVEL_VISITORS
        || $this->isLogEnabled();
    }

    /**
     * Are all events should be logged
     *
     * @return bool
     */
    public function isLogEnabled()
    {
        return $this->_logLevel == Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel::LOG_LEVEL_ALL;
    }

    /**
     * Are all events should be disabled
     *
     * @return bool
     */
    public function isLogDisabled()
    {
        return $this->_logLevel == Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel::LOG_LEVEL_NONE;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Logging level backend source model
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel
{
    /**
     * Don't log anything
     */
    const LOG_LEVEL_NONE = 0;

    /**
     * All possible logs enabled
     */
    const LOG_LEVEL_ALL = 1;

    /**
     * Logs only visitors, needs for working compare products and customer segment's related functionality
     * (eg. shopping cart discount for segments with not logged in customers)
     */
    const LOG_LEVEL_VISITORS = 2;

    /**
     * @var Mage_Log_Helper_Data
     */
    protected $_helper;

    public function __construct(array $data = array())
    {
        $this->_helper = !empty($data['helper']) ? $data['helper'] : Mage::helper('log');
    }

    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => $this->_helper->__('Yes'),
                'value' => self::LOG_LEVEL_ALL,
            ),
            array(
                'label' => $this->_helper->__('No'),
                'value' => self::LOG_LEVEL_NONE,
            ),
            array(
                'label' => $this->_helper->__('Visitors only'),
                'value' => self::LOG_LEVEL_VISITORS,
            ),
        );

        return $options;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page cache observer model
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Model_Observer
{
    const XML_NODE_ALLOWED_CACHE = 'frontend/cache/allowed_requests';

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return Mage::helper('pagecache')->isEnabled();
    }

    /**
     * Check when cache should be disabled
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_PageCache_Model_Observer
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        $request = $action->getRequest();
        $needCaching = true;

        if ($request->isPost()) {
            $needCaching = false;
        }

        $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);

        if (!$configuration) {
            $needCaching = false;
        }

        $configuration = $configuration->asArray();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!isset($configuration[$module])) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['controller']) && $configuration[$module]['controller'] != $controller) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['action']) && $configuration[$module]['action'] != $action) {
            $needCaching = false;
        }

        if (!$needCaching) {
            Mage::helper('pagecache')->setNoCacheCookie();
        }

        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page cache data helper
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Pathes to external cache config options
     */
    const XML_PATH_EXTERNAL_CACHE_ENABLED  = 'system/external_page_cache/enabled';
    const XML_PATH_EXTERNAL_CACHE_LIFETIME = 'system/external_page_cache/cookie_lifetime';
    const XML_PATH_EXTERNAL_CACHE_CONTROL  = 'system/external_page_cache/control';

    /**
     * Path to external cache controls
     */
    const XML_PATH_EXTERNAL_CACHE_CONTROLS = 'global/external_cache/controls';

    /**
     * Cookie name for disabling external caching
     *
     * @var string
     */
    const NO_CACHE_COOKIE = 'external_no_cache';

    /**
     * Check whether external cache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_EXTERNAL_CACHE_ENABLED);
    }

    /**
     * Return all available external cache controls
     *
     * @return array
     */
    public function getCacheControls()
    {
        $controls = Mage::app()->getConfig()->getNode(self::XML_PATH_EXTERNAL_CACHE_CONTROLS);
        return $controls->asCanonicalArray();
    }

    /**
     * Initialize proper external cache control model
     *
     * @throws Mage_Core_Exception
     * @return Mage_PageCache_Model_Control_Interface
     */
    public function getCacheControlInstance()
    {
        $usedControl = Mage::getStoreConfig(self::XML_PATH_EXTERNAL_CACHE_CONTROL);
        if ($usedControl) {
            foreach ($this->getCacheControls() as $control => $info) {
                if ($control == $usedControl && !empty($info['class'])) {
                    return Mage::getSingleton($info['class']);
                }
            }
        }
        Mage::throwException($this->__('Failed to load external cache control'));
    }

    /**
     * Disable caching on external storage side by setting special cookie
     *
     * @return void
     */
    public function setNoCacheCookie()
    {
        $cookie   = Mage::getSingleton('core/cookie');
        $lifetime = Mage::getStoreConfig(self::XML_PATH_EXTERNAL_CACHE_LIFETIME);
        $noCache  = $cookie->get(self::NO_CACHE_COOKIE);

        if ($noCache) {
            $cookie->renew(self::NO_CACHE_COOKIE, $lifetime);
        } else {
            $cookie->set(self::NO_CACHE_COOKIE, 1, $lifetime);
        }
    }

    /**
     * Returns a lifetime of cookie for external cache
     *
     * @return string Time in seconds
     */
    public function getNoCacheCookieLifetime()
    {
        return Mage::getStoreConfig(self::XML_PATH_EXTERNAL_CACHE_LIFETIME);
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Observer
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Observer
{
    /**
     * Whether set quote to be persistent in workflow
     *
     * @var bool
     */
    protected $_setQuotePersistent = true;

    /**
     * Apply persistent data
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Persistent_Model_Observer
     */
    public function applyPersistentData($observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !$this->_getPersistentHelper()->isPersistent() || Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this;
        }
        Mage::getModel('persistent/persistent_config')
            ->setConfigFilePath(Mage::helper('persistent')->getPersistentConfigFilePath())
            ->fire();
        return $this;
    }

    /**
     * Apply persistent data to specific block
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Persistent_Model_Observer
     */
    public function applyBlockPersistentData($observer)
    {
        if (!$this->_getPersistentHelper()->isPersistent() || Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this;
        }

        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getEvent()->getBlock();

        if (!$block) {
            return $this;
        }

        $xPath = '//instances/blocks/*[block_type="' . get_class($block) . '"]';
        $configFilePath = $observer->getEvent()->getConfigFilePath();

        /** @var $persistentConfig Mage_Persistent_Model_Persistent_Config */
        $persistentConfig = Mage::getModel('persistent/persistent_config')
            ->setConfigFilePath(
                $configFilePath ? $configFilePath : Mage::helper('persistent')->getPersistentConfigFilePath()
            );

        foreach ($persistentConfig->getXmlConfig()->xpath($xPath) as $persistentConfigInfo) {
            $persistentConfig->fireOne($persistentConfigInfo->asArray(), $block);
        }

        return $this;
    }
    /**
     * Emulate welcome message with persistent data
     *
     * @param Mage_Core_Block_Abstract $block
     * @return Mage_Persistent_Model_Observer
     */
    public function emulateWelcomeMessageBlock($block)
    {
        $block->setWelcome(
            Mage::helper('persistent')->__('Welcome, %s!', Mage::helper('core')->escapeHtml($this->_getPersistentCustomer()->getName(), null))
        );
        return $this;
    }
    /**
     * Emulate 'welcome' block with persistent data
     *
     * @param Mage_Core_Block_Abstract $block
     * @return Mage_Persistent_Model_Observer
     */
    public function emulateWelcomeBlock($block)
    {
        $this->_applyAccountLinksPersistentData();
        $block->setAdditionalHtml(Mage::app()->getLayout()->getBlock('header.additional')->toHtml());

        return $this;
    }

    /**
     * Emulate 'account links' block with persistent data
     */
    protected function _applyAccountLinksPersistentData()
    {
        if (!Mage::app()->getLayout()->getBlock('header.additional')) {
            Mage::app()->getLayout()->addBlock('persistent/header_additional', 'header.additional');
        }
    }

    /**
     * Emulate 'account links' block with persistent data
     *
     * @param Mage_Core_Block_Abstract $block
     */
    public function emulateAccountLinks($block)
    {
        $this->_applyAccountLinksPersistentData();
        $block->getCacheKeyInfo();
        $block->addLink(
            Mage::helper('persistent')->getPersistentName(),
            Mage::helper('persistent')->getUnsetCookieUrl(),
            Mage::helper('persistent')->getPersistentName(),
            false,
            array(),
            110
        );
        $block->removeLinkByUrl(Mage::helper('customer')->getRegisterUrl());
        $block->removeLinkByUrl(Mage::helper('customer')->getLoginUrl());
    }

    /**
     * Emulate 'top links' block with persistent data
     *
     * @param Mage_Core_Block_Abstract $block
     */
    public function emulateTopLinks($block)
    {
        $this->_applyAccountLinksPersistentData();
    }

    /**
     * Emulate quote by persistent data
     *
     * @param Varien_Event_Observer $observer
     */
    public function emulateQuote($observer)
    {
        $stopActions = array(
            'persistent_index_saveMethod',
            'customer_account_createpost'
        );

        if (!Mage::helper('persistent')->canProcess($observer)
            || !$this->_getPersistentHelper()->isPersistent() || Mage::getSingleton('customer/session')->isLoggedIn()) {
            return;
        }

        /** @var $action Mage_Checkout_OnepageController */
        $action = $observer->getEvent()->getControllerAction();
        $actionName = $action->getFullActionName();

        if (in_array($actionName, $stopActions)) {
            return;
        }

        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = Mage::getSingleton('checkout/session');
        if ($this->_isShoppingCartPersist()) {
            $checkoutSession->setCustomer($this->_getPersistentCustomer());
            if (!$checkoutSession->hasQuote()) {
                $checkoutSession->getQuote();
            }
        }
    }

    /**
     * Set persistent data into quote
     *
     * @param Varien_Event_Observer $observer
     */
    public function setQuotePersistentData($observer)
    {
        if (!$this->_isPersistent()) {
            return;
        }

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        if ($this->_isGuestShoppingCart() && $this->_setQuotePersistent) {
            //Quote is not actual customer's quote, just persistent
            $quote->setIsActive(false)->setIsPersistent(true);
        }
    }

    /**
     * Set quote to be loaded even if not active
     *
     * @param Varien_Event_Observer $observer
     */
    public function setLoadPersistentQuote($observer)
    {
        if (!$this->_isGuestShoppingCart()) {
            return;
        }

        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = $observer->getEvent()->getCheckoutSession();
        if ($checkoutSession) {
            $checkoutSession->setLoadInactive();
        }
    }

    /**
     * Prevent clear checkout session
     *
     * @param Varien_Event_Observer $observer
     */
    public function preventClearCheckoutSession($observer)
    {
        $action = $this->_checkClearCheckoutSessionNecessity($observer);

        if ($action) {
            $action->setClearCheckoutSession(false);
        }
    }

    /**
     * Make persistent quote to be guest
     *
     * @param Varien_Event_Observer $observer
     */
    public function makePersistentQuoteGuest($observer)
    {
        if (!$this->_checkClearCheckoutSessionNecessity($observer)) {
            return;
        }

        $this->setQuoteGuest(true);
    }

    /**
     * Check if checkout session should NOT be cleared
     *
     * @param Varien_Event_Observer $observer
     * @return bool|Mage_Persistent_IndexController
     */
    protected function _checkClearCheckoutSessionNecessity($observer)
    {
        if (!$this->_isGuestShoppingCart()) {
            return false;
        }

        /** @var $action Mage_Persistent_IndexController */
        $action = $observer->getEvent()->getControllerAction();
        if ($action instanceof Mage_Persistent_IndexController) {
            return $action;
        }

        return false;
    }

    /**
     * Reset session data when customer re-authenticates
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerAuthenticatedEvent($observer)
    {
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        $customerSession->setCustomerId(null)->setCustomerGroupId(null);

        if (Mage::app()->getRequest()->getParam('context') != 'checkout') {
            $this->_expirePersistentSession();
            return;
        }

        $this->setQuoteGuest();
    }

    /**
     * Unset persistent cookie and make customer's quote as a guest
     *
     * @param Varien_Event_Observer $observer
     */
    public function removePersistentCookie($observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer) || !$this->_isPersistent()) {
            return;
        }

        $this->_getPersistentHelper()->getSession()->removePersistentCookie();
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            $customerSession->setCustomerId(null)->setCustomerGroupId(null);
        }

        $this->setQuoteGuest();
    }

    /**
     * Disable guest checkout if we are in persistent mode
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableGuestCheckout($observer)
    {
        if ($this->_getPersistentHelper()->isPersistent()) {
            $observer->getEvent()->getResult()->setIsAllowed(false);
        }
    }

    /**
     * Prevent express checkout with PayPal Express checkout
     *
     * @param Varien_Event_Observer $observer
     */
    public function preventExpressCheckout($observer)
    {
        if (!$this->_isLoggedOut()) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Front_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if (method_exists($controllerAction, 'redirectLogin')) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('persistent')->__('To proceed to Checkout, please log in using your email address.')
            );
            $controllerAction->redirectLogin();
            if ($controllerAction instanceof Mage_Paypal_Controller_Express_Abstract) {
                Mage::getSingleton('customer/session')
                    ->setBeforeAuthUrl(Mage::getUrl('persistent/index/expressCheckout'));
            }
        }
    }

    /**
     * Retrieve persistent customer instance
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getPersistentCustomer()
    {
        return Mage::getModel('customer/customer')->load(
            $this->_getPersistentHelper()->getSession()->getCustomerId()
        );
    }

    /**
     * Retrieve persistent helper
     *
     * @return Mage_Persistent_Helper_Session
     */
    protected function _getPersistentHelper()
    {
        return Mage::helper('persistent/session');
    }

    /**
     * Return current active quote for persistent customer
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        $quote = Mage::getModel('sales/quote');
        $quote->loadByCustomer($this->_getPersistentCustomer());
        return $quote;
    }

    /**
     * Check whether shopping cart is persistent
     *
     * @return bool
     */
    protected function _isShoppingCartPersist()
    {
        return Mage::helper('persistent')->isShoppingCartPersist();
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    protected function _isPersistent()
    {
        return $this->_getPersistentHelper()->isPersistent();
    }

    /**
     * Check if persistent mode is running and customer is logged out
     *
     * @return bool
     */
    protected function _isLoggedOut()
    {
        return $this->_isPersistent() && !Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Check if shopping cart is guest while persistent session and user is logged out
     *
     * @return bool
     */
    protected function _isGuestShoppingCart()
    {
        return $this->_isLoggedOut() && !Mage::helper('persistent')->isShoppingCartPersist();
    }

    /**
     * Make quote to be guest
     *
     * @param bool $checkQuote Check quote to be persistent (not stolen)
     */
    public function setQuoteGuest($checkQuote = false)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote && $quote->getId()) {
            if ($checkQuote && !Mage::helper('persistent')->isShoppingCartPersist() && !$quote->getIsPersistent()) {
                Mage::getSingleton('checkout/session')->unsetAll();
                return;
            }

            $quote->getPaymentsCollection()->walk('delete');
            $quote->getAddressesCollection()->walk('delete');
            $this->_setQuotePersistent = false;
            $quote
                ->setIsActive(true)
                ->setCustomerId(null)
                ->setCustomerEmail(null)
                ->setCustomerFirstname(null)
                ->setCustomerMiddlename(null)
                ->setCustomerLastname(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
                ->setIsPersistent(false)
                ->removeAllAddresses();
            //Create guest addresses
            $quote->getShippingAddress();
            $quote->getBillingAddress();
            $quote->collectTotals()->save();
        }

        $this->_getPersistentHelper()->getSession()->removePersistentCookie();
    }

    /**
     * Check and clear session data if persistent session expired
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkExpirePersistentQuote(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)) {
            return;
        }

        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');

        if (Mage::helper('persistent')->isEnabled()
            && !$this->_isPersistent()
            && !$customerSession->isLoggedIn()
            && Mage::getSingleton('checkout/session')->getQuoteId()
            && !($observer->getControllerAction() instanceof Mage_Checkout_OnepageController)
            // persistent session does not expire on onepage checkout page to not spoil customer group id
        ) {
            Mage::dispatchEvent('persistent_session_expired');
            $this->_expirePersistentSession();
            $customerSession->setCustomerId(null)->setCustomerGroupId(null);
        }
    }
    /**
     * Active Persistent Sessions
     */
    protected function _expirePersistentSession()
    {
        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = Mage::getSingleton('checkout/session');

        $quote = $checkoutSession->setLoadInactive()->getQuote();
        if ($quote->getIsActive() && $quote->getCustomerId()) {
            $checkoutSession->setCustomer(null)->unsetAll();
        } else {
            $quote
                ->setIsActive(true)
                ->setIsPersistent(false)
                ->setCustomerId(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }
    }

    /**
     * Clear expired persistent sessions
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return Mage_Persistent_Model_Observer_Cron
     */
    public function clearExpiredCronJob(Mage_Cron_Model_Schedule $schedule)
    {
        $websiteIds = Mage::getResourceModel('core/website_collection')->getAllIds();
        if (!is_array($websiteIds)) {
            return $this;
        }

        foreach ($websiteIds as $websiteId) {
            Mage::getModel('persistent/session')->deleteExpired($websiteId);
        }

        return $this;
    }

    /**
     * Create handle for persistent session if persistent cookie and customer not logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function createPersistentHandleLayout(Varien_Event_Observer $observer)
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();
        if (Mage::helper('persistent')->canProcess($observer) && $layout && Mage::helper('persistent')->isEnabled()
            && Mage::helper('persistent/session')->isPersistent()
        ) {
            $handle = (Mage::getSingleton('customer/session')->isLoggedIn())
                ? Mage_Persistent_Helper_Data::LOGGED_IN_LAYOUT_HANDLE
                : Mage_Persistent_Helper_Data::LOGGED_OUT_LAYOUT_HANDLE;
            $layout->getUpdate()->addHandle($handle);
        }
    }

    /**
     * Update customer id and customer group id if user is in persistent session
     *
     * @param Varien_Event_Observer $observer
     */
    public function updateCustomerCookies(Varien_Event_Observer $observer)
    {
        if (!$this->_isPersistent()) {
            return;
        }

        $customerCookies = $observer->getEvent()->getCustomerCookies();
        if ($customerCookies instanceof Varien_Object) {
            $persistentCustomer = $this->_getPersistentCustomer();
            $customerCookies->setCustomerId($persistentCustomer->getId());
            $customerCookies->setCustomerGroupId($persistentCustomer->getGroupId());
        }
    }

    /**
     * Set persistent data to customer session
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Persistent_Model_Observer
     */
    public function emulateCustomer($observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !$this->_isShoppingCartPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')->load(
                $this->_getPersistentHelper()->getSession()->getCustomerId()
            );
            Mage::getSingleton('customer/session')
                ->setCustomerId($customer->getId())
                ->setCustomerGroupId($customer->getGroupId());
        }
        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Shopping Cart Data Helper
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Helper_Data extends Mage_Core_Helper_Data
{
    const XML_PATH_ENABLED = 'persistent/options/enabled';
    const XML_PATH_LIFE_TIME = 'persistent/options/lifetime';
    const XML_PATH_LOGOUT_CLEAR = 'persistent/options/logout_clear';
    const XML_PATH_REMEMBER_ME_ENABLED = 'persistent/options/remember_enabled';
    const XML_PATH_REMEMBER_ME_DEFAULT = 'persistent/options/remember_default';
    const XML_PATH_PERSIST_SHOPPING_CART = 'persistent/options/shopping_cart';

    const LOGGED_IN_LAYOUT_HANDLE = 'customer_logged_in_psc_handle';
    const LOGGED_OUT_LAYOUT_HANDLE = 'customer_logged_out_psc_handle';

    /**
     * Name of config file
     *
     * @var string
     */
    protected $_configFileName = 'persistent.xml';

    /**
     * Checks whether Persistence Functionality is enabled
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Checks whether "Remember Me" enabled
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isRememberMeEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_ENABLED, $store);
    }

    /**
     * Is "Remember Me" checked by default
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isRememberMeCheckedDefault($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_DEFAULT, $store);
    }

    /**
     * Is shopping cart persist
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isShoppingCartPersist($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PERSIST_SHOPPING_CART, $store);
    }

    /**
     * Get Persistence Lifetime
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return int
     */
    public function getLifeTime($store = null)
    {
        $lifeTime = intval(Mage::getStoreConfig(self::XML_PATH_LIFE_TIME, $store));
        return ($lifeTime < 0) ? 0 : $lifeTime;
    }

    /**
     * Check if set `Clear on Logout` in config settings
     *
     * @return bool
     */
    public function getClearOnLogout()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_LOGOUT_CLEAR);
    }

    /**
     * Retrieve url for unset long-term cookie
     *
     * @return string
     */
    public function getUnsetCookieUrl()
    {
        return $this->_getUrl('persistent/index/unsetCookie');
    }

    /**
     * Retrieve name of persistent customer
     *
     * @return string
     */
    public function getPersistentName()
    {
        return $this->__('(Not %s?)', $this->escapeHtml(Mage::helper('persistent/session')->getCustomer()->getName()));
    }

    /**
     * Retrieve path for config file
     *
     * @return string
     */
    public function getPersistentConfigFilePath()
    {
        return Mage::getConfig()->getModuleDir('etc', $this->_getModuleName()) . DS . $this->_configFileName;
    }

    /**
     * Check whether specified action should be processed
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function canProcess($observer)
    {
        $action = $observer->getEvent()->getAction();
        $controllerAction = $observer->getEvent()->getControllerAction();

        if ($action instanceof Mage_Core_Controller_Varien_Action) {
            return !$action->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION);
        }
        if ($controllerAction instanceof Mage_Core_Controller_Varien_Action) {
            return !$controllerAction->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION);
        }
        return true;
    }

    /**
     * Get create account url depends on checkout
     *
     * @param  $url string
     * @return string
     */
    public function getCreateAccountUrl($url)
    {
        if (Mage::helper('checkout')->isContextCheckout()) {
            $url = Mage::helper('core/url')->addRequestParam($url, array('context' => 'checkout'));
        }
        return $url;
    }

}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Shopping Cart Data Helper
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Helper_Session extends Mage_Core_Helper_Data
{
    /**
     * Instance of Session Model
     *
     * @var null|Mage_Persistent_Model_Session
     */
    protected $_sessionModel;

    /**
     * Persistent customer
     *
     * @var null|Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Is "Remember Me" checked
     *
     * @var null|bool
     */
    protected $_isRememberMeChecked;

    /**
     * Get Session model
     *
     * @return Mage_Persistent_Model_Session
     */
    public function getSession()
    {
        if (is_null($this->_sessionModel)) {
            $this->_sessionModel = Mage::getModel('persistent/session');
            $this->_sessionModel->loadByCookieKey();
        }
        return $this->_sessionModel;
    }

    /**
     * Force setting session model
     *
     * @param Mage_Persistent_Model_Session $sessionModel
     * @return Mage_Persistent_Model_Session
     */
    public function setSession($sessionModel)
    {
        $this->_sessionModel = $sessionModel;
        return $this->_sessionModel;
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    public function isPersistent()
    {
        return $this->getSession()->getId() && Mage::helper('persistent')->isEnabled();
    }

    /**
     * Check if "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        if (is_null($this->_isRememberMeChecked)) {
            //Try to get from checkout session
            $isRememberMeChecked = Mage::getSingleton('checkout/session')->getRememberMeChecked();
            if (!is_null($isRememberMeChecked)) {
                $this->_isRememberMeChecked = $isRememberMeChecked;
                Mage::getSingleton('checkout/session')->unsRememberMeChecked();
                return $isRememberMeChecked;
            }

            /** @var $helper Mage_Persistent_Helper_Data */
            $helper = Mage::helper('persistent');
            return $helper->isEnabled() && $helper->isRememberMeEnabled() && $helper->isRememberMeCheckedDefault();
        }

        return (bool)$this->_isRememberMeChecked;
    }

    /**
     * Set "Remember Me" checked or not
     *
     * @param bool $checked
     */
    public function setRememberMeChecked($checked = true)
    {
        $this->_isRememberMeChecked = $checked;
    }

    /**
     * Return persistent customer
     *
     * @return Mage_Customer_Model_Customer|bool
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $customerId = $this->getSession()->getCustomerId();
            $this->_customer = Mage::getModel('customer/customer')->load($customerId);
        }
        return $this->_customer;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Session Model
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Session extends Mage_Core_Model_Abstract
{
    const KEY_LENGTH = 50;
    const COOKIE_NAME = 'persistent_shopping_cart';

    /**
     * Fields which model does not save into `info` db field
     *
     * @var array
     */
    protected $_unserializableFields = array('persistent_id', 'key', 'customer_id', 'website_id', 'info', 'updated_at');

    /**
     * If model loads expired sessions
     *
     * @var bool
     */
    protected $_loadExpired = false;

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('persistent/session');
    }

    /**
     * Set if load expired persistent session
     *
     * @param bool $loadExpired
     * @return Mage_Persistent_Model_Session
     */
    public function setLoadExpired($loadExpired = true)
    {
        $this->_loadExpired = $loadExpired;
        return $this;
    }

    /**
     * Get if model loads expired sessions
     *
     * @return bool
     */
    public function getLoadExpired()
    {
        return $this->_loadExpired;
    }

    /**
     * Get date-time before which persistent session is expired
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return string
     */
    public function getExpiredBefore($store = null)
    {
        return gmdate('Y-m-d H:i:s', time() - Mage::helper('persistent')->getLifeTime($store));
    }

    /**
     * Serialize info for Resource Model to save
     * For new model check and set available cookie key
     *
     * @return Mage_Persistent_Model_Session
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Setting info
        $info = array();
        foreach ($this->getData() as $index => $value) {
            if (!in_array($index, $this->_unserializableFields)) {
                $info[$index] = $value;
            }
        }
        $this->setInfo(Mage::helper('core')->jsonEncode($info));

        if ($this->isObjectNew()) {
            $this->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            // Setting cookie key
            do {
                $this->setKey(Mage::helper('core')->getRandomString(self::KEY_LENGTH));
            } while (!$this->getResource()->isKeyAllowed($this->getKey()));
        }

        return $this;
    }

    /**
     * Set model data from info field
     *
     * @return Mage_Persistent_Model_Session
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $info = Mage::helper('core')->jsonDecode($this->getInfo());
        if (is_array($info)) {
            foreach ($info as $key => $value) {
                $this->setData($key, $value);
            }
        }
        return $this;
    }

    /**
     * Get persistent session by cookie key
     *
     * @param string $key
     * @return Mage_Persistent_Model_Session
     */
    public function loadByCookieKey($key = null)
    {
        if (is_null($key)) {
            $key = Mage::getSingleton('core/cookie')->get(Mage_Persistent_Model_Session::COOKIE_NAME);
        }
        if ($key) {
            $this->load($key, 'key');
        }

        return $this;
    }

    /**
     * Load session model by specified customer id
     *
     * @param int $id
     * @return Mage_Core_Model_Abstract
     */
    public function loadByCustomerId($id)
    {
        return $this->load($id, 'customer_id');
    }

    /**
     * Delete customer persistent session by customer id
     *
     * @param int $customerId
     * @param bool $clearCookie
     * @return Mage_Persistent_Model_Session
     */
    public function deleteByCustomerId($customerId, $clearCookie = true)
    {
        if ($clearCookie) {
            $this->removePersistentCookie();
        }
        $this->getResource()->deleteByCustomerId($customerId);
        return $this;
    }

    /**
     * Remove persistent cookie
     *
     * @return Mage_Persistent_Model_Session
     */
    public function removePersistentCookie()
    {
        Mage::getSingleton('core/cookie')->delete(Mage_Persistent_Model_Session::COOKIE_NAME);
        return $this;
    }

    /**
     * Delete expired persistent sessions for the website
     *
     * @param null|int $websiteId
     * @return Mage_Persistent_Model_Session
     */
    public function deleteExpired($websiteId = null)
    {
        if (is_null($websiteId)) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        }

        $lifetime = Mage::getConfig()->getNode(
            Mage_Persistent_Helper_Data::XML_PATH_LIFE_TIME,
            'website',
            intval($websiteId)
        );

        if ($lifetime) {
            $this->getResource()->deleteExpired(
                $websiteId,
                gmdate('Y-m-d H:i:s', time() - $lifetime)
            );
        }

        return $this;
    }

    /**
     * Delete 'persistent' cookie
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterDeleteCommit() {
        Mage::getSingleton('core/cookie')->delete(Mage_Persistent_Model_Session::COOKIE_NAME);
        return parent::_afterDeleteCommit();
    }

    /**
     * Set `updated_at` to be always changed
     *
     * @return Mage_Persistent_Model_Session
     */
    public function save()
    {
        $this->setUpdatedAt(gmdate('Y-m-d H:i:s'));
        return parent::save();
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Session Resource Model
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Resource_Session extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Use is object new method for object saving
     *
     * @var boolean
     */
    protected $_useIsObjectNew = true;

    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('persistent/session', 'persistent_id');
    }

    /**
     * Add expiration date filter to select
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Persistent_Model_Session $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if (!$object->getLoadExpired()) {
            $tableName = $this->getMainTable();
            $select->join(array('customer' => $this->getTable('customer/entity')),
                'customer.entity_id = ' . $tableName . '.customer_id'
            )->where($tableName . '.updated_at >= ?', $object->getExpiredBefore());
        }

        return $select;
    }

    /**
     * Delete customer persistent session by customer id
     *
     * @param int $customerId
     * @return Mage_Persistent_Model_Resource_Session
     */
    public function deleteByCustomerId($customerId)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), array('customer_id = ?' => $customerId));
        return $this;
    }

    /**
     * Check if such session key allowed (not exists)
     *
     * @param string $key
     * @return bool
     */
    public function isKeyAllowed($key)
    {
        $sameSession = Mage::getModel('persistent/session')->setLoadExpired();
        $sameSession->loadByCookieKey($key);
        return !$sameSession->getId();
    }

    /**
     * Delete expired persistent sessions
     *
     * @param  $websiteId
     * @param  $expiredBefore
     * @return Mage_Persistent_Model_Resource_Session
     */
    public function deleteExpired($websiteId, $expiredBefore)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array(
                'website_id = ?' => $websiteId,
                'updated_at < ?' => $expiredBefore,
            )
        );
        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer extends Mage_Core_Model_Abstract
{
    /**#@+
     * Configuration pathes for email templates and identities
     */
    const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'customer/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';
    const XML_PATH_REMIND_EMAIL_TEMPLATE = 'customer/password/remind_email_template';
    const XML_PATH_FORGOT_EMAIL_TEMPLATE = 'customer/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY = 'customer/password/forgot_email_identity';
    const XML_PATH_DEFAULT_EMAIL_DOMAIN         = 'customer/create_account/email_domain';
    const XML_PATH_IS_CONFIRM                   = 'customer/create_account/confirm';
    const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'customer/create_account/email_confirmation_template';
    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE     = 'customer/create_account/email_confirmed_template';
    const XML_PATH_GENERATE_HUMAN_FRIENDLY_ID   = 'customer/create_account/generate_human_friendly_id';
    /**#@-*/

    /**#@+
     * Codes of exceptions related to customer model
     */
    const EXCEPTION_EMAIL_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;
    const EXCEPTION_EMAIL_EXISTS              = 3;
    const EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN = 4;
    /**#@-*/

    /**#@+
     * Subscriptions
     */
    const SUBSCRIBED_YES = 'yes';
    const SUBSCRIBED_NO  = 'no';
    /**#@-*/

    const CACHE_TAG = 'customer';

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'customer';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'customer';

    /**
     * List of errors
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Assoc array of customer attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Customer addresses array
     *
     * @var array
     * @deprecated after 1.4.0.0-rc1
     */
    protected $_addresses = null;

    /**
     * Customer addresses collection
     *
     * @var Mage_Customer_Model_Entity_Address_Collection
     */
    protected $_addressesCollection;

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Confirmation requirement flag
     *
     * @var boolean
     */
    private static $_isConfirmationRequired;

    /**
     * Initialize customer model
     */
    function _construct()
    {
        $this->_init('customer/customer');
    }

    /**
     * Retrieve customer sharing configuration model
     *
     * @return Mage_Customer_Model_Config_Share
     */
    public function getSharingConfig()
    {
        return Mage::getSingleton('customer/config_share');
    }

    /**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }
        Mage::dispatchEvent('customer_customer_authenticated', array(
           'model'    => $this,
           'password' => $password,
        ));

        return true;
    }

    /**
     * Load customer by email
     *
     * @param   string $customerEmail
     * @return  Mage_Customer_Model_Customer
     */
    public function loadByEmail($customerEmail)
    {
        $this->_getResource()->loadByEmail($this, $customerEmail);
        return $this;
    }


    /**
     * Processing object before save data
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $storeId = $this->getStoreId();
        if ($storeId === null) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $this->getGroupId();
        return $this;
    }

    /**
     * Change customer password
     *
     * @param   string $newPassword
     * @return  Mage_Customer_Model_Customer
     */
    public function changePassword($newPassword)
    {
        $this->_getResource()->changePassword($this, $newPassword);
        return $this;
    }

    /**
     * Get full customer name
     *
     * @return string
     */
    public function getName()
    {
        $name = '';
        $config = Mage::getSingleton('eav/config');
        if ($config->getAttribute('customer', 'prefix')->getIsVisible() && $this->getPrefix()) {
            $name .= $this->getPrefix() . ' ';
        }
        $name .= $this->getFirstname();
        if ($config->getAttribute('customer', 'middlename')->getIsVisible() && $this->getMiddlename()) {
            $name .= ' ' . $this->getMiddlename();
        }
        $name .=  ' ' . $this->getLastname();
        if ($config->getAttribute('customer', 'suffix')->getIsVisible() && $this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }
        return $name;
    }

    /**
     * Add address to address collection
     *
     * @param   Mage_Customer_Model_Address $address
     * @return  Mage_Customer_Model_Customer
     */
    public function addAddress(Mage_Customer_Model_Address $address)
    {
        $this->getAddressesCollection()->addItem($address);
        $this->getAddresses();
        $this->_addresses[] = $address;
        return $this;
    }

    /**
     * Retrieve customer address by address id
     *
     * @param   int $addressId
     * @return  Mage_Customer_Model_Address
     */
    public function getAddressById($addressId)
    {
        $address = Mage::getModel('customer/address')->load($addressId);
        if ($this->getId() == $address->getParentId()) {
            return $address;
        }
        return Mage::getModel('customer/address');
    }

    /**
     * Getting customer address object from collection by identifier
     *
     * @param int $addressId
     * @return Mage_Customer_Model_Address
     */
    public function getAddressItemById($addressId)
    {
        return $this->getAddressesCollection()->getItemById($addressId);
    }

    /**
     * Retrieve not loaded address collection
     *
     * @return Mage_Customer_Model_Entity_Address_Collection
     */
    public function getAddressCollection()
    {
        return Mage::getResourceModel('customer/address_collection');
    }

    /**
     * Customer addresses collection
     *
     * @return Mage_Customer_Model_Entity_Address_Collection
     */
    public function getAddressesCollection()
    {
        if ($this->_addressesCollection === null) {
            $this->_addressesCollection = $this->getAddressCollection()
                ->setCustomerFilter($this)
                ->addAttributeToSelect('*');
            foreach ($this->_addressesCollection as $address) {
                $address->setCustomer($this);
            }
        }

        return $this->_addressesCollection;
    }

    /**
     * Retrieve customer address array
     *
     * @return array
     */
    public function getAddresses()
    {
        $this->_addresses = $this->getAddressesCollection()->getItems();
        return $this->_addresses;
    }

    /**
     * Retrieve all customer attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = $this->_getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();
        }
        return $this->_attributes;
    }

    /**
     * Get customer attribute model object
     *
     * @param   string $attributeCode
     * @return  Mage_Customer_Model_Entity_Attribute | null
     */
    public function getAttribute($attributeCode)
    {
        $this->getAttributes();
        if (isset($this->_attributes[$attributeCode])) {
            return $this->_attributes[$attributeCode];
        }
        return null;
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Mage_Customer_Model_Customer
     */
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        $this->setPasswordConfirmation(null);
        return $this;
    }

    /**
     * Hash customer password
     *
     * @param   string $password
     * @param   int    $salt
     * @return  string
     */
    public function hashPassword($password, $salt = null)
    {
        return $this->_getHelper('core')
            ->getHash($password, !is_null($salt) ? $salt : Mage_Admin_Model_User::HASH_SALT_LENGTH);
    }

    /**
     * Get helper instance
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        $hash = $this->getPasswordHash();
        if (!$hash) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }


    /**
     * Encrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    /**
     * Decrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
    }

    /**
     * Retrieve default address by type(attribute)
     *
     * @param   string $attributeCode address type attribute code
     * @return  Mage_Customer_Model_Address
     */
    public function getPrimaryAddress($attributeCode)
    {
        $primaryAddress = $this->getAddressesCollection()->getItemById($this->getData($attributeCode));

        return $primaryAddress ? $primaryAddress : false;
    }

    /**
     * Get customer default billing address
     *
     * @return Mage_Customer_Model_Address
     */
    public function getPrimaryBillingAddress()
    {
        return $this->getPrimaryAddress('default_billing');
    }

    /**
     * Get customer default billing address
     *
     * @return Mage_Customer_Model_Address
     */
    public function getDefaultBillingAddress()
    {
        return $this->getPrimaryBillingAddress();
    }

    /**
     * Get default customer shipping address
     *
     * @return Mage_Customer_Model_Address
     */
    public function getPrimaryShippingAddress()
    {
        return $this->getPrimaryAddress('default_shipping');
    }

    /**
     * Get default customer shipping address
     *
     * @return Mage_Customer_Model_Address
     */
    public function getDefaultShippingAddress()
    {
        return $this->getPrimaryShippingAddress();
    }

    /**
     * Retrieve ids of default addresses
     *
     * @return array
     */
    public function getPrimaryAddressIds()
    {
        $ids = array();
        if ($this->getDefaultBilling()) {
            $ids[] = $this->getDefaultBilling();
        }
        if ($this->getDefaultShipping()) {
            $ids[] = $this->getDefaultShipping();
        }
        return $ids;
    }

    /**
     * Retrieve all customer default addresses
     *
     * @return array
     */
    public function getPrimaryAddresses()
    {
        $addresses = array();
        $primaryBilling = $this->getPrimaryBillingAddress();
        if ($primaryBilling) {
            $addresses[] = $primaryBilling;
            $primaryBilling->setIsPrimaryBilling(true);
        }

        $primaryShipping = $this->getPrimaryShippingAddress();
        if ($primaryShipping) {
            if ($primaryBilling->getId() == $primaryShipping->getId()) {
                $primaryBilling->setIsPrimaryShipping(true);
            } else {
                $primaryShipping->setIsPrimaryShipping(true);
                $addresses[] = $primaryShipping;
            }
        }
        return $addresses;
    }

    /**
     * Retrieve not default addresses
     *
     * @return array
     */
    public function getAdditionalAddresses()
    {
        $addresses = array();
        $primatyIds = $this->getPrimaryAddressIds();
        foreach ($this->getAddressesCollection() as $address) {
            if (!in_array($address->getId(), $primatyIds)) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    /**
     * Check if address is primary
     *
     * @param Mage_Customer_Model_Address $address
     * @return boolean
     */
    public function isAddressPrimary(Mage_Customer_Model_Address $address)
    {
        if (!$address->getId()) {
            return false;
        }
        return ($address->getId() == $this->getDefaultBilling()) || ($address->getId() == $this->getDefaultShipping());
    }

    /**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('customer' => $this, 'back_url' => $backUrl), $storeId);

        return $this;
    }

    /**
     * Check if accounts confirmation is required in config
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        if ($this->canSkipConfirmation()) {
            return false;
        }
        if (self::$_isConfirmationRequired === null) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : null;
            self::$_isConfirmationRequired = (bool)Mage::getStoreConfig(self::XML_PATH_IS_CONFIRM, $storeId);
        }

        return self::$_isConfirmationRequired;
    }

    /**
     * Generate random confirmation key
     *
     * @return string
     */
    public function getRandomConfirmationKey()
    {
        return md5(uniqid());
    }

    /**
     * Send email with new customer password
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordReminderEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(self::XML_PATH_REMIND_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('customer' => $this), $storeId);

        return $this;
    }

    /**
     * Send corresponding email template
     *
     * @param string $emailTemplate configuration path of email template
     * @param string $emailSender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @return Mage_Customer_Model_Customer
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null)
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getEmail(), $this->getName());
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        $mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }

    /**
     * Send email with reset password confirmation link
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordResetConfirmationEmail()
    {
        $storeId = Mage::app()->getStore()->getId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(self::XML_PATH_FORGOT_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('customer' => $this), $storeId);

        return $this;
    }

    /**
     * Retrieve customer group identifier
     *
     * @return int
     */
    public function getGroupId()
    {
        if (!$this->hasData('group_id')) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
            $groupId = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, $storeId);
            $this->setData('group_id', $groupId);
        }
        return $this->getData('group_id');
    }

    /**
     * Retrieve customer tax class identifier
     *
     * @return int
     */
    public function getTaxClassId()
    {
        if (!$this->getData('tax_class_id')) {
            $this->setTaxClassId(Mage::getModel('customer/group')->getTaxClassId($this->getGroupId()));
        }
        return $this->getData('tax_class_id');
    }

    /**
     * Check store availability for customer
     *
     * @param   Mage_Core_Model_Store | int $store
     * @return  bool
     */
    public function isInStore($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $storeId = $store->getId();
        } else {
            $storeId = $store;
        }

        $availableStores = $this->getSharedStoreIds();
        return in_array($storeId, $availableStores);
    }

    /**
     * Retrieve store where customer was created
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    /**
     * Retrieve shared store ids
     *
     * @return array
     */
    public function getSharedStoreIds()
    {
        $ids = $this->_getData('shared_store_ids');
        if ($ids === null) {
            $ids = array();
            if ((bool)$this->getSharingConfig()->isWebsiteScope()) {
                $ids = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            } else {
                foreach (Mage::app()->getStores() as $store) {
                    $ids[] = $store->getId();
                }
            }
            $this->setData('shared_store_ids', $ids);
        }

        return $ids;
    }

    /**
     * Retrive shared website ids
     *
     * @return array
     */
    public function getSharedWebsiteIds()
    {
        $ids = $this->_getData('shared_website_ids');
        if ($ids === null) {
            $ids = array();
            if ((bool)$this->getSharingConfig()->isWebsiteScope()) {
                $ids[] = $this->getWebsiteId();
            } else {
                foreach (Mage::app()->getWebsites() as $website) {
                    $ids[] = $website->getId();
                }
            }
            $this->setData('shared_website_ids', $ids);
        }
        return $ids;
    }

    /**
     * Set store to customer
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Customer_Model_Customer
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        $this->setWebsiteId($store->getWebsite()->getId());
        return $this;
    }

    /**
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
        if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is( trim($this->getLastname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('customer');
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'dob');
        if ($attribute->getIsRequired() && '' == trim($this->getDob())) {
            $errors[] = Mage::helper('customer')->__('The Date of Birth is required.');
        }
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'taxvat');
        if ($attribute->getIsRequired() && '' == trim($this->getTaxvat())) {
            $errors[] = Mage::helper('customer')->__('The TAX/VAT number is required.');
        }
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'gender');
        if ($attribute->getIsRequired() && '' == trim($this->getGender())) {
            $errors[] = Mage::helper('customer')->__('Gender is required.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Import customer data from text array
     *
     * @param array $row
     * @return Mage_Customer_Model_Customer
     */
    public function importFromTextArray(array $row)
    {
        $this->resetErrors();
        $line = $row['i'];
        $row = $row['row'];

        $regions = Mage::getResourceModel('directory/region_collection');

        $website = Mage::getModel('core/website')->load($row['website_code'], 'code');

        if (!$website->getId()) {
            $this->addError(Mage::helper('customer')->__('Invalid website, skipping the record, line: %s', $line));

        } else {
            $row['website_id'] = $website->getWebsiteId();
            $this->setWebsiteId($row['website_id']);
        }

        // Validate Email
        if (empty($row['email'])) {
            $this->addError(Mage::helper('customer')->__('Missing email, skipping the record, line: %s', $line));
        } else {
            $this->loadByEmail($row['email']);
        }

        if (empty($row['entity_id'])) {
            if ($this->getData('entity_id')) {
                $this->addError(Mage::helper('customer')->__('The customer email (%s) already exists, skipping the record, line: %s', $row['email'], $line));
            }
        } else {
            if ($row['entity_id'] != $this->getData('entity_id')) {
                $this->addError(Mage::helper('customer')->__('The customer ID and email did not match, skipping the record, line: %s', $line));
            } else {
                $this->unsetData();
                $this->load($row['entity_id']);
                if (isset($row['store_view'])) {
                    $storeId = Mage::app()->getStore($row['store_view'])->getId();
                    if ($storeId) $this->setStoreId($storeId);
                }
            }
        }

        if (empty($row['website_code'])) {
            $this->addError(Mage::helper('customer')->__('Missing website, skipping the record, line: %s', $line));
        }

        if (empty($row['group'])) {
            $row['group'] = 'General';
        }

        if (empty($row['firstname'])) {
            $this->addError(Mage::helper('customer')->__('Missing first name, skipping the record, line: %s', $line));
        }
        if (empty($row['lastname'])) {
            $this->addError(Mage::helper('customer')->__('Missing last name, skipping the record, line: %s', $line));
        }

        if (!empty($row['password_new'])) {
            $this->setPassword($row['password_new']);
            unset($row['password_new']);
            if (!empty($row['password_hash'])) unset($row['password_hash']);
        }

        $errors = $this->getErrors();
        if ($errors) {
            $this->unsetData();
            $this->printError(implode('<br />', $errors));
            return;
        }

        foreach ($row as $field => $value) {
            $this->setData($field, $value);
        }

        if (!$this->validateAddress($row, 'billing')) {
            $this->printError(Mage::helper('customer')->__('Invalid billing address for (%s)', $row['email']), $line);
        } else {
            // Handling billing address
            $billingAddress = $this->getPrimaryBillingAddress();
            if (!$billingAddress  instanceof Mage_Customer_Model_Address) {
                $billingAddress = Mage::getModel('customer/address');
            }

            $regions->addRegionNameFilter($row['billing_region'])->load();
            if ($regions) foreach($regions as $region) {
                $regionId = intval($region->getId());
            }

            $billingAddress->setFirstname($row['firstname']);
            $billingAddress->setLastname($row['lastname']);
            $billingAddress->setCity($row['billing_city']);
            $billingAddress->setRegion($row['billing_region']);
            if (isset($regionId)) {
                $billingAddress->setRegionId($regionId);
            }
            $billingAddress->setCountryId($row['billing_country']);
            $billingAddress->setPostcode($row['billing_postcode']);
            if (isset($row['billing_street2'])) {
                $billingAddress->setStreet(array($row['billing_street1'], $row['billing_street2']));
            } else {
                $billingAddress->setStreet(array($row['billing_street1']));
            }
            if (isset($row['billing_telephone'])) {
                $billingAddress->setTelephone($row['billing_telephone']);
            }

            if (!$billingAddress->getId()) {
                $billingAddress->setIsDefaultBilling(true);
                if ($this->getDefaultBilling()) {
                    $this->setData('default_billing', '');
                }
                $this->addAddress($billingAddress);
            } // End handling billing address
        }

        if (!$this->validateAddress($row, 'shipping')) {
            $this->printError(Mage::helper('customer')->__('Invalid shipping address for (%s)', $row['email']), $line);
        } else {
            // Handling shipping address
            $shippingAddress = $this->getPrimaryShippingAddress();
            if (!$shippingAddress instanceof Mage_Customer_Model_Address) {
                $shippingAddress = Mage::getModel('customer/address');
            }

            $regions->addRegionNameFilter($row['shipping_region'])->load();

            if ($regions) foreach($regions as $region) {
               $regionId = intval($region->getId());
            }

            $shippingAddress->setFirstname($row['firstname']);
            $shippingAddress->setLastname($row['lastname']);
            $shippingAddress->setCity($row['shipping_city']);
            $shippingAddress->setRegion($row['shipping_region']);
            if (isset($regionId)) {
                $shippingAddress->setRegionId($regionId);
            }
            $shippingAddress->setCountryId($row['shipping_country']);
            $shippingAddress->setPostcode($row['shipping_postcode']);
            if (isset($row['shipping_street2'])) {
                $shippingAddress->setStreet(array($row['shipping_street1'], $row['shipping_street2']));
            } else {
                $shippingAddress->setStreet(array($row['shipping_street1']));
            }
            if (!empty($row['shipping_telephone'])) {
                $shippingAddress->setTelephone($row['shipping_telephone']);
            }

            if (!$shippingAddress->getId()) {
               $shippingAddress->setIsDefaultShipping(true);
               $this->addAddress($shippingAddress);
            }
            // End handling shipping address
        }
        if (!empty($row['is_subscribed'])) {
            $isSubscribed = (bool)strtolower($row['is_subscribed']) == self::SUBSCRIBED_YES;
            $this->setIsSubscribed($isSubscribed);
        }
        unset($row);
        return $this;
    }

    /**
     * Unset subscription
     *
     * @return Mage_Customer_Model_Customer
     */
    function unsetSubscription()
    {
        if (isset($this->_isSubscribed)) {
            unset($this->_isSubscribed);
        }
        return $this;
    }

    /**
     * Clean all addresses
     *
     * @return Mage_Customer_Model_Customer
     */
    function cleanAllAddresses() {
        $this->_addressesCollection = null;
        $this->_addresses           = null;
    }

    /**
     * Add error
     *
     * @return Mage_Customer_Model_Customer
     */
    function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Retreive errors
     *
     * @return array
     */
    function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Reset errors array
     *
     * @return Mage_Customer_Model_Customer
     */
    function resetErrors()
    {
        $this->_errors = array();
        return $this;
    }

    /**
     * Print error
     *
     * @param $error
     * @param $line
     * @return boolean
     */
    function printError($error, $line = null)
    {
        if ($error == null) {
            return false;
        }

        $liStyle = 'background-color: #FDD; ';
        echo '<li style="' . $liStyle . '">';
        echo '<img src="' . Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif') . '" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>' . $line . '</b></small>';
        }
        echo '</li>';
    }

    /**
     * Validate address
     *
     * @param array $data
     * @param string $type
     * @return bool
     */
    function validateAddress(array $data, $type = 'billing')
    {
        $fields = array('city', 'country', 'postcode', 'telephone', 'street1');
        $usca   = array('US', 'CA');
        $prefix = $type ? $type . '_' : '';

        if ($data) {
            foreach ($fields as $field) {
                if (!isset($data[$prefix . $field])) {
                    return false;
                }
                if ($field == 'country'
                    && in_array(strtolower($data[$prefix . $field]), array('US', 'CA'))) {

                    if (!isset($data[$prefix . 'region'])) {
                        return false;
                    }

                    $region = Mage::getModel('directory/region')
                        ->loadByName($data[$prefix . 'region']);
                    if (!$region->getId()) {
                        return false;
                    }
                    unset($region);
                }
            }
            unset($data);
            return true;
        }
        return false;
    }

    /**
     * Prepare customer for delete
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Get customer created at date timestamp
     *
     * @return int|null
     */
    public function getCreatedAtTimestamp()
    {
        $date = $this->getCreatedAt();
        if ($date) {
            return Varien_Date::toTimestamp($date);
        }
        return null;
    }

    /**
     * Reset all model data
     *
     * @return Mage_Customer_Model_Customer
     */
    public function reset()
    {
        $this->setData(array());
        $this->setOrigData();
        $this->_attributes = null;

        return $this;
    }

    /**
     * Checks model is deleteable
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param boolean $value
     * @return Mage_Customer_Model_Customer
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (bool)$value;
        return $this;
    }

    /**
     * Checks model is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param boolean $value
     * @return Mage_Customer_Model_Customer
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (bool)$value;
        return $this;
    }

    /**
     * Check whether confirmation may be skipped when registering using certain email address
     *
     * @return bool
     */
    public function canSkipConfirmation()
    {
        return $this->getId() && $this->hasSkipConfirmationIfEmail()
            && strtolower($this->getSkipConfirmationIfEmail()) === strtolower($this->getEmail());
    }

    /**
     * Clone current object
     */
    public function __clone()
    {
        $newAddressCollection = $this->getPrimaryAddresses();
        $newAddressCollection = array_merge($newAddressCollection, $this->getAdditionalAddresses());
        $this->setId(null);
        $this->cleanAllAddresses();
        foreach ($newAddressCollection as $address) {
            $this->addAddress(clone $address);
        }
    }

    /**
     * Return Entity Type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        return $this->_getResource()->getEntityType();
    }

    /**
     * Return Entity Type ID
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }
        return $entityTypeId;
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param int|string|null $storeId
     *
     * @return int
     */
    protected function _getWebsiteStoreId($defaultStoreId = null)
    {
        if ($this->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token
     *
     * @param string $newResetPasswordLinkToken
     * @return Mage_Customer_Model_Customer
     */
    public function changeResetPasswordLinkToken($newResetPasswordLinkToken) {
        if (!is_string($newResetPasswordLinkToken) || empty($newResetPasswordLinkToken)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid password reset token.'),
                self::EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN);
        }
        $this->_getResource()->changeResetPasswordLinkToken($this, $newResetPasswordLinkToken);
        return $this;
    }

    /**
     * Check if current reset password link token is expired
     *
     * @return boolean
     */
    public function isResetPasswordLinkTokenExpired()
    {
        $resetPasswordLinkToken = $this->getRpToken();
        $resetPasswordLinkTokenCreatedAt = $this->getRpTokenCreatedAt();

        if (empty($resetPasswordLinkToken) || empty($resetPasswordLinkTokenCreatedAt)) {
            return true;
        }

        $tokenExpirationPeriod = Mage::helper('customer')->getResetPasswordLinkExpirationPeriod();

        $currentDate = Varien_Date::now();
        $currentTimestamp = Varien_Date::toTimestamp($currentDate);
        $tokenTimestamp = Varien_Date::toTimestamp($resetPasswordLinkTokenCreatedAt);
        if ($tokenTimestamp > $currentTimestamp) {
            return true;
        }

        $dayDifference = floor(($currentTimestamp - $tokenTimestamp) / (24 * 60 * 60));
        if ($dayDifference >= $tokenExpirationPeriod) {
            return true;
        }

        return false;
    }

    /**
     * Clean password's validation data (password, password_confirmation)
     *
     * @return Mage_Customer_Model_Customer
     */
    public function cleanPasswordsValidationData()
    {
        $this->setData('password', null);
        $this->setData('password_confirmation', null);
        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer entity resource model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Customer extends Mage_Eav_Model_Entity_Abstract
{
    /**
     * Resource initialization
     */
    public function __construct()
    {
        $this->setType('customer');
        $this->setConnection('customer_read', 'customer_write');
    }

    /**
     * Retrieve customer entity default attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array(
            'entity_type_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'increment_id',
            'store_id',
            'website_id'
        );
    }

    /**
     * Check customer scope, email and confirmation key before saving
     *
     * @param Mage_Customer_Model_Customer $customer
     * @throws Mage_Customer_Exception
     * @return Mage_Customer_Model_Resource_Customer
     */
    protected function _beforeSave(Varien_Object $customer)
    {
        parent::_beforeSave($customer);

        if (!$customer->getEmail()) {
            throw Mage::exception('Mage_Customer', Mage::helper('customer')->__('Customer email is required'));
        }

        $adapter = $this->_getWriteAdapter();
        $bind    = array('email' => $customer->getEmail());

        $select = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('email = :email');
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $bind['website_id'] = (int)$customer->getWebsiteId();
            $select->where('website_id = :website_id');
        }
        if ($customer->getId()) {
            $bind['entity_id'] = (int)$customer->getId();
            $select->where('entity_id != :entity_id');
        }

        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            throw Mage::exception(
                'Mage_Customer', Mage::helper('customer')->__('This customer email already exists'),
                Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS
            );
        }

        // set confirmation key logic
        if ($customer->getForceConfirmed()) {
            $customer->setConfirmation(null);
        } elseif (!$customer->getId() && $customer->isConfirmationRequired()) {
            $customer->setConfirmation($customer->getRandomConfirmationKey());
        }
        // remove customer confirmation key from database, if empty
        if (!$customer->getConfirmation()) {
            $customer->setConfirmation(null);
        }

        return $this;
    }

    /**
     * Save customer addresses and set default addresses in attributes backend
     *
     * @param Varien_Object $customer
     * @return Mage_Eav_Model_Entity_Abstract
     */
    protected function _afterSave(Varien_Object $customer)
    {
        $this->_saveAddresses($customer);
        return parent::_afterSave($customer);
    }

    /**
     * Save/delete customer address
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Resource_Customer
     */
    protected function _saveAddresses(Mage_Customer_Model_Customer $customer)
    {
        $defaultBillingId   = $customer->getData('default_billing');
        $defaultShippingId  = $customer->getData('default_shipping');
        foreach ($customer->getAddresses() as $address) {
            if ($address->getData('_deleted')) {
                if ($address->getId() == $defaultBillingId) {
                    $customer->setData('default_billing', null);
                }
                if ($address->getId() == $defaultShippingId) {
                    $customer->setData('default_shipping', null);
                }
                $address->delete();
            } else {
                $address->setParentId($customer->getId())
                    ->setStoreId($customer->getStoreId())
                    ->setIsCustomerSaveTransaction(true)
                    ->save();
                if (($address->getIsPrimaryBilling() || $address->getIsDefaultBilling())
                    && $address->getId() != $defaultBillingId
                ) {
                    $customer->setData('default_billing', $address->getId());
                }
                if (($address->getIsPrimaryShipping() || $address->getIsDefaultShipping())
                    && $address->getId() != $defaultShippingId
                ) {
                    $customer->setData('default_shipping', $address->getId());
                }
            }
        }
        if ($customer->dataHasChangedFor('default_billing')) {
            $this->saveAttribute($customer, 'default_billing');
        }
        if ($customer->dataHasChangedFor('default_shipping')) {
            $this->saveAttribute($customer, 'default_shipping');
        }

        return $this;
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param Varien_Object $object
     * @param mixed $rowId
     * @return Varien_Db_Select
     */
    protected function _getLoadRowSelect($object, $rowId)
    {
        $select = parent::_getLoadRowSelect($object, $rowId);
        if ($object->getWebsiteId() && $object->getSharingConfig()->isWebsiteScope()) {
            $select->where('website_id =?', (int)$object->getWebsiteId());
        }

        return $select;
    }

    /**
     * Load customer by email
     *
     * @throws Mage_Core_Exception
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param string $email
     * @param bool $testOnly
     * @return Mage_Customer_Model_Resource_Customer
     */
    public function loadByEmail(Mage_Customer_Model_Customer $customer, $email, $testOnly = false)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('customer_email' => $email);
        $select  = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('email = :customer_email');

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(
                    Mage::helper('customer')->__('Customer website ID must be specified when using the website scope')
                );
            }
            $bind['website_id'] = (int)$customer->getWebsiteId();
            $select->where('website_id = :website_id');
        }

        $customerId = $adapter->fetchOne($select, $bind);
        if ($customerId) {
            $this->load($customer, $customerId);
        } else {
            $customer->setData(array());
        }

        return $this;
    }

    /**
     * Change customer password
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param string $newPassword
     * @return Mage_Customer_Model_Resource_Customer
     */
    public function changePassword(Mage_Customer_Model_Customer $customer, $newPassword)
    {
        $customer->setPassword($newPassword);
        $this->saveAttribute($customer, 'password_hash');
        return $this;
    }

    /**
     * Check whether there are email duplicates of customers in global scope
     *
     * @return bool
     */
    public function findEmailDuplicates()
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('customer/entity'), array('email', 'cnt' => 'COUNT(*)'))
            ->group('email')
            ->order('cnt DESC')
            ->limit(1);
        $lookup = $adapter->fetchRow($select);
        if (empty($lookup)) {
            return false;
        }
        return $lookup['cnt'] > 1;
    }

    /**
     * Check customer by id
     *
     * @param int $customerId
     * @return bool
     */
    public function checkCustomerId($customerId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('entity_id' => (int)$customerId);
        $select  = $adapter->select()
            ->from($this->getTable('customer/entity'), 'entity_id')
            ->where('entity_id = :entity_id')
            ->limit(1);

        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * Get customer website id
     *
     * @param int $customerId
     * @return int
     */
    public function getWebsiteId($customerId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('entity_id' => (int)$customerId);
        $select  = $adapter->select()
            ->from($this->getTable('customer/entity'), 'website_id')
            ->where('entity_id = :entity_id');

        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Custom setter of increment ID if its needed
     *
     * @param Varien_Object $object
     * @return Mage_Customer_Model_Resource_Customer
     */
    public function setNewIncrementId(Varien_Object $object)
    {
        if (Mage::getStoreConfig(Mage_Customer_Model_Customer::XML_PATH_GENERATE_HUMAN_FRIENDLY_ID)) {
            parent::setNewIncrementId($object);
        }
        return $this;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token and its creation time
     *
     * @param Mage_Customer_Model_Customer $newResetPasswordLinkToken
     * @param string $newResetPasswordLinkToken
     * @return Mage_Customer_Model_Resource_Customer
     */
    public function changeResetPasswordLinkToken(Mage_Customer_Model_Customer $customer, $newResetPasswordLinkToken) {
        if (is_string($newResetPasswordLinkToken) && !empty($newResetPasswordLinkToken)) {
            $customer->setRpToken($newResetPasswordLinkToken);
            $currentDate = Varien_Date::now();
            $customer->setRpTokenCreatedAt($currentDate);
            $this->saveAttribute($customer, 'rp_token');
            $this->saveAttribute($customer, 'rp_token_created_at');
        }
        return $this;
    }
}
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mswebdesign
 * @package    Mswebdesign_Mswebdesign_CustomOrderNumber
 * @copyright  Copyright (c) 2013 münster-webdesign.net (http://www.muenster-webdesign.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Christian Grugel <cgrugel@muenster-webdesign.net>
 */
class Mswebdesign_CustomOrderNumber_Model_Eav_Entity_Type extends Mage_Eav_Model_Entity_Type
{

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var string
     */
    protected $_entityTypeCode;

    /**
     * @var array
     */
    protected $_entityStoreConfig = array();

    /**
     * @var string
     */
    protected $_prefix = '';

    /**
     * @var string
     */
    protected $_datePrefix = '';

    /**
     * @var string
     */
    protected $_incrementId = '';

    /**
     * @var object
     */
    protected $_incrementInstance;

    /**
     * @var array
     */
    protected $_processedEntityTypeCodes = array(
        'order',
        'invoice',
        'shipment',
        'creditmemo'
    );

    /**
     * Retreive new incrementId
     *
     * @param int $storeId
     * @return string
     */
    public function fetchNewIncrementId($storeId = null)
    {
        $this->_storeId = $storeId;

        // @TODO: make this configurable
        $this->setIncrementPerStore(true);

        if (!$this->getIncrementModel()) {
            return false;
        }
        if(!in_array($this->_entityTypeCode = $this->getEntityTypeCode(), $this->_processedEntityTypeCodes)) {
            return parent::fetchNewIncrementId($this->_storeId);
        }
        if (!$this->getIncrementPerStore() || ($this->_storeId === null)) {
            $this->_storeId = 0;
        }

        $this->_computeAndPersistNewIncrementId();

        return $this->_incrementId;
    }

    protected function _computeAndPersistNewIncrementId()
    {
        $this->_getResource()->beginTransaction();
        $this->_loadEntityStoreConfig();

        if (!$this->_entityStoreConfig->getId()) {
            $this->_saveDefaultEntityStoreConfig();
        }

        $this->_loadAndConfigureIncrementInstance();
        $this->_incrementId = $this->_incrementInstance->getNextId();
        $this->_appendDefaultNumberConstraint();

        if(false === $this->_isIncrementIdUinique()) {
            $this->_generateUniqueIncrementId();
        }

        $this->_updateEntityStoreConfig();
        $this->_getResource()->commit();
    }

    protected function _loadEntityStoreConfig()
    {
        $this->_entityStoreConfig = Mage::getModel('eav/entity_store')
            ->loadByEntityStore($this->getId(), $this->_storeId);
    }

    protected function _saveDefaultEntityStoreConfig() {
        $this->_entityStoreConfig
            ->setEntityTypeId($this->getId())
            ->setStoreId($this->_storeId)
            ->setIncrementPrefix($this->_storeId)
            ->save();
    }

    protected function _loadAndConfigureIncrementInstance()
    {
        $this->_incrementInstance = Mage::getModel($this->getIncrementModel())
            ->setPrefix($this->_getIncrementPrefix())
            ->setPadLength($this->_getIncrementPadLength())
            ->setPadChar($this->getIncrementPadChar())
            ->setLastId($this->_getIncrementLastId())
            ->setEntityTypeId($this->_entityStoreConfig->getEntityTypeId())
            ->setStoreId($this->_entityStoreConfig->getStoreId());
    }

    protected function _updateEntityStoreConfig()
    {
        $this->_entityStoreConfig->setIncrementLastId($this->_incrementId);
        $this->_entityStoreConfig->setIncrementPrefix($this->_getIncrementPrefix());
        $this->_entityStoreConfig->save();
    }

    /**
     * @return mixed|string
     */
    protected function _getIncrementPrefix()
    {
        $prefix = Mage::getStoreConfig('mswebdesign_customordernumber/'.$this->_entityTypeCode.'/prefix', $this->_storeId);
        $datePrefix = Mage::getStoreConfig('mswebdesign_customordernumber/'.$this->_entityTypeCode.'/date_prefix', $this->_storeId);

        if('' !== $datePrefix) {
            return $this->_datePrefix = $this->_convertDatePrefixToDate($datePrefix);
        }

        if('' !== $prefix) {
            return $this->_prefix = $prefix;
        }

        return null;
    }

    /**
     * @param $datePrefix
     *
     * @return string
     */
    protected function _convertDatePrefixToDate($datePrefix)
    {
        return date($datePrefix);
    }

    /**
     * @return int
     */
    protected function _getIncrementPadLength()
    {
        return intval(Mage::getStoreConfig('mswebdesign_customordernumber/'.$this->_entityTypeCode.'/padding_length', $this->_storeId));
    }

    /**
     * @return int
     */
    protected function _getIncrementLastId()
    {
        if('' !== $this->_datePrefix) {
            $this->_handleIncrementLastIdIfDateHasChanged();
        } else {
            $this->_handleIncrementLastIdIfPrefixLengthHasChanged();
        }

        return $this->_entityStoreConfig->getIncrementLastId();
    }


    protected function _handleIncrementLastIdIfDateHasChanged()
    {
        if($this->_entityStoreConfig->getIncrementPrefix() !== $this->_datePrefix) {
            if (1 === intval(Mage::getStoreConfig('mswebdesign_customordernumber/'.$this->_entityTypeCode.'/date_prefix_reset_enabled', $this->_storeId))) {
                $this->_entityStoreConfig->setIncrementLastId(0);
            } else {
                $this->_entityStoreConfig->setIncrementLastId($this->_datePrefix . substr($this->_entityStoreConfig->getIncrementLastId(), strlen($this->_datePrefix)));
            }
        }
    }

    protected function _handleIncrementLastIdIfPrefixLengthHasChanged()
    {
        if(strlen($this->_prefix) !== $this->_entityStoreConfig->getIncrementPrefix()) {
            $this->_entityStoreConfig->setIncrementLastId($this->_prefix.substr($this->_entityStoreConfig->getIncrementLastId(), strlen($this->_entityStoreConfig->getIncrementPrefix())));
        }
    }

    /**
     * @return bool
     */
    protected function _isIncrementIdUinique()
    {
        switch($this->_entityTypeCode) {
            case('order'):
                $collection = Mage::getSingleton('sales/'.$this->_entityTypeCode)->getCollection();
                break;
            default:
                $collection = Mage::getSingleton('sales/order_'.$this->_entityTypeCode)->getCollection();
        }

        $collection->clear();
        $count = $collection->addAttributeToFilter('increment_id', $this->_incrementId)->count();
        return ($count == 0)? true:false;
    }

    protected function _generateUniqueIncrementId()
    {
        do {
            $this->_incrementInstance->setLastId($this->_incrementId);
            $this->_incrementId = $this->_incrementInstance->getNextId();
        } while (false === $this->_isIncrementIdUinique());
    }

    protected function _appendDefaultNumberConstraint()
    {
        $defaultNumber = intval(Mage::getStoreConfig('mswebdesign_customordernumber/'.$this->_entityTypeCode.'/number', $this->_storeId));
        $currentNumber = $this->_getCurrentNumber();

        if($currentNumber < $defaultNumber) {
            $this->_incrementInstance->setLastId($this->_getIncrementPrefix() . ($defaultNumber - 1));
            $this->_incrementId = $this->_incrementInstance->getNextId();
        }
    }

    /**
     * @return int
     */
    protected function _getCurrentNumber()
    {
        if (strpos($this->_incrementId, $this->_getIncrementPrefix()) === 0) {
            $currentNumber = (int)substr($this->_incrementId, strlen($this->_getIncrementPrefix()));
        } else {
            $currentNumber = (int)$this->_incrementId;
        }

        return $currentNumber;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV additional attribute resource collection (Using Forms)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Resource_Attribute_Collection
    extends Mage_Eav_Model_Resource_Entity_Attribute_Collection
{
    /**
     * code of password hash in customer's EAV tables
     */
    const EAV_CODE_PASSWORD_HASH = 'password_hash';

    /**
     * Current website scope instance
     *
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * Attribute Entity Type Filter
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    abstract protected function _getEntityTypeCode();

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    abstract protected function _getEavWebsiteTable();

    /**
     * Return eav entity type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if ($this->_entityType === null) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($this->_getEntityTypeCode());
        }
        return $this->_entityType;
    }

    /**
     * Set Website scope
     *
     * @param Mage_Core_Model_Website|int $website
     * @return Mage_Eav_Model_Resource_Attribute_Collection
     */
    public function setWebsite($website)
    {
        $this->_website = Mage::app()->getWebsite($website);
        $this->addBindParam('scope_website_id', $this->_website->getId());
        return $this;
    }

    /**
     * Return current website scope instance
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        if ($this->_website === null) {
            $this->_website = Mage::app()->getStore()->getWebsite();
        }
        return $this->_website;
    }

    /**
     * Initialize collection select
     *
     * @return Mage_Eav_Model_Resource_Attribute_Collection
     */
    protected function _initSelect()
    {
        $select         = $this->getSelect();
        $connection     = $this->getConnection();
        $entityType     = $this->getEntityType();
        $extraTable     = $entityType->getAdditionalAttributeTable();
        $mainDescribe   = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        $mainColumns    = array();

        foreach (array_keys($mainDescribe) as $columnName) {
            $mainColumns[$columnName] = $columnName;
        }

        $select->from(array('main_table' => $this->getResource()->getMainTable()), $mainColumns);

        // additional attribute data table
        $extraDescribe  = $connection->describeTable($this->getTable($extraTable));
        $extraColumns   = array();
        foreach (array_keys($extraDescribe) as $columnName) {
            if (isset($mainColumns[$columnName])) {
                continue;
            }
            $extraColumns[$columnName] = $columnName;
        }

        $this->addBindParam('mt_entity_type_id', (int)$entityType->getId());
        $select
            ->join(
                array('additional_table' => $this->getTable($extraTable)),
                'additional_table.attribute_id = main_table.attribute_id',
                $extraColumns)
            ->where('main_table.entity_type_id = :mt_entity_type_id');

        // scope values

        $scopeDescribe  = $connection->describeTable($this->_getEavWebsiteTable());
        unset($scopeDescribe['attribute_id']);
        $scopeColumns   = array();
        foreach (array_keys($scopeDescribe) as $columnName) {
            if ($columnName == 'website_id') {
                $scopeColumns['scope_website_id'] = $columnName;
            } else {
                if (isset($mainColumns[$columnName])) {
                    $alias = sprintf('scope_%s', $columnName);
                    $expression = $connection->getCheckSql('main_table.%s IS NULL',
                        'scope_table.%s', 'main_table.%s');
                    $expression = sprintf($expression, $columnName, $columnName, $columnName);
                    $this->addFilterToMap($columnName, $expression);
                    $scopeColumns[$alias] = $columnName;
                } elseif (isset($extraColumns[$columnName])) {
                    $alias = sprintf('scope_%s', $columnName);
                    $expression = $connection->getCheckSql('additional_table.%s IS NULL',
                        'scope_table.%s', 'additional_table.%s');
                    $expression = sprintf($expression, $columnName, $columnName, $columnName);
                    $this->addFilterToMap($columnName, $expression);
                    $scopeColumns[$alias] = $columnName;
                }
            }
        }

        $select->joinLeft(
            array('scope_table' => $this->_getEavWebsiteTable()),
            'scope_table.attribute_id = main_table.attribute_id AND scope_table.website_id = :scope_website_id',
            $scopeColumns
        );
        $websiteId = $this->getWebsite() ? (int)$this->getWebsite()->getId() : 0;
        $this->addBindParam('scope_website_id', $websiteId);

        return $this;
    }

    /**
     * Specify attribute entity type filter.
     * Entity type is defined.
     *
     * @param  int $type
     * @return Mage_Eav_Model_Resource_Attribute_Collection
     */
    public function setEntityTypeFilter($type)
    {
        return $this;
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return Mage_Eav_Model_Resource_Attribute_Collection
     */
    public function addVisibleFilter()
    {
        return $this->addFieldToFilter('is_visible', 1);
    }

    /**
     * Exclude system hidden attributes
     *
     * @return Mage_Eav_Model_Resource_Attribute_Collection
     */
    public function addSystemHiddenFilter()
    {
        $field = '(CASE WHEN additional_table.is_system = 1 AND additional_table.is_visible = 0 THEN 1 ELSE 0 END)';
        $resultCondition = $this->_getConditionSql($field, 0);
        $this->_select->where($resultCondition);
        return $this;
    }

    /**
     * Exclude system hidden attributes but include password hash
     *
     * @return Mage_Customer_Model_Entity_Attribute_Collection
     */
    public function addSystemHiddenFilterWithPasswordHash()
    {
        $field = '(CASE WHEN additional_table.is_system = 1 AND additional_table.is_visible = 0
            AND main_table.attribute_code != "' . self::EAV_CODE_PASSWORD_HASH . '" THEN 1 ELSE 0 END)';
        $resultCondition = $this->_getConditionSql($field, 0);
        $this->_select->where($resultCondition);
        return $this;
    }

    /**
     * Add exclude hidden frontend input attribute filter to collection
     *
     * @return Mage_Eav_Model_Resource_Attribute_Collection
     */
    public function addExcludeHiddenFrontendFilter()
    {
        return $this->addFieldToFilter('main_table.frontend_input', array('neq' => 'hidden'));
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer EAV additional attribute resource collection
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Attribute_Collection extends Mage_Eav_Model_Resource_Attribute_Collection
{
    /**
     * Default attribute entity type code
     *
     * @var string
     */
    protected $_entityTypeCode   = 'customer';

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return $this->_entityTypeCode;
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('customer/eav_attribute_website');
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV attribute model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Mysql4_Entity_Attribute extends Mage_Eav_Model_Resource_Entity_Attribute
{
}
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Model_Eav_Mysql4_Entity_Attribute extends Mage_Eav_Model_Mysql4_Entity_Attribute
{
	protected function _saveOption(Mage_Core_Model_Abstract $object)
    {
        $option = $object->getOption();
        if (!isset($option['parent'])&&!isset($option['group_id']))
        {
        	return parent::_saveOption($object);
        }
        if (is_array($option)) {
            $write = $this->_getWriteAdapter();
            $optionTable        = $this->getTable('attribute_option');
            $optionValueTable   = $this->getTable('attribute_option_value');
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();

            if (isset($option['value'])) {
                $attributeDefaultValue = array();
                if (!is_array($object->getDefault())) {
                    $object->setDefault(array());
                }

                foreach ($option['value'] as $optionId => $values) {
                    $intOptionId = (int) $optionId;
                    if (!empty($option['delete'][$optionId])) {
                        if ($intOptionId) {
                            $condition = $write->quoteInto('option_id=?', $intOptionId);
                            $write->delete($optionTable, $condition);
                        }

                        continue;
                    }

                    if (!$intOptionId) {
                        $data = array(
                           'attribute_id'     => $object->getId(),
                           'sort_order'    	  => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                        );
                        if (isset($option['parent']))
                        {
                           $data['parent_option_id'] = isset($option['parent'][$optionId]) ? $option['parent'][$optionId] : 0;
                        }
                        else if(isset($option['group_id']))
                        {
                           $data['group_id'] = isset($option['group_id'][$optionId]) ? $option['group_id'][$optionId] : 0;
                        }
                        $write->insert($optionTable, $data);
                        $intOptionId = $write->lastInsertId();
                    }
                    else {
                        $data = array(
                           'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                        );
                        if (isset($option['parent']))
                        {
                           $data['parent_option_id'] = isset($option['parent'][$optionId]) ? $option['parent'][$optionId] : 0;
                        }
                        else if(isset($option['group_id']))
                        {
                           $data['group_id'] = isset($option['group_id'][$optionId]) ? $option['group_id'][$optionId] : 0;
                        }
                        $write->update($optionTable, $data, $write->quoteInto('option_id=?', $intOptionId));
                    }

                    if (in_array($optionId, $object->getDefault())) {
                        if ($object->getFrontendInput() == 'multiselect') {
                            $attributeDefaultValue[] = $intOptionId;
                        } else if ($object->getFrontendInput() == 'select') {
                            $attributeDefaultValue = array($intOptionId);
                        }
                    }


                    // Default value
                    if (!isset($values[0])) {
                        Mage::throwException(Mage::helper('eav')->__('Default option value is not defined.'));
                    }

                    $write->delete($optionValueTable, $write->quoteInto('option_id=?', $intOptionId));
                    foreach ($stores as $store) {
                        if (isset($values[$store->getId()]) && (!empty($values[$store->getId()]) || $values[$store->getId()] == "0")) {
                            $data = array(
                                'option_id' => $intOptionId,
                                'store_id'  => $store->getId(),
                                'value'     => $values[$store->getId()],
                            );
                            $write->insert($optionValueTable, $data);
                        }
                    }
                }

                $write->update($this->getMainTable(), array(
                    'default_value' => implode(',', $attributeDefaultValue)
                ), $write->quoteInto($this->getIdFieldName() . '=?', $object->getId()));
            }
        }

        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * EAV attribute resource model (Using Forms)
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    /**
     * Name of the module
     * Override it
     */
    //const MODULE_NAME = 'Mage_Eav';

    /**
     * Prefix of model events object
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    /**
     * Active Website instance
     *
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * Set active website instance
     *
     * @param Mage_Core_Model_Website|int $website
     * @return Mage_Eav_Model_Attribute
     */
    public function setWebsite($website)
    {
        $this->_website = Mage::app()->getWebsite($website);
        return $this;
    }

    /**
     * Return active website instance
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        if (is_null($this->_website)) {
            $this->_website = Mage::app()->getWebsite();
        }

        return $this->_website;
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Eav_Model_Attribute
     */
    protected function _afterSave()
    {
        Mage::getSingleton('eav/config')->clear();
        return parent::_afterSave();
    }

    /**
     * Return forms in which the attribute
     *
     * @return array
     */
    public function getUsedInForms()
    {
        $forms = $this->getData('used_in_forms');
        if (is_null($forms)) {
            $forms = $this->_getResource()->getUsedInForms($this);
            $this->setData('used_in_forms', $forms);
        }
        return $forms;
    }

    /**
     * Return validate rules
     *
     * @return array
     */
    public function getValidateRules()
    {
        $rules = $this->getData('validate_rules');
        if (is_array($rules)) {
            return $rules;
        } else if (!empty($rules)) {
            return unserialize($rules);
        }
        return array();
    }

    /**
     * Set validate rules
     *
     * @param array|string $rules
     * @return Mage_Eav_Model_Attribute
     */
    public function setValidateRules($rules)
    {
        if (empty($rules)) {
            $rules = null;
        } else if (is_array($rules)) {
            $rules = serialize($rules);
        }
        $this->setData('validate_rules', $rules);

        return $this;
    }

    /**
     * Return scope value by key
     *
     * @param string $key
     * @return mixed
     */
    protected function _getScopeValue($key)
    {
        $scopeKey = sprintf('scope_%s', $key);
        if ($this->getData($scopeKey) !== null) {
            return $this->getData($scopeKey);
        }
        return $this->getData($key);
    }

    /**
     * Return is attribute value required
     *
     * @return mixed
     */
    public function getIsRequired()
    {
        return $this->_getScopeValue('is_required');
    }

    /**
     * Return is visible attribute flag
     *
     * @return mixed
     */
    public function getIsVisible()
    {
        return $this->_getScopeValue('is_visible');
    }

    /**
     * Return default value for attribute
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->_getScopeValue('default_value');
    }

    /**
     * Return count of lines for multiply line attribute
     *
     * @return mixed
     */
    public function getMultilineCount()
    {
        return $this->_getScopeValue('multiline_count');
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer attribute model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Attribute extends Mage_Eav_Model_Attribute
{
    /**
     * Name of the module
     */
    const MODULE_NAME = 'Mage_Customer';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_entity_attribute';

    /**
     * Prefix of model events object
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('customer/attribute');
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV attribute resource model (Using Forms)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Resource_Attribute extends Mage_Eav_Model_Resource_Entity_Attribute
{
    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    abstract protected function _getEavWebsiteTable();

    /**
     * Get Form attribute table
     *
     * Get table, where dependency between form name and attribute ids are stored
     *
     * @return string|null
     */
    abstract protected function _getFormAttributeTable();

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Resource_Attribute
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $validateRules = $object->getData('validate_rules');
        if (is_array($validateRules)) {
            $object->setData('validate_rules', serialize($validateRules));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select     = parent::_getLoadSelect($field, $value, $object);
        $websiteId  = (int)$object->getWebsite()->getId();
        if ($websiteId) {
            $adapter    = $this->_getReadAdapter();
            $columns    = array();
            $scopeTable = $this->_getEavWebsiteTable();
            $describe   = $adapter->describeTable($scopeTable);
            unset($describe['attribute_id']);
            foreach (array_keys($describe) as $columnName) {
                $columns['scope_' . $columnName] = $columnName;
            }
            $conditionSql = $adapter->quoteInto(
                $this->getMainTable() . '.attribute_id = scope_table.attribute_id AND scope_table.website_id =?',
                $websiteId);
            $select->joinLeft(
                array('scope_table' => $scopeTable),
                $conditionSql,
                $columns
            );
        }

        return $select;
    }

    /**
     * Save attribute/form relations after attribute save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Resource_Attribute
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $forms      = $object->getData('used_in_forms');
        $adapter    = $this->_getWriteAdapter();
        if (is_array($forms)) {
            $where = array('attribute_id=?' => $object->getId());
            $adapter->delete($this->_getFormAttributeTable(), $where);

            $data = array();
            foreach ($forms as $formCode) {
                $data[] = array(
                    'form_code'     => $formCode,
                    'attribute_id'  => (int)$object->getId()
                );
            }

            if ($data) {
                $adapter->insertMultiple($this->_getFormAttributeTable(), $data);
            }
        }

        // update sort order
        if (!$object->isObjectNew() && $object->dataHasChangedFor('sort_order')) {
            $data  = array('sort_order' => $object->getSortOrder());
            $where = array('attribute_id=?' => (int)$object->getId());
            $adapter->update($this->getTable('eav/entity_attribute'), $data, $where);
        }

        // save scope attributes
        $websiteId = (int)$object->getWebsite()->getId();
        if ($websiteId) {
            $table      = $this->_getEavWebsiteTable();
            $describe   = $this->_getReadAdapter()->describeTable($table);
            $data       = array();
            if (!$object->getScopeWebsiteId() || $object->getScopeWebsiteId() != $websiteId) {
                $data = $this->getScopeValues($object);
            }

            $data['attribute_id']   = (int)$object->getId();
            $data['website_id']     = (int)$websiteId;
            unset($describe['attribute_id']);
            unset($describe['website_id']);

            $updateColumns = array();
            foreach (array_keys($describe) as $columnName) {
                $data[$columnName] = $object->getData('scope_' . $columnName);
                $updateColumns[]   = $columnName;
            }

            $adapter->insertOnDuplicate($table, $data, $updateColumns);
        }

        return parent::_afterSave($object);
    }

    /**
     * Return scope values for attribute and website
     *
     * @param Mage_Eav_Model_Attribute $object
     * @return array
     */
    public function getScopeValues(Mage_Eav_Model_Attribute $object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array(
            'attribute_id' => (int)$object->getId(),
            'website_id'   => (int)$object->getWebsite()->getId()
        );
        $select = $adapter->select()
            ->from($this->_getEavWebsiteTable())
            ->where('attribute_id = :attribute_id')
            ->where('website_id = :website_id')
            ->limit(1);
        $result = $adapter->fetchRow($select, $bind);

        if (!$result) {
            $result = array();
        }

        return $result;
    }

    /**
     * Return forms in which the attribute
     *
     * @param Mage_Core_Model_Abstract $object
     * @return array
     */
    public function getUsedInForms(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('attribute_id' => (int)$object->getId());
        $select  = $adapter->select()
            ->from($this->_getFormAttributeTable(), 'form_code')
            ->where('attribute_id = :attribute_id');

        return $adapter->fetchCol($select, $bind);
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer attribute resource model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Attribute extends Mage_Eav_Model_Resource_Attribute
{
    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('customer/eav_attribute_website');
    }

    /**
     * Get Form attribute table
     *
     * Get table, where dependency between form name and attribute ids is stored
     *
     * @return string|null
     */
    protected function _getFormAttributeTable()
    {
        return $this->getTable('customer/form_attribute');
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource helper class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Resource_Helper_Abstract
{
    /**
     * Read adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_readAdapter;

    /**
     * Write adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_writeAdapter;

    /**
     * Resource helper module prefix
     *
     * @var string
     */
    protected $_modulePrefix;

    /**
     * Initialize resource helper instance
     *
     * @param string $module
     */
    public function __construct($module)
    {
        $this->_modulePrefix = (string)$module;
    }

    /**
     * Retrieve connection for read data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getReadAdapter()
    {
        if ($this->_readAdapter === null) {
            $this->_readAdapter = $this->_getConnection('read');
        }

        return $this->_readAdapter;
    }

    /**
     * Retrieve connection for write data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getWriteAdapter()
    {
        if ($this->_writeAdapter === null) {
            $this->_writeAdapter = $this->_getConnection('write');
        }

        return $this->_writeAdapter;
    }

    /**
     * Retrieves connection to the resource
     *
     * @param string $name
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection($name)
    {
        $connection = sprintf('%s_%s', $this->_modulePrefix, $name);
        /** @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('core/resource');

        return $resource->getConnection($connection);
    }

    /**
     * Escapes value, that participates in LIKE, with '\' symbol.
     * Note: this func cannot be used on its own, because different RDMBS may use different default escape symbols,
     * so you should either use addLikeEscape() to produce LIKE construction, or add escape symbol on your own.
     *
     * By default escapes '_', '%' and '\' symbols. If some masking symbols must not be escaped, then you can set
     * appropriate options in $options.
     *
     * $options can contain following flags:
     * - 'allow_symbol_mask' - the '_' symbol will not be escaped
     * - 'allow_string_mask' - the '%' symbol will not be escaped
     * - 'position' ('any', 'start', 'end') - expression will be formed so that $value will be found at position within string,
     *     by default when nothing set - string must be fully matched with $value
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function escapeLikeValue($value, $options = array())
    {
        $value = str_replace('\\', '\\\\', $value);

        $from = array();
        $to = array();
        if (empty($options['allow_symbol_mask'])) {
            $from[] = '_';
            $to[] = '\_';
        }
        if (empty($options['allow_string_mask'])) {
            $from[] = '%';
            $to[] = '\%';
        }
        if ($from) {
            $value = str_replace($from, $to, $value);
        }

        if (isset($options['position'])) {
            switch ($options['position']) {
                case 'any':
                    $value = '%' . $value . '%';
                    break;
                case 'start':
                    $value = $value . '%';
                    break;
                case 'end':
                    $value = '%' . $value;
                    break;
            }
        }

        return $value;
    }

    /**
     * Escapes, quotes and adds escape symbol to LIKE expression.
     * For options and escaping see escapeLikeValue().
     *
     * @param string $value
     * @param array $options
     * @return Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    abstract public function addLikeEscape($value, $options = array());

    /**
     * Returns case insensitive LIKE construction.
     * For options and escaping see escapeLikeValue().
     *
     * @param string $field
     * @param string $value
     * @param array $options
     * @return Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    public function getCILike($field, $value, $options = array())
    {
        $quotedField = $this->_getReadAdapter()->quoteIdentifier($field);
        return new Zend_Db_Expr($quotedField . ' LIKE ' . $this->addLikeEscape($value, $options));
    }

    /**
     * Converts old pre-MMDB column definition for MySQL to new cross-db column DDL definition.
     * Used to convert data from 3rd party extensions that hasn't been updated to MMDB style yet.
     *
     * E.g. Converts type 'varchar(255)' to array('type' => Varien_Db_Ddl_Table::TYPE_TEXT, 'length' => 255)
     *
     * @param array $column
     * @return array
     */
    public function convertOldColumnDefinition($column)
    {
        // Match type and size - e.g. varchar(100) or decimal(12,4) or int
        $matches    = array();
        $definition = trim($column['type']);
        if (!preg_match('/([^(]*)(\\((.*)\\))?/', $definition, $matches)) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('core')->__("Wrong old style column type definition: {$definition}.")
            );
        }

        $length = null;
        $proposedLength = (isset($matches[3]) && strlen($matches[3])) ? $matches[3] : null;
        switch (strtolower($matches[1])) {
            case 'bool':
                $length = null;
                $type = Varien_Db_Ddl_Table::TYPE_BOOLEAN;
                break;
            case 'char':
            case 'varchar':
            case 'tinytext':
                $length = $proposedLength;
                if (!$length) {
                    $length = 255;
                }
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                break;
            case 'text':
                $length = $proposedLength;
                if (!$length) {
                    $length = '64k';
                }
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                break;
            case 'mediumtext':
                $length = $proposedLength;
                if (!$length) {
                    $length = '16M';
                }
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                break;
            case 'longtext':
                $length = $proposedLength;
                if (!$length) {
                    $length = '4G';
                }
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                break;
            case 'blob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '64k';
                }
                $type = Varien_Db_Ddl_Table::TYPE_BLOB;
                break;
            case 'mediumblob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '16M';
                }
                $type = Varien_Db_Ddl_Table::TYPE_BLOB;
                break;
            case 'longblob':
                $length = $proposedLength;
                if (!$length) {
                    $length = '4G';
                }
                $type = Varien_Db_Ddl_Table::TYPE_BLOB;
                break;
            case 'tinyint':
            case 'smallint':
                $type = Varien_Db_Ddl_Table::TYPE_SMALLINT;
                break;
            case 'mediumint':
            case 'int':
                $type = Varien_Db_Ddl_Table::TYPE_INTEGER;
                break;
            case 'bigint':
                $type = Varien_Db_Ddl_Table::TYPE_BIGINT;
                break;
            case 'float':
                $type = Varien_Db_Ddl_Table::TYPE_FLOAT;
                break;
            case 'decimal':
            case 'numeric':
                $length = $proposedLength;
                $type = Varien_Db_Ddl_Table::TYPE_DECIMAL;
                break;
            case 'datetime':
                $type = Varien_Db_Ddl_Table::TYPE_DATETIME;
                break;
            case 'timestamp':
            case 'time':
                $type = Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
                break;
            case 'date':
                $type = Varien_Db_Ddl_Table::TYPE_DATE;
                break;
            default:
                throw Mage::exception(
                    'Mage_Core',
                    Mage::helper('core')->__("Unknown old style column type definition: {$definition}.")
                );
        }

        $result = array(
            'type'     => $type,
            'length'   => $length,
            'unsigned' => $column['unsigned'],
            'nullable' => $column['is_null'],
            'default'  => $column['default'],
            'identity' => stripos($column['extra'], 'auto_increment') !== false
        );

        /**
         * Process the case when 'is_null' prohibits null value, and 'default' proposed to be null.
         * It just means that default value not specified, and we must remove it from column definition.
         */
        if (false === $column['is_null'] && null === $column['default']) {
            unset($result['default']);
        }

        return $result;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Resource helper class for MySql Varien DB Adapter
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Abstract
{
    /**
     * Returns expresion for field unification
     *
     * @param string $field
     * @return Zend_Db_Expr
     */
    public function castField($field)
    {
        return $field;
    }
    /**
     * Returns analytic expression for database column
     *
     * @param string $column
     * @param string $groupAliasName OPTIONAL
     * @param string $orderBy OPTIONAL
     * @return Zend_Db_Expr
     */
    public function prepareColumn($column, $groupAliasName = null, $orderBy = null)
    {
        return new Zend_Db_Expr((string)$column);
    }

    /**
     * Returns select query with analytic functions
     *
     * @param Varien_Db_Select $select
     * @return string
     */
    public function getQueryUsingAnalyticFunction(Varien_Db_Select $select)
    {
        return $select->assemble();
    }

    /**
     *
     * Returns Insert From Select On Duplicate query with analytic functions
     *
     * @param Varien_Db_Select $select
     * @param string $table
     * @param array $table
     * @return string
     */
    public function getInsertFromSelectUsingAnalytic(Varien_Db_Select $select, $table, $fields)
    {
        return $select->insertFromSelect($table, $fields);
    }

    /**
     * Correct limitation of queries with UNION
     * No need to do additional actions on MySQL
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     */
    public function limitUnion($select)
    {
        return $select;
    }

    /**
     * Returns array of quoted orders with direction
     *
     * @param Varien_Db_Select $select
     * @param bool $autoReset
     * @return array
     */
    protected function _prepareOrder(Varien_Db_Select $select, $autoReset = false)
    {
        $selectOrders = $select->getPart(Zend_Db_Select::ORDER);
        if (!$selectOrders) {
            return array();
        }

        $orders = array();
        foreach ($selectOrders as $term) {
            if (is_array($term)) {
                if (!is_numeric($term[0])) {
                    $orders[]   = sprintf('%s %s', $this->_getReadAdapter()->quoteIdentifier($term[0], true), $term[1]);
                }
            } else {
                if (!is_numeric($term)) {
                    $orders[] = $this->_getReadAdapter()->quoteIdentifier($term, true);
                }
            }
        }

        if ($autoReset) {
            $select->reset(Zend_Db_Select::ORDER);
        }

        return $orders;
    }

    /**
     * Truncate alias name from field.
     *
     * Result string depends from second optional argument $reverse
     * which can be true if you need the first part of the field.
     * Field can be with 'dot' delimiter.
     *
     * @param string $field
     * @param bool   $reverse OPTIONAL
     * @return string
     */
    protected function _truncateAliasName($field, $reverse = false)
    {
        $string = $field;
        if (!is_numeric($field) && (strpos($field, '.') !== false)) {
            $size  = strpos($field, '.');
            if ($reverse) {
                $string = substr($field, 0, $size);
            } else {
                $string = substr($field, $size + 1);
            }
        }

        return $string;
    }

    /**
     * Returns quoted group by fields
     *
     * @param Varien_Db_Select $select
     * @param bool $autoReset
     * @return array
     */
    protected function _prepareGroup(Varien_Db_Select $select, $autoReset = false)
    {
        $selectGroups = $select->getPart(Zend_Db_Select::GROUP);
        if (!$selectGroups) {
            return array();
        }

        $groups = array();
        foreach ($selectGroups as $term) {
            $groups[] = $this->_getReadAdapter()->quoteIdentifier($term, true);
        }

        if ($autoReset) {
            $select->reset(Zend_Db_Select::GROUP);
        }

        return $groups;
    }

    /**
     * Prepare and returns having array
     *
     * @param Varien_Db_Select $select
     * @param bool $autoReset
     * @return array
     * @throws Zend_Db_Exception
     */
    protected function _prepareHaving(Varien_Db_Select $select, $autoReset = false)
    {
        $selectHavings = $select->getPart(Zend_Db_Select::HAVING);
        if (!$selectHavings) {
            return array();
        }

        $havings = array();
        $columns = $select->getPart(Zend_Db_Select::COLUMNS);
        foreach ($columns as $columnEntry) {
            $correlationName = (string)$columnEntry[1];
            $column          = $columnEntry[2];
            foreach ($selectHavings as $having) {
                /**
                 * Looking for column expression in the having clause
                 */
                if (strpos($having, $correlationName) !== false) {
                    if (is_string($column)) {
                        /**
                         * Replace column expression to column alias in having clause
                         */
                        $havings[] = str_replace($correlationName, $column, $having);
                    } else {
                        throw new Zend_Db_Exception(sprintf("Can't prepare expression without column alias: '%s'", $correlationName));
                    }
                }
            }
        }

        if ($autoReset) {
            $select->reset(Zend_Db_Select::HAVING);
        }

        return $havings;
    }

    /**
     *
     * @param string $query
     * @param int $limitCount
     * @param int $limitOffset
     * @param array $columnList
     * @return string
     */
    protected function _assembleLimit($query, $limitCount, $limitOffset, $columnList = array())
    {
        if ($limitCount !== null) {
              $limitCount = intval($limitCount);
            if ($limitCount <= 0) {
//                throw new Exception("LIMIT argument count={$limitCount} is not valid");
            }

            $limitOffset = intval($limitOffset);
            if ($limitOffset < 0) {
//                throw new Exception("LIMIT argument offset={$limitOffset} is not valid");
            }

            if ($limitOffset + $limitCount != $limitOffset + 1) {
                $columns = array();
                foreach ($columnList as $columnEntry) {
                    $columns[] = $columnEntry[2] ? $columnEntry[2] : $columnEntry[1];
                }

                $query = sprintf('%s LIMIT %s, %s', $query, $limitCount, $limitOffset);
            }
        }

        return $query;
    }

    /**
     * Prepare select column list
     *
     * @param Varien_Db_Select $select
     * @param $groupByCondition OPTIONAL
     * @return array
     * @throws Zend_Db_Exception
     */
    public function prepareColumnsList(Varien_Db_Select $select, $groupByCondition = null)
    {
        if (!count($select->getPart(Zend_Db_Select::FROM))) {
            return $select->getPart(Zend_Db_Select::COLUMNS);
        }

        $columns          = $select->getPart(Zend_Db_Select::COLUMNS);
        $tables           = $select->getPart(Zend_Db_Select::FROM);
        $preparedColumns  = array();

        foreach ($columns as $columnEntry) {
            list($correlationName, $column, $alias) = $columnEntry;
            if ($column instanceof Zend_Db_Expr) {
                if ($alias !== null) {
                    if (preg_match('/(^|[^a-zA-Z_])^(SELECT)?(SUM|MIN|MAX|AVG|COUNT)\s*\(/i', $column, $matches)) {
                        $column = $this->prepareColumn($column, $groupByCondition);
                    }
                    $preparedColumns[strtoupper($alias)] = array(null, $column, $alias);
                } else {
                    throw new Zend_Db_Exception("Can't prepare expression without alias");
                }
            } else {
                if ($column == Zend_Db_Select::SQL_WILDCARD) {
                    if ($tables[$correlationName]['tableName'] instanceof Zend_Db_Expr) {
                        throw new Zend_Db_Exception("Can't prepare expression when tableName is instance of Zend_Db_Expr");
                    }
                    $tableColumns = $this->_getReadAdapter()->describeTable($tables[$correlationName]['tableName']);
                    foreach(array_keys($tableColumns) as $col) {
                        $preparedColumns[strtoupper($col)] = array($correlationName, $col, null);
                    }
                } else {
                    $columnKey = is_null($alias) ? $column : $alias;
                    $preparedColumns[strtoupper($columnKey)] = array($correlationName, $column, $alias);
                }
            }
        }

//        $select->reset(Zend_Db_Select::COLUMNS);
//        $select->setPart(Zend_Db_Select::COLUMNS, array_values($preparedColumns));

        return $preparedColumns;
    }

    /**
     * Add prepared column group_concat expression
     *
     * @param Varien_Db_Select $select
     * @param string $fieldAlias Field alias which will be added with column group_concat expression
     * @param string $fields
     * @param string $groupConcatDelimiter
     * @param string $fieldsDelimiter
     * @param string $additionalWhere
     * @return Varien_Db_Select
     */
    public function addGroupConcatColumn($select, $fieldAlias, $fields, $groupConcatDelimiter = ',', $fieldsDelimiter = '', $additionalWhere = '')
    {
        if (is_array($fields)) {
            $fieldExpr = $this->_getReadAdapter()->getConcatSql($fields, $fieldsDelimiter);
        } else {
            $fieldExpr = $fields;
        }
        if ($additionalWhere) {
            $fieldExpr = $this->_getReadAdapter()->getCheckSql($additionalWhere, $fieldExpr, "''");
        }
        $separator = '';
        if ($groupConcatDelimiter) {
            $separator = sprintf(" SEPARATOR '%s'",  $groupConcatDelimiter);
        }

        $select->columns(array($fieldAlias => new Zend_Db_Expr(sprintf('GROUP_CONCAT(%s%s)', $fieldExpr, $separator))));

        return $select;
    }

    /**
     * Returns expression of days passed from $startDate to $endDate
     *
     * @param  string|Zend_Db_Expr $startDate
     * @param  string|Zend_Db_Expr $endDate
     * @return Zend_Db_Expr
     */
    public function getDateDiff($startDate, $endDate)
    {
        $dateDiff = '(TO_DAYS(' . $endDate . ') - TO_DAYS(' . $startDate . '))';
        return new Zend_Db_Expr($dateDiff);
    }

    /**
     * Escapes and quotes LIKE value.
     * Stating escape symbol in expression is not required, because we use standard MySQL escape symbol.
     * For options and escaping see escapeLikeValue().
     *
     * @param string $value
     * @param array $options
     * @return Zend_Db_Expr
     *
     * @see escapeLikeValue()
     */
    public function addLikeEscape($value, $options = array())
    {
        $value = $this->escapeLikeValue($value, $options);
        return new Zend_Db_Expr($this->_getReadAdapter()->quote($value));
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Eav Mysql resource helper model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4
{
    /**
     * Mysql column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'bool',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'int',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'float',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'timestamp',
        Varien_Db_Ddl_Table::TYPE_DATETIME      => 'datetime',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'text',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'blob',
        Varien_Db_Ddl_Table::TYPE_VARBINARY     => 'blob'
    );

    /**
     * Returns columns for select
     *
     * @param string $tableAlias
     * @param string $eavType
     * @return string|array
     */
    public function attributeSelectFields($tableAlias, $eavType)
    {
        return '*';
    }

    /**
     * Returns DDL type by column type in database
     *
     * @param string $columnType
     * @return string
     */
    public function getDdlTypeByColumnType($columnType)
    {
        switch ($columnType) {
            case 'char':
            case 'varchar':
                $columnType = 'text';
                break;
            case 'tinyint':
                $columnType = 'smallint';
                break;
        }

        return array_search($columnType, $this->_ddlColumnTypes);
    }

    /**
     * Prepares value fields for unions depend on type
     *
     * @param string $value
     * @param string $eavType
     * @return Zend_Db_Expr
     */
    public function prepareEavAttributeValue($value, $eavType)
    {
        return $value;
    }

    /**
     * Groups selects to separate unions depend on type
     *
     * @param array $selects
     * @return array
     */
    public function getLoadAttributesSelectGroups($selects)
    {
        $mainGroup  = array();
        foreach ($selects as $eavType => $selectGroup) {
            $mainGroup = array_merge($mainGroup, $selectGroup);
        }
        return array($mainGroup);
    }

    /**
     * Retrieve 'cast to int' expression
     *
     * @param string|Zend_Db_Expr $expression
     * @return Zend_Db_Expr
     */
    public function getCastToIntExpression($expression)
    {
        return new Zend_Db_Expr("CAST($expression AS SIGNED)");
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer password attribute backend
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Password extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     */
    public function beforeSave($object)
    {
        $password = trim($object->getPassword());
        $len = Mage::helper('core/string')->strlen($password);
        if ($len) {
             if ($len < 6) {
                Mage::throwException(Mage::helper('customer')->__('The password must have at least 6 characters. Leading or trailing spaces will be ignored.'));
            }
            $object->setPasswordHash($object->hashPassword($password));
        }
    }

    public function validate($object)
    {
        if ($password = $object->getPassword()) {
            if ($password == $object->getPasswordConfirm()) {
                return true;
            }
        }

        return parent::validate($object);
    }

}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer default billing address backend
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Billing extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $defaultBilling = $object->getDefaultBilling();
        if (is_null($defaultBilling)) {
            $object->unsetDefaultBilling();
        }
    }
    
    public function afterSave($object)
    {
        if ($defaultBilling = $object->getDefaultBilling()) 
        {
            $addressId = false;
            /**
             * post_index set in customer save action for address
             * this is $_POST array index for address
             */
            foreach ($object->getAddresses() as $address) {
                if ($address->getPostIndex() == $defaultBilling) {
                    $addressId = $address->getId();
                }
            }
            if ($addressId) {
                $object->setDefaultBilling($addressId);
                $this->getAttribute()->getEntity()
                    ->saveAttribute($object, $this->getAttribute()->getAttributeCode());
            }
        }
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer default shipping address backend
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Shipping extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $defaultShipping = $object->getDefaultShipping();
        if (is_null($defaultShipping)) {
            $object->unsetDefaultShipping();
        }
    }
    
    public function afterSave($object)
    {
        if ($defaultShipping = $object->getDefaultShipping()) 
        {
            $addressId = false;
            /**
             * post_index set in customer save action for address
             * this is $_POST array index for address
             */
            foreach ($object->getAddresses() as $address) {
                if ($address->getPostIndex() == $defaultShipping) {
                    $addressId = $address->getId();
                }
            }
            
            if ($addressId) {
                $object->setDefaultShipping($addressId);
                $this->getAttribute()->getEntity()
                    ->saveAttribute($object, $this->getAttribute()->getAttributeCode());
            }
        }
    }
}
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Date
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_Date
 * @subpackage Zend_Date_DateObject
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Date_DateObject {

    /**
     * UNIX Timestamp
     */
    private   $_unixTimestamp;
    protected static $_cache         = null;
    protected static $_cacheTags     = false;
    protected static $_defaultOffset = 0;

    /**
     * active timezone
     */
    private   $_timezone    = 'UTC';
    private   $_offset      = 0;
    private   $_syncronised = 0;

    // turn off DST correction if UTC or GMT
    protected $_dst         = true;

    /**
     * Table of Monthdays
     */
    private static $_monthTable = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    /**
     * Table of Years
     */
    private static $_yearTable = array(
        1970 => 0,            1960 => -315619200,   1950 => -631152000,
        1940 => -946771200,   1930 => -1262304000,  1920 => -1577923200,
        1910 => -1893456000,  1900 => -2208988800,  1890 => -2524521600,
        1880 => -2840140800,  1870 => -3155673600,  1860 => -3471292800,
        1850 => -3786825600,  1840 => -4102444800,  1830 => -4417977600,
        1820 => -4733596800,  1810 => -5049129600,  1800 => -5364662400,
        1790 => -5680195200,  1780 => -5995814400,  1770 => -6311347200,
        1760 => -6626966400,  1750 => -6942499200,  1740 => -7258118400,
        1730 => -7573651200,  1720 => -7889270400,  1710 => -8204803200,
        1700 => -8520336000,  1690 => -8835868800,  1680 => -9151488000,
        1670 => -9467020800,  1660 => -9782640000,  1650 => -10098172800,
        1640 => -10413792000, 1630 => -10729324800, 1620 => -11044944000,
        1610 => -11360476800, 1600 => -11676096000);

    /**
     * Set this object to have a new UNIX timestamp.
     *
     * @param  string|integer  $timestamp  OPTIONAL timestamp; defaults to local time using time()
     * @return string|integer  old timestamp
     * @throws Zend_Date_Exception
     */
    protected function setUnixTimestamp($timestamp = null)
    {
        $old = $this->_unixTimestamp;

        if (is_numeric($timestamp)) {
            $this->_unixTimestamp = $timestamp;
        } else if ($timestamp === null) {
            $this->_unixTimestamp = time();
        } else {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('\'' . $timestamp . '\' is not a valid UNIX timestamp', 0, null, $timestamp);
        }

        return $old;
    }

    /**
     * Returns this object's UNIX timestamp
     * A timestamp greater then the integer range will be returned as string
     * This function does not return the timestamp as object. Use copy() instead.
     *
     * @return  integer|string  timestamp
     */
    protected function getUnixTimestamp()
    {
        if ($this->_unixTimestamp === intval($this->_unixTimestamp)) {
            return (int) $this->_unixTimestamp;
        } else {
            return (string) $this->_unixTimestamp;
        }
    }

    /**
     * Internal function.
     * Returns time().  This method exists to allow unit tests to work-around methods that might otherwise
     * be hard-coded to use time().  For example, this makes it possible to test isYesterday() in Date.php.
     *
     * @param   integer  $sync      OPTIONAL time syncronisation value
     * @return  integer  timestamp
     */
    protected function _getTime($sync = null)
    {
        if ($sync !== null) {
            $this->_syncronised = round($sync);
        }
        return (time() + $this->_syncronised);
    }

    /**
     * Internal mktime function used by Zend_Date.
     * The timestamp returned by mktime() can exceed the precision of traditional UNIX timestamps,
     * by allowing PHP to auto-convert to using a float value.
     *
     * Returns a timestamp relative to 1970/01/01 00:00:00 GMT/UTC.
     * DST (Summer/Winter) is depriciated since php 5.1.0.
     * Year has to be 4 digits otherwise it would be recognised as
     * year 70 AD instead of 1970 AD as expected !!
     *
     * @param  integer  $hour
     * @param  integer  $minute
     * @param  integer  $second
     * @param  integer  $month
     * @param  integer  $day
     * @param  integer  $year
     * @param  boolean  $gmt     OPTIONAL true = other arguments are for UTC time, false = arguments are for local time/date
     * @return  integer|float  timestamp (number of seconds elapsed relative to 1970/01/01 00:00:00 GMT/UTC)
     */
    protected function mktime($hour, $minute, $second, $month, $day, $year, $gmt = false)
    {
        // complete date but in 32bit timestamp - use PHP internal
        if ((1901 < $year) and ($year < 2038)) {

            $oldzone = @date_default_timezone_get();
            // Timezone also includes DST settings, therefor substracting the GMT offset is not enough
            // We have to set the correct timezone to get the right value
            if (($this->_timezone != $oldzone) and ($gmt === false)) {
                date_default_timezone_set($this->_timezone);
            }
            $result = ($gmt) ? @gmmktime($hour, $minute, $second, $month, $day, $year)
                             :   @mktime($hour, $minute, $second, $month, $day, $year);
            date_default_timezone_set($oldzone);

            return $result;
        }

        if ($gmt !== true) {
            $second += $this->_offset;
        }

        if (isset(self::$_cache)) {
            $id = strtr('Zend_DateObject_mkTime_' . $this->_offset . '_' . $year.$month.$day.'_'.$hour.$minute.$second . '_'.(int)$gmt, '-','_');
            if ($result = self::$_cache->load($id)) {
                return unserialize($result);
            }
        }

        // date to integer
        $day   = intval($day);
        $month = intval($month);
        $year  = intval($year);

        // correct months > 12 and months < 1
        if ($month > 12) {
            $overlap = floor($month / 12);
            $year   += $overlap;
            $month  -= $overlap * 12;
        } else {
            $overlap = ceil((1 - $month) / 12);
            $year   -= $overlap;
            $month  += $overlap * 12;
        }

        $date = 0;
        if ($year >= 1970) {

            // Date is after UNIX epoch
            // go through leapyears
            // add months from latest given year
            for ($count = 1970; $count <= $year; $count++) {

                $leapyear = self::isYearLeapYear($count);
                if ($count < $year) {

                    $date += 365;
                    if ($leapyear === true) {
                        $date++;
                    }

                } else {

                    for ($mcount = 0; $mcount < ($month - 1); $mcount++) {
                        $date += self::$_monthTable[$mcount];
                        if (($leapyear === true) and ($mcount == 1)) {
                            $date++;
                        }

                    }
                }
            }

            $date += $day - 1;
            $date = (($date * 86400) + ($hour * 3600) + ($minute * 60) + $second);
        } else {

            // Date is before UNIX epoch
            // go through leapyears
            // add months from latest given year
            for ($count = 1969; $count >= $year; $count--) {

                $leapyear = self::isYearLeapYear($count);
                if ($count > $year)
                {
                    $date += 365;
                    if ($leapyear === true)
                        $date++;
                } else {

                    for ($mcount = 11; $mcount > ($month - 1); $mcount--) {
                        $date += self::$_monthTable[$mcount];
                        if (($leapyear === true) and ($mcount == 2)) {
                            $date++;
                        }

                    }
                }
            }

            $date += (self::$_monthTable[$month - 1] - $day);
            $date = -(($date * 86400) + (86400 - (($hour * 3600) + ($minute * 60) + $second)));

            // gregorian correction for 5.Oct.1582
            if ($date < -12220185600) {
                $date += 864000;
            } else if ($date < -12219321600) {
                $date  = -12219321600;
            }
        }

        if (isset(self::$_cache)) {
            if (self::$_cacheTags) {
                self::$_cache->save( serialize($date), $id, array('Zend_Date'));
            } else {
                self::$_cache->save( serialize($date), $id);
            }
        }

        return $date;
    }

    /**
     * Returns true, if given $year is a leap year.
     *
     * @param  integer  $year
     * @return boolean  true, if year is leap year
     */
    protected static function isYearLeapYear($year)
    {
        // all leapyears can be divided through 4
        if (($year % 4) != 0) {
            return false;
        }

        // all leapyears can be divided through 400
        if ($year % 400 == 0) {
            return true;
        } else if (($year > 1582) and ($year % 100 == 0)) {
            return false;
        }

        return true;
    }

    /**
     * Internal mktime function used by Zend_Date for handling 64bit timestamps.
     *
     * Returns a formatted date for a given timestamp.
     *
     * @param  string   $format     format for output
     * @param  mixed    $timestamp
     * @param  boolean  $gmt        OPTIONAL true = other arguments are for UTC time, false = arguments are for local time/date
     * @return string
     */
    protected function date($format, $timestamp = null, $gmt = false)
    {
        $oldzone = @date_default_timezone_get();
        if ($this->_timezone != $oldzone) {
            date_default_timezone_set($this->_timezone);
        }

        if ($timestamp === null) {
            $result = ($gmt) ? @gmdate($format) : @date($format);
            date_default_timezone_set($oldzone);
            return $result;
        }

        if (abs($timestamp) <= 0x7FFFFFFF) {
            // See ZF-11992
            // "o" will sometimes resolve to the previous year (see 
            // http://php.net/date ; it's part of the ISO 8601 
            // standard). However, this is not desired, so replacing 
            // all occurrences of "o" not preceded by a backslash 
            // with "Y"
            $format = preg_replace('/(?<!\\\\)o/', 'Y', $format);
            $result = ($gmt) ? @gmdate($format, $timestamp) : @date($format, $timestamp);
            date_default_timezone_set($oldzone);
            return $result;
        }

        $jump      = false;
        $origstamp = $timestamp;
        if (isset(self::$_cache)) {
            $idstamp = strtr('Zend_DateObject_date_' . $this->_offset . '_'. $timestamp . '_'.(int)$gmt, '-','_');
            if ($result2 = self::$_cache->load($idstamp)) {
                $timestamp = unserialize($result2);
                $jump = true;
            }
        }

        // check on false or null alone fails
        if (empty($gmt) and empty($jump)) {
            $tempstamp = $timestamp;
            if ($tempstamp > 0) {
                while (abs($tempstamp) > 0x7FFFFFFF) {
                    $tempstamp -= (86400 * 23376);
                }

                $dst = date("I", $tempstamp);
                if ($dst === 1) {
                    $timestamp += 3600;
                }

                $temp       = date('Z', $tempstamp);
                $timestamp += $temp;
            }

            if (isset(self::$_cache)) {
                if (self::$_cacheTags) {
                    self::$_cache->save( serialize($timestamp), $idstamp, array('Zend_Date'));
                } else {
                    self::$_cache->save( serialize($timestamp), $idstamp);
                }
            }
        }

        if (($timestamp < 0) and ($gmt !== true)) {
            $timestamp -= $this->_offset;
        }

        date_default_timezone_set($oldzone);
        $date   = $this->getDateParts($timestamp, true);
        $length = strlen($format);
        $output = '';

        for ($i = 0; $i < $length; $i++) {
            switch($format[$i]) {
                // day formats
                case 'd':  // day of month, 2 digits, with leading zero, 01 - 31
                    $output .= (($date['mday'] < 10) ? '0' . $date['mday'] : $date['mday']);
                    break;

                case 'D':  // day of week, 3 letters, Mon - Sun
                    $output .= date('D', 86400 * (3 + self::dayOfWeek($date['year'], $date['mon'], $date['mday'])));
                    break;

                case 'j':  // day of month, without leading zero, 1 - 31
                    $output .= $date['mday'];
                    break;

                case 'l':  // day of week, full string name, Sunday - Saturday
                    $output .= date('l', 86400 * (3 + self::dayOfWeek($date['year'], $date['mon'], $date['mday'])));
                    break;

                case 'N':  // ISO 8601 numeric day of week, 1 - 7
                    $day = self::dayOfWeek($date['year'], $date['mon'], $date['mday']);
                    if ($day == 0) {
                        $day = 7;
                    }
                    $output .= $day;
                    break;

                case 'S':  // english suffix for day of month, st nd rd th
                    if (($date['mday'] % 10) == 1) {
                        $output .= 'st';
                    } else if ((($date['mday'] % 10) == 2) and ($date['mday'] != 12)) {
                        $output .= 'nd';
                    } else if (($date['mday'] % 10) == 3) {
                        $output .= 'rd';
                    } else {
                        $output .= 'th';
                    }
                    break;

                case 'w':  // numeric day of week, 0 - 6
                    $output .= self::dayOfWeek($date['year'], $date['mon'], $date['mday']);
                    break;

                case 'z':  // day of year, 0 - 365
                    $output .= $date['yday'];
                    break;


                // week formats
                case 'W':  // ISO 8601, week number of year
                    $output .= $this->weekNumber($date['year'], $date['mon'], $date['mday']);
                    break;


                // month formats
                case 'F':  // string month name, january - december
                    $output .= date('F', mktime(0, 0, 0, $date['mon'], 2, 1971));
                    break;

                case 'm':  // number of month, with leading zeros, 01 - 12
                    $output .= (($date['mon'] < 10) ? '0' . $date['mon'] : $date['mon']);
                    break;

                case 'M':  // 3 letter month name, Jan - Dec
                    $output .= date('M',mktime(0, 0, 0, $date['mon'], 2, 1971));
                    break;

                case 'n':  // number of month, without leading zeros, 1 - 12
                    $output .= $date['mon'];
                    break;

                case 't':  // number of day in month
                    $output .= self::$_monthTable[$date['mon'] - 1];
                    break;


                // year formats
                case 'L':  // is leap year ?
                    $output .= (self::isYearLeapYear($date['year'])) ? '1' : '0';
                    break;

                case 'o':  // ISO 8601 year number
                    $week = $this->weekNumber($date['year'], $date['mon'], $date['mday']);
                    if (($week > 50) and ($date['mon'] == 1)) {
                        $output .= ($date['year'] - 1);
                    } else {
                        $output .= $date['year'];
                    }
                    break;

                case 'Y':  // year number, 4 digits
                    $output .= $date['year'];
                    break;

                case 'y':  // year number, 2 digits
                    $output .= substr($date['year'], strlen($date['year']) - 2, 2);
                    break;


                // time formats
                case 'a':  // lower case am/pm
                    $output .= (($date['hours'] >= 12) ? 'pm' : 'am');
                    break;

                case 'A':  // upper case am/pm
                    $output .= (($date['hours'] >= 12) ? 'PM' : 'AM');
                    break;

                case 'B':  // swatch internet time
                    $dayseconds = ($date['hours'] * 3600) + ($date['minutes'] * 60) + $date['seconds'];
                    if ($gmt === true) {
                        $dayseconds += 3600;
                    }
                    $output .= (int) (($dayseconds % 86400) / 86.4);
                    break;

                case 'g':  // hours without leading zeros, 12h format
                    if ($date['hours'] > 12) {
                        $hour = $date['hours'] - 12;
                    } else {
                        if ($date['hours'] == 0) {
                            $hour = '12';
                        } else {
                            $hour = $date['hours'];
                        }
                    }
                    $output .= $hour;
                    break;

                case 'G':  // hours without leading zeros, 24h format
                    $output .= $date['hours'];
                    break;

                case 'h':  // hours with leading zeros, 12h format
                    if ($date['hours'] > 12) {
                        $hour = $date['hours'] - 12;
                    } else {
                        if ($date['hours'] == 0) {
                            $hour = '12';
                        } else {
                            $hour = $date['hours'];
                        }
                    }
                    $output .= (($hour < 10) ? '0'.$hour : $hour);
                    break;

                case 'H':  // hours with leading zeros, 24h format
                    $output .= (($date['hours'] < 10) ? '0' . $date['hours'] : $date['hours']);
                    break;

                case 'i':  // minutes with leading zeros
                    $output .= (($date['minutes'] < 10) ? '0' . $date['minutes'] : $date['minutes']);
                    break;

                case 's':  // seconds with leading zeros
                    $output .= (($date['seconds'] < 10) ? '0' . $date['seconds'] : $date['seconds']);
                    break;


                // timezone formats
                case 'e':  // timezone identifier
                    if ($gmt === true) {
                        $output .= gmdate('e', mktime($date['hours'], $date['minutes'], $date['seconds'],
                                                      $date['mon'], $date['mday'], 2000));
                    } else {
                        $output .=   date('e', mktime($date['hours'], $date['minutes'], $date['seconds'],
                                                      $date['mon'], $date['mday'], 2000));
                    }
                    break;

                case 'I':  // daylight saving time or not
                    if ($gmt === true) {
                        $output .= gmdate('I', mktime($date['hours'], $date['minutes'], $date['seconds'],
                                                      $date['mon'], $date['mday'], 2000));
                    } else {
                        $output .=   date('I', mktime($date['hours'], $date['minutes'], $date['seconds'],
                                                      $date['mon'], $date['mday'], 2000));
                    }
                    break;

                case 'O':  // difference to GMT in hours
                    $gmtstr = ($gmt === true) ? 0 : $this->getGmtOffset();
                    $output .= sprintf('%s%04d', ($gmtstr <= 0) ? '+' : '-', abs($gmtstr) / 36);
                    break;

                case 'P':  // difference to GMT with colon
                    $gmtstr = ($gmt === true) ? 0 : $this->getGmtOffset();
                    $gmtstr = sprintf('%s%04d', ($gmtstr <= 0) ? '+' : '-', abs($gmtstr) / 36);
                    $output = $output . substr($gmtstr, 0, 3) . ':' . substr($gmtstr, 3);
                    break;

                case 'T':  // timezone settings
                    if ($gmt === true) {
                        $output .= gmdate('T', mktime($date['hours'], $date['minutes'], $date['seconds'],
                                                      $date['mon'], $date['mday'], 2000));
                    } else {
                        $output .=   date('T', mktime($date['hours'], $date['minutes'], $date['seconds'],
                                                      $date['mon'], $date['mday'], 2000));
                    }
                    break;

                case 'Z':  // timezone offset in seconds
                    $output .= ($gmt === true) ? 0 : -$this->getGmtOffset();
                    break;


                // complete time formats
                case 'c':  // ISO 8601 date format
                    $difference = $this->getGmtOffset();
                    $difference = sprintf('%s%04d', ($difference <= 0) ? '+' : '-', abs($difference) / 36);
                    $difference = substr($difference, 0, 3) . ':' . substr($difference, 3);
                    $output .= $date['year'] . '-'
                             . (($date['mon']     < 10) ? '0' . $date['mon']     : $date['mon'])     . '-'
                             . (($date['mday']    < 10) ? '0' . $date['mday']    : $date['mday'])    . 'T'
                             . (($date['hours']   < 10) ? '0' . $date['hours']   : $date['hours'])   . ':'
                             . (($date['minutes'] < 10) ? '0' . $date['minutes'] : $date['minutes']) . ':'
                             . (($date['seconds'] < 10) ? '0' . $date['seconds'] : $date['seconds'])
                             . $difference;
                    break;

                case 'r':  // RFC 2822 date format
                    $difference = $this->getGmtOffset();
                    $difference = sprintf('%s%04d', ($difference <= 0) ? '+' : '-', abs($difference) / 36);
                    $output .= gmdate('D', 86400 * (3 + self::dayOfWeek($date['year'], $date['mon'], $date['mday']))) . ', '
                             . (($date['mday']    < 10) ? '0' . $date['mday']    : $date['mday'])    . ' '
                             . date('M', mktime(0, 0, 0, $date['mon'], 2, 1971)) . ' '
                             . $date['year'] . ' '
                             . (($date['hours']   < 10) ? '0' . $date['hours']   : $date['hours'])   . ':'
                             . (($date['minutes'] < 10) ? '0' . $date['minutes'] : $date['minutes']) . ':'
                             . (($date['seconds'] < 10) ? '0' . $date['seconds'] : $date['seconds']) . ' '
                             . $difference;
                    break;

                case 'U':  // Unix timestamp
                    $output .= $origstamp;
                    break;


                // special formats
                case "\\":  // next letter to print with no format
                    $i++;
                    if ($i < $length) {
                        $output .= $format[$i];
                    }
                    break;

                default:  // letter is no format so add it direct
                    $output .= $format[$i];
                    break;
            }
        }

        return (string) $output;
    }

    /**
     * Returns the day of week for a Gregorian calendar date.
     * 0 = sunday, 6 = saturday
     *
     * @param  integer  $year
     * @param  integer  $month
     * @param  integer  $day
     * @return integer  dayOfWeek
     */
    protected static function dayOfWeek($year, $month, $day)
    {
        if ((1901 < $year) and ($year < 2038)) {
            return (int) date('w', mktime(0, 0, 0, $month, $day, $year));
        }

        // gregorian correction
        $correction = 0;
        if (($year < 1582) or (($year == 1582) and (($month < 10) or (($month == 10) && ($day < 15))))) {
            $correction = 3;
        }

        if ($month > 2) {
            $month -= 2;
        } else {
            $month += 10;
            $year--;
        }

        $day  = floor((13 * $month - 1) / 5) + $day + ($year % 100) + floor(($year % 100) / 4);
        $day += floor(($year / 100) / 4) - 2 * floor($year / 100) + 77 + $correction;

        return (int) ($day - 7 * floor($day / 7));
    }

    /**
     * Internal getDateParts function for handling 64bit timestamps, similar to:
     * http://www.php.net/getdate
     *
     * Returns an array of date parts for $timestamp, relative to 1970/01/01 00:00:00 GMT/UTC.
     *
     * $fast specifies ALL date parts should be returned (slower)
     * Default is false, and excludes $dayofweek, weekday, month and timestamp from parts returned.
     *
     * @param   mixed    $timestamp
     * @param   boolean  $fast   OPTIONAL defaults to fast (false), resulting in fewer date parts
     * @return  array
     */
    protected function getDateParts($timestamp = null, $fast = null)
    {

        // actual timestamp
        if (!is_numeric($timestamp)) {
            return getdate();
        }

        // 32bit timestamp
        if (abs($timestamp) <= 0x7FFFFFFF) {
            return @getdate((int) $timestamp);
        }

        if (isset(self::$_cache)) {
            $id = strtr('Zend_DateObject_getDateParts_' . $timestamp.'_'.(int)$fast, '-','_');
            if ($result = self::$_cache->load($id)) {
                return unserialize($result);
            }
        }

        $otimestamp = $timestamp;
        $numday = 0;
        $month = 0;
        // gregorian correction
        if ($timestamp < -12219321600) {
            $timestamp -= 864000;
        }

        // timestamp lower 0
        if ($timestamp < 0) {
            $sec = 0;
            $act = 1970;

            // iterate through 10 years table, increasing speed
            foreach(self::$_yearTable as $year => $seconds) {
                if ($timestamp >= $seconds) {
                    $i = $act;
                    break;
                }
                $sec = $seconds;
                $act = $year;
            }

            $timestamp -= $sec;
            if (!isset($i)) {
                $i = $act;
            }

            // iterate the max last 10 years
            do {
                --$i;
                $day = $timestamp;

                $timestamp += 31536000;
                $leapyear = self::isYearLeapYear($i);
                if ($leapyear === true) {
                    $timestamp += 86400;
                }

                if ($timestamp >= 0) {
                    $year = $i;
                    break;
                }
            } while ($timestamp < 0);

            $secondsPerYear = 86400 * ($leapyear ? 366 : 365) + $day;

            $timestamp = $day;
            // iterate through months
            for ($i = 12; --$i >= 0;) {
                $day = $timestamp;

                $timestamp += self::$_monthTable[$i] * 86400;
                if (($leapyear === true) and ($i == 1)) {
                    $timestamp += 86400;
                }

                if ($timestamp >= 0) {
                    $month  = $i;
                    $numday = self::$_monthTable[$i];
                    if (($leapyear === true) and ($i == 1)) {
                        ++$numday;
                    }
                    break;
                }
            }

            $timestamp  = $day;
            $numberdays = $numday + ceil(($timestamp + 1) / 86400);

            $timestamp += ($numday - $numberdays + 1) * 86400;
            $hours      = floor($timestamp / 3600);
        } else {

            // iterate through years
            for ($i = 1970;;$i++) {
                $day = $timestamp;

                $timestamp -= 31536000;
                $leapyear = self::isYearLeapYear($i);
                if ($leapyear === true) {
                    $timestamp -= 86400;
                }

                if ($timestamp < 0) {
                    $year = $i;
                    break;
                }
            }

            $secondsPerYear = $day;

            $timestamp = $day;
            // iterate through months
            for ($i = 0; $i <= 11; $i++) {
                $day = $timestamp;
                $timestamp -= self::$_monthTable[$i] * 86400;

                if (($leapyear === true) and ($i == 1)) {
                    $timestamp -= 86400;
                }

                if ($timestamp < 0) {
                    $month  = $i;
                    $numday = self::$_monthTable[$i];
                    if (($leapyear === true) and ($i == 1)) {
                        ++$numday;
                    }
                    break;
                }
            }

            $timestamp  = $day;
            $numberdays = ceil(($timestamp + 1) / 86400);
            $timestamp  = $timestamp - ($numberdays - 1) * 86400;
            $hours = floor($timestamp / 3600);
        }

        $timestamp -= $hours * 3600;

        $month  += 1;
        $minutes = floor($timestamp / 60);
        $seconds = $timestamp - $minutes * 60;

        if ($fast === true) {
            $array = array(
                'seconds' => $seconds,
                'minutes' => $minutes,
                'hours'   => $hours,
                'mday'    => $numberdays,
                'mon'     => $month,
                'year'    => $year,
                'yday'    => floor($secondsPerYear / 86400),
            );
        } else {

            $dayofweek = self::dayOfWeek($year, $month, $numberdays);
            $array = array(
                    'seconds' => $seconds,
                    'minutes' => $minutes,
                    'hours'   => $hours,
                    'mday'    => $numberdays,
                    'wday'    => $dayofweek,
                    'mon'     => $month,
                    'year'    => $year,
                    'yday'    => floor($secondsPerYear / 86400),
                    'weekday' => gmdate('l', 86400 * (3 + $dayofweek)),
                    'month'   => gmdate('F', mktime(0, 0, 0, $month, 1, 1971)),
                    0         => $otimestamp
            );
        }

        if (isset(self::$_cache)) {
            if (self::$_cacheTags) {
                self::$_cache->save( serialize($array), $id, array('Zend_Date'));
            } else {
                self::$_cache->save( serialize($array), $id);
            }
        }

        return $array;
    }

    /**
     * Internal getWeekNumber function for handling 64bit timestamps
     *
     * Returns the ISO 8601 week number of a given date
     *
     * @param  integer  $year
     * @param  integer  $month
     * @param  integer  $day
     * @return integer
     */
    protected function weekNumber($year, $month, $day)
    {
        if ((1901 < $year) and ($year < 2038)) {
            return (int) date('W', mktime(0, 0, 0, $month, $day, $year));
        }

        $dayofweek = self::dayOfWeek($year, $month, $day);
        $firstday  = self::dayOfWeek($year, 1, 1);
        if (($month == 1) and (($firstday < 1) or ($firstday > 4)) and ($day < 4)) {
            $firstday  = self::dayOfWeek($year - 1, 1, 1);
            $month     = 12;
            $day       = 31;

        } else if (($month == 12) and ((self::dayOfWeek($year + 1, 1, 1) < 5) and
                   (self::dayOfWeek($year + 1, 1, 1) > 0))) {
            return 1;
        }

        return intval (((self::dayOfWeek($year, 1, 1) < 5) and (self::dayOfWeek($year, 1, 1) > 0)) +
               4 * ($month - 1) + (2 * ($month - 1) + ($day - 1) + $firstday - $dayofweek + 6) * 36 / 256);
    }

    /**
     * Internal _range function
     * Sets the value $a to be in the range of [0, $b]
     *
     * @param float $a - value to correct
     * @param float $b - maximum range to set
     */
    private function _range($a, $b) {
        while ($a < 0) {
            $a += $b;
        }
        while ($a >= $b) {
            $a -= $b;
        }
        return $a;
    }

    /**
     * Calculates the sunrise or sunset based on a location
     *
     * @param  array  $location  Location for calculation MUST include 'latitude', 'longitude', 'horizon'
     * @param  bool   $horizon   true: sunrise; false: sunset
     * @return mixed  - false: midnight sun, integer:
     */
    protected function calcSun($location, $horizon, $rise = false)
    {
        // timestamp within 32bit
        if (abs($this->_unixTimestamp) <= 0x7FFFFFFF) {
            if ($rise === false) {
                return date_sunset($this->_unixTimestamp, SUNFUNCS_RET_TIMESTAMP, $location['latitude'],
                                   $location['longitude'], 90 + $horizon, $this->getGmtOffset() / 3600);
            }
            return date_sunrise($this->_unixTimestamp, SUNFUNCS_RET_TIMESTAMP, $location['latitude'],
                                $location['longitude'], 90 + $horizon, $this->getGmtOffset() / 3600);
        }

        // self calculation - timestamp bigger than 32bit
        // fix circle values
        $quarterCircle      = 0.5 * M_PI;
        $halfCircle         =       M_PI;
        $threeQuarterCircle = 1.5 * M_PI;
        $fullCircle         = 2   * M_PI;

        // radiant conversion for coordinates
        $radLatitude  = $location['latitude']   * $halfCircle / 180;
        $radLongitude = $location['longitude']  * $halfCircle / 180;

        // get solar coordinates
        $tmpRise       = $rise ? $quarterCircle : $threeQuarterCircle;
        $radDay        = $this->date('z',$this->_unixTimestamp) + ($tmpRise - $radLongitude) / $fullCircle;

        // solar anomoly and longitude
        $solAnomoly    = $radDay * 0.017202 - 0.0574039;
        $solLongitude  = $solAnomoly + 0.0334405 * sin($solAnomoly);
        $solLongitude += 4.93289 + 3.49066E-4 * sin(2 * $solAnomoly);

        // get quadrant
        $solLongitude = $this->_range($solLongitude, $fullCircle);

        if (($solLongitude / $quarterCircle) - intval($solLongitude / $quarterCircle) == 0) {
            $solLongitude += 4.84814E-6;
        }

        // solar ascension
        $solAscension = sin($solLongitude) / cos($solLongitude);
        $solAscension = atan2(0.91746 * $solAscension, 1);

        // adjust quadrant
        if ($solLongitude > $threeQuarterCircle) {
            $solAscension += $fullCircle;
        } else if ($solLongitude > $quarterCircle) {
            $solAscension += $halfCircle;
        }

        // solar declination
        $solDeclination  = 0.39782 * sin($solLongitude);
        $solDeclination /=  sqrt(-$solDeclination * $solDeclination + 1);
        $solDeclination  = atan2($solDeclination, 1);

        $solHorizon = $horizon - sin($solDeclination) * sin($radLatitude);
        $solHorizon /= cos($solDeclination) * cos($radLatitude);

        // midnight sun, always night
        if (abs($solHorizon) > 1) {
            return false;
        }

        $solHorizon /= sqrt(-$solHorizon * $solHorizon + 1);
        $solHorizon  = $quarterCircle - atan2($solHorizon, 1);

        if ($rise) {
            $solHorizon = $fullCircle - $solHorizon;
        }

        // time calculation
        $localTime     = $solHorizon + $solAscension - 0.0172028 * $radDay - 1.73364;
        $universalTime = $localTime - $radLongitude;

        // determinate quadrant
        $universalTime = $this->_range($universalTime, $fullCircle);

        // radiant to hours
        $universalTime *= 24 / $fullCircle;

        // convert to time
        $hour = intval($universalTime);
        $universalTime    = ($universalTime - $hour) * 60;
        $min  = intval($universalTime);
        $universalTime    = ($universalTime - $min) * 60;
        $sec  = intval($universalTime);

        return $this->mktime($hour, $min, $sec, $this->date('m', $this->_unixTimestamp),
                             $this->date('j', $this->_unixTimestamp), $this->date('Y', $this->_unixTimestamp),
                             -1, true);
    }

    /**
     * Sets a new timezone for calculation of $this object's gmt offset.
     * For a list of supported timezones look here: http://php.net/timezones
     * If no timezone can be detected or the given timezone is wrong UTC will be set.
     *
     * @param  string  $zone      OPTIONAL timezone for date calculation; defaults to date_default_timezone_get()
     * @return Zend_Date_DateObject Provides fluent interface
     * @throws Zend_Date_Exception
     */
    public function setTimezone($zone = null)
    {
        $oldzone = @date_default_timezone_get();
        if ($zone === null) {
            $zone = $oldzone;
        }

        // throw an error on false input, but only if the new date extension is available
        if (function_exists('timezone_open')) {
            if (!@timezone_open($zone)) {
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("timezone ($zone) is not a known timezone", 0, null, $zone);
            }
        }
        // this can generate an error if the date extension is not available and a false timezone is given
        $result = @date_default_timezone_set($zone);
        if ($result === true) {
            $this->_offset   = mktime(0, 0, 0, 1, 2, 1970) - gmmktime(0, 0, 0, 1, 2, 1970);
            $this->_timezone = $zone;
        }
        date_default_timezone_set($oldzone);

        if (($zone == 'UTC') or ($zone == 'GMT')) {
            $this->_dst = false;
        } else {
            $this->_dst = true;
        }

        return $this;
    }

    /**
     * Return the timezone of $this object.
     * The timezone is initially set when the object is instantiated.
     *
     * @return  string  actual set timezone string
     */
    public function getTimezone()
    {
        return $this->_timezone;
    }

    /**
     * Return the offset to GMT of $this object's timezone.
     * The offset to GMT is initially set when the object is instantiated using the currently,
     * in effect, default timezone for PHP functions.
     *
     * @return  integer  seconds difference between GMT timezone and timezone when object was instantiated
     */
    public function getGmtOffset()
    {
        $date   = $this->getDateParts($this->getUnixTimestamp(), true);
        $zone   = @date_default_timezone_get();
        $result = @date_default_timezone_set($this->_timezone);
        if ($result === true) {
            $offset = $this->mktime($date['hours'], $date['minutes'], $date['seconds'],
                                    $date['mon'], $date['mday'], $date['year'], false)
                    - $this->mktime($date['hours'], $date['minutes'], $date['seconds'],
                                    $date['mon'], $date['mday'], $date['year'], true);
        }
        date_default_timezone_set($zone);

        return $offset;
    }

    /**
     * Internal method to check if the given cache supports tags
     *
     * @param Zend_Cache $cache
     */
    protected static function _getTagSupportForCache()
    {
        $backend = self::$_cache->getBackend();
        if ($backend instanceof Zend_Cache_Backend_ExtendedInterface) {
            $cacheOptions = $backend->getCapabilities();
            self::$_cacheTags = $cacheOptions['tags'];
        } else {
            self::$_cacheTags = false;
        }

        return self::$_cacheTags;
    }
}
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Date
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * Include needed Date classes
 */
#require_once 'Zend/Date/DateObject.php';
#require_once 'Zend/Locale.php';
#require_once 'Zend/Locale/Format.php';
#require_once 'Zend/Locale/Math.php';

/**
 * This class replaces default Zend_Date because of problem described in Jira ticket MAGE-4872
 * The only difference between current class and original one is overwritten implementation of mktime method
 *
 * @category  Zend
 * @package   Zend_Date
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Date extends Zend_Date_DateObject
{
    private $_locale  = null;

    // Fractional second variables
    private $_fractional = 0;
    private $_precision  = 3;

    private static $_options = array(
        'format_type'  => 'iso',      // format for date strings 'iso' or 'php'
        'fix_dst'      => true,       // fix dst on summer/winter time change
        'extend_month' => false,      // false - addMonth like SQL, true like excel
        'cache'        => null,       // cache to set
        'timesync'     => null        // timesync server to set
    );

    // Class wide Date Constants
    const DAY               = 'dd';
    const DAY_SHORT         = 'd';
    const DAY_SUFFIX        = 'SS';
    const DAY_OF_YEAR       = 'D';
    const WEEKDAY           = 'EEEE';
    const WEEKDAY_SHORT     = 'EEE';
    const WEEKDAY_NARROW    = 'E';
    const WEEKDAY_NAME      = 'EE';
    const WEEKDAY_8601      = 'eee';
    const WEEKDAY_DIGIT     = 'e';
    const WEEK              = 'ww';
    const MONTH             = 'MM';
    const MONTH_SHORT       = 'M';
    const MONTH_DAYS        = 'ddd';
    const MONTH_NAME        = 'MMMM';
    const MONTH_NAME_SHORT  = 'MMM';
    const MONTH_NAME_NARROW = 'MMMMM';
    const YEAR              = 'y';
    const YEAR_SHORT        = 'yy';
    const YEAR_8601         = 'Y';
    const YEAR_SHORT_8601   = 'YY';
    const LEAPYEAR          = 'l';
    const MERIDIEM          = 'a';
    const SWATCH            = 'B';
    const HOUR              = 'HH';
    const HOUR_SHORT        = 'H';
    const HOUR_AM           = 'hh';
    const HOUR_SHORT_AM     = 'h';
    const MINUTE            = 'mm';
    const MINUTE_SHORT      = 'm';
    const SECOND            = 'ss';
    const SECOND_SHORT      = 's';
    const MILLISECOND       = 'S';
    const TIMEZONE_NAME     = 'zzzz';
    const DAYLIGHT          = 'I';
    const GMT_DIFF          = 'Z';
    const GMT_DIFF_SEP      = 'ZZZZ';
    const TIMEZONE          = 'z';
    const TIMEZONE_SECS     = 'X';
    const ISO_8601          = 'c';
    const RFC_2822          = 'r';
    const TIMESTAMP         = 'U';
    const ERA               = 'G';
    const ERA_NAME          = 'GGGG';
    const ERA_NARROW        = 'GGGGG';
    const DATES             = 'F';
    const DATE_FULL         = 'FFFFF';
    const DATE_LONG         = 'FFFF';
    const DATE_MEDIUM       = 'FFF';
    const DATE_SHORT        = 'FF';
    const TIMES             = 'WW';
    const TIME_FULL         = 'TTTTT';
    const TIME_LONG         = 'TTTT';
    const TIME_MEDIUM       = 'TTT';
    const TIME_SHORT        = 'TT';
    const DATETIME          = 'K';
    const DATETIME_FULL     = 'KKKKK';
    const DATETIME_LONG     = 'KKKK';
    const DATETIME_MEDIUM   = 'KKK';
    const DATETIME_SHORT    = 'KK';
    const ATOM              = 'OOO';
    const COOKIE            = 'CCC';
    const RFC_822           = 'R';
    const RFC_850           = 'RR';
    const RFC_1036          = 'RRR';
    const RFC_1123          = 'RRRR';
    const RFC_3339          = 'RRRRR';
    const RSS               = 'SSS';
    const W3C               = 'WWW';

    /**
     * Minimum allowed year value
     */
    const YEAR_MIN_VALUE = -10000;

    /**
     * Maximum allowed year value
     */
    const YEAR_MAX_VALUE = 10000;

    /**
     * Generates the standard date object, could be a unix timestamp, localized date,
     * string, integer, array and so on. Also parts of dates or time are supported
     * Always set the default timezone: http://php.net/date_default_timezone_set
     * For example, in your bootstrap: date_default_timezone_set('America/Los_Angeles');
     * For detailed instructions please look in the docu.
     *
     * @param  string|integer|Zend_Date|array  $date    OPTIONAL Date value or value of date part to set
     *                                                 ,depending on $part. If null the actual time is set
     * @param  string                          $part    OPTIONAL Defines the input format of $date
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function __construct($date = null, $part = null, $locale = null)
    {
        if (is_object($date) and !($date instanceof Zend_TimeSync_Protocol) and
            !($date instanceof Zend_Date)) {
            if ($locale instanceof Zend_Locale) {
                $locale = $date;
                $date   = null;
                $part   = null;
            } else {
                $date = (string) $date;
            }
        }

        if (($date !== null) and !is_array($date) and !($date instanceof Zend_TimeSync_Protocol) and
            !($date instanceof Zend_Date) and !defined($date) and Zend_Locale::isLocale($date, true, false)) {
            $locale = $date;
            $date   = null;
            $part   = null;
        } else if (($part !== null) and !defined($part) and Zend_Locale::isLocale($part, true, false)) {
            $locale = $part;
            $part   = null;
        }

        $this->setLocale($locale);
        if (is_string($date) && ($part === null) && (strlen($date) <= 5)) {
            $part = $date;
            $date = null;
        }

        if ($date === null) {
            if ($part === null) {
                $date = time();
            } else if ($part !== self::TIMESTAMP) {
                $date = self::now($locale);
                $date = $date->get($part);
            }
        }

        if ($date instanceof Zend_TimeSync_Protocol) {
            $date = $date->getInfo();
            $date = $this->_getTime($date['offset']);
            $part = null;
        } else if (parent::$_defaultOffset != 0) {
            $date = $this->_getTime(parent::$_defaultOffset);
        }

        // set the timezone and offset for $this
        $zone = @date_default_timezone_get();
        $this->setTimezone($zone);

        // try to get timezone from date-string
        if (!is_int($date)) {
            $zone = $this->getTimezoneFromString($date);
            $this->setTimezone($zone);
        }

        // set datepart
        if (($part !== null && $part !== self::TIMESTAMP) or (!is_numeric($date))) {
            // switch off dst handling for value setting
            $this->setUnixTimestamp($this->getGmtOffset());
            $this->set($date, $part, $this->_locale);

            // DST fix
            if (is_array($date) === true) {
                if (!isset($date['hour'])) {
                    $date['hour'] = 0;
                }

                $hour = $this->toString('H', 'iso', true);
                $hour = $date['hour'] - $hour;
                switch ($hour) {
                    case 1 :
                    case -23 :
                        $this->addTimestamp(3600);
                        break;
                    case -1 :
                    case 23 :
                        $this->subTimestamp(3600);
                        break;
                    case 2 :
                    case -22 :
                        $this->addTimestamp(7200);
                        break;
                    case -2 :
                    case 22 :
                        $this->subTimestamp(7200);
                        break;
                }
            }
        } else {
            $this->setUnixTimestamp($date);
        }
    }

    /**
     * Sets class wide options, if no option was given, the actual set options will be returned
     *
     * @param  array  $options  Options to set
     * @throws Zend_Date_Exception
     * @return Options array if no option was given
     */
    public static function setOptions(array $options = array())
    {
        if (empty($options)) {
            return self::$_options;
        }

        foreach ($options as $name => $value) {
            $name  = strtolower($name);

            if (array_key_exists($name, self::$_options)) {
                switch($name) {
                    case 'format_type' :
                        if ((strtolower($value) != 'php') && (strtolower($value) != 'iso')) {
                            #require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception("Unknown format type ($value) for dates, only 'iso' and 'php' supported", 0, null, $value);
                        }
                        break;
                    case 'fix_dst' :
                        if (!is_bool($value)) {
                            #require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception("'fix_dst' has to be boolean", 0, null, $value);
                        }
                        break;
                    case 'extend_month' :
                        if (!is_bool($value)) {
                            #require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception("'extend_month' has to be boolean", 0, null, $value);
                        }
                        break;
                    case 'cache' :
                        if ($value === null) {
                            parent::$_cache = null;
                        } else {
                            if (!$value instanceof Zend_Cache_Core) {
                                #require_once 'Zend/Date/Exception.php';
                                throw new Zend_Date_Exception("Instance of Zend_Cache expected");
                            }

                            parent::$_cache = $value;
                            parent::$_cacheTags = Zend_Date_DateObject::_getTagSupportForCache();
                            Zend_Locale_Data::setCache($value);
                        }
                        break;
                    case 'timesync' :
                        if ($value === null) {
                            parent::$_defaultOffset = 0;
                        } else {
                            if (!$value instanceof Zend_TimeSync_Protocol) {
                                #require_once 'Zend/Date/Exception.php';
                                throw new Zend_Date_Exception("Instance of Zend_TimeSync expected");
                            }

                            $date = $value->getInfo();
                            parent::$_defaultOffset = $date['offset'];
                        }
                        break;
                }
                self::$_options[$name] = $value;
            }
            else {
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("Unknown option: $name = $value");
            }
        }
    }

    /**
     * Returns this object's internal UNIX timestamp (equivalent to Zend_Date::TIMESTAMP).
     * If the timestamp is too large for integers, then the return value will be a string.
     * This function does not return the timestamp as an object.
     * Use clone() or copyPart() instead.
     *
     * @return integer|string  UNIX timestamp
     */
    public function getTimestamp()
    {
        return $this->getUnixTimestamp();
    }

    /**
     * Returns the calculated timestamp
     * HINT: timestamps are always GMT
     *
     * @param  string                          $calc    Type of calculation to make
     * @param  string|integer|array|Zend_Date  $stamp   Timestamp to calculate, when null the actual timestamp is calculated
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _timestamp($calc, $stamp)
    {
        if ($stamp instanceof Zend_Date) {
            // extract timestamp from object
            $stamp = $stamp->getTimestamp();
        }

        if (is_array($stamp)) {
            if (isset($stamp['timestamp']) === true) {
                $stamp = $stamp['timestamp'];
            } else {
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('no timestamp given in array');
            }
        }

        if ($calc === 'set') {
            $return = $this->setUnixTimestamp($stamp);
        } else {
            $return = $this->_calcdetail($calc, $stamp, self::TIMESTAMP, null);
        }
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }

    /**
     * Sets a new timestamp
     *
     * @param  integer|string|array|Zend_Date  $timestamp  Timestamp to set
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setTimestamp($timestamp)
    {
        return $this->_timestamp('set', $timestamp);
    }

    /**
     * Adds a timestamp
     *
     * @param  integer|string|array|Zend_Date  $timestamp  Timestamp to add
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addTimestamp($timestamp)
    {
        return $this->_timestamp('add', $timestamp);
    }

    /**
     * Subtracts a timestamp
     *
     * @param  integer|string|array|Zend_Date  $timestamp  Timestamp to sub
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subTimestamp($timestamp)
    {
        return $this->_timestamp('sub', $timestamp);
    }

    /**
     * Compares two timestamps, returning the difference as integer
     *
     * @param  integer|string|array|Zend_Date  $timestamp  Timestamp to compare
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareTimestamp($timestamp)
    {
        return $this->_timestamp('cmp', $timestamp);
    }

    /**
     * Returns a string representation of the object
     * Supported format tokens are:
     * G - era, y - year, Y - ISO year, M - month, w - week of year, D - day of year, d - day of month
     * E - day of week, e - number of weekday (1-7), h - hour 1-12, H - hour 0-23, m - minute, s - second
     * A - milliseconds of day, z - timezone, Z - timezone offset, S - fractional second, a - period of day
     *
     * Additionally format tokens but non ISO conform are:
     * SS - day suffix, eee - php number of weekday(0-6), ddd - number of days per month
     * l - Leap year, B - swatch internet time, I - daylight saving time, X - timezone offset in seconds
     * r - RFC2822 format, U - unix timestamp
     *
     * Not supported ISO tokens are
     * u - extended year, Q - quarter, q - quarter, L - stand alone month, W - week of month
     * F - day of week of month, g - modified julian, c - stand alone weekday, k - hour 0-11, K - hour 1-24
     * v - wall zone
     *
     * @param  string              $format  OPTIONAL Rule for formatting output. If null the default date format is used
     * @param  string              $type    OPTIONAL Type for the format string which overrides the standard setting
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function toString($format = null, $type = null, $locale = null)
    {
        if (is_object($format)) {
            if ($format instanceof Zend_Locale) {
                $locale = $format;
                $format = null;
            } else {
                $format = (string) $format;
            }
        }

        if (is_object($type)) {
            if ($type instanceof Zend_Locale) {
                $locale = $type;
                $type   = null;
            } else {
                $type = (string) $type;
            }
        }

        if (($format !== null) && !defined($format)
            && ($format != 'ee') && ($format != 'ss') && ($format != 'GG') && ($format != 'MM') && ($format != 'EE') && ($format != 'TT')
            && Zend_Locale::isLocale($format, null, false)) {
            $locale = $format;
            $format = null;
        }

        if (($type !== null) and ($type != 'php') and ($type != 'iso') and
            Zend_Locale::isLocale($type, null, false)) {
            $locale = $type;
            $type = null;
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($format === null) {
            $format = Zend_Locale_Format::getDateFormat($locale) . ' ' . Zend_Locale_Format::getTimeFormat($locale);
        } else if (((self::$_options['format_type'] == 'php') && ($type === null)) or ($type == 'php')) {
            $format = Zend_Locale_Format::convertPhpToIsoFormat($format);
        }

        return $this->date($this->_toToken($format, $locale), $this->getUnixTimestamp(), false);
    }

    /**
     * Returns a string representation of the date which is equal with the timestamp
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString(null, $this->_locale);
    }

    /**
     * Returns a integer representation of the object
     * But returns false when the given part is no value f.e. Month-Name
     *
     * @param  string|integer|Zend_Date  $part  OPTIONAL Defines the date or datepart to return as integer
     * @return integer|false
     */
    public function toValue($part = null)
    {
        $result = $this->get($part);
        if (is_numeric($result)) {
            return intval("$result");
        } else {
            return false;
        }
    }

    /**
     * Returns an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        return array('day'       => $this->toString(self::DAY_SHORT, 'iso'),
            'month'     => $this->toString(self::MONTH_SHORT, 'iso'),
            'year'      => $this->toString(self::YEAR, 'iso'),
            'hour'      => $this->toString(self::HOUR_SHORT, 'iso'),
            'minute'    => $this->toString(self::MINUTE_SHORT, 'iso'),
            'second'    => $this->toString(self::SECOND_SHORT, 'iso'),
            'timezone'  => $this->toString(self::TIMEZONE, 'iso'),
            'timestamp' => $this->toString(self::TIMESTAMP, 'iso'),
            'weekday'   => $this->toString(self::WEEKDAY_8601, 'iso'),
            'dayofyear' => $this->toString(self::DAY_OF_YEAR, 'iso'),
            'week'      => $this->toString(self::WEEK, 'iso'),
            'gmtsecs'   => $this->toString(self::TIMEZONE_SECS, 'iso'));
    }

    /**
     * Returns a representation of a date or datepart
     * This could be for example a localized monthname, the time without date,
     * the era or only the fractional seconds. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string              $part    OPTIONAL Part of the date to return, if null the timestamp is returned
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string  date or datepart
     */
    public function get($part = null, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if (($part !== null) && !defined($part)
            && ($part != 'ee') && ($part != 'ss') && ($part != 'GG') && ($part != 'MM') && ($part != 'EE') && ($part != 'TT')
            && Zend_Locale::isLocale($part, null, false)) {
            $locale = $part;
            $part = null;
        }

        if ($part === null) {
            $part = self::TIMESTAMP;
        } else if (self::$_options['format_type'] == 'php') {
            $part = Zend_Locale_Format::convertPhpToIsoFormat($part);
        }

        return $this->date($this->_toToken($part, $locale), $this->getUnixTimestamp(), false);
    }

    /**
     * Internal method to apply tokens
     *
     * @param string $part
     * @param string $locale
     * @return string
     */
    private function _toToken($part, $locale) {
        // get format tokens
        $comment = false;
        $format  = '';
        $orig    = '';
        for ($i = 0; isset($part[$i]); ++$i) {
            if ($part[$i] == "'") {
                $comment = $comment ? false : true;
                if (isset($part[$i+1]) && ($part[$i+1] == "'")) {
                    $comment = $comment ? false : true;
                    $format .= "\\'";
                    ++$i;
                }

                $orig = '';
                continue;
            }

            if ($comment) {
                $format .= '\\' . $part[$i];
                $orig = '';
            } else {
                $orig .= $part[$i];
                if (!isset($part[$i+1]) || (isset($orig[0]) && ($orig[0] != $part[$i+1]))) {
                    $format .= $this->_parseIsoToDate($orig, $locale);
                    $orig  = '';
                }
            }
        }

        return $format;
    }

    /**
     * Internal parsing method
     *
     * @param string $token
     * @param string $locale
     * @return string
     */
    private function _parseIsoToDate($token, $locale) {
        switch($token) {
            case self::DAY :
                return 'd';
                break;

            case self::WEEKDAY_SHORT :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), false));
                $day     = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'format', 'wide', $weekday));
                return $this->_toComment(iconv_substr($day, 0, 3, 'UTF-8'));
                break;

            case self::DAY_SHORT :
                return 'j';
                break;

            case self::WEEKDAY :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), false));
                return $this->_toComment(Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'format', 'wide', $weekday)));
                break;

            case self::WEEKDAY_8601 :
                return 'N';
                break;

            case 'ee' :
                return $this->_toComment(str_pad($this->date('N', $this->getUnixTimestamp(), false), 2, '0', STR_PAD_LEFT));
                break;

            case self::DAY_SUFFIX :
                return 'S';
                break;

            case self::WEEKDAY_DIGIT :
                return 'w';
                break;

            case self::DAY_OF_YEAR :
                return 'z';
                break;

            case 'DDD' :
                return $this->_toComment(str_pad($this->date('z', $this->getUnixTimestamp(), false), 3, '0', STR_PAD_LEFT));
                break;

            case 'DD' :
                return $this->_toComment(str_pad($this->date('z', $this->getUnixTimestamp(), false), 2, '0', STR_PAD_LEFT));
                break;

            case self::WEEKDAY_NARROW :
            case 'EEEEE' :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), false));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'format', 'abbreviated', $weekday));
                return $this->_toComment(iconv_substr($day, 0, 1, 'UTF-8'));
                break;

            case self::WEEKDAY_NAME :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), false));
                return $this->_toComment(Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'format', 'abbreviated', $weekday)));
                break;

            case 'w' :
                $week = $this->date('W', $this->getUnixTimestamp(), false);
                return $this->_toComment(($week[0] == '0') ? $week[1] : $week);
                break;

            case self::WEEK :
                return 'W';
                break;

            case self::MONTH_NAME :
                $month = $this->date('n', $this->getUnixTimestamp(), false);
                return $this->_toComment(Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'wide', $month)));
                break;

            case self::MONTH :
                return 'm';
                break;

            case self::MONTH_NAME_SHORT :
                $month = $this->date('n', $this->getUnixTimestamp(), false);
                return $this->_toComment(Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month)));
                break;

            case self::MONTH_SHORT :
                return 'n';
                break;

            case self::MONTH_DAYS :
                return 't';
                break;

            case self::MONTH_NAME_NARROW :
                $month = $this->date('n', $this->getUnixTimestamp(), false);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                return $this->_toComment(iconv_substr($mon, 0, 1, 'UTF-8'));
                break;

            case self::LEAPYEAR :
                return 'L';
                break;

            case self::YEAR_8601 :
                return 'o';
                break;

            case self::YEAR :
                return 'Y';
                break;

            case self::YEAR_SHORT :
                return 'y';
                break;

            case self::YEAR_SHORT_8601 :
                return $this->_toComment(substr($this->date('o', $this->getUnixTimestamp(), false), -2, 2));
                break;

            case self::MERIDIEM :
                $am = $this->date('a', $this->getUnixTimestamp(), false);
                if ($am == 'am') {
                    return $this->_toComment(Zend_Locale_Data::getContent($locale, 'am'));
                }

                return $this->_toComment(Zend_Locale_Data::getContent($locale, 'pm'));
                break;

            case self::SWATCH :
                return 'B';
                break;

            case self::HOUR_SHORT_AM :
                return 'g';
                break;

            case self::HOUR_SHORT :
                return 'G';
                break;

            case self::HOUR_AM :
                return 'h';
                break;

            case self::HOUR :
                return 'H';
                break;

            case self::MINUTE :
                return $this->_toComment(str_pad($this->date('i', $this->getUnixTimestamp(), false), 2, '0', STR_PAD_LEFT));
                break;

            case self::SECOND :
                return $this->_toComment(str_pad($this->date('s', $this->getUnixTimestamp(), false), 2, '0', STR_PAD_LEFT));
                break;

            case self::MINUTE_SHORT :
                return 'i';
                break;

            case self::SECOND_SHORT :
                return 's';
                break;

            case self::MILLISECOND :
                return $this->_toComment($this->getMilliSecond());
                break;

            case self::TIMEZONE_NAME :
            case 'vvvv' :
                return 'e';
                break;

            case self::DAYLIGHT :
                return 'I';
                break;

            case self::GMT_DIFF :
            case 'ZZ' :
            case 'ZZZ' :
                return 'O';
                break;

            case self::GMT_DIFF_SEP :
                return 'P';
                break;

            case self::TIMEZONE :
            case 'v' :
            case 'zz' :
            case 'zzz' :
                return 'T';
                break;

            case self::TIMEZONE_SECS :
                return 'Z';
                break;

            case self::ISO_8601 :
                return 'c';
                break;

            case self::RFC_2822 :
                return 'r';
                break;

            case self::TIMESTAMP :
                return 'U';
                break;

            case self::ERA :
            case 'GG' :
            case 'GGG' :
                $year = $this->date('Y', $this->getUnixTimestamp(), false);
                if ($year < 0) {
                    return $this->_toComment(Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Abbr', '0')));
                }

                return $this->_toComment(Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Abbr', '1')));
                break;

            case self::ERA_NARROW :
                $year = $this->date('Y', $this->getUnixTimestamp(), false);
                if ($year < 0) {
                    return $this->_toComment(iconv_substr(Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Abbr', '0')), 0, 1, 'UTF-8')) . '.';
                }

                return $this->_toComment(iconv_substr(Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Abbr', '1')), 0, 1, 'UTF-8')) . '.';
                break;

            case self::ERA_NAME :
                $year = $this->date('Y', $this->getUnixTimestamp(), false);
                if ($year < 0) {
                    return $this->_toComment(Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Names', '0')));
                }

                return $this->_toComment(Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Names', '1')));
                break;

            case self::DATES :
                return $this->_toToken(Zend_Locale_Format::getDateFormat($locale), $locale);
                break;

            case self::DATE_FULL :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'full')), $locale);
                break;

            case self::DATE_LONG :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'long')), $locale);
                break;

            case self::DATE_MEDIUM :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'medium')), $locale);
                break;

            case self::DATE_SHORT :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'short')), $locale);
                break;

            case self::TIMES :
                return $this->_toToken(Zend_Locale_Format::getTimeFormat($locale), $locale);
                break;

            case self::TIME_FULL :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'time', 'full'), $locale);
                break;

            case self::TIME_LONG :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'time', 'long'), $locale);
                break;

            case self::TIME_MEDIUM :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'time', 'medium'), $locale);
                break;

            case self::TIME_SHORT :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'time', 'short'), $locale);
                break;

            case self::DATETIME :
                return $this->_toToken(Zend_Locale_Format::getDateTimeFormat($locale), $locale);
                break;

            case self::DATETIME_FULL :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'full')), $locale);
                break;

            case self::DATETIME_LONG :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'long')), $locale);
                break;

            case self::DATETIME_MEDIUM :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'medium')), $locale);
                break;

            case self::DATETIME_SHORT :
                return $this->_toToken(Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'short')), $locale);
                break;

            case self::ATOM :
                return 'Y\-m\-d\TH\:i\:sP';
                break;

            case self::COOKIE :
                return 'l\, d\-M\-y H\:i\:s e';
                break;

            case self::RFC_822 :
                return 'D\, d M y H\:i\:s O';
                break;

            case self::RFC_850 :
                return 'l\, d\-M\-y H\:i\:s e';
                break;

            case self::RFC_1036 :
                return 'D\, d M y H\:i\:s O';
                break;

            case self::RFC_1123 :
                return 'D\, d M Y H\:i\:s O';
                break;

            case self::RFC_3339 :
                return 'Y\-m\-d\TH\:i\:sP';
                break;

            case self::RSS :
                return 'D\, d M Y H\:i\:s O';
                break;

            case self::W3C :
                return 'Y\-m\-d\TH\:i\:sP';
                break;
        }

        if ($token == '') {
            return '';
        }

        switch ($token[0]) {
            case 'y' :
                if ((strlen($token) == 4) && (abs($this->getUnixTimestamp()) <= 0x7FFFFFFF)) {
                    return 'Y';
                }

                $length = iconv_strlen($token, 'UTF-8');
                return $this->_toComment(str_pad($this->date('Y', $this->getUnixTimestamp(), false), $length, '0', STR_PAD_LEFT));
                break;

            case 'Y' :
                if ((strlen($token) == 4) && (abs($this->getUnixTimestamp()) <= 0x7FFFFFFF)) {
                    return 'o';
                }

                $length = iconv_strlen($token, 'UTF-8');
                return $this->_toComment(str_pad($this->date('o', $this->getUnixTimestamp(), false), $length, '0', STR_PAD_LEFT));
                break;

            case 'A' :
                $length  = iconv_strlen($token, 'UTF-8');
                $result  = substr($this->getMilliSecond(), 0, 3);
                $result += $this->date('s', $this->getUnixTimestamp(), false) * 1000;
                $result += $this->date('i', $this->getUnixTimestamp(), false) * 60000;
                $result += $this->date('H', $this->getUnixTimestamp(), false) * 3600000;

                return $this->_toComment(str_pad($result, $length, '0', STR_PAD_LEFT));
                break;
        }

        return $this->_toComment($token);
    }

    /**
     * Private function to make a comment of a token
     *
     * @param string $token
     * @return string
     */
    private function _toComment($token)
    {
        $token = str_split($token);
        $result = '';
        foreach ($token as $tok) {
            $result .= '\\' . $tok;
        }

        return $result;
    }

    /**
     * Return digit from standard names (english)
     * Faster implementation than locale aware searching
     *
     * @param  string  $name
     * @return integer  Number of this month
     * @throws Zend_Date_Exception
     */
    private function _getDigitFromName($name)
    {
        switch($name) {
            case "Jan":
                return 1;

            case "Feb":
                return 2;

            case "Mar":
                return 3;

            case "Apr":
                return 4;

            case "May":
                return 5;

            case "Jun":
                return 6;

            case "Jul":
                return 7;

            case "Aug":
                return 8;

            case "Sep":
                return 9;

            case "Oct":
                return 10;

            case "Nov":
                return 11;

            case "Dec":
                return 12;

            default:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('Month ($name) is not a known month');
        }
    }

    /**
     * Counts the exact year number
     * < 70 - 2000 added, >70 < 100 - 1900, others just returned
     *
     * @param  integer  $value year number
     * @return integer  Number of year
     */
    public static function getFullYear($value)
    {
        if ($value >= 0) {
            if ($value < 70) {
                $value += 2000;
            } else if ($value < 100) {
                $value += 1900;
            }
        }
        return $value;
    }

    /**
     * Sets the given date as new date or a given datepart as new datepart returning the new datepart
     * This could be for example a localized dayname, the date without time,
     * the month or only the seconds. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to set
     * @param  string                          $part    OPTIONAL Part of the date to set, if null the timestamp is set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function set($date, $part = null, $locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $part = Zend_Locale_Format::convertPhpToIsoFormat($part);
        }

        $zone = $this->getTimezoneFromString($date);
        $this->setTimezone($zone);

        $this->_calculate('set', $date, $part, $locale);
        return $this;
    }

    /**
     * Adds a date or datepart to the existing date, by extracting $part from $date,
     * and modifying this object by adding that part.  The $part is then extracted from
     * this object and returned as an integer or numeric string (for large values, or $part's
     * corresponding to pre-defined formatted date strings).
     * This could be for example a ISO 8601 date, the hour the monthname or only the minute.
     * There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu.
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to add
     * @param  string                          $part    OPTIONAL Part of the date to add, if null the timestamp is added
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function add($date, $part = self::TIMESTAMP, $locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $part = Zend_Locale_Format::convertPhpToIsoFormat($part);
        }

        $this->_calculate('add', $date, $part, $locale);
        return $this;
    }

    /**
     * Subtracts a date from another date.
     * This could be for example a RFC2822 date, the time,
     * the year or only the timestamp. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     * Be aware: Adding -2 Months is not equal to Subtracting 2 Months !!!
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to subtract
     * @param  string                          $part    OPTIONAL Part of the date to sub, if null the timestamp is subtracted
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function sub($date, $part = self::TIMESTAMP, $locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $part = Zend_Locale_Format::convertPhpToIsoFormat($part);
        }

        $this->_calculate('sub', $date, $part, $locale);
        return $this;
    }

    /**
     * Compares a date or datepart with the existing one.
     * Returns -1 if earlier, 0 if equal and 1 if later.
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to compare with the date object
     * @param  string                          $part    OPTIONAL Part of the date to compare, if null the timestamp is subtracted
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compare($date, $part = self::TIMESTAMP, $locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $part = Zend_Locale_Format::convertPhpToIsoFormat($part);
        }

        $compare = $this->_calculate('cmp', $date, $part, $locale);

        if ($compare > 0) {
            return 1;
        } else if ($compare < 0) {
            return -1;
        }
        return 0;
    }

    /**
     * Returns a new instance of Zend_Date with the selected part copied.
     * To make an exact copy, use PHP's clone keyword.
     * For a complete list of supported date part values look into the docu.
     * If a date part is copied, all other date parts are set to standard values.
     * For example: If only YEAR is copied, the returned date object is equal to
     * 01-01-YEAR 00:00:00 (01-01-1970 00:00:00 is equal to timestamp 0)
     * If only HOUR is copied, the returned date object is equal to
     * 01-01-1970 HOUR:00:00 (so $this contains a timestamp equal to a timestamp of 0 plus HOUR).
     *
     * @param  string              $part    Part of the date to compare, if null the timestamp is subtracted
     * @param  string|Zend_Locale  $locale  OPTIONAL New object's locale.  No adjustments to timezone are made.
     * @return Zend_Date New clone with requested part
     */
    public function copyPart($part, $locale = null)
    {
        $clone = clone $this;           // copy all instance variables
        $clone->setUnixTimestamp(0);    // except the timestamp
        if ($locale != null) {
            $clone->setLocale($locale); // set an other locale if selected
        }
        $clone->set($this, $part);
        return $clone;
    }

    /**
     * Internal function, returns the offset of a given timezone
     *
     * @param string $zone
     * @return integer
     */
    public function getTimezoneFromString($zone)
    {
        if (is_array($zone)) {
            return $this->getTimezone();
        }

        if ($zone instanceof Zend_Date) {
            return $zone->getTimezone();
        }

        $match = array();
        preg_match('/\dZ$/', $zone, $match);
        if (!empty($match)) {
            return "Etc/UTC";
        }

        preg_match('/([+-]\d{2}):{0,1}\d{2}/', $zone, $match);
        if (!empty($match) and ($match[count($match) - 1] <= 14) and ($match[count($match) - 1] >= -12)) {
            $zone = "Etc/GMT";
            $zone .= ($match[count($match) - 1] < 0) ? "+" : "-";
            $zone .= (int) abs($match[count($match) - 1]);
            return $zone;
        }

        preg_match('/([[:alpha:]\/_]{3,30})(?!.*([[:alpha:]\/]{3,30}))/', $zone, $match);
        try {
            if (!empty($match) and (!is_int($match[count($match) - 1]))) {
                $oldzone = $this->getTimezone();
                $this->setTimezone($match[count($match) - 1]);
                $result = $this->getTimezone();
                $this->setTimezone($oldzone);
                if ($result !== $oldzone) {
                    return $match[count($match) - 1];
                }
            }
        } catch (Exception $e) {
            // fall through
        }

        return $this->getTimezone();
    }

    /**
     * Calculates the date or object
     *
     * @param  string                    $calc  Calculation to make
     * @param  string|integer            $date  Date for calculation
     * @param  string|integer            $comp  Second date for calculation
     * @param  boolean|integer           $dst   Use dst correction if option is set
     * @return integer|string|Zend_Date  new timestamp or Zend_Date depending on calculation
     */
    private function _assign($calc, $date, $comp = 0, $dst = false)
    {
        switch ($calc) {
            case 'set' :
                if (!empty($comp)) {
                    $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$sub, $this->getUnixTimestamp(), $comp));
                }
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$add, $this->getUnixTimestamp(), $date));
                $value = $this->getUnixTimestamp();
                break;
            case 'add' :
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$add, $this->getUnixTimestamp(), $date));
                $value = $this->getUnixTimestamp();
                break;
            case 'sub' :
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$sub, $this->getUnixTimestamp(), $date));
                $value = $this->getUnixTimestamp();
                break;
            default :
                // cmp - compare
                return call_user_func(Zend_Locale_Math::$comp, $comp, $date);
                break;
        }

        // dst-correction if 'fix_dst' = true and dst !== false but only for non UTC and non GMT
        if ((self::$_options['fix_dst'] === true) and ($dst !== false) and ($this->_dst === true)) {
            $hour = $this->toString(self::HOUR, 'iso');
            if ($hour != $dst) {
                if (($dst == ($hour + 1)) or ($dst == ($hour - 23))) {
                    $value += 3600;
                } else if (($dst == ($hour - 1)) or ($dst == ($hour + 23))) {
                    $value -= 3600;
                }
                $this->setUnixTimestamp($value);
            }
        }
        return $this->getUnixTimestamp();
    }


    /**
     * Calculates the date or object
     *
     * @param  string                          $calc    Calculation to make, one of: 'add'|'sub'|'cmp'|'copy'|'set'
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to calculate with
     * @param  string                          $part    Part of the date to calculate, if null the timestamp is used
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|string|Zend_Date        new timestamp
     * @throws Zend_Date_Exception
     */
    private function _calculate($calc, $date, $part, $locale)
    {
        if ($date === null) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $date must be set, null is not allowed');
        }

        if (($part !== null) && (strlen($part) !== 2) && (Zend_Locale::isLocale($part, null, false))) {
            $locale = $part;
            $part   = null;
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $locale = (string) $locale;

        // Create date parts
        $year   = $this->toString(self::YEAR, 'iso');
        $month  = $this->toString(self::MONTH_SHORT, 'iso');
        $day    = $this->toString(self::DAY_SHORT, 'iso');
        $hour   = $this->toString(self::HOUR_SHORT, 'iso');
        $minute = $this->toString(self::MINUTE_SHORT, 'iso');
        $second = $this->toString(self::SECOND_SHORT, 'iso');
        // If object extract value
        if ($date instanceof Zend_Date) {
            $date = $date->toString($part, 'iso', $locale);
        }

        if (is_array($date) === true) {
            if (empty($part) === false) {
                switch($part) {
                    // Fall through
                    case self::DAY:
                    case self::DAY_SHORT:
                        if (isset($date['day']) === true) {
                            $date = $date['day'];
                        }
                        break;
                    // Fall through
                    case self::WEEKDAY_SHORT:
                    case self::WEEKDAY:
                    case self::WEEKDAY_8601:
                    case self::WEEKDAY_DIGIT:
                    case self::WEEKDAY_NARROW:
                    case self::WEEKDAY_NAME:
                        if (isset($date['weekday']) === true) {
                            $date = $date['weekday'];
                            $part = self::WEEKDAY_DIGIT;
                        }
                        break;
                    case self::DAY_OF_YEAR:
                        if (isset($date['day_of_year']) === true) {
                            $date = $date['day_of_year'];
                        }
                        break;
                    // Fall through
                    case self::MONTH:
                    case self::MONTH_SHORT:
                    case self::MONTH_NAME:
                    case self::MONTH_NAME_SHORT:
                    case self::MONTH_NAME_NARROW:
                        if (isset($date['month']) === true) {
                            $date = $date['month'];
                        }
                        break;
                    // Fall through
                    case self::YEAR:
                    case self::YEAR_SHORT:
                    case self::YEAR_8601:
                    case self::YEAR_SHORT_8601:
                        if (isset($date['year']) === true) {
                            $date = $date['year'];
                        }
                        break;
                    // Fall through
                    case self::HOUR:
                    case self::HOUR_AM:
                    case self::HOUR_SHORT:
                    case self::HOUR_SHORT_AM:
                        if (isset($date['hour']) === true) {
                            $date = $date['hour'];
                        }
                        break;
                    // Fall through
                    case self::MINUTE:
                    case self::MINUTE_SHORT:
                        if (isset($date['minute']) === true) {
                            $date = $date['minute'];
                        }
                        break;
                    // Fall through
                    case self::SECOND:
                    case self::SECOND_SHORT:
                        if (isset($date['second']) === true) {
                            $date = $date['second'];
                        }
                        break;
                    // Fall through
                    case self::TIMEZONE:
                    case self::TIMEZONE_NAME:
                        if (isset($date['timezone']) === true) {
                            $date = $date['timezone'];
                        }
                        break;
                    case self::TIMESTAMP:
                        if (isset($date['timestamp']) === true) {
                            $date = $date['timestamp'];
                        }
                        break;
                    case self::WEEK:
                        if (isset($date['week']) === true) {
                            $date = $date['week'];
                        }
                        break;
                    case self::TIMEZONE_SECS:
                        if (isset($date['gmtsecs']) === true) {
                            $date = $date['gmtsecs'];
                        }
                        break;
                    default:
                        #require_once 'Zend/Date/Exception.php';
                        throw new Zend_Date_Exception("datepart for part ($part) not found in array");
                        break;
                }
            } else {
                $hours = 0;
                if (isset($date['hour']) === true) {
                    $hours = $date['hour'];
                }
                $minutes = 0;
                if (isset($date['minute']) === true) {
                    $minutes = $date['minute'];
                }
                $seconds = 0;
                if (isset($date['second']) === true) {
                    $seconds = $date['second'];
                }
                $months = 0;
                if (isset($date['month']) === true) {
                    $months = $date['month'];
                }
                $days = 0;
                if (isset($date['day']) === true) {
                    $days = $date['day'];
                }
                $years = 0;
                if (isset($date['year']) === true) {
                    $years = $date['year'];
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, $months, $days, $years, true),
                    $this->mktime($hour, $minute, $second, $month, $day, $year, true), $hour);
            }
        }

        // $date as object, part of foreign date as own date
        switch($part) {

            // day formats
            case self::DAY:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, true),
                        $this->mktime(0, 0, 0, 1, 1 + intval($day), 1970, true), $hour);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", 0, null, $date);
                break;

            case self::WEEKDAY_SHORT:
                $daylist = Zend_Locale_Data::getList($locale, 'day');
                $weekday = (int) $this->toString(self::WEEKDAY_DIGIT, 'iso', $locale);
                $cnt = 0;

                foreach ($daylist as $key => $value) {
                    if (strtoupper(iconv_substr($value, 0, 3, 'UTF-8')) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found, 1970, true),
                        $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", 0, null, $date);
                break;

            case self::DAY_SHORT:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, true),
                        $this->mktime(0, 0, 0, 1, 1 + intval($day), 1970, true), $hour);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", 0, null, $date);
                break;

            case self::WEEKDAY:
                $daylist = Zend_Locale_Data::getList($locale, 'day');
                $weekday = (int) $this->toString(self::WEEKDAY_DIGIT, 'iso', $locale);
                $cnt = 0;

                foreach ($daylist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found, 1970, true),
                        $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", 0, null, $date);
                break;

            case self::WEEKDAY_8601:
                $weekday = (int) $this->toString(self::WEEKDAY_8601, 'iso', $locale);
                if ((intval($date) > 0) and (intval($date) < 8)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, true),
                        $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", 0, null, $date);
                break;

            case self::DAY_SUFFIX:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('day suffix not supported', 0, null, $date);
                break;

            case self::WEEKDAY_DIGIT:
                $weekday = (int) $this->toString(self::WEEKDAY_DIGIT, 'iso', $locale);
                if (is_numeric($date) and (intval($date) >= 0) and (intval($date) < 7)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $date, 1970, true),
                        $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", 0, null, $date);
                break;

            case self::DAY_OF_YEAR:
                if (is_numeric($date)) {
                    if (($calc == 'add') || ($calc == 'sub')) {
                        $year = 1970;
                        ++$date;
                        ++$day;
                    }

                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, $date, $year, true),
                        $this->mktime(0, 0, 0, $month, $day, $year, true), $hour);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", 0, null, $date);
                break;

            case self::WEEKDAY_NARROW:
                $daylist = Zend_Locale_Data::getList($locale, 'day', array('gregorian', 'format', 'abbreviated'));
                $weekday = (int) $this->toString(self::WEEKDAY_DIGIT, 'iso', $locale);
                $cnt = 0;
                foreach ($daylist as $key => $value) {
                    if (strtoupper(iconv_substr($value, 0, 1, 'UTF-8')) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found, 1970, true),
                        $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", 0, null, $date);
                break;

            case self::WEEKDAY_NAME:
                $daylist = Zend_Locale_Data::getList($locale, 'day', array('gregorian', 'format', 'abbreviated'));
                $weekday = (int) $this->toString(self::WEEKDAY_DIGIT, 'iso', $locale);
                $cnt = 0;
                foreach ($daylist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found, 1970, true),
                        $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", 0, null, $date);
                break;

            // week formats
            case self::WEEK:
                if (is_numeric($date)) {
                    $week = (int) $this->toString(self::WEEK, 'iso', $locale);
                    return $this->_assign($calc, parent::mktime(0, 0, 0, 1, 1 + ($date * 7), 1970, true),
                        parent::mktime(0, 0, 0, 1, 1 + ($week * 7), 1970, true), $hour);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, week expected", 0, null, $date);
                break;

            // month formats
            case self::MONTH_NAME:
                $monthlist = Zend_Locale_Data::getList($locale, 'month');
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }
                $date = array_search($date, $monthlist);

                // Monthname found
                if ($cnt < 12) {
                    $fixday = 0;
                    if ($calc == 'add') {
                        $date += $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] == false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc == 'sub') {
                        $date = $month - $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] == false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date,  $day + $fixday, $year, true),
                        $this->mktime(0, 0, 0, $month, $day, $year, true), $hour);
                }

                // Monthname not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", 0, null, $date);
                break;

            case self::MONTH:
                if (is_numeric($date)) {
                    $fixday = 0;
                    if ($calc == 'add') {
                        $date += $month;
                        $calc = 'set';
                        if (self::$_options['extend_month'] == false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc == 'sub') {
                        $date = $month - $date;
                        $calc = 'set';
                        if (self::$_options['extend_month'] == false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date, $day + $fixday, $year, true),
                        $this->mktime(0, 0, 0, $month, $day, $year, true), $hour);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", 0, null, $date);
                break;

            case self::MONTH_NAME_SHORT:
                $monthlist = Zend_Locale_Data::getList($locale, 'month', array('gregorian', 'format', 'abbreviated'));
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }
                $date = array_search($date, $monthlist);

                // Monthname found
                if ($cnt < 12) {
                    $fixday = 0;
                    if ($calc == 'add') {
                        $date += $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc == 'sub') {
                        $date = $month - $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date, $day + $fixday, $year, true),
                        $this->mktime(0, 0, 0, $month, $day, $year, true), $hour);
                }

                // Monthname not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", 0, null, $date);
                break;

            case self::MONTH_SHORT:
                if (is_numeric($date) === true) {
                    $fixday = 0;
                    if ($calc === 'add') {
                        $date += $month;
                        $calc  = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc === 'sub') {
                        $date = $month - $date;
                        $calc = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }

                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date,  $day + $fixday, $year, true),
                        $this->mktime(0, 0, 0, $month, $day,           $year, true), $hour);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", 0, null, $date);
                break;

            case self::MONTH_DAYS:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('month days not supported', 0, null, $date);
                break;

            case self::MONTH_NAME_NARROW:
                $monthlist = Zend_Locale_Data::getList($locale, 'month', array('gregorian', 'stand-alone', 'narrow'));
                $cnt       = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) === strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }
                $date = array_search($date, $monthlist);

                // Monthname found
                if ($cnt < 12) {
                    $fixday = 0;
                    if ($calc === 'add') {
                        $date += $found;
                        $calc  = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc === 'sub') {
                        $date = $month - $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime($hour, $minute, $second, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date,  $day + $fixday, $year, true),
                        $this->mktime(0, 0, 0, $month, $day,           $year, true), $hour);
                }

                // Monthname not found
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", 0, null, $date);
                break;

            // year formats
            case self::LEAPYEAR:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('leap year not supported', 0, null, $date);
                break;

            case self::YEAR_8601:
                if (is_numeric($date)) {
                    if ($calc === 'add') {
                        $date += $year;
                        $calc  = 'set';
                    } else if ($calc === 'sub') {
                        $date = $year - $date;
                        $calc = 'set';
                    }

                    return $this->_assign($calc, $this->mktime(0, 0, 0, $month, $day, intval($date), true),
                        $this->mktime(0, 0, 0, $month, $day, $year,         true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", 0, null, $date);
                break;

            case self::YEAR:
                if (is_numeric($date)) {
                    if ($calc === 'add') {
                        $date += $year;
                        $calc  = 'set';
                    } else if ($calc === 'sub') {
                        $date = $year - $date;
                        $calc = 'set';
                    }

                    return $this->_assign($calc, $this->mktime(0, 0, 0, $month, $day, intval($date), true),
                        $this->mktime(0, 0, 0, $month, $day, $year,         true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", 0, null, $date);
                break;

            case self::YEAR_SHORT:
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($calc == 'set') || ($calc == 'cmp')) {
                        $date = self::getFullYear($date);
                    }
                    if ($calc === 'add') {
                        $date += $year;
                        $calc  = 'set';
                    } else if ($calc === 'sub') {
                        $date = $year - $date;
                        $calc = 'set';
                    }

                    return $this->_assign($calc, $this->mktime(0, 0, 0, $month, $day, $date, true),
                        $this->mktime(0, 0, 0, $month, $day, $year, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", 0, null, $date);
                break;

            case self::YEAR_SHORT_8601:
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($calc === 'set') || ($calc === 'cmp')) {
                        $date = self::getFullYear($date);
                    }
                    if ($calc === 'add') {
                        $date += $year;
                        $calc  = 'set';
                    } else if ($calc === 'sub') {
                        $date = $year - $date;
                        $calc = 'set';
                    }

                    return $this->_assign($calc, $this->mktime(0, 0, 0, $month, $day, $date, true),
                        $this->mktime(0, 0, 0, $month, $day, $year, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", 0, null, $date);
                break;

            // time formats
            case self::MERIDIEM:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('meridiem not supported', 0, null, $date);
                break;

            case self::SWATCH:
                if (is_numeric($date)) {
                    $rest    = intval($date);
                    $hours   = floor($rest * 24 / 1000);
                    $rest    = $rest - ($hours * 1000 / 24);
                    $minutes = floor($rest * 1440 / 1000);
                    $rest    = $rest - ($minutes * 1000 / 1440);
                    $seconds = floor($rest * 86400 / 1000);
                    return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1, 1, 1970, true),
                        $this->mktime($hour,  $minute,  $second,  1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, swatchstamp expected", 0, null, $date);
                break;

            case self::HOUR_SHORT_AM:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, true),
                        $this->mktime($hour,         0, 0, 1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", 0, null, $date);
                break;

            case self::HOUR_SHORT:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, true),
                        $this->mktime($hour,         0, 0, 1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", 0, null, $date);
                break;

            case self::HOUR_AM:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, true),
                        $this->mktime($hour,         0, 0, 1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", 0, null, $date);
                break;

            case self::HOUR:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, true),
                        $this->mktime($hour,         0, 0, 1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", 0, null, $date);
                break;

            case self::MINUTE:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, intval($date), 0, 1, 1, 1970, true),
                        $this->mktime(0, $minute,       0, 1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, minute expected", 0, null, $date);
                break;

            case self::SECOND:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, intval($date), 1, 1, 1970, true),
                        $this->mktime(0, 0, $second,       1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, second expected", 0, null, $date);
                break;

            case self::MILLISECOND:
                if (is_numeric($date)) {
                    switch($calc) {
                        case 'set' :
                            return $this->setMillisecond($date);
                            break;
                        case 'add' :
                            return $this->addMillisecond($date);
                            break;
                        case 'sub' :
                            return $this->subMillisecond($date);
                            break;
                    }

                    return $this->compareMillisecond($date);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, milliseconds expected", 0, null, $date);
                break;

            case self::MINUTE_SHORT:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, intval($date), 0, 1, 1, 1970, true),
                        $this->mktime(0, $minute,       0, 1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, minute expected", 0, null, $date);
                break;

            case self::SECOND_SHORT:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, intval($date), 1, 1, 1970, true),
                        $this->mktime(0, 0, $second,       1, 1, 1970, true), false);
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, second expected", 0, null, $date);
                break;

            // timezone formats
            // break intentionally omitted
            case self::TIMEZONE_NAME:
            case self::TIMEZONE:
            case self::TIMEZONE_SECS:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('timezone not supported', 0, null, $date);
                break;

            case self::DAYLIGHT:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('daylight not supported', 0, null, $date);
                break;

            case self::GMT_DIFF:
            case self::GMT_DIFF_SEP:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('gmtdiff not supported', 0, null, $date);
                break;

            // date strings
            case self::ISO_8601:
                // (-)YYYY-MM-dd
                preg_match('/^(-{0,1}\d{4})-(\d{2})-(\d{2})/', $date, $datematch);
                // (-)YY-MM-dd
                if (empty($datematch)) {
                    preg_match('/^(-{0,1}\d{2})-(\d{2})-(\d{2})/', $date, $datematch);
                }
                // (-)YYYYMMdd
                if (empty($datematch)) {
                    preg_match('/^(-{0,1}\d{4})(\d{2})(\d{2})/', $date, $datematch);
                }
                // (-)YYMMdd
                if (empty($datematch)) {
                    preg_match('/^(-{0,1}\d{2})(\d{2})(\d{2})/', $date, $datematch);
                }
                $tmpdate = $date;
                if (!empty($datematch)) {
                    $dateMatchCharCount = iconv_strlen($datematch[0], 'UTF-8');
                    $tmpdate = iconv_substr($date,
                        $dateMatchCharCount,
                        iconv_strlen($date, 'UTF-8') - $dateMatchCharCount,
                        'UTF-8');
                }
                // (T)hh:mm:ss
                preg_match('/[T,\s]{0,1}(\d{2}):(\d{2}):(\d{2})/', $tmpdate, $timematch);
                // (T)hhmmss
                if (empty($timematch)) {
                    preg_match('/[T,\s]{0,1}(\d{2})(\d{2})(\d{2})/', $tmpdate, $timematch);
                }
                if (empty($datematch) and empty($timematch)) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("unsupported ISO8601 format ($date)", 0, null, $date);
                }
                if (!empty($timematch)) {
                    $timeMatchCharCount = iconv_strlen($timematch[0], 'UTF-8');
                    $tmpdate = iconv_substr($tmpdate,
                        $timeMatchCharCount,
                        iconv_strlen($tmpdate, 'UTF-8') - $timeMatchCharCount,
                        'UTF-8');
                }
                if (empty($datematch)) {
                    $datematch[1] = 1970;
                    $datematch[2] = 1;
                    $datematch[3] = 1;
                } else if (iconv_strlen($datematch[1], 'UTF-8') == 2) {
                    $datematch[1] = self::getFullYear($datematch[1]);
                }
                if (empty($timematch)) {
                    $timematch[1] = 0;
                    $timematch[2] = 0;
                    $timematch[3] = 0;
                }
                if (!isset($timematch[3])) {
                    $timematch[3] = 0;
                }

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$datematch[2];
                    --$month;
                    --$datematch[3];
                    --$day;
                    $datematch[1] -= 1970;
                    $year         -= 1970;
                }
                return $this->_assign($calc, $this->mktime($timematch[1], $timematch[2], $timematch[3], 1 + $datematch[2], 1 + $datematch[3], 1970 + $datematch[1], false),
                    $this->mktime($hour,         $minute,       $second,       1 + $month,        1 + $day,          1970 + $year,         false), false);
                break;

            case self::RFC_2822:
                $result = preg_match('/^\w{3},\s(\d{1,2})\s(\w{3})\s(\d{4})\s'
                    . '(\d{2}):(\d{2}):{0,1}(\d{0,2})\s([+-]'
                    . '{1}\d{4}|\w{1,20})$/', $date, $match);

                if (!$result) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("no RFC 2822 format ($date)", 0, null, $date);
                }

                $months  = $this->_getDigitFromName($match[2]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], false),
                    $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     false), false);
                break;

            case self::TIMESTAMP:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $date, $this->getUnixTimestamp());
                }

                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, timestamp expected", 0, null, $date);
                break;

            // additional formats
            // break intentionally omitted
            case self::ERA:
            case self::ERA_NAME:
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('era not supported', 0, null, $date);
                break;

            case self::DATES:
                try {
                    $parsed = Zend_Locale_Format::getDate($date, array('locale' => $locale, 'format_type' => 'iso', 'fix_date' => true));

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }

                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATE_FULL:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATE_LONG:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    if (($calc == 'set') || ($calc == 'cmp')){
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATE_MEDIUM:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATE_SHORT:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    $parsed['year'] = self::getFullYear($parsed['year']);

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::TIMES:
                try {
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }
                    $parsed = Zend_Locale_Format::getTime($date, array('locale' => $locale, 'format_type' => 'iso', 'fix_date' => true));
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $month, $day, $year, true),
                        $this->mktime($hour,           $minute,           $second,           $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::TIME_FULL:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }

                    if (!isset($parsed['second'])) {
                        $parsed['second'] = 0;
                    }

                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $month, $day, $year, true),
                        $this->mktime($hour,           $minute,           $second,           $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::TIME_LONG:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $month, $day, $year, true),
                        $this->mktime($hour,           $minute,           $second,           $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::TIME_MEDIUM:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $month, $day, $year, true),
                        $this->mktime($hour,           $minute,           $second,           $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::TIME_SHORT:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }

                    if (!isset($parsed['second'])) {
                        $parsed['second'] = 0;
                    }

                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $month, $day, $year, true),
                        $this->mktime($hour,           $minute,           $second,           $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATETIME:
                try {
                    $parsed = Zend_Locale_Format::getDateTime($date, array('locale' => $locale, 'format_type' => 'iso', 'fix_date' => true));
                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime($hour,           $minute,           $second,           1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATETIME_FULL:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getDateTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }

                    if (!isset($parsed['second'])) {
                        $parsed['second'] = 0;
                    }

                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime($hour,           $minute,           $second,           1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATETIME_LONG:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getDateTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    if (($calc == 'set') || ($calc == 'cmp')){
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime($hour,           $minute,           $second,           1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATETIME_MEDIUM:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getDateTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime($hour,           $minute,           $second,           1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            case self::DATETIME_SHORT:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getDateTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    $parsed['year'] = self::getFullYear($parsed['year']);

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }

                    if (!isset($parsed['second'])) {
                        $parsed['second'] = 0;
                    }

                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                        $this->mktime($hour,           $minute,           $second,           1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                }
                break;

            // ATOM and RFC_3339 are identical
            case self::ATOM:
            case self::RFC_3339:
                $result = preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})\d{0,4}([+-]{1}\d{2}:\d{2}|Z)$/', $date, $match);
                if (!$result) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, ATOM format expected", 0, null, $date);
                }

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$match[2];
                    --$month;
                    --$match[3];
                    --$day;
                    $match[1] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $match[2], 1 + $match[3], 1970 + $match[1], true),
                    $this->mktime($hour,     $minute,   $second,   1 + $month,    1 + $day,      1970 + $year,     true), false);
                break;

            case self::COOKIE:
                $result = preg_match("/^\w{6,9},\s(\d{2})-(\w{3})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})\s.{3,20}$/", $date, $match);
                if (!$result) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, COOKIE format expected", 0, null, $date);
                }
                $matchStartPos = iconv_strpos($match[0], ' ', 0, 'UTF-8') + 1;
                $match[0] = iconv_substr($match[0],
                    $matchStartPos,
                    iconv_strlen($match[0], 'UTF-8') - $matchStartPos,
                    'UTF-8');

                $months    = $this->_getDigitFromName($match[2]);
                $match[3] = self::getFullYear($match[3]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], true),
                    $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     true), false);
                break;

            case self::RFC_822:
            case self::RFC_1036:
                // new RFC 822 format, identical to RFC 1036 standard
                $result = preg_match('/^\w{0,3},{0,1}\s{0,1}(\d{1,2})\s(\w{3})\s(\d{2})\s(\d{2}):(\d{2}):{0,1}(\d{0,2})\s([+-]{1}\d{4}|\w{1,20})$/', $date, $match);
                if (!$result) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 822 date format expected", 0, null, $date);
                }

                $months    = $this->_getDigitFromName($match[2]);
                $match[3] = self::getFullYear($match[3]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], false),
                    $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     false), false);
                break;

            case self::RFC_850:
                $result = preg_match('/^\w{6,9},\s(\d{2})-(\w{3})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})\s.{3,21}$/', $date, $match);
                if (!$result) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 850 date format expected", 0, null, $date);
                }

                $months    = $this->_getDigitFromName($match[2]);
                $match[3] = self::getFullYear($match[3]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], true),
                    $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     true), false);
                break;

            case self::RFC_1123:
                $result = preg_match('/^\w{0,3},{0,1}\s{0,1}(\d{1,2})\s(\w{3})\s(\d{2,4})\s(\d{2}):(\d{2}):{0,1}(\d{0,2})\s([+-]{1}\d{4}|\w{1,20})$/', $date, $match);
                if (!$result) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 1123 date format expected", 0, null, $date);
                }

                $months  = $this->_getDigitFromName($match[2]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], true),
                    $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     true), false);
                break;

            case self::RSS:
                $result = preg_match('/^\w{3},\s(\d{2})\s(\w{3})\s(\d{2,4})\s(\d{1,2}):(\d{2}):(\d{2})\s.{1,21}$/', $date, $match);
                if (!$result) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, RSS date format expected", 0, null, $date);
                }

                $months  = $this->_getDigitFromName($match[2]);
                $match[3] = self::getFullYear($match[3]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], true),
                    $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     true), false);
                break;

            case self::W3C:
                $result = preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})[+-]{1}\d{2}:\d{2}$/', $date, $match);
                if (!$result) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, W3C date format expected", 0, null, $date);
                }

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$match[2];
                    --$month;
                    --$match[3];
                    --$day;
                    $match[1] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $match[2], 1 + $match[3], 1970 + $match[1], true),
                    $this->mktime($hour,     $minute,   $second,   1 + $month,    1 + $day,      1970 + $year,     true), false);
                break;

            default:
                if (!is_numeric($date) || !empty($part)) {
                    try {
                        if (empty($part)) {
                            $part  = Zend_Locale_Format::getDateFormat($locale) . " ";
                            $part .= Zend_Locale_Format::getTimeFormat($locale);
                        }

                        $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $part, 'locale' => $locale, 'fix_date' => true, 'format_type' => 'iso'));
                        if ((strpos(strtoupper($part), 'YY') !== false) and (strpos(strtoupper($part), 'YYYY') === false)) {
                            $parsed['year'] = self::getFullYear($parsed['year']);
                        }

                        if (($calc == 'set') || ($calc == 'cmp')) {
                            if (isset($parsed['month'])) {
                                --$parsed['month'];
                            } else {
                                $parsed['month'] = 0;
                            }

                            if (isset($parsed['day'])) {
                                --$parsed['day'];
                            } else {
                                $parsed['day'] = 0;
                            }

                            if (!isset($parsed['year'])) {
                                $parsed['year'] = 1970;
                            }
                        }

                        return $this->_assign($calc, $this->mktime(
                            isset($parsed['hour']) ? $parsed['hour'] : 0,
                            isset($parsed['minute']) ? $parsed['minute'] : 0,
                            isset($parsed['second']) ? $parsed['second'] : 0,
                            isset($parsed['month']) ? (1 + $parsed['month']) : 1,
                            isset($parsed['day']) ? (1 + $parsed['day']) : 1,
                            $parsed['year'],
                            false), $this->getUnixTimestamp(), false);
                    } catch (Zend_Locale_Exception $e) {
                        if (!is_numeric($date)) {
                            #require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception($e->getMessage(), 0, $e, $date);
                        }
                    }
                }

                return $this->_assign($calc, $date, $this->getUnixTimestamp(), false);
                break;
        }
    }

    /**
     * Returns true when both date objects or date parts are equal.
     * For example:
     * 15.May.2000 <-> 15.June.2000 Equals only for Day or Year... all other will return false
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to equal with
     * @param  string                          $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function equals($date, $part = self::TIMESTAMP, $locale = null)
    {
        $result = $this->compare($date, $part, $locale);

        if ($result == 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns if the given date or datepart is earlier
     * For example:
     * 15.May.2000 <-> 13.June.1999 will return true for day, year and date, but not for month
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to compare with
     * @param  string                          $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function isEarlier($date, $part = null, $locale = null)
    {
        $result = $this->compare($date, $part, $locale);

        if ($result == -1) {
            return true;
        }

        return false;
    }

    /**
     * Returns if the given date or datepart is later
     * For example:
     * 15.May.2000 <-> 13.June.1999 will return true for month but false for day, year and date
     * Returns if the given date is later
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to compare with
     * @param  string                          $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function isLater($date, $part = null, $locale = null)
    {
        $result = $this->compare($date, $part, $locale);

        if ($result == 1) {
            return true;
        }

        return false;
    }

    /**
     * Returns only the time of the date as new Zend_Date object
     * For example:
     * 15.May.2000 10:11:23 will return a dateobject equal to 01.Jan.1970 10:11:23
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getTime($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 'H:i:s';
        } else {
            $format = self::TIME_MEDIUM;
        }

        return $this->copyPart($format, $locale);
    }

    /**
     * Returns the calculated time
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|array|Zend_Date  $time    Time to calculate with, if null the actual time is taken
     * @param  string                          $format  Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    private function _time($calc, $time, $format, $locale)
    {
        if ($time === null) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $time must be set, null is not allowed');
        }

        if ($time instanceof Zend_Date) {
            // extract time from object
            $time = $time->toString('HH:mm:ss', 'iso');
        } else {
            if (is_array($time)) {
                if ((isset($time['hour']) === true) or (isset($time['minute']) === true) or
                    (isset($time['second']) === true)) {
                    $parsed = $time;
                } else {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("no hour, minute or second given in array");
                }
            } else {
                if (self::$_options['format_type'] == 'php') {
                    $format = Zend_Locale_Format::convertPhpToIsoFormat($format);
                }
                try {
                    if ($locale === null) {
                        $locale = $this->getLocale();
                    }

                    $parsed = Zend_Locale_Format::getTime($time, array('date_format' => $format, 'locale' => $locale, 'format_type' => 'iso'));
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e);
                }
            }

            if (!array_key_exists('hour', $parsed)) {
                $parsed['hour'] = 0;
            }

            if (!array_key_exists('minute', $parsed)) {
                $parsed['minute'] = 0;
            }

            if (!array_key_exists('second', $parsed)) {
                $parsed['second'] = 0;
            }

            $time  = str_pad($parsed['hour'], 2, '0', STR_PAD_LEFT) . ":";
            $time .= str_pad($parsed['minute'], 2, '0', STR_PAD_LEFT) . ":";
            $time .= str_pad($parsed['second'], 2, '0', STR_PAD_LEFT);
        }

        $return = $this->_calcdetail($calc, $time, self::TIMES, 'de');
        if ($calc != 'cmp') {
            return $this;
        }

        return $return;
    }


    /**
     * Sets a new time for the date object. Format defines how to parse the time string.
     * Also a complete date can be given, but only the time is used for setting.
     * For example: dd.MMMM.yyTHH:mm' and 'ss sec'-> 10.May.07T25:11 and 44 sec => 1h11min44sec + 1 day
     * Returned is the new date object and the existing date is left as it was before
     *
     * @param  string|integer|array|Zend_Date  $time    Time to set
     * @param  string                          $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setTime($time, $format = null, $locale = null)
    {
        return $this->_time('set', $time, $format, $locale);
    }


    /**
     * Adds a time to the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> +10 hours
     *
     * @param  string|integer|array|Zend_Date  $time    Time to add
     * @param  string                          $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addTime($time, $format = null, $locale = null)
    {
        return $this->_time('add', $time, $format, $locale);
    }


    /**
     * Subtracts a time from the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> -10 hours
     *
     * @param  string|integer|array|Zend_Date  $time    Time to sub
     * @param  string                          $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent inteface
     * @throws Zend_Date_Exception
     */
    public function subTime($time, $format = null, $locale = null)
    {
        return $this->_time('sub', $time, $format, $locale);
    }


    /**
     * Compares the time from the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to default.
     * If no format us given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> 10 hours
     *
     * @param  string|integer|array|Zend_Date  $time    Time to compare
     * @param  string                          $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareTime($time, $format = null, $locale = null)
    {
        return $this->_time('cmp', $time, $format, $locale);
    }

    /**
     * Returns a clone of $this, with the time part set to 00:00:00.
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDate($locale = null)
    {
        $orig = self::$_options['format_type'];
        if (self::$_options['format_type'] == 'php') {
            self::$_options['format_type'] = 'iso';
        }

        $date = $this->copyPart(self::DATE_MEDIUM, $locale);
        $date->addTimestamp($this->getGmtOffset());
        self::$_options['format_type'] = $orig;

        return $date;
    }

    /**
     * Returns the calculated date
     *
     * @param  string                          $calc    Calculation to make
     * @param  string|integer|array|Zend_Date  $date    Date to calculate with, if null the actual date is taken
     * @param  string                          $format  Date format for parsing
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _date($calc, $date, $format, $locale)
    {
        if ($date === null) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $date must be set, null is not allowed');
        }

        if ($date instanceof Zend_Date) {
            // extract date from object
            $date = $date->toString('d.M.y', 'iso');
        } else {
            if (is_array($date)) {
                if ((isset($date['year']) === true) or (isset($date['month']) === true) or
                    (isset($date['day']) === true)) {
                    $parsed = $date;
                } else {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("no day,month or year given in array");
                }
            } else {
                if ((self::$_options['format_type'] == 'php') && !defined($format)) {
                    $format = Zend_Locale_Format::convertPhpToIsoFormat($format);
                }
                try {
                    if ($locale === null) {
                        $locale = $this->getLocale();
                    }

                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'locale' => $locale, 'format_type' => 'iso'));
                    if ((strpos(strtoupper($format), 'YY') !== false) and (strpos(strtoupper($format), 'YYYY') === false)) {
                        $parsed['year'] = self::getFullYear($parsed['year']);
                    }
                } catch (Zend_Locale_Exception $e) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), 0, $e);
                }
            }

            if (!array_key_exists('day', $parsed)) {
                $parsed['day'] = 1;
            }

            if (!array_key_exists('month', $parsed)) {
                $parsed['month'] = 1;
            }

            if (!array_key_exists('year', $parsed)) {
                $parsed['year'] = 0;
            }

            $date  = $parsed['day'] . "." . $parsed['month'] . "." . $parsed['year'];
        }

        $return = $this->_calcdetail($calc, $date, self::DATE_MEDIUM, 'de');
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new date for the date object. Format defines how to parse the date string.
     * Also a complete date with time can be given, but only the date is used for setting.
     * For example: MMMM.yy HH:mm-> May.07 22:11 => 01.May.07 00:00
     * Returned is the new date object and the existing time is left as it was before
     *
     * @param  string|integer|array|Zend_Date  $date    Date to set
     * @param  string                          $format  OPTIONAL Date format for parsing
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setDate($date, $format = null, $locale = null)
    {
        return $this->_date('set', $date, $format, $locale);
    }


    /**
     * Adds a date to the existing date object. Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: MM.dd.YYYY -> 10 -> +10 months
     *
     * @param  string|integer|array|Zend_Date  $date    Date to add
     * @param  string                          $format  OPTIONAL Date format for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addDate($date, $format = null, $locale = null)
    {
        return $this->_date('add', $date, $format, $locale);
    }


    /**
     * Subtracts a date from the existing date object. Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: MM.dd.YYYY -> 10 -> -10 months
     * Be aware: Subtracting 2 months is not equal to Adding -2 months !!!
     *
     * @param  string|integer|array|Zend_Date  $date    Date to sub
     * @param  string                          $format  OPTIONAL Date format for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subDate($date, $format = null, $locale = null)
    {
        return $this->_date('sub', $date, $format, $locale);
    }


    /**
     * Compares the date from the existing date object, ignoring the time.
     * Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: 10.01.2000 => 10.02.1999 -> false
     *
     * @param  string|integer|array|Zend_Date  $date    Date to compare
     * @param  string                          $format  OPTIONAL Date format for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareDate($date, $format = null, $locale = null)
    {
        return $this->_date('cmp', $date, $format, $locale);
    }


    /**
     * Returns the full ISO 8601 date from the date object.
     * Always the complete ISO 8601 specifiction is used. If an other ISO date is needed
     * (ISO 8601 defines several formats) use toString() instead.
     * This function does not return the ISO date as object. Use copy() instead.
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function getIso($locale = null)
    {
        return $this->toString(self::ISO_8601, 'iso', $locale);
    }


    /**
     * Sets a new date for the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> 01.Sept.2005 00:00:00, 20050201T10:00:30 -> 01.Feb.2005 10h00m30s
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setIso($date, $locale = null)
    {
        return $this->_calcvalue('set', $date, 'iso', self::ISO_8601, $locale);
    }


    /**
     * Adds a ISO date to the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> + 01.Sept.2005 00:00:00, 10:00:00 -> +10h
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addIso($date, $locale = null)
    {
        return $this->_calcvalue('add', $date, 'iso', self::ISO_8601, $locale);
    }


    /**
     * Subtracts a ISO date from the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> - 01.Sept.2005 00:00:00, 10:00:00 -> -10h
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subIso($date, $locale = null)
    {
        return $this->_calcvalue('sub', $date, 'iso', self::ISO_8601, $locale);
    }


    /**
     * Compares a ISO date with the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> - 01.Sept.2005 00:00:00, 10:00:00 -> -10h
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareIso($date, $locale = null)
    {
        return $this->_calcvalue('cmp', $date, 'iso', self::ISO_8601, $locale);
    }


    /**
     * Returns a RFC 822 compilant datestring from the date object.
     * This function does not return the RFC date as object. Use copy() instead.
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function getArpa($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 'D\, d M y H\:i\:s O';
        } else {
            $format = self::RFC_822;
        }

        return $this->toString($format, 'iso', $locale);
    }


    /**
     * Sets a RFC 822 date as new date for the date object.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setArpa($date, $locale = null)
    {
        return $this->_calcvalue('set', $date, 'arpa', self::RFC_822, $locale);
    }


    /**
     * Adds a RFC 822 date to the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addArpa($date, $locale = null)
    {
        return $this->_calcvalue('add', $date, 'arpa', self::RFC_822, $locale);
    }


    /**
     * Subtracts a RFC 822 date from the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subArpa($date, $locale = null)
    {
        return $this->_calcvalue('sub', $date, 'arpa', self::RFC_822, $locale);
    }


    /**
     * Compares a RFC 822 compilant date with the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareArpa($date, $locale = null)
    {
        return $this->_calcvalue('cmp', $date, 'arpa', self::RFC_822, $locale);
    }

    /**
     * Check if location is supported
     *
     * @param array $location locations array
     * @throws Zend_Date_Exception
     * @return float $horizon float
     */
    private function _checkLocation($location)
    {
        if (!isset($location['longitude']) or !isset($location['latitude'])) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('Location must include \'longitude\' and \'latitude\'', 0, null, $location);
        }
        if (($location['longitude'] > 180) or ($location['longitude'] < -180)) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('Longitude must be between -180 and 180', 0, null, $location);
        }
        if (($location['latitude'] > 90) or ($location['latitude'] < -90)) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('Latitude must be between -90 and 90', 0, null, $location);
        }

        if (!isset($location['horizon'])){
            $location['horizon'] = 'effective';
        }

        switch ($location['horizon']) {
            case 'civil' :
                return -0.104528;
                break;
            case 'nautic' :
                return -0.207912;
                break;
            case 'astronomic' :
                return -0.309017;
                break;
            default :
                return -0.0145439;
                break;
        }
    }


    /**
     * Returns the time of sunrise for this date and a given location as new date object
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param array $location location of sunrise
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function getSunrise($location)
    {
        $horizon = $this->_checkLocation($location);
        $result = clone $this;
        $result->set($this->calcSun($location, $horizon, true), self::TIMESTAMP);
        return $result;
    }


    /**
     * Returns the time of sunset for this date and a given location as new date object
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param array $location location of sunset
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function getSunset($location)
    {
        $horizon = $this->_checkLocation($location);
        $result = clone $this;
        $result->set($this->calcSun($location, $horizon, false), self::TIMESTAMP);
        return $result;
    }


    /**
     * Returns an array with the sunset and sunrise dates for all horizon types
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param array $location location of suninfo
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return array - [sunset|sunrise][effective|civil|nautic|astronomic]
     * @throws Zend_Date_Exception
     */
    public function getSunInfo($location)
    {
        $suninfo = array();
        for ($i = 0; $i < 4; ++$i) {
            switch ($i) {
                case 0 :
                    $location['horizon'] = 'effective';
                    break;
                case 1 :
                    $location['horizon'] = 'civil';
                    break;
                case 2 :
                    $location['horizon'] = 'nautic';
                    break;
                case 3 :
                    $location['horizon'] = 'astronomic';
                    break;
            }
            $horizon = $this->_checkLocation($location);
            $result = clone $this;
            $result->set($this->calcSun($location, $horizon, true), self::TIMESTAMP);
            $suninfo['sunrise'][$location['horizon']] = $result;
            $result = clone $this;
            $result->set($this->calcSun($location, $horizon, false), self::TIMESTAMP);
            $suninfo['sunset'][$location['horizon']]  = $result;
        }
        return $suninfo;
    }

    /**
     * Check a given year for leap year.
     *
     * @param  integer|array|Zend_Date $year Year to check
     * @throws Zend_Date_Exception
     * @return boolean
     */
    public static function checkLeapYear($year)
    {
        if ($year instanceof Zend_Date) {
            $year = (int) $year->toString(self::YEAR, 'iso');
        }

        if (is_array($year)) {
            if (isset($year['year']) === true) {
                $year = $year['year'];
            } else {
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("no year given in array");
            }
        }

        if (!is_numeric($year)) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("year ($year) has to be integer for checkLeapYear()", 0, null, $year);
        }

        return (bool) parent::isYearLeapYear($year);
    }


    /**
     * Returns true, if the year is a leap year.
     *
     * @return boolean
     */
    public function isLeapYear()
    {
        return self::checkLeapYear($this);
    }


    /**
     * Returns if the set date is todays date
     *
     * @return boolean
     */
    public function isToday()
    {
        $today = $this->date('Ymd', $this->_getTime());
        $day   = $this->date('Ymd', $this->getUnixTimestamp());
        return ($today == $day);
    }


    /**
     * Returns if the set date is yesterdays date
     *
     * @return boolean
     */
    public function isYesterday()
    {
        list($year, $month, $day) = explode('-', $this->date('Y-m-d', $this->_getTime()));
        // adjusts for leap days and DST changes that are timezone specific
        $yesterday = $this->date('Ymd', $this->mktime(0, 0, 0, $month, $day -1, $year));
        $day   = $this->date('Ymd', $this->getUnixTimestamp());
        return $day == $yesterday;
    }


    /**
     * Returns if the set date is tomorrows date
     *
     * @return boolean
     */
    public function isTomorrow()
    {
        list($year, $month, $day) = explode('-', $this->date('Y-m-d', $this->_getTime()));
        // adjusts for leap days and DST changes that are timezone specific
        $tomorrow = $this->date('Ymd', $this->mktime(0, 0, 0, $month, $day +1, $year));
        $day   = $this->date('Ymd', $this->getUnixTimestamp());
        return $day == $tomorrow;
    }

    /**
     * Returns the actual date as new date object
     *
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public static function now($locale = null)
    {
        return new Zend_Date(time(), self::TIMESTAMP, $locale);
    }

    /**
     * Calculate date details
     *
     * @param  string                          $calc    Calculation to make
     * @param  string|integer|array|Zend_Date  $date    Date or Part to calculate
     * @param  string                          $type    Datepart for Calculation
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|string  new date
     * @throws Zend_Date_Exception
     */
    private function _calcdetail($calc, $date, $type, $locale)
    {
        $old = false;
        if (self::$_options['format_type'] == 'php') {
            self::$_options['format_type'] = 'iso';
            $old = true;
        }

        switch($calc) {
            case 'set' :
                $return = $this->set($date, $type, $locale);
                break;
            case 'add' :
                $return = $this->add($date, $type, $locale);
                break;
            case 'sub' :
                $return = $this->sub($date, $type, $locale);
                break;
            default :
                $return = $this->compare($date, $type, $locale);
                break;
        }

        if ($old) {
            self::$_options['format_type'] = 'php';
        }

        return $return;
    }

    /**
     * Internal calculation, returns the requested date type
     *
     * @param  string                   $calc   Calculation to make
     * @param  string|integer|Zend_Date $value  Datevalue to calculate with, if null the actual value is taken
     * @param  string                   $type
     * @param  string                   $parameter
     * @param  string|Zend_Locale       $locale Locale for parsing input
     * @throws Zend_Date_Exception
     * @return integer|Zend_Date  new date
     */
    private function _calcvalue($calc, $value, $type, $parameter, $locale)
    {
        if ($value === null) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("parameter $type must be set, null is not allowed");
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($value instanceof Zend_Date) {
            // extract value from object
            $value = $value->toString($parameter, 'iso', $locale);
        } else if (!is_array($value) && !is_numeric($value) && ($type != 'iso') && ($type != 'arpa')) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("invalid $type ($value) operand", 0, null, $value);
        }

        $return = $this->_calcdetail($calc, $value, $parameter, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Returns only the year from the date object as new object.
     * For example: 10.May.2000 10:30:00 -> 01.Jan.2000 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getYear($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 'Y';
        } else {
            $format = self::YEAR;
        }

        return $this->copyPart($format, $locale);
    }


    /**
     * Sets a new year
     * If the year is between 0 and 69, 2000 will be set (2000-2069)
     * If the year if between 70 and 99, 1999 will be set (1970-1999)
     * 3 or 4 digit years are set as expected. If you need to set year 0-99
     * use set() instead.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $year    Year to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setYear($year, $locale = null)
    {
        return $this->_calcvalue('set', $year, 'year', self::YEAR, $locale);
    }


    /**
     * Adds the year to the existing date object
     * If the year is between 0 and 69, 2000 will be added (2000-2069)
     * If the year if between 70 and 99, 1999 will be added (1970-1999)
     * 3 or 4 digit years are added as expected. If you need to add years from 0-99
     * use add() instead.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $year    Year to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addYear($year, $locale = null)
    {
        return $this->_calcvalue('add', $year, 'year', self::YEAR, $locale);
    }


    /**
     * Subs the year from the existing date object
     * If the year is between 0 and 69, 2000 will be subtracted (2000-2069)
     * If the year if between 70 and 99, 1999 will be subtracted (1970-1999)
     * 3 or 4 digit years are subtracted as expected. If you need to subtract years from 0-99
     * use sub() instead.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $year    Year to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subYear($year, $locale = null)
    {
        return $this->_calcvalue('sub', $year, 'year', self::YEAR, $locale);
    }


    /**
     * Compares the year with the existing date object, ignoring other date parts.
     * For example: 10.03.2000 -> 15.02.2000 -> true
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $year    Year to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareYear($year, $locale = null)
    {
        return $this->_calcvalue('cmp', $year, 'year', self::YEAR, $locale);
    }


    /**
     * Returns only the month from the date object as new object.
     * For example: 10.May.2000 10:30:00 -> 01.May.1970 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getMonth($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 'm';
        } else {
            $format = self::MONTH;
        }

        return $this->copyPart($format, $locale);
    }


    /**
     * Returns the calculated month
     *
     * @param  string                          $calc    Calculation to make
     * @param  string|integer|array|Zend_Date  $month   Month to calculate with, if null the actual month is taken
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    private function _month($calc, $month, $locale)
    {
        if ($month === null) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $month must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($month instanceof Zend_Date) {
            // extract month from object
            $found = $month->toString(self::MONTH_SHORT, 'iso', $locale);
        } else {
            if (is_numeric($month)) {
                $found = $month;
            } else if (is_array($month)) {
                if (isset($month['month']) === true) {
                    $month = $month['month'];
                } else {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("no month given in array");
                }
            } else {
                $monthlist  = Zend_Locale_Data::getList($locale, 'month');
                $monthlist2 = Zend_Locale_Data::getList($locale, 'month', array('gregorian', 'format', 'abbreviated'));

                $monthlist = array_merge($monthlist, $monthlist2);
                $found = 0;
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($month)) {
                        $found = ($key % 12) + 1;
                        break;
                    }
                    ++$cnt;
                }
                if ($found == 0) {
                    foreach ($monthlist2 as $key => $value) {
                        if (strtoupper(iconv_substr($value, 0, 1, 'UTF-8')) == strtoupper($month)) {
                            $found = $key + 1;
                            break;
                        }
                        ++$cnt;
                    }
                }
                if ($found == 0) {
                    #require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("unknown month name ($month)", 0, null, $month);
                }
            }
        }
        $return = $this->_calcdetail($calc, $found, self::MONTH_SHORT, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new month
     * The month can be a number or a string. Setting months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $month   Month to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setMonth($month, $locale = null)
    {
        return $this->_month('set', $month, $locale);
    }


    /**
     * Adds months to the existing date object.
     * The month can be a number or a string. Adding months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $month   Month to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addMonth($month, $locale = null)
    {
        return $this->_month('add', $month, $locale);
    }


    /**
     * Subtracts months from the existing date object.
     * The month can be a number or a string. Subtracting months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $month   Month to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subMonth($month, $locale = null)
    {
        return $this->_month('sub', $month, $locale);
    }


    /**
     * Compares the month with the existing date object, ignoring other date parts.
     * For example: 10.03.2000 -> 15.03.1950 -> true
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $month   Month to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareMonth($month, $locale = null)
    {
        return $this->_month('cmp', $month, $locale);
    }


    /**
     * Returns the day as new date object
     * Example: 20.May.1986 -> 20.Jan.1970 00:00:00
     *
     * @param Zend_Locale $locale OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDay($locale = null)
    {
        return $this->copyPart(self::DAY_SHORT, $locale);
    }

    /**
     * Returns the calculated day
     *
     * @param string      $calc   Type of calculation to make
     * @param Zend_Date   $day    Day to calculate, when null the actual day is calculated
     * @param Zend_Locale $locale Locale for parsing input
     * @throws Zend_Date_Exception
     * @return Zend_Date|integer
     */
    private function _day($calc, $day, $locale)
    {
        if ($day === null) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $day must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($day instanceof Zend_Date) {
            $day = $day->toString(self::DAY_SHORT, 'iso', $locale);
        }

        if (is_numeric($day)) {
            $type = self::DAY_SHORT;
        } else if (is_array($day)) {
            if (isset($day['day']) === true) {
                $day = $day['day'];
                $type = self::WEEKDAY;
            } else {
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("no day given in array");
            }
        } else {
            switch (iconv_strlen($day, 'UTF-8')) {
                case 1 :
                    $type = self::WEEKDAY_NARROW;
                    break;
                case 2:
                    $type = self::WEEKDAY_NAME;
                    break;
                case 3:
                    $type = self::WEEKDAY_SHORT;
                    break;
                default:
                    $type = self::WEEKDAY;
                    break;
            }
        }
        $return = $this->_calcdetail($calc, $day, $type, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new day
     * The day can be a number or a string. Setting days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: setDay('Montag', 'de_AT'); will set the monday of this week as day.
     *
     * @param  string|integer|array|Zend_Date  $day     Day to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setDay($day, $locale = null)
    {
        return $this->_day('set', $day, $locale);
    }


    /**
     * Adds days to the existing date object.
     * The day can be a number or a string. Adding days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     *
     * @param  string|integer|array|Zend_Date  $day     Day to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addDay($day, $locale = null)
    {
        return $this->_day('add', $day, $locale);
    }


    /**
     * Subtracts days from the existing date object.
     * The day can be a number or a string. Subtracting days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     *
     * @param  string|integer|array|Zend_Date  $day     Day to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subDay($day, $locale = null)
    {
        return $this->_day('sub', $day, $locale);
    }


    /**
     * Compares the day with the existing date object, ignoring other date parts.
     * For example: 'Monday', 'en' -> 08.Jan.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $day     Day to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareDay($day, $locale = null)
    {
        return $this->_day('cmp', $day, $locale);
    }


    /**
     * Returns the weekday as new date object
     * Weekday is always from 1-7
     * Example: 09-Jan-2007 -> 2 = Tuesday -> 02-Jan-1970 (when 02.01.1970 is also Tuesday)
     *
     * @param Zend_Locale $locale OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getWeekday($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 'l';
        } else {
            $format = self::WEEKDAY;
        }

        return $this->copyPart($format, $locale);
    }


    /**
     * Returns the calculated weekday
     *
     * @param  string      $calc     Type of calculation to make
     * @param  Zend_Date   $weekday  Weekday to calculate, when null the actual weekday is calculated
     * @param  Zend_Locale $locale   Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _weekday($calc, $weekday, $locale)
    {
        if ($weekday === null) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $weekday must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($weekday instanceof Zend_Date) {
            $weekday = $weekday->toString(self::WEEKDAY_8601, 'iso', $locale);
        }

        if (is_numeric($weekday)) {
            $type = self::WEEKDAY_8601;
        } else if (is_array($weekday)) {
            if (isset($weekday['weekday']) === true) {
                $weekday = $weekday['weekday'];
                $type = self::WEEKDAY;
            } else {
                #require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("no weekday given in array");
            }
        } else {
            switch(iconv_strlen($weekday, 'UTF-8')) {
                case 1:
                    $type = self::WEEKDAY_NARROW;
                    break;
                case 2:
                    $type = self::WEEKDAY_NAME;
                    break;
                case 3:
                    $type = self::WEEKDAY_SHORT;
                    break;
                default:
                    $type = self::WEEKDAY;
                    break;
            }
        }
        $return = $this->_calcdetail($calc, $weekday, $type, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new weekday
     * The weekday can be a number or a string. If a localized weekday name is given,
     * then it will be parsed as a date in $locale (defaults to the same locale as $this).
     * Returned is the new date object.
     * Example: setWeekday(3); will set the wednesday of this week as day.
     *
     * @param  string|integer|array|Zend_Date  $weekday Weekday to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setWeekday($weekday, $locale = null)
    {
        return $this->_weekday('set', $weekday, $locale);
    }


    /**
     * Adds weekdays to the existing date object.
     * The weekday can be a number or a string.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: addWeekday(3); will add the difference of days from the begining of the month until
     * wednesday.
     *
     * @param  string|integer|array|Zend_Date  $weekday Weekday to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addWeekday($weekday, $locale = null)
    {
        return $this->_weekday('add', $weekday, $locale);
    }


    /**
     * Subtracts weekdays from the existing date object.
     * The weekday can be a number or a string.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: subWeekday(3); will subtract the difference of days from the begining of the month until
     * wednesday.
     *
     * @param  string|integer|array|Zend_Date  $weekday Weekday to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subWeekday($weekday, $locale = null)
    {
        return $this->_weekday('sub', $weekday, $locale);
    }


    /**
     * Compares the weekday with the existing date object, ignoring other date parts.
     * For example: 'Monday', 'en' -> 08.Jan.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $weekday  Weekday to compare
     * @param  string|Zend_Locale              $locale   OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareWeekday($weekday, $locale = null)
    {
        return $this->_weekday('cmp', $weekday, $locale);
    }


    /**
     * Returns the day of year as new date object
     * Example: 02.Feb.1986 10:00:00 -> 02.Feb.1970 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDayOfYear($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 'D';
        } else {
            $format = self::DAY_OF_YEAR;
        }

        return $this->copyPart($format, $locale);
    }


    /**
     * Sets a new day of year
     * The day of year is always a number.
     * Returned is the new date object
     * Example: 04.May.2004 -> setDayOfYear(10) -> 10.Jan.2004
     *
     * @param  string|integer|array|Zend_Date  $day     Day of Year to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setDayOfYear($day, $locale = null)
    {
        return $this->_calcvalue('set', $day, 'day of year', self::DAY_OF_YEAR, $locale);
    }


    /**
     * Adds a day of year to the existing date object.
     * The day of year is always a number.
     * Returned is the new date object
     * Example: addDayOfYear(10); will add 10 days to the existing date object.
     *
     * @param  string|integer|array|Zend_Date  $day     Day of Year to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addDayOfYear($day, $locale = null)
    {
        return $this->_calcvalue('add', $day, 'day of year', self::DAY_OF_YEAR, $locale);
    }


    /**
     * Subtracts a day of year from the existing date object.
     * The day of year is always a number.
     * Returned is the new date object
     * Example: subDayOfYear(10); will subtract 10 days from the existing date object.
     *
     * @param  string|integer|array|Zend_Date  $day     Day of Year to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subDayOfYear($day, $locale = null)
    {
        return $this->_calcvalue('sub', $day, 'day of year', self::DAY_OF_YEAR, $locale);
    }


    /**
     * Compares the day of year with the existing date object.
     * For example: compareDayOfYear(33) -> 02.Feb.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $day     Day of Year to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareDayOfYear($day, $locale = null)
    {
        return $this->_calcvalue('cmp', $day, 'day of year', self::DAY_OF_YEAR, $locale);
    }


    /**
     * Returns the hour as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 10:00:00
     *
     * @param Zend_Locale $locale OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getHour($locale = null)
    {
        return $this->copyPart(self::HOUR, $locale);
    }


    /**
     * Sets a new hour
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setHour(7); -> 04.May.1993 07:07:25
     *
     * @param  string|integer|array|Zend_Date  $hour    Hour to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setHour($hour, $locale = null)
    {
        return $this->_calcvalue('set', $hour, 'hour', self::HOUR_SHORT, $locale);
    }


    /**
     * Adds hours to the existing date object.
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addHour(12); -> 05.May.1993 01:07:25
     *
     * @param  string|integer|array|Zend_Date  $hour    Hour to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addHour($hour, $locale = null)
    {
        return $this->_calcvalue('add', $hour, 'hour', self::HOUR_SHORT, $locale);
    }


    /**
     * Subtracts hours from the existing date object.
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subHour(6); -> 05.May.1993 07:07:25
     *
     * @param  string|integer|array|Zend_Date  $hour    Hour to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subHour($hour, $locale = null)
    {
        return $this->_calcvalue('sub', $hour, 'hour', self::HOUR_SHORT, $locale);
    }


    /**
     * Compares the hour with the existing date object.
     * For example: 10:30:25 -> compareHour(10) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $hour    Hour to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareHour($hour, $locale = null)
    {
        return $this->_calcvalue('cmp', $hour, 'hour', self::HOUR_SHORT, $locale);
    }


    /**
     * Returns the minute as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 00:30:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getMinute($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 'i';
        } else {
            $format = self::MINUTE;
        }

        return $this->copyPart($format, $locale);
    }


    /**
     * Sets a new minute
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setMinute(29); -> 04.May.1993 13:29:25
     *
     * @param  string|integer|array|Zend_Date  $minute  Minute to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setMinute($minute, $locale = null)
    {
        return $this->_calcvalue('set', $minute, 'minute', self::MINUTE_SHORT, $locale);
    }


    /**
     * Adds minutes to the existing date object.
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addMinute(65); -> 04.May.1993 13:12:25
     *
     * @param  string|integer|array|Zend_Date  $minute  Minute to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addMinute($minute, $locale = null)
    {
        return $this->_calcvalue('add', $minute, 'minute', self::MINUTE_SHORT, $locale);
    }


    /**
     * Subtracts minutes from the existing date object.
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subMinute(9); -> 04.May.1993 12:58:25
     *
     * @param  string|integer|array|Zend_Date  $minute  Minute to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subMinute($minute, $locale = null)
    {
        return $this->_calcvalue('sub', $minute, 'minute', self::MINUTE_SHORT, $locale);
    }


    /**
     * Compares the minute with the existing date object.
     * For example: 10:30:25 -> compareMinute(30) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $minute  Hour to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareMinute($minute, $locale = null)
    {
        return $this->_calcvalue('cmp', $minute, 'minute', self::MINUTE_SHORT, $locale);
    }


    /**
     * Returns the second as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 00:00:25
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getSecond($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 's';
        } else {
            $format = self::SECOND;
        }

        return $this->copyPart($format, $locale);
    }


    /**
     * Sets new seconds to the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setSecond(100); -> 04.May.1993 13:08:40
     *
     * @param  string|integer|array|Zend_Date $second Second to set
     * @param  string|Zend_Locale             $locale (Optional) Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setSecond($second, $locale = null)
    {
        return $this->_calcvalue('set', $second, 'second', self::SECOND_SHORT, $locale);
    }


    /**
     * Adds seconds to the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addSecond(65); -> 04.May.1993 13:08:30
     *
     * @param  string|integer|array|Zend_Date $second Second to add
     * @param  string|Zend_Locale             $locale (Optional) Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addSecond($second, $locale = null)
    {
        return $this->_calcvalue('add', $second, 'second', self::SECOND_SHORT, $locale);
    }


    /**
     * Subtracts seconds from the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subSecond(10); -> 04.May.1993 13:07:15
     *
     * @param  string|integer|array|Zend_Date $second Second to sub
     * @param  string|Zend_Locale             $locale (Optional) Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subSecond($second, $locale = null)
    {
        return $this->_calcvalue('sub', $second, 'second', self::SECOND_SHORT, $locale);
    }


    /**
     * Compares the second with the existing date object.
     * For example: 10:30:25 -> compareSecond(25) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date $second Second to compare
     * @param  string|Zend_Locale             $locale (Optional) Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareSecond($second, $locale = null)
    {
        return $this->_calcvalue('cmp', $second, 'second', self::SECOND_SHORT, $locale);
    }


    /**
     * Returns the precision for fractional seconds
     *
     * @return integer
     */
    public function getFractionalPrecision()
    {
        return $this->_precision;
    }


    /**
     * Sets a new precision for fractional seconds
     *
     * @param  integer $precision Precision for the fractional datepart 3 = milliseconds
     * @throws Zend_Date_Exception
     * @return Zend_Date Provides a fluent interface
     */
    public function setFractionalPrecision($precision)
    {
        if (!intval($precision) or ($precision < 0) or ($precision > 9)) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", 0, null, $precision);
        }

        $this->_precision = (int) $precision;
        if ($this->_precision < strlen($this->_fractional)) {
            $this->_fractional = substr($this->_fractional, 0, $this->_precision);
        } else {
            $this->_fractional = str_pad($this->_fractional, $this->_precision, '0', STR_PAD_RIGHT);
        }

        return $this;
    }


    /**
     * Returns the milliseconds of the date object
     *
     * @return string
     */
    public function getMilliSecond()
    {
        return $this->_fractional;
    }

    /**
     * Sets new milliseconds for the date object
     * Example: setMilliSecond(550, 2) -> equals +5 Sec +50 MilliSec
     *
     * @param  integer|Zend_Date $milli     (Optional) Millisecond to set, when null the actual millisecond is set
     * @param  integer           $precision (Optional) Fraction precision of the given milliseconds
     * @throws Zend_Date_Exception
     * @return Zend_Date Provides a fluent interface
     */
    public function setMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
            $precision = 6;
        } else if (!is_numeric($milli)) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", 0, null, $milli);
        }

        if ($precision === null) {
            $precision = $this->_precision;
        }

        if (!is_int($precision) || $precision < 1 || $precision > 9) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", 0, null, $precision);
        }

        $this->_fractional = 0;
        $this->addMilliSecond($milli, $precision);
        return $this;
    }

    /**
     * Adds milliseconds to the date object
     *
     * @param  integer|Zend_Date $milli     (Optional) Millisecond to add, when null the actual millisecond is added
     * @param  integer           $precision (Optional) Fractional precision for the given milliseconds
     * @throws Zend_Date_Exception
     * @return Zend_Date Provides a fluent interface
     */
    public function addMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
        } else if (!is_numeric($milli)) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", 0, null, $milli);
        }

        if ($precision === null) {
            // Use internal default precision
            // Is not as logic as using the length of the input. But this would break tests and maybe other things
            // as an input value of integer 10, which is used in tests, must be parsed as 10 milliseconds (real milliseconds, precision 3)
            // but with auto-detect of precision, 100 milliseconds would be added.
            $precision = $this->_precision;
        }

        if (!is_int($precision) || $precision < 1 || $precision > 9) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception(
                "precision ($precision) must be a positive integer less than 10", 0, null, $precision
            );
        }

        if ($this->_precision > $precision) {
            $milli = $milli * pow(10, $this->_precision - $precision);
        } elseif ($this->_precision < $precision) {
            $milli = round($milli / pow(10, $precision - $this->_precision));
        }

        $this->_fractional += $milli;

        // Add/sub milliseconds + add/sub seconds
        $max = pow(10, $this->_precision);
        // Milli includes seconds
        if ($this->_fractional >= $max) {
            while ($this->_fractional >= $max) {
                $this->addSecond(1);
                $this->_fractional -= $max;
            }
        }

        if ($this->_fractional < 0) {
            while ($this->_fractional < 0) {
                $this->subSecond(1);
                $this->_fractional += $max;
            }
        }

        if ($this->_precision > strlen($this->_fractional)) {
            $this->_fractional = str_pad($this->_fractional, $this->_precision, '0', STR_PAD_LEFT);
        }

        return $this;
    }


    /**
     * Subtracts a millisecond
     *
     * @param  integer|Zend_Date $milli     (Optional) Millisecond to sub, when null the actual millisecond is subtracted
     * @param  integer           $precision (Optional) Fractional precision for the given milliseconds
     * @return Zend_Date Provides a fluent interface
     */
    public function subMilliSecond($milli = null, $precision = null)
    {
        $this->addMilliSecond(0 - $milli, $precision);
        return $this;
    }

    /**
     * Compares only the millisecond part, returning the difference
     *
     * @param  integer|Zend_Date  $milli  OPTIONAL Millisecond to compare, when null the actual millisecond is compared
     * @param  integer            $precision  OPTIONAL Fractional precision for the given milliseconds
     * @throws Zend_Date_Exception On invalid input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     */
    public function compareMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
        } else if (is_numeric($milli) === false) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", 0, null, $milli);
        }

        if ($precision === null) {
            $precision = strlen($milli);
        } else if (!is_int($precision) || $precision < 1 || $precision > 9) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", 0, null, $precision);
        }

        if ($precision === 0) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('precision is 0');
        }

        if ($precision != $this->_precision) {
            if ($precision > $this->_precision) {
                $diff = $precision - $this->_precision;
                $milli = (int) ($milli / (10 * $diff));
            } else {
                $diff = $this->_precision - $precision;
                $milli = (int) ($milli * (10 * $diff));
            }
        }

        $comp = $this->_fractional - $milli;
        if ($comp < 0) {
            return -1;
        } else if ($comp > 0) {
            return 1;
        }
        return 0;
    }

    /**
     * Returns the week as new date object using monday as begining of the week
     * Example: 12.Jan.2007 -> 08.Jan.1970 00:00:00
     *
     * @param Zend_Locale $locale OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getWeek($locale = null)
    {
        if (self::$_options['format_type'] == 'php') {
            $format = 'W';
        } else {
            $format = self::WEEK;
        }

        return $this->copyPart($format, $locale);
    }

    /**
     * Sets a new week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> setWeek(1); -> 02.Jan.2007 13:07:25
     *
     * @param  string|integer|array|Zend_Date  $week    Week to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function setWeek($week, $locale = null)
    {
        return $this->_calcvalue('set', $week, 'week', self::WEEK, $locale);
    }

    /**
     * Adds a week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> addWeek(1); -> 16.Jan.2007 13:07:25
     *
     * @param  string|integer|array|Zend_Date  $week    Week to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function addWeek($week, $locale = null)
    {
        return $this->_calcvalue('add', $week, 'week', self::WEEK, $locale);
    }

    /**
     * Subtracts a week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> subWeek(1); -> 02.Jan.2007 13:07:25
     *
     * @param  string|integer|array|Zend_Date  $week    Week to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date Provides a fluent interface
     * @throws Zend_Date_Exception
     */
    public function subWeek($week, $locale = null)
    {
        return $this->_calcvalue('sub', $week, 'week', self::WEEK, $locale);
    }

    /**
     * Compares only the week part, returning the difference
     * Returned is the new date object
     * Returns if equal, earlier or later
     * Example: 09.Jan.2007 13:07:25 -> compareWeek(2); -> 0
     *
     * @param  string|integer|array|Zend_Date  $week    Week to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer 0 = equal, 1 = later, -1 = earlier
     */
    public function compareWeek($week, $locale = null)
    {
        return $this->_calcvalue('cmp', $week, 'week', self::WEEK, $locale);
    }

    /**
     * Sets a new standard locale for the date object.
     * This locale will be used for all functions
     * Returned is the really set locale.
     * Example: 'de_XX' will be set to 'de' because 'de_XX' does not exist
     * 'xx_YY' will be set to 'root' because 'xx' does not exist
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for parsing input
     * @throws Zend_Date_Exception When the given locale does not exist
     * @return Zend_Date Provides fluent interface
     */
    public function setLocale($locale = null)
    {
        try {
            $this->_locale = Zend_Locale::findLocale($locale);
        } catch (Zend_Locale_Exception $e) {
            #require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception($e->getMessage(), 0, $e);
        }

        return $this;
    }

    /**
     * Returns the actual set locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Checks if the given date is a real date or datepart.
     * Returns false if a expected datepart is missing or a datepart exceeds its possible border.
     * But the check will only be done for the expected dateparts which are given by format.
     * If no format is given the standard dateformat for the actual locale is used.
     * f.e. 30.February.2007 will return false if format is 'dd.MMMM.YYYY'
     *
     * @param  string|array|Zend_Date $date   Date to parse for correctness
     * @param  string                 $format (Optional) Format for parsing the date string
     * @param  string|Zend_Locale     $locale (Optional) Locale for parsing date parts
     * @return boolean                True when all date parts are correct
     */
    public static function isDate($date, $format = null, $locale = null)
    {
        if (!is_string($date) && !is_numeric($date) && !($date instanceof Zend_Date) &&
            !is_array($date)) {
            return false;
        }

        if (($format !== null) && ($format != 'ee') && ($format != 'ss') && ($format != 'GG') && ($format != 'MM') && ($format != 'EE') && ($format != 'TT')
            && (Zend_Locale::isLocale($format, null, false))) {
            $locale = $format;
            $format = null;
        }

        $locale = Zend_Locale::findLocale($locale);

        if ($format === null) {
            $format = Zend_Locale_Format::getDateFormat($locale);
        } else if ((self::$_options['format_type'] == 'php') && !defined($format)) {
            $format = Zend_Locale_Format::convertPhpToIsoFormat($format);
        }

        $format = self::_getLocalizedToken($format, $locale);
        if (!is_array($date)) {
            try {
                $parsed = Zend_Locale_Format::getDate($date, array('locale' => $locale,
                    'date_format' => $format, 'format_type' => 'iso',
                    'fix_date' => false));
            } catch (Zend_Locale_Exception $e) {
                // Date can not be parsed
                return false;
            }
        } else {
            $parsed = $date;
        }

        if (((strpos($format, 'Y') !== false) or (strpos($format, 'y') !== false)) and
            (!isset($parsed['year']))) {
            // Year expected but not found
            return false;
        }

        if ((strpos($format, 'M') !== false) and (!isset($parsed['month']))) {
            // Month expected but not found
            return false;
        }

        if ((strpos($format, 'd') !== false) and (!isset($parsed['day']))) {
            // Day expected but not found
            return false;
        }

        if (((strpos($format, 'H') !== false) or (strpos($format, 'h') !== false)) and
            (!isset($parsed['hour']))) {
            // Hour expected but not found
            return false;
        }

        if ((strpos($format, 'm') !== false) and (!isset($parsed['minute']))) {
            // Minute expected but not found
            return false;
        }

        if ((strpos($format, 's') !== false) and (!isset($parsed['second']))) {
            // Second expected  but not found
            return false;
        }

        // Set not given dateparts
        if (isset($parsed['hour']) === false) {
            $parsed['hour'] = 12;
        }

        if (isset($parsed['minute']) === false) {
            $parsed['minute'] = 0;
        }

        if (isset($parsed['second']) === false) {
            $parsed['second'] = 0;
        }

        if (isset($parsed['month']) === false) {
            $parsed['month'] = 1;
        }

        if (isset($parsed['day']) === false) {
            $parsed['day'] = 1;
        }

        if (isset($parsed['year']) === false) {
            $parsed['year'] = 1970;
        }

        if (self::isYearLeapYear($parsed['year'])) {
            $parsed['year'] = 1972;
        } else {
            $parsed['year'] = 1971;
        }

        $date      = new self($parsed, null, $locale);
        $timestamp = $date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
            $parsed['month'], $parsed['day'], $parsed['year']);

        if ($parsed['year'] != $date->date('Y', $timestamp)) {
            // Given year differs from parsed year
            return false;
        }

        if ($parsed['month'] != $date->date('n', $timestamp)) {
            // Given month differs from parsed month
            return false;
        }

        if ($parsed['day'] != $date->date('j', $timestamp)) {
            // Given day differs from parsed day
            return false;
        }

        if ($parsed['hour'] != $date->date('G', $timestamp)) {
            // Given hour differs from parsed hour
            return false;
        }

        if ($parsed['minute'] != $date->date('i', $timestamp)) {
            // Given minute differs from parsed minute
            return false;
        }

        if ($parsed['second'] != $date->date('s', $timestamp)) {
            // Given second differs from parsed second
            return false;
        }

        return true;
    }

    /**
     * Returns the ISO Token for all localized constants
     *
     * @param string $token Token to normalize
     * @param string $locale Locale to search
     * @return string
     */
    protected static function _getLocalizedToken($token, $locale)
    {
        switch($token) {
            case self::ISO_8601 :
                return "yyyy-MM-ddThh:mm:ss";
                break;
            case self::RFC_2822 :
                return "EEE, dd MMM yyyy HH:mm:ss";
                break;
            case self::DATES :
                return Zend_Locale_Data::getContent($locale, 'date');
                break;
            case self::DATE_FULL :
                return Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'full'));
                break;
            case self::DATE_LONG :
                return Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'long'));
                break;
            case self::DATE_MEDIUM :
                return Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'medium'));
                break;
            case self::DATE_SHORT :
                return Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'short'));
                break;
            case self::TIMES :
                return Zend_Locale_Data::getContent($locale, 'time');
                break;
            case self::TIME_FULL :
                return Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'full'));
                break;
            case self::TIME_LONG :
                return Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'long'));
                break;
            case self::TIME_MEDIUM :
                return Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'medium'));
                break;
            case self::TIME_SHORT :
                return Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'short'));
                break;
            case self::DATETIME :
                return Zend_Locale_Data::getContent($locale, 'datetime');
                break;
            case self::DATETIME_FULL :
                return Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'full'));
                break;
            case self::DATETIME_LONG :
                return Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'long'));
                break;
            case self::DATETIME_MEDIUM :
                return Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'medium'));
                break;
            case self::DATETIME_SHORT :
                return Zend_Locale_Data::getContent($locale, 'datetime', array('gregorian', 'short'));
                break;
            case self::ATOM :
            case self::RFC_3339 :
            case self::W3C :
                return "yyyy-MM-DD HH:mm:ss";
                break;
            case self::COOKIE :
            case self::RFC_850 :
                return "EEEE, dd-MM-yyyy HH:mm:ss";
                break;
            case self::RFC_822 :
            case self::RFC_1036 :
            case self::RFC_1123 :
            case self::RSS :
                return "EEE, dd MM yyyy HH:mm:ss";
                break;
        }

        return $token;
    }

    /**
     * Get unix timestamp.
     * Added limitation: $year value must be between -10 000 and 10 000
     * Parent method implementation causes 504 error if it gets too big(small) year value
     *
     * @see Zend_Date_DateObject::mktime
     * @throws Zend_Date_Exception
     * @param $hour
     * @param $minute
     * @param $second
     * @param $month
     * @param $day
     * @param $year
     * @param bool $gmt
     * @return float|int
     */
    protected function mktime($hour, $minute, $second, $month, $day, $year, $gmt = false)
    {
        $day   = intval($day);
        $month = intval($month);
        $year  = intval($year);

        // correct months > 12 and months < 1
        if ($month > 12) {
            $overlap = floor($month / 12);
            $year   += $overlap;
            $month  -= $overlap * 12;
        } else {
            $overlap = ceil((1 - $month) / 12);
            $year   -= $overlap;
            $month  += $overlap * 12;
        }

        if ($year > self::YEAR_MAX_VALUE || $year < self::YEAR_MIN_VALUE) {
            throw new Zend_Date_Exception('Invalid year, it must be between ' . self::YEAR_MIN_VALUE . ' and '
                . self::YEAR_MAX_VALUE);
        }

        return parent::mktime($hour, $minute, $second, $month, $day, $year, $gmt);
    }
}
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Data
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * include needed classes
 */
#require_once 'Zend/Locale.php';

/** @see Zend_Xml_Security */
#require_once 'Zend/Xml/Security.php';

/**
 * Locale data reader, handles the CLDR
 *
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Data
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale_Data
{
    /**
     * Locale files
     *
     * @var array
     */
    private static $_ldml = array();

    /**
     * List of values which are collected
     *
     * @var array
     */
    private static $_list = array();

    /**
     * Internal cache for ldml values
     *
     * @var Zend_Cache_Core
     */
    private static $_cache = null;

    /**
     * Internal value to remember if cache supports tags
     *
     * @var boolean
     */
    private static $_cacheTags = false;

    /**
     * Internal option, cache disabled
     *
     * @var boolean
     */
    private static $_cacheDisabled = false;

    /**
     * Read the content from locale
     *
     * Can be called like:
     * <ldml>
     *     <delimiter>test</delimiter>
     *     <second type='myone'>content</second>
     *     <second type='mysecond'>content2</second>
     *     <third type='mythird' />
     * </ldml>
     *
     * Case 1: _readFile('ar','/ldml/delimiter')             -> returns [] = test
     * Case 1: _readFile('ar','/ldml/second[@type=myone]')   -> returns [] = content
     * Case 2: _readFile('ar','/ldml/second','type')         -> returns [myone] = content; [mysecond] = content2
     * Case 3: _readFile('ar','/ldml/delimiter',,'right')    -> returns [right] = test
     * Case 4: _readFile('ar','/ldml/third','type','myone')  -> returns [myone] = mythird
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $attribute
     * @param  string $value
     * @access private
     * @return array
     */
    private static function _readFile($locale, $path, $attribute, $value, $temp)
    {
        // without attribute - read all values
        // with attribute    - read only this value
        if (!empty(self::$_ldml[(string) $locale])) {

            $result = self::$_ldml[(string) $locale]->xpath($path);
            if (!empty($result)) {
                foreach ($result as &$found) {

                    if (empty($value)) {

                        if (empty($attribute)) {
                            // Case 1
                            $temp[] = (string) $found;
                        } else if (empty($temp[(string) $found[$attribute]])){
                            // Case 2
                            $temp[(string) $found[$attribute]] = (string) $found;
                        }

                    } else if (empty ($temp[$value])) {

                        if (empty($attribute)) {
                            // Case 3
                            $temp[$value] = (string) $found;
                        } else {
                            // Case 4
                            $temp[$value] = (string) $found[$attribute];
                        }

                    }
                }
            }
        }
        return $temp;
    }

    /**
     * Find possible routing to other path or locale
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $attribute
     * @param  string $value
     * @param  array  $temp
     * @return bool
     * @throws Zend_Locale_Exception
     */
    private static function _findRoute($locale, $path, $attribute, $value, &$temp)
    {
        // load locale file if not already in cache
        // needed for alias tag when referring to other locale
        if (empty(self::$_ldml[(string) $locale])) {
            $filename = dirname(__FILE__) . '/Data/' . $locale . '.xml';
            if (!file_exists($filename)) {
                #require_once 'Zend/Locale/Exception.php';
                throw new Zend_Locale_Exception("Missing locale file '$filename' for '$locale' locale.");
            }

            self::$_ldml[(string) $locale] = Zend_Xml_Security::scanFile($filename);
        }

        // search for 'alias' tag in the search path for redirection
        $search = '';
        $tok = strtok($path, '/');

        // parse the complete path
        if (!empty(self::$_ldml[(string) $locale])) {
            while ($tok !== false) {
                $search .=  '/' . $tok;
                if (strpos($search, '[@') !== false) {
                    while (strrpos($search, '[@') > strrpos($search, ']')) {
                        $tok = strtok('/');
                        if (empty($tok)) {
                            $search .= '/';
                        }
                        $search = $search . '/' . $tok;
                    }
                }
                $result = self::$_ldml[(string) $locale]->xpath($search . '/alias');

                // alias found
                if (!empty($result)) {

                    $source = $result[0]['source'];
                    $newpath = $result[0]['path'];

                    // new path - path //ldml is to ignore
                    if ($newpath != '//ldml') {
                        // other path - parse to make real path

                        while (substr($newpath,0,3) == '../') {
                            $newpath = substr($newpath, 3);
                            $search = substr($search, 0, strrpos($search, '/'));
                        }

                        // truncate ../ to realpath otherwise problems with alias
                        $path = $search . '/' . $newpath;
                        while (($tok = strtok('/'))!== false) {
                            $path = $path . '/' . $tok;
                        }
                    }

                    // reroute to other locale
                    if ($source != 'locale') {
                        $locale = $source;
                    }

                    $temp = self::_getFile($locale, $path, $attribute, $value, $temp);
                    return false;
                }

                $tok = strtok('/');
            }
        }
        return true;
    }

    /**
     * Read the right LDML file
     *
     * @param  string      $locale
     * @param  string      $path
     * @param  string|bool $attribute
     * @param  string|bool $value
     * @param  array       $temp
     * @return array
     * @throws Zend_Locale_Exception
     */
    private static function _getFile($locale, $path, $attribute = false, $value = false, $temp = array())
    {
        $result = self::_findRoute($locale, $path, $attribute, $value, $temp);
        if ($result) {
            $temp = self::_readFile($locale, $path, $attribute, $value, $temp);
        }

        // parse required locales reversive
        // example: when given zh_Hans_CN
        // 1. -> zh_Hans_CN
        // 2. -> zh_Hans
        // 3. -> zh
        // 4. -> root
        if (($locale != 'root') && ($result)) {
            // Search for parent locale
            if (false !== strpos($locale, '_')) {
                $parentLocale = self::getContent($locale, 'parentlocale');
                if ($parentLocale) {
                    $temp = self::_getFile($parentLocale, $path, $attribute, $value, $temp);
                }
            }

            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
            if (!empty($locale)) {
                $temp = self::_getFile($locale, $path, $attribute, $value, $temp);
            } else {
                $temp = self::_getFile('root', $path, $attribute, $value, $temp);
            }
        }
        return $temp;
    }

    /**
     * Find the details for supplemental calendar datas
     *
     * @param  string $locale Locale for Detaildata
     * @param  array  $list   List to search
     * @return string         Key for Detaildata
     */
    private static function _calendarDetail($locale, $list)
    {
        $ret = "001";
        foreach ($list as $key => $value) {
            if (strpos($locale, '_') !== false) {
                $locale = substr($locale, strpos($locale, '_') + 1);
            }
            if (strpos($key, $locale) !== false) {
                $ret = $key;
                break;
            }
        }
        return $ret;
    }

    /**
     * Internal function for checking the locale
     *
     * @param  string|Zend_Locale $locale Locale to check
     * @return string
     * @throws Zend_Locale_Exception
     */
    private static function _checkLocale($locale)
    {
        if (empty($locale)) {
            $locale = new Zend_Locale();
        }

        if (!(Zend_Locale::isLocale((string) $locale, null, false))) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("Locale (" . (string) $locale . ") is a unknown locale");
        }

        if (Zend_Locale::isAlias($locale)) {
            // Return a valid CLDR locale so that the XML file can be loaded.
            return Zend_Locale::getAlias($locale);
        }
        return (string) $locale;
    }

    /**
     * Read the LDML file, get a array of multipath defined value
     *
     * @param  string      $locale
     * @param  string      $path
     * @param  bool|string $value
     * @return array
     * @throws Zend_Locale_Exception
     */
    public static function getList($locale, $path, $value = false)
    {
        $locale = self::_checkLocale($locale);

        if (!isset(self::$_cache) && !self::$_cacheDisabled) {
            #require_once 'Zend/Cache.php';
            self::$_cache = Zend_Cache::factory(
                'Core',
                'File',
                array('automatic_serialization' => true),
                array());
        }

        $val = $value;
        if (is_array($value)) {
            $val = implode('_' , $value);
        }

        $val = urlencode($val);
        $id  = self::_filterCacheId('Zend_LocaleL_' . $locale . '_' . $path . '_' . $val);
        if (!self::$_cacheDisabled && ($result = self::$_cache->load($id))) {
            return unserialize($result);
        }

        $temp = array();
        switch(strtolower($path)) {
            case 'language':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/languages/language', 'type');
                break;

            case 'script':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/scripts/script', 'type');
                break;

            case 'territory':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/territories/territory', 'type');
                if ($value === 1) {
                    foreach($temp as $key => $value) {
                        if ((is_numeric($key) === false) and ($key != 'QO') and ($key != 'EU')) {
                            unset($temp[$key]);
                        }
                    }
                } else if ($value === 2) {
                    foreach($temp as $key => $value) {
                        if (is_numeric($key) or ($key == 'QO') or ($key == 'EU')) {
                            unset($temp[$key]);
                        }
                    }
                }
                break;

            case 'variant':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/variants/variant', 'type');
                break;

            case 'key':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/keys/key', 'type');
                break;

            case 'type':
                if (empty($value)) {
                    $temp = self::_getFile($locale, '/ldml/localeDisplayNames/types/type', 'type');
                } else {
                    if (($value == 'calendar') or
                        ($value == 'collation') or
                        ($value == 'currency')) {
                        $temp = self::_getFile($locale, '/ldml/localeDisplayNames/types/type[@key=\'' . $value . '\']', 'type');
                    } else {
                        $temp = self::_getFile($locale, '/ldml/localeDisplayNames/types/type[@type=\'' . $value . '\']', 'type');
                    }
                }
                break;

            case 'layout':
                $temp  = self::_getFile($locale, '/ldml/layout/orientation/characterOrder', '', 'characterOrder');
                $temp += self::_getFile($locale, '/ldml/layout/orientation/lineOrder', '', 'lineOrder');
                break;

            case 'contexttransform':
                if (empty($value)) {
                    $value = 'uiListOrMenu';
                }
                $temp = self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'languages\']/contextTransform[@type=\''.$value.'\']', '', 'languages');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'day-format-except-narrow\']/contextTransform[@type=\''.$value.'\']', '', 'day-format-except-narrow');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'day-standalone-except-narrow\']/contextTransform[@type=\''.$value.'\']', '', 'day-standalone-except-narrow');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'day-narrow\']/contextTransform[@type=\''.$value.'\']', '', 'day-narrow');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'month-format-except-narrow\']/contextTransform[@type=\''.$value.'\']', '', 'month-format-except-narrow');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'month-standalone-except-narrow\']/contextTransform[@type=\''.$value.'\']', '', 'month-standalone-except-narrow');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'month-narrow\']/contextTransform[@type=\''.$value.'\']', '', 'month-narrow');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'script\']/contextTransform[@type=\''.$value.'\']', '', 'script');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'territory\']/contextTransform[@type=\''.$value.'\']', '', 'territory');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'variant\']/contextTransform[@type=\''.$value.'\']', '', 'variant');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'key\']/contextTransform[@type=\''.$value.'\']', '', 'key');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'type\']/contextTransform[@type=\''.$value.'\']', '', 'type');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'era-name\']/contextTransform[@type=\''.$value.'\']', '', 'era-name');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'era-abbr\']/contextTransform[@type=\''.$value.'\']', '', 'era-abbr');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'era-narrow\']/contextTransform[@type=\''.$value.'\']', '', 'era-narrow');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'quater-format-wide\']/contextTransform[@type=\''.$value.'\']', '', 'quater-format-wide');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'quater-standalone-wide\']/contextTransform[@type=\''.$value.'\']', '', 'quater-standalone-wide');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'quater-abbreviated\']/contextTransform[@type=\''.$value.'\']', '', 'quater-abbreviated');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'quater-narrow\']/contextTransform[@type=\''.$value.'\']', '', 'quater-narrow');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'calendar-field\']/contextTransform[@type=\''.$value.'\']', '', 'calendar-field');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'symbol\']/contextTransform[@type=\''.$value.'\']', '', 'symbol');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'tense\']/contextTransform[@type=\''.$value.'\']', '', 'tense');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'zone-exemplarCity\']/contextTransform[@type=\''.$value.'\']', '', 'zone-exemplarCity');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'zone-long\']/contextTransform[@type=\''.$value.'\']', '', 'zone-long');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'zone-short\']/contextTransform[@type=\''.$value.'\']', '', 'zone-short');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'metazone-long\']/contextTransform[@type=\''.$value.'\']', '', 'metazone-long');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'metazone-short\']/contextTransform[@type=\''.$value.'\']', '', 'metazone-short');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'displayName-count\']/contextTransform[@type=\''.$value.'\']', '', 'displayName-count');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'displayName\']/contextTransform[@type=\''.$value.'\']', '', 'displayName');
                $temp += self::_getFile($locale, '/ldml/contextTransforms/contextTransformUsage[@type=\'unit-pattern\']/contextTransform[@type=\''.$value.'\']', '', 'unit-pattern');
                break;

            case 'characters':
                $temp  = self::_getFile($locale, '/ldml/characters/exemplarCharacters',                           '', 'characters');
                $temp += self::_getFile($locale, '/ldml/characters/exemplarCharacters[@type=\'auxiliary\']',      '', 'auxiliary');
                // $temp += self::_getFile($locale, '/ldml/characters/exemplarCharacters[@type=\'currencySymbol\']', '', 'currencySymbol');
                break;

            case 'delimiters':
                $temp  = self::_getFile($locale, '/ldml/delimiters/quotationStart',          '', 'quoteStart');
                $temp += self::_getFile($locale, '/ldml/delimiters/quotationEnd',            '', 'quoteEnd');
                $temp += self::_getFile($locale, '/ldml/delimiters/alternateQuotationStart', '', 'quoteStartAlt');
                $temp += self::_getFile($locale, '/ldml/delimiters/alternateQuotationEnd',   '', 'quoteEndAlt');
                break;

            case 'measurement':
                $temp  = self::_getFile('supplementalData', '/supplementalData/measurementData/measurementSystem[@type=\'metric\']', 'territories', 'metric');
                $temp += self::_getFile('supplementalData', '/supplementalData/measurementData/measurementSystem[@type=\'US\']',     'territories', 'US');
                $temp += self::_getFile('supplementalData', '/supplementalData/measurementData/paperSize[@type=\'A4\']',             'territories', 'A4');
                $temp += self::_getFile('supplementalData', '/supplementalData/measurementData/paperSize[@type=\'US-Letter\']',      'territories', 'US-Letter');
                break;

            case 'months':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp['context'] = "format";
                $temp['default'] = "wide";
                $temp['format']['abbreviated'] = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'abbreviated\']/month', 'type');
                $temp['format']['narrow']      = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'narrow\']/month', 'type');
                $temp['format']['wide']        = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'wide\']/month', 'type');
                $temp['stand-alone']['abbreviated']  = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'abbreviated\']/month', 'type');
                $temp['stand-alone']['narrow']       = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'narrow\']/month', 'type');
                $temp['stand-alone']['wide']         = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'wide\']/month', 'type');
                break;

            case 'month':
                if (empty($value)) {
                    $value = array("gregorian", "format", "wide");
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/months/monthContext[@type=\'' . $value[1] . '\']/monthWidth[@type=\'' . $value[2] . '\']/month', 'type');
                break;

            case 'days':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp['context'] = "format";
                $temp['default'] = "wide";
                $temp['format']['abbreviated'] = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'abbreviated\']/day', 'type');
                $temp['format']['narrow']      = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'narrow\']/day', 'type');
                $temp['format']['wide']        = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'wide\']/day', 'type');
                $temp['stand-alone']['abbreviated']  = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'abbreviated\']/day', 'type');
                $temp['stand-alone']['narrow']       = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'narrow\']/day', 'type');
                $temp['stand-alone']['wide']         = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'wide\']/day', 'type');
                break;

            case 'day':
                if (empty($value)) {
                    $value = array("gregorian", "format", "wide");
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/days/dayContext[@type=\'' . $value[1] . '\']/dayWidth[@type=\'' . $value[2] . '\']/day', 'type');
                break;

            case 'week':
                $minDays   = self::_calendarDetail($locale, self::_getFile('supplementalData', '/supplementalData/weekData/minDays', 'territories'));
                $firstDay  = self::_calendarDetail($locale, self::_getFile('supplementalData', '/supplementalData/weekData/firstDay', 'territories'));
                $weekStart = self::_calendarDetail($locale, self::_getFile('supplementalData', '/supplementalData/weekData/weekendStart', 'territories'));
                $weekEnd   = self::_calendarDetail($locale, self::_getFile('supplementalData', '/supplementalData/weekData/weekendEnd', 'territories'));

                $temp  = self::_getFile('supplementalData', "/supplementalData/weekData/minDays[@territories='" . $minDays . "']", 'count', 'minDays');
                $temp += self::_getFile('supplementalData', "/supplementalData/weekData/firstDay[@territories='" . $firstDay . "']", 'day', 'firstDay');
                $temp += self::_getFile('supplementalData', "/supplementalData/weekData/weekendStart[@territories='" . $weekStart . "']", 'day', 'weekendStart');
                $temp += self::_getFile('supplementalData', "/supplementalData/weekData/weekendEnd[@territories='" . $weekEnd . "']", 'day', 'weekendEnd');
                break;

            case 'quarters':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp['format']['abbreviated'] = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'abbreviated\']/quarter', 'type');
                $temp['format']['narrow']      = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'narrow\']/quarter', 'type');
                $temp['format']['wide']        = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'wide\']/quarter', 'type');
                $temp['stand-alone']['abbreviated']  = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'abbreviated\']/quarter', 'type');
                $temp['stand-alone']['narrow']       = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'narrow\']/quarter', 'type');
                $temp['stand-alone']['wide']         = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'wide\']/quarter', 'type');
                break;

            case 'quarter':
                if (empty($value)) {
                    $value = array("gregorian", "format", "wide");
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/quarters/quarterContext[@type=\'' . $value[1] . '\']/quarterWidth[@type=\'' . $value[2] . '\']/quarter', 'type');
                break;

            case 'eras':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp['names']       = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraNames/era', 'type');
                $temp['abbreviated'] = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraAbbr/era', 'type');
                $temp['narrow']      = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraNarrow/era', 'type');
                break;

            case 'era':
                if (empty($value)) {
                    $value = array("gregorian", "Abbr");
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/eras/era' . $value[1] . '/era', 'type');
                break;

            case 'date':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp  = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'full\']/dateFormat/pattern', '', 'full');
                $temp += self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'long\']/dateFormat/pattern', '', 'long');
                $temp += self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'medium\']/dateFormat/pattern', '', 'medium');
                $temp += self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'short\']/dateFormat/pattern', '', 'short');
                break;

            case 'time':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp  = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'full\']/timeFormat/pattern', '', 'full');
                $temp += self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'long\']/timeFormat/pattern', '', 'long');
                $temp += self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'medium\']/timeFormat/pattern', '', 'medium');
                $temp += self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'short\']/timeFormat/pattern', '', 'short');
                break;

            case 'datetime':
                if (empty($value)) {
                    $value = "gregorian";
                }

                $timefull = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'full\']/timeFormat/pattern', '', 'full');
                $timelong = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'long\']/timeFormat/pattern', '', 'long');
                $timemedi = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'medium\']/timeFormat/pattern', '', 'medi');
                $timeshor = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'short\']/timeFormat/pattern', '', 'shor');

                $datefull = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'full\']/dateFormat/pattern', '', 'full');
                $datelong = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'long\']/dateFormat/pattern', '', 'long');
                $datemedi = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'medium\']/dateFormat/pattern', '', 'medi');
                $dateshor = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'short\']/dateFormat/pattern', '', 'shor');

                $full = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'full\']/dateTimeFormat/pattern', '', 'full');
                $long = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'long\']/dateTimeFormat/pattern', '', 'long');
                $medi = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'medium\']/dateTimeFormat/pattern', '', 'medi');
                $shor = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'short\']/dateTimeFormat/pattern', '', 'shor');

                $temp['full']   = str_replace(array('{0}', '{1}'), array($timefull['full'], $datefull['full']), $full['full']);
                $temp['long']   = str_replace(array('{0}', '{1}'), array($timelong['long'], $datelong['long']), $long['long']);
                $temp['medium'] = str_replace(array('{0}', '{1}'), array($timemedi['medi'], $datemedi['medi']), $medi['medi']);
                $temp['short']  = str_replace(array('{0}', '{1}'), array($timeshor['shor'], $dateshor['shor']), $shor['shor']);
                break;

            case 'dateitem':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $_temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/availableFormats/dateFormatItem', 'id');
                foreach($_temp as $key => $found) {
                    $temp += self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/availableFormats/dateFormatItem[@id=\'' . $key . '\']', '', $key);
                }
                break;

            case 'dateinterval':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $_temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/intervalFormats/intervalFormatItem', 'id');
                foreach($_temp as $key => $found) {
                    $temp[$key] = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/intervalFormats/intervalFormatItem[@id=\'' . $key . '\']/greatestDifference', 'id');
                }
                break;

            case 'field':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp2 = self::_getFile($locale, '/ldml/dates/fields/field', 'type');
                // $temp2 = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/fields/field', 'type');
                foreach ($temp2 as $key => $keyvalue) {
                    $temp += self::_getFile($locale, '/ldml/dates/fields/field[@type=\'' . $key . '\']/displayName', '', $key);
                    // $temp += self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/fields/field[@type=\'' . $key . '\']/displayName', '', $key);
                }
                break;

            case 'relative':
                if (empty($value)) {
                    $value = "day";
                }
                $temp = self::_getFile($locale, '/ldml/dates/fields/field[@type=\'' . $value . '\']/relative', 'type');
                break;

            case 'symbols':
                $temp  = self::_getFile($locale, '/ldml/numbers/symbols/decimal',         '', 'decimal');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/group',           '', 'group');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/list',            '', 'list');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/percentSign',     '', 'percent');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/nativeZeroDigit', '', 'zero');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/patternDigit',    '', 'pattern');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/plusSign',        '', 'plus');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/minusSign',       '', 'minus');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/exponential',     '', 'exponent');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/perMille',        '', 'mille');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/infinity',        '', 'infinity');
                $temp += self::_getFile($locale, '/ldml/numbers/symbols/nan',             '', 'nan');
                break;

            case 'nametocurrency':
                $_temp = self::_getFile($locale, '/ldml/numbers/currencies/currency', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
                }
                break;

            case 'currencytoname':
                $_temp = self::_getFile($locale, '/ldml/numbers/currencies/currency', 'type');
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
                    if (!isset($val[$key])) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'currencysymbol':
                $_temp = self::_getFile($locale, '/ldml/numbers/currencies/currency', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/symbol', '', $key);
                }
                break;

            case 'question':
                $temp  = self::_getFile($locale, '/ldml/posix/messages/yesstr',  '', 'yes');
                $temp += self::_getFile($locale, '/ldml/posix/messages/nostr',   '', 'no');
                break;

            case 'currencyfraction':
                $_temp = self::_getFile('supplementalData', '/supplementalData/currencyData/fractions/info', 'iso4217');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $key . '\']', 'digits', $key);
                }
                break;

            case 'currencyrounding':
                $_temp = self::_getFile('supplementalData', '/supplementalData/currencyData/fractions/info', 'iso4217');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $key . '\']', 'rounding', $key);
                }
                break;

            case 'currencytoregion':
                $_temp = self::_getFile('supplementalData', '/supplementalData/currencyData/region', 'iso3166');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
                }
                break;

            case 'regiontocurrency':
                $_temp = self::_getFile('supplementalData', '/supplementalData/currencyData/region', 'iso3166');
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile('supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
                    if (!isset($val[$key])) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'regiontoterritory':
                $_temp = self::_getFile('supplementalData', '/supplementalData/territoryContainment/group', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
                }
                break;

            case 'territorytoregion':
                $_temp2 = self::_getFile('supplementalData', '/supplementalData/territoryContainment/group', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
                }
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'scripttolanguage':
                $_temp = self::_getFile('supplementalData', '/supplementalData/languageData/language', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
                    if (empty($temp[$key])) {
                        unset($temp[$key]);
                    }
                }
                break;

            case 'languagetoscript':
                $_temp2 = self::_getFile('supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
                }
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if (empty($found3)) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'territorytolanguage':
                $_temp = self::_getFile('supplementalData', '/supplementalData/languageData/language', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
                    if (empty($temp[$key])) {
                        unset($temp[$key]);
                    }
                }
                break;

            case 'languagetoterritory':
                $_temp2 = self::_getFile('supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
                }
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if (empty($found3)) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'timezonetowindows':
                $_temp = self::_getFile('windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone', 'other');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone[@other=\'' . $key . '\']', 'type', $key);
                }
                break;

            case 'windowstotimezone':
                $_temp = self::_getFile('windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone[@type=\'' .$key . '\']', 'other', $key);
                }
                break;

            case 'territorytotimezone':
                $_temp = self::_getFile('metaZones', '/supplementalData/metaZones/mapTimezones/mapZone', 'type');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('metaZones', '/supplementalData/metaZones/mapTimezones/mapZone[@type=\'' . $key . '\']', 'territory', $key);
                }
                break;

            case 'timezonetoterritory':
                $_temp = self::_getFile('metaZones', '/supplementalData/metaZones/mapTimezones/mapZone', 'territory');
                foreach ($_temp as $key => $found) {
                    $temp += self::_getFile('metaZones', '/supplementalData/metaZones/mapTimezones/mapZone[@territory=\'' . $key . '\']', 'type', $key);
                }
                break;

            case 'citytotimezone':
                $_temp = self::_getFile($locale, '/ldml/dates/timeZoneNames/zone', 'type');
                foreach($_temp as $key => $found) {
                    $temp += self::_getFile($locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
                }
                break;

            case 'timezonetocity':
                $_temp  = self::_getFile($locale, '/ldml/dates/timeZoneNames/zone', 'type');
                $temp = array();
                foreach($_temp as $key => $found) {
                    $temp += self::_getFile($locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
                    if (!empty($temp[$key])) {
                        $temp[$temp[$key]] = $key;
                    }
                    unset($temp[$key]);
                }
                break;

            case 'phonetoterritory':
                $_temp = self::_getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
                }
                break;

            case 'territorytophone':
                $_temp = self::_getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
                    if (!isset($val[$key])) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'numerictoterritory':
                $_temp = self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes', 'type');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\'' . $key . '\']', 'numeric', $key);
                }
                break;

            case 'territorytonumeric':
                $_temp = self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes', 'numeric');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@numeric=\'' . $key . '\']', 'type', $key);
                }
                break;

            case 'alpha3toterritory':
                $_temp = self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes', 'type');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\'' . $key . '\']', 'alpha3', $key);
                }
                break;

            case 'territorytoalpha3':
                $_temp = self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes', 'alpha3');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@alpha3=\'' . $key . '\']', 'type', $key);
                }
                break;

            case 'postaltoterritory':
                $_temp = self::_getFile('postalCodeData', '/supplementalData/postalCodeData/postCodeRegex', 'territoryId');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('postalCodeData', '/supplementalData/postalCodeData/postCodeRegex[@territoryId=\'' . $key . '\']', 'territoryId');
                }
                break;

            case 'numberingsystem':
                $_temp = self::_getFile('numberingSystems', '/supplementalData/numberingSystems/numberingSystem', 'id');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('numberingSystems', '/supplementalData/numberingSystems/numberingSystem[@id=\'' . $key . '\']', 'digits', $key);
                    if (empty($temp[$key])) {
                        unset($temp[$key]);
                    }
                }
                break;

            case 'chartofallback':
                $_temp = self::_getFile('characters', '/supplementalData/characters/character-fallback/character', 'value');
                foreach ($_temp as $key => $keyvalue) {
                    $temp2 = self::_getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
                    $temp[current($temp2)] = $key;
                }
                break;

            case 'fallbacktochar':
                $_temp = self::_getFile('characters', '/supplementalData/characters/character-fallback/character', 'value');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
                }
                break;

            case 'localeupgrade':
                $_temp = self::_getFile('likelySubtags', '/supplementalData/likelySubtags/likelySubtag', 'from');
                foreach ($_temp as $key => $keyvalue) {
                    $temp += self::_getFile('likelySubtags', '/supplementalData/likelySubtags/likelySubtag[@from=\'' . $key . '\']', 'to', $key);
                }
                break;

            case 'unit':
                $_temp = self::_getFile($locale, '/ldml/units/unitLength/unit', 'type');
                foreach($_temp as $key => $keyvalue) {
                    $_temp2 = self::_getFile($locale, '/ldml/units/unitLength/unit[@type=\'' . $key . '\']/unitPattern', 'count');
                    $temp[$key] = $_temp2;
                }
                break;

            default :
                #require_once 'Zend/Locale/Exception.php';
                throw new Zend_Locale_Exception("Unknown list ($path) for parsing locale data.");
                break;
        }

        if (isset(self::$_cache)) {
            if (self::$_cacheTags) {
                self::$_cache->save( serialize($temp), $id, array('Zend_Locale'));
            } else {
                self::$_cache->save( serialize($temp), $id);
            }
        }

        return $temp;
    }

    /**
     * Read the LDML file, get a single path defined value
     *
     * @param  string      $locale
     * @param  string      $path
     * @param  bool|string $value
     * @return string
     * @throws Zend_Locale_Exception
     */
    public static function getContent($locale, $path, $value = false)
    {
        $locale = self::_checkLocale($locale);

        if (!isset(self::$_cache) && !self::$_cacheDisabled) {
            #require_once 'Zend/Cache.php';
            self::$_cache = Zend_Cache::factory(
                'Core',
                'File',
                array('automatic_serialization' => true),
                array());
        }

        $val = $value;
        if (is_array($value)) {
            $val = implode('_' , $value);
        }
        $val = urlencode($val);
        $id  = self::_filterCacheId('Zend_LocaleC_' . $locale . '_' . $path . '_' . $val);
        if (!self::$_cacheDisabled && ($result = self::$_cache->load($id))) {
            return unserialize($result);
        }

        switch(strtolower($path)) {
            case 'language':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/languages/language[@type=\'' . $value . '\']', 'type');
                break;

            case 'script':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/scripts/script[@type=\'' . $value . '\']', 'type');
                break;

            case 'country':
            case 'territory':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/territories/territory[@type=\'' . $value . '\']', 'type');
                break;

            case 'variant':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/variants/variant[@type=\'' . $value . '\']', 'type');
                break;

            case 'key':
                $temp = self::_getFile($locale, '/ldml/localeDisplayNames/keys/key[@type=\'' . $value . '\']', 'type');
                break;

            case 'defaultcalendar':
                $givenLocale = new Zend_Locale($locale);
                $territory = $givenLocale->getRegion();
                unset($givenLocale);
                $temp = self::_getFile('supplementalData', '/supplementalData/calendarPreferenceData/calendarPreference[contains(@territories,\'' . $territory . '\')]', 'ordering', 'ordering');
                if (isset($temp['ordering'])) {
                    list($temp) = explode(' ', $temp['ordering']);
                } else {
                    $temp = 'gregorian';
                }
                break;

            case 'monthcontext':
                /* default context is always 'format'
                if (empty ($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/default', 'choice', 'context');
                */
                $temp = 'format';
                break;

            case 'defaultmonth':
                /* default width is always 'wide'
                if (empty ($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/default', 'choice', 'default');
                */
                $temp = 'wide';
                break;

            case 'month':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/months/monthContext[@type=\'' . $value[1] . '\']/monthWidth[@type=\'' . $value[2] . '\']/month[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'daycontext':
                /* default context is always 'format'
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/default', 'choice', 'context');
                */
                $temp = 'format';
                break;

            case 'defaultday':
                /* default width is always 'wide'
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/default', 'choice', 'default');
                */
                $temp = 'wide';
                break;

            case 'day':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/days/dayContext[@type=\'' . $value[1] . '\']/dayWidth[@type=\'' . $value[2] . '\']/day[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'quarter':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/quarters/quarterContext[@type=\'' . $value[1] . '\']/quarterWidth[@type=\'' . $value[2] . '\']/quarter[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'am':
                if (empty($value)) {
                    $value = array("gregorian", "format", "wide");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array($temp, "format", "wide");
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dayPeriods/dayPeriodContext[@type=\'' . $value[1] . '\']/dayPeriodWidth[@type=\'' . $value[2] . '\']/dayPeriod[@type=\'am\']', '', 'dayPeriod');
                break;

            case 'pm':
                if (empty($value)) {
                    $value = array("gregorian", "format", "wide");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array($temp, "format", "wide");
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dayPeriods/dayPeriodContext[@type=\'' . $value[1] . '\']/dayPeriodWidth[@type=\'' . $value[2] . '\']/dayPeriod[@type=\'pm\']', '', 'dayPeriod');
                break;

            case 'era':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "Abbr", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/eras/era' . $value[1] . '/era[@type=\'' . $value[2] . '\']', 'type');
                break;

            case 'defaultdate':
                /* default choice is deprecated in CDLR - should be always medium here
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/default', 'choice', 'default');
                */
                $temp = 'medium';
                break;

            case 'date':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateFormats/dateFormatLength[@type=\'' . $value[1] . '\']/dateFormat/pattern', '', 'pattern');
                break;

            case 'defaulttime':
                /* default choice is deprecated in CDLR - should be always medium here
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/default', 'choice', 'default');
                */
                $temp = 'medium';
                break;

            case 'time':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/timeFormats/timeFormatLength[@type=\'' . $value[1] . '\']/timeFormat/pattern', '', 'pattern');
                break;

            case 'datetime':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }

                $date     = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateFormats/dateFormatLength[@type=\'' . $value[1] . '\']/dateFormat/pattern', '', 'pattern');
                $time     = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/timeFormats/timeFormatLength[@type=\'' . $value[1] . '\']/timeFormat/pattern', '', 'pattern');
                $datetime = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'' . $value[1] . '\']/dateTimeFormat/pattern', '', 'pattern');
                $temp = str_replace(array('{0}', '{1}'), array(current($time), current($date)), current($datetime));
                break;

            case 'dateitem':
                if (empty($value)) {
                    $value = array("gregorian", "yyMMdd");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/availableFormats/dateFormatItem[@id=\'' . $value[1] . '\']', '');
                break;

            case 'dateinterval':
                if (empty($value)) {
                    $value = array("gregorian", "yMd", "y");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp, $temp[0]);
                }
                $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/intervalFormats/intervalFormatItem[@id=\'' . $value[1] . '\']/greatestDifference[@id=\'' . $value[2] . '\']', '');
                break;

            case 'field':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/fields/field[@type=\'' . $value[1] . '\']/displayName', '', $value[1]);
                break;

            case 'relative':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = self::_getFile($locale, '/ldml/dates/fields/field[@type=\'day\']/relative[@type=\'' . $value[1] . '\']', '', $value[1]);
                // $temp = self::_getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/fields/field/relative[@type=\'' . $value[1] . '\']', '', $value[1]);
                break;

            case 'defaultnumberingsystem':
                $temp = self::_getFile($locale, '/ldml/numbers/defaultNumberingSystem', '', 'default');
                break;

            case 'decimalnumber':
                $temp = self::_getFile($locale, '/ldml/numbers/decimalFormats/decimalFormatLength/decimalFormat/pattern', '', 'default');
                break;

            case 'scientificnumber':
                $temp = self::_getFile($locale, '/ldml/numbers/scientificFormats/scientificFormatLength/scientificFormat/pattern', '', 'default');
                break;

            case 'percentnumber':
                $temp = self::_getFile($locale, '/ldml/numbers/percentFormats/percentFormatLength/percentFormat/pattern', '', 'default');
                break;

            case 'currencynumber':
                $temp = self::_getFile($locale, '/ldml/numbers/currencyFormats/currencyFormatLength/currencyFormat/pattern', '', 'default');
                break;

            case 'nametocurrency':
                $temp = self::_getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/displayName', '', $value);
                break;

            case 'currencytoname':
                $temp = self::_getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/displayName', '', $value);
                $_temp = self::_getFile($locale, '/ldml/numbers/currencies/currency', 'type');
                $temp = array();
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
                    if (!isset($val[$key]) or ($val[$key] != $value)) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'currencysymbol':
                $temp = self::_getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/symbol', '', $value);
                break;

            case 'question':
                $temp = self::_getFile($locale, '/ldml/posix/messages/' . $value . 'str',  '', $value);
                break;

            case 'currencyfraction':
                if (empty($value)) {
                    $value = "DEFAULT";
                }
                $temp = self::_getFile('supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $value . '\']', 'digits', 'digits');
                break;

            case 'currencyrounding':
                if (empty($value)) {
                    $value = "DEFAULT";
                }
                $temp = self::_getFile('supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $value . '\']', 'rounding', 'rounding');
                break;

            case 'currencytoregion':
                $temp = self::_getFile('supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $value . '\']/currency', 'iso4217', $value);
                break;

            case 'regiontocurrency':
                $_temp = self::_getFile('supplementalData', '/supplementalData/currencyData/region', 'iso3166');
                $temp = array();
                foreach ($_temp as $key => $keyvalue) {
                    $val = self::_getFile('supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
                    if (!isset($val[$key]) or ($val[$key] != $value)) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'regiontoterritory':
                $temp = self::_getFile('supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $value . '\']', 'contains', $value);
                break;

            case 'territorytoregion':
                $_temp2 = self::_getFile('supplementalData', '/supplementalData/territoryContainment/group', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'scripttolanguage':
                $temp = self::_getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $value . '\']', 'scripts', $value);
                break;

            case 'languagetoscript':
                $_temp2 = self::_getFile('supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'territorytolanguage':
                $temp = self::_getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $value . '\']', 'territories', $value);
                break;

            case 'languagetoterritory':
                $_temp2 = self::_getFile('supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'timezonetowindows':
                $temp = self::_getFile('windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone[@other=\''.$value.'\']', 'type', $value);
                break;

            case 'windowstotimezone':
                $temp = self::_getFile('windowsZones', '/supplementalData/windowsZones/mapTimezones/mapZone[@type=\''.$value.'\']', 'other', $value);
                break;

            case 'territorytotimezone':
                $temp = self::_getFile('metaZones', '/supplementalData/metaZones/mapTimezones/mapZone[@type=\'' . $value . '\']', 'territory', $value);
                break;

            case 'timezonetoterritory':
                $temp = self::_getFile('metaZones', '/supplementalData/metaZones/mapTimezones/mapZone[@territory=\'' . $value . '\']', 'type', $value);
                break;

            case 'citytotimezone':
                $temp = self::_getFile($locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $value . '\']/exemplarCity', '', $value);
                break;

            case 'timezonetocity':
                $_temp  = self::_getFile($locale, '/ldml/dates/timeZoneNames/zone', 'type');
                $temp = array();
                foreach($_temp as $key => $found) {
                    $temp += self::_getFile($locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
                    if (!empty($temp[$key])) {
                        if ($temp[$key] == $value) {
                            $temp[$temp[$key]] = $key;
                        }
                    }
                    unset($temp[$key]);
                }
                break;

            case 'phonetoterritory':
                $temp = self::_getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $value . '\']/telephoneCountryCode', 'code', $value);
                break;

            case 'territorytophone':
                $_temp2 = self::_getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += self::_getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'numerictoterritory':
                $temp = self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\''.$value.'\']', 'numeric', $value);
                break;

            case 'territorytonumeric':
                $temp = self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@numeric=\''.$value.'\']', 'type', $value);
                break;

            case 'alpha3toterritory':
                $temp = self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\''.$value.'\']', 'alpha3', $value);
                break;

            case 'territorytoalpha3':
                $temp = self::_getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@alpha3=\''.$value.'\']', 'type', $value);
                break;

            case 'postaltoterritory':
                $temp = self::_getFile('postalCodeData', '/supplementalData/postalCodeData/postCodeRegex[@territoryId=\'' . $value . '\']', 'territoryId');
                break;

            case 'numberingsystem':
                $temp = self::_getFile('numberingSystems', '/supplementalData/numberingSystems/numberingSystem[@id=\'' . strtolower($value) . '\']', 'digits', $value);
                break;

            case 'chartofallback':
                $_temp = self::_getFile('characters', '/supplementalData/characters/character-fallback/character', 'value');
                foreach ($_temp as $key => $keyvalue) {
                    $temp2 = self::_getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
                    if (current($temp2) == $value) {
                        $temp = $key;
                    }
                }
                break;

                $temp = self::_getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $value . '\']/substitute', '', $value);
                break;

            case 'fallbacktochar':
                $temp = self::_getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $value . '\']/substitute', '');
                break;

            case 'localeupgrade':
                $temp = self::_getFile('likelySubtags', '/supplementalData/likelySubtags/likelySubtag[@from=\'' . $value . '\']', 'to', $value);
                break;

            case 'unit':
                $temp = self::_getFile($locale, '/ldml/units/unitLength/unit[@type=\'' . $value[0] . '\']/unitPattern[@count=\'' . $value[1] . '\']', '');
                break;

            case 'parentlocale':
                if (false === $value) {
                    $value = $locale;
                }
                $temp = self::_getFile('supplementalData', "/supplementalData/parentLocales/parentLocale[contains(@locales, '" . $value . "')]", 'parent', 'parent');
                break;

            default :
                #require_once 'Zend/Locale/Exception.php';
                throw new Zend_Locale_Exception("Unknown detail ($path) for parsing locale data.");
                break;
        }

        if (is_array($temp)) {
            $temp = current($temp);
        }
        if (isset(self::$_cache)) {
            if (self::$_cacheTags) {
                self::$_cache->save( serialize($temp), $id, array('Zend_Locale'));
            } else {
                self::$_cache->save( serialize($temp), $id);
            }
        }

        return $temp;
    }

    /**
     * Returns the set cache
     *
     * @return Zend_Cache_Core The set cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Set a cache for Zend_Locale_Data
     *
     * @param Zend_Cache_Core $cache A cache frontend
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        self::$_cache = $cache;
        self::_getTagSupportForCache();
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        if (self::$_cache !== null) {
            return true;
        }

        return false;
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        self::$_cache = null;
    }

    /**
     * Clears all set cache data
     *
     * @return void
     */
    public static function clearCache()
    {
        if (self::$_cacheTags) {
            self::$_cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('Zend_Locale'));
        } else {
            self::$_cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        }
    }

    /**
     * Disables the cache
     *
     * @param bool $flag
     */
    public static function disableCache($flag)
    {
        self::$_cacheDisabled = (boolean) $flag;
    }

    /**
     * Internal method to check if the given cache supports tags
     *
     * @return bool
     */
    private static function _getTagSupportForCache()
    {
        $backend = self::$_cache->getBackend();
        if ($backend instanceof Zend_Cache_Backend_ExtendedInterface) {
            $cacheOptions = $backend->getCapabilities();
            self::$_cacheTags = $cacheOptions['tags'];
        } else {
            self::$_cacheTags = false;
        }

        return self::$_cacheTags;
    }

    /**
     * Filter an ID to only allow valid variable characters
     *
     * @param  string $value
     * @return string
     */
    protected static function _filterCacheId($value)
    {
        return strtr(
            $value,
            array(
                '-' => '_',
                '%' => '_',
                '+' => '_',
                '.' => '_',
            )
        );
    }
}
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Locale
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * Base class for localization
 *
 * @category  Zend
 * @package   Zend_Locale
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale
{
    /**
     * List of locales that are no longer part of CLDR along with a
     * mapping to an appropriate alternative.
     *
     * @var array
     */
    private static $_localeAliases = array(
        'az_AZ'  => 'az_Latn_AZ',
        'bs_BA'  => 'bs_Latn_BA',
        'ha_GH'  => 'ha_Latn_GH',
        'ha_NE'  => 'ha_Latn_NE',
        'ha_NG'  => 'ha_Latn_NG',
        'kk_KZ'  => 'kk_Cyrl_KZ',
        'ks_IN'  => 'ks_Arab_IN',
        'mn_MN'  => 'mn_Cyrl_MN',
        'ms_BN'  => 'ms_Latn_BN',
        'ms_MY'  => 'ms_Latn_MY',
        'ms_SG'  => 'ms_Latn_SG',
        'pa_IN'  => 'pa_Guru_IN',
        'pa_PK'  => 'pa_Arab_PK',
        'shi_MA' => 'shi_Latn_MA',
        'sr_BA'  => 'sr_Latn_BA',
        'sr_ME'  => 'sr_Latn_ME',
        'sr_RS'  => 'sr_Latn_RS',
        'sr_XK'  => 'sr_Latn_XK',
        'tg_TJ'  => 'tg_Cyrl_TJ',
        'tzm_MA' => 'tzm_Latn_MA',
        'uz_AF'  => 'uz_Arab_AF',
        'uz_UZ'  => 'uz_Latn_UZ',
        'vai_LR' => 'vai_Latn_LR',
        'zh_CN' => 'zh_Hans_CN',
        'zh_HK' => 'zh_Hant_HK',
        'zh_MO' => 'zh_Hans_MO',
        'zh_SG' => 'zh_Hans_SG',
        'zh_TW' => 'zh_Hant_TW',
    );

    /**
     * Class wide Locale Constants
     *
     * @var array $_localeData
     */
    private static $_localeData = array(
        'root'        => true,
        'aa'          => true,
        'aa_DJ'       => true,
        'aa_ER'       => true,
        'aa_ET'       => true,
        'af'          => true,
        'af_NA'       => true,
        'af_ZA'       => true,
        'agq'         => true,
        'agq_CM'      => true,
        'ak'          => true,
        'ak_GH'       => true,
        'am'          => true,
        'am_ET'       => true,
        'ar'          => true,
        'ar_001'      => true,
        'ar_AE'       => true,
        'ar_BH'       => true,
        'ar_DJ'       => true,
        'ar_DZ'       => true,
        'ar_EG'       => true,
        'ar_EH'       => true,
        'ar_ER'       => true,
        'ar_IL'       => true,
        'ar_IQ'       => true,
        'ar_JO'       => true,
        'ar_KM'       => true,
        'ar_KW'       => true,
        'ar_LB'       => true,
        'ar_LY'       => true,
        'ar_MA'       => true,
        'ar_MR'       => true,
        'ar_OM'       => true,
        'ar_PS'       => true,
        'ar_QA'       => true,
        'ar_SA'       => true,
        'ar_SD'       => true,
        'ar_SO'       => true,
        'ar_SS'       => true,
        'ar_SY'       => true,
        'ar_TD'       => true,
        'ar_TN'       => true,
        'ar_YE'       => true,
        'as'          => true,
        'as_IN'       => true,
        'asa'         => true,
        'asa_TZ'      => true,
        'ast'         => true,
        'ast_ES'      => true,
        'az'          => true,
        'az_Cyrl'     => true,
        'az_Cyrl_AZ'  => true,
        'az_Latn'     => true,
        'az_Latn_AZ'  => true,
        'bas'         => true,
        'bas_CM'      => true,
        'be'          => true,
        'be_BY'       => true,
        'bem'         => true,
        'bem_ZM'      => true,
        'bez'         => true,
        'bez_TZ'      => true,
        'bg'          => true,
        'bg_BG'       => true,
        'bm'          => true,
        'bm_ML'       => true,
        'bn'          => true,
        'bn_BD'       => true,
        'bn_IN'       => true,
        'bo'          => true,
        'bo_CN'       => true,
        'bo_IN'       => true,
        'br'          => true,
        'br_FR'       => true,
        'brx'         => true,
        'brx_IN'      => true,
        'bs'          => true,
        'bs_Cyrl'     => true,
        'bs_Cyrl_BA'  => true,
        'bs_Latn'     => true,
        'bs_Latn_BA'  => true,
        'byn'         => true,
        'byn_ER'      => true,
        'ca'          => true,
        'ca_AD'       => true,
        'ca_ES'       => true,
        'ca_ES_VALENCIA' => true,
        'ca_FR'       => true,
        'ca_IT'       => true,
        'cgg'         => true,
        'cgg_UG'      => true,
        'chr'         => true,
        'chr_US'      => true,
        'cs'          => true,
        'cs_CZ'       => true,
        'cy'          => true,
        'cy_GB'       => true,
        'da'          => true,
        'da_DK'       => true,
        'da_GL'       => true,
        'dav'         => true,
        'dav_KE'      => true,
        'de'          => true,
        'de_AT'       => true,
        'de_BE'       => true,
        'de_CH'       => true,
        'de_DE'       => true,
        'de_LI'       => true,
        'de_LU'       => true,
        'dje'         => true,
        'dje_NE'      => true,
        'dua'         => true,
        'dua_CM'      => true,
        'dyo'         => true,
        'dyo_SN'      => true,
        'dz'          => true,
        'dz_BT'       => true,
        'ebu'         => true,
        'ebu_KE'      => true,
        'ee'          => true,
        'ee_GH'       => true,
        'ee_TG'       => true,
        'el'          => true,
        'el_CY'       => true,
        'el_GR'       => true,
        'en'          => true,
        'en_001'      => true,
        'en_150'      => true,
        'en_AG'       => true,
        'en_AI'       => true,
        'en_AS'       => true,
        'en_AU'       => true,
        'en_BB'       => true,
        'en_BE'       => true,
        'en_BM'       => true,
        'en_BS'       => true,
        'en_BW'       => true,
        'en_BZ'       => true,
        'en_CA'       => true,
        'en_CC'       => true,
        'en_CK'       => true,
        'en_CM'       => true,
        'en_CX'       => true,
        'en_DG'       => true,
        'en_DM'       => true,
        'en_Dsrt'     => true,
        'en_Dsrt_US'  => true,
        'en_ER'       => true,
        'en_FJ'       => true,
        'en_FK'       => true,
        'en_FM'       => true,
        'en_GB'       => true,
        'en_GD'       => true,
        'en_GG'       => true,
        'en_GH'       => true,
        'en_GI'       => true,
        'en_GM'       => true,
        'en_GU'       => true,
        'en_GY'       => true,
        'en_HK'       => true,
        'en_IE'       => true,
        'en_IM'       => true,
        'en_IN'       => true,
        'en_IO'       => true,
        'en_JE'       => true,
        'en_JM'       => true,
        'en_KE'       => true,
        'en_KI'       => true,
        'en_KN'       => true,
        'en_KY'       => true,
        'en_LC'       => true,
        'en_LR'       => true,
        'en_LS'       => true,
        'en_MG'       => true,
        'en_MH'       => true,
        'en_MO'       => true,
        'en_MP'       => true,
        'en_MS'       => true,
        'en_MT'       => true,
        'en_MU'       => true,
        'en_MW'       => true,
        'en_NA'       => true,
        'en_NF'       => true,
        'en_NG'       => true,
        'en_NR'       => true,
        'en_NU'       => true,
        'en_NZ'       => true,
        'en_PG'       => true,
        'en_PH'       => true,
        'en_PK'       => true,
        'en_PN'       => true,
        'en_PR'       => true,
        'en_PW'       => true,
        'en_RW'       => true,
        'en_SB'       => true,
        'en_SC'       => true,
        'en_SD'       => true,
        'en_SG'       => true,
        'en_SH'       => true,
        'en_SL'       => true,
        'en_SS'       => true,
        'en_SX'       => true,
        'en_SZ'       => true,
        'en_TC'       => true,
        'en_TK'       => true,
        'en_TO'       => true,
        'en_TT'       => true,
        'en_TV'       => true,
        'en_TZ'       => true,
        'en_UG'       => true,
        'en_UM'       => true,
        'en_US'       => true,
        'en_US_POSIX' => true,
        'en_VC'       => true,
        'en_VG'       => true,
        'en_VI'       => true,
        'en_VU'       => true,
        'en_WS'       => true,
        'en_ZA'       => true,
        'en_ZM'       => true,
        'en_ZW'       => true,
        'eo'          => true,
        'eo_001'      => true,
        'es'          => true,
        'es_419'      => true,
        'es_AR'       => true,
        'es_BO'       => true,
        'es_CL'       => true,
        'es_CO'       => true,
        'es_CR'       => true,
        'es_CU'       => true,
        'es_DO'       => true,
        'es_EA'       => true,
        'es_EC'       => true,
        'es_ES'       => true,
        'es_GQ'       => true,
        'es_GT'       => true,
        'es_HN'       => true,
        'es_IC'       => true,
        'es_MX'       => true,
        'es_NI'       => true,
        'es_PA'       => true,
        'es_PE'       => true,
        'es_PH'       => true,
        'es_PR'       => true,
        'es_PY'       => true,
        'es_SV'       => true,
        'es_US'       => true,
        'es_UY'       => true,
        'es_VE'       => true,
        'et'          => true,
        'et_EE'       => true,
        'eu'          => true,
        'eu_ES'       => true,
        'ewo'         => true,
        'ewo_CM'      => true,
        'fa'          => true,
        'fa_AF'       => true,
        'fa_IR'       => true,
        'ff'          => true,
        'ff_CM'       => true,
        'ff_GN'       => true,
        'ff_MR'       => true,
        'fr_PM'       => true,
        'ff_SN'       => true,
        'fr_WF'       => true,
        'fi'          => true,
        'fi_FI'       => true,
        'fil'         => true,
        'fil_PH'      => true,
        'fo'          => true,
        'fo_FO'       => true,
        'fr'          => true,
        'fr_BE'       => true,
        'fr_BF'       => true,
        'fr_BI'       => true,
        'fr_BJ'       => true,
        'fr_BL'       => true,
        'fr_CA'       => true,
        'fr_CD'       => true,
        'fr_CF'       => true,
        'fr_CG'       => true,
        'fr_CH'       => true,
        'fr_CI'       => true,
        'fr_CM'       => true,
        'fr_DJ'       => true,
        'fr_DZ'       => true,
        'fr_FR'       => true,
        'fr_GA'       => true,
        'fr_GF'       => true,
        'fr_GN'       => true,
        'fr_GP'       => true,
        'fr_GQ'       => true,
        'fr_HT'       => true,
        'fr_KM'       => true,
        'fr_LU'       => true,
        'fr_MA'       => true,
        'fr_MC'       => true,
        'fr_MF'       => true,
        'fr_MG'       => true,
        'fr_ML'       => true,
        'fr_MQ'       => true,
        'fr_MR'       => true,
        'fr_MU'       => true,
        'fr_NC'       => true,
        'fr_NE'       => true,
        'fr_PF'       => true,
        'fr_RE'       => true,
        'fr_RW'       => true,
        'fr_SC'       => true,
        'fr_SN'       => true,
        'fr_SY'       => true,
        'fr_TD'       => true,
        'fr_TG'       => true,
        'fr_TN'       => true,
        'fr_VU'       => true,
        'fr_YT'       => true,
        'fur'         => true,
        'fur_IT'      => true,
        'fy'          => true,
        'fy_NL'       => true,
        'ga'          => true,
        'ga_IE'       => true,
        'gd'          => true,
        'gd_GB'       => true,
        'gl'          => true,
        'gl_ES'       => true,
        'gsw'         => true,
        'gsw_CH'      => true,
        'gsw_LI'      => true,
        'gu'          => true,
        'gu_IN'       => true,
        'guz'         => true,
        'guz_KE'      => true,
        'gv'          => true,
        'gv_IM'       => true,
        'ha'          => true,
        'ha_Latn'     => true,
        'ha_Latn_GH'  => true,
        'ha_Latn_NE'  => true,
        'ha_Latn_NG'  => true,
        'haw'         => true,
        'haw_US'      => true,
        'he'          => true,
        'he_IL'       => true,
        'hi'          => true,
        'hi_IN'       => true,
        'hr'          => true,
        'hr_BA'       => true,
        'hr_HR'       => true,
        'hu'          => true,
        'hu_HU'       => true,
        'hy'          => true,
        'hy_AM'       => true,
        'ia'          => true,
        'ia_FR'       => true,
        'id'          => true,
        'id_ID'       => true,
        'ig'          => true,
        'ig_NG'       => true,
        'ii'          => true,
        'ii_CN'       => true,
        'is'          => true,
        'is_IS'       => true,
        'it'          => true,
        'it_CH'       => true,
        'it_IT'       => true,
        'it_SM'       => true,
        'ja'          => true,
        'ja_JP'       => true,
        'jgo'         => true,
        'jgo_CM'      => true,
        'jmc'         => true,
        'jmc_TZ'      => true,
        'ka'          => true,
        'ka_GE'       => true,
        'kab'         => true,
        'kab_DZ'      => true,
        'kam'         => true,
        'kam_KE'      => true,
        'kde'         => true,
        'kde_TZ'      => true,
        'kea'         => true,
        'kea_CV'      => true,
        'khq'         => true,
        'khq_ML'      => true,
        'ki'          => true,
        'ki_KE'       => true,
        'kk'          => true,
        'kk_Cyrl'     => true,
        'kk_Cyrl_KZ'  => true,
        'kkj'         => true,
        'kkj_CM'      => true,
        'kl'          => true,
        'kl_GL'       => true,
        'kln'         => true,
        'kln_KE'      => true,
        'km'          => true,
        'km_KH'       => true,
        'kn'          => true,
        'kn_IN'       => true,
        'ko'          => true,
        'ko_KP'       => true,
        'ko_KR'       => true,
        'kok'         => true,
        'kok_IN'      => true,
        'ks'          => true,
        'ks_Arab'     => true,
        'ks_Arab_IN'  => true,
        'ksb'         => true,
        'ksb_TZ'      => true,
        'ksf'         => true,
        'ksf_CM'      => true,
        'ksh'         => true,
        'ksh_DE'      => true,
        'kw'          => true,
        'kw_GB'       => true,
        'ky'          => true,
        'ky_Cyrl'     => true,
        'ky_Cyrl_KG'  => true,
        'lag'         => true,
        'lag_TZ'      => true,
        'lg'          => true,
        'lg_UG'       => true,
        'lkt'         => true,
        'lkt_US'      => true,
        'ln'          => true,
        'ln_AO'       => true,
        'ln_CD'       => true,
        'ln_CF'       => true,
        'ln_CG'       => true,
        'lo'          => true,
        'lo_LA'       => true,
        'lt'          => true,
        'lt_LT'       => true,
        'lu'          => true,
        'lu_CD'       => true,
        'luo'         => true,
        'luo_KE'      => true,
        'luy'         => true,
        'luy_KE'      => true,
        'lv'          => true,
        'lv_LV'       => true,
        'mas'         => true,
        'mas_KE'      => true,
        'mas_TZ'      => true,
        'mer'         => true,
        'mer_KE'      => true,
        'mfe'         => true,
        'mfe_MU'      => true,
        'mg'          => true,
        'mg_MG'       => true,
        'mgh'         => true,
        'mgh_MZ'      => true,
        'mgo'         => true,
        'mgo_CM'      => true,
        'mk'          => true,
        'mk_MK'       => true,
        'ml'          => true,
        'ml_IN'       => true,
        'mn'          => true,
        'mn_Cyrl'     => true,
        'mn_Cyrl_MN'  => true,
        'mr'          => true,
        'mr_IN'       => true,
        'ms'          => true,
        'ms_Latn'     => true,
        'ms_Latn_BN'  => true,
        'ms_Latn_MY'  => true,
        'ms_Latn_SG'  => true,
        'mt'          => true,
        'mt_MT'       => true,
        'mua'         => true,
        'mua_CM'      => true,
        'my'          => true,
        'my_MM'       => true,
        'naq'         => true,
        'naq_NA'      => true,
        'nb'          => true,
        'nb_NO'       => true,
        'nb_SJ'       => true,
        'nd'          => true,
        'nd_ZW'       => true,
        'ne'          => true,
        'ne_IN'       => true,
        'ne_NP'       => true,
        'nl'          => true,
        'nl_AW'       => true,
        'nl_BE'       => true,
        'nl_BQ'       => true,
        'nl_CW'       => true,
        'nl_NL'       => true,
        'nl_SR'       => true,
        'nl_SX'       => true,
        'nmg'         => true,
        'nmg_CM'      => true,
        'nn'          => true,
        'nn_NO'       => true,
        'nnh'         => true,
        'nnh_CM'      => true,
        'nr'          => true,
        'nr_ZA'       => true,
        'nso'         => true,
        'nso_ZA'      => true,
        'nus'         => true,
        'nus_SD'      => true,
        'nyn'         => true,
        'nyn_UG'      => true,
        'om'          => true,
        'om_ET'       => true,
        'om_KE'       => true,
        'or'          => true,
        'or_IN'       => true,
        'ordinals'    => true,
        'os'          => true,
        'os_GE'       => true,
        'os_RU'       => true,
        'pa'          => true,
        'pa_Arab'     => true,
        'pa_Arab_PK'  => true,
        'pa_Guru'     => true,
        'pa_Guru_IN'  => true,
        'pl'          => true,
        'pl_PL'       => true,
        'plurals'     => true,
        'ps'          => true,
        'ps_AF'       => true,
        'pt'          => true,
        'pt_AO'       => true,
        'pt_BR'       => true,
        'pt_CV'       => true,
        'pt_GW'       => true,
        'pt_MO'       => true,
        'pt_MZ'       => true,
        'pt_PT'       => true,
        'pt_ST'       => true,
        'pt_TL'       => true,
        'rm'          => true,
        'rm_CH'       => true,
        'rn'          => true,
        'rn_BI'       => true,
        'ro'          => true,
        'ro_MD'       => true,
        'ro_RO'       => true,
        'rof'         => true,
        'rof_TZ'      => true,
        'ru'          => true,
        'ru_BY'       => true,
        'ru_KG'       => true,
        'ru_KZ'       => true,
        'ru_MD'       => true,
        'ru_RU'       => true,
        'ru_UA'       => true,
        'rw'          => true,
        'rw_RW'       => true,
        'rwk'         => true,
        'rwk_TZ'      => true,
        'sah'         => true,
        'sah_RU'      => true,
        'saq'         => true,
        'saq_KE'      => true,
        'sbp'         => true,
        'sbp_TZ'      => true,
        'se'          => true,
        'se_FI'       => true,
        'se_NO'       => true,
        'seh'         => true,
        'seh_MZ'      => true,
        'ses'         => true,
        'ses_ML'      => true,
        'sg'          => true,
        'sg_CF'       => true,
        'shi'         => true,
        'shi_Latn'    => true,
        'shi_Latn_MA' => true,
        'shi_Tfng'    => true,
        'shi_Tfng_MA' => true,
        'si'          => true,
        'si_LK'       => true,
        'sk'          => true,
        'sk_SK'       => true,
        'sl'          => true,
        'sl_SI'       => true,
        'sn'          => true,
        'sn_ZW'       => true,
        'so'          => true,
        'so_DJ'       => true,
        'so_ET'       => true,
        'so_KE'       => true,
        'so_SO'       => true,
        'sq'          => true,
        'sq_AL'       => true,
        'sq_MK'       => true,
        'sq_XK'       => true,
        'sr'          => true,
        'sr_Cyrl'     => true,
        'sr_Cyrl_BA'  => true,
        'sr_Cyrl_ME'  => true,
        'sr_Cyrl_RS'  => true,
        'sr_Cyrl_XK'  => true,
        'sr_Latn'     => true,
        'sr_Latn_BA'  => true,
        'sr_Latn_ME'  => true,
        'sr_Latn_RS'  => true,
        'sr_Latn_XK'  => true,
        'ss'          => true,
        'ss_SZ'       => true,
        'ss_ZA'       => true,
        'ssy'         => true,
        'ssy_ER'      => true,
        'st'          => true,
        'st_LS'       => true,
        'st_ZA'       => true,
        'sv'          => true,
        'sv_AX'       => true,
        'sv_FI'       => true,
        'sv_SE'       => true,
        'sw'          => true,
        'sw_KE'       => true,
        'sw_TZ'       => true,
        'sw_UG'       => true,
        'swc'         => true,
        'swc_CD'      => true,
        'ta'          => true,
        'ta_IN'       => true,
        'ta_LK'       => true,
        'ta_MY'       => true,
        'ta_SG'       => true,
        'te'          => true,
        'te_IN'       => true,
        'teo'         => true,
        'teo_KE'      => true,
        'teo_UG'      => true,
        'tg'          => true,
        'tg_Cyrl'     => true,
        'tg_Cyrl_TJ'  => true,
        'th'          => true,
        'th_TH'       => true,
        'ti'          => true,
        'ti_ER'       => true,
        'ti_ET'       => true,
        'tig'         => true,
        'tig_ER'      => true,
        'tn'          => true,
        'tn_BW'       => true,
        'tn_ZA'       => true,
        'to'          => true,
        'to_TO'       => true,
        'tr'          => true,
        'tr_CY'       => true,
        'tr_TR'       => true,
        'ts'          => true,
        'ts_ZA'       => true,
        'twq'         => true,
        'twq_NE'      => true,
        'tzm'         => true,
        'tzm_Latn'    => true,
        'tzm_Latn_MA' => true,
        'ug'          => true,
        'ug_Arab'     => true,
        'ug_Arab_CN'  => true,
        'uk'          => true,
        'uk_UA'       => true,
        'ur'          => true,
        'ur_IN'       => true,
        'ur_PK'       => true,
        'uz'          => true,
        'uz_Arab'     => true,
        'uz_Arab_AF'  => true,
        'uz_Cyrl'     => true,
        'uz_Cyrl_UZ'  => true,
        'uz_Latn'     => true,
        'uz_Latn_UZ'  => true,
        'vai'         => true,
        'vai_Latn'    => true,
        'vai_Latn_LR' => true,
        'vai_Vaii'    => true,
        'vai_Vaii_LR' => true,
        've'          => true,
        've_ZA'       => true,
        'vi'          => true,
        'vi_VN'       => true,
        'vo'          => true,
        'vo_001'      => true,
        'vun'         => true,
        'vun_TZ'      => true,
        'wae'         => true,
        'wae_CH'      => true,
        'wal'         => true,
        'wal_ET'      => true,
        'xh'          => true,
        'xh_ZA'       => true,
        'xog'         => true,
        'xog_UG'      => true,
        'yav'         => true,
        'yav_CM'      => true,
        'yo'          => true,
        'yo_BJ'       => true,
        'yo_NG'       => true,
        'zgh'         => true,
        'zgh_MA'      => true,
        'zh'          => true,
        'zh_Hans'     => true,
        'zh_Hans_CN'  => true,
        'zh_Hans_HK'  => true,
        'zh_Hans_MO'  => true,
        'zh_Hans_SG'  => true,
        'zh_Hant'     => true,
        'zh_Hant_HK'  => true,
        'zh_Hant_MO'  => true,
        'zh_Hant_TW'  => true,
        'zu'          => true,
        'zu_ZA'       => true,
    );

    /**
     * Class wide Locale Constants
     *
     * @var array $_territoryData
     */
    private static $_territoryData = array(
        'AD' => 'ca_AD',
        'AE' => 'ar_AE',
        'AF' => 'fa_AF',
        'AG' => 'en_AG',
        'AI' => 'en_AI',
        'AL' => 'sq_AL',
        'AM' => 'hy_AM',
        'AN' => 'pap_AN',
        'AO' => 'pt_AO',
        'AQ' => 'und_AQ',
        'AR' => 'es_AR',
        'AS' => 'sm_AS',
        'AT' => 'de_AT',
        'AU' => 'en_AU',
        'AW' => 'nl_AW',
        'AX' => 'sv_AX',
        'AZ' => 'az_Latn_AZ',
        'BA' => 'bs_BA',
        'BB' => 'en_BB',
        'BD' => 'bn_BD',
        'BE' => 'nl_BE',
        'BF' => 'mos_BF',
        'BG' => 'bg_BG',
        'BH' => 'ar_BH',
        'BI' => 'rn_BI',
        'BJ' => 'fr_BJ',
        'BL' => 'fr_BL',
        'BM' => 'en_BM',
        'BN' => 'ms_BN',
        'BO' => 'es_BO',
        'BR' => 'pt_BR',
        'BS' => 'en_BS',
        'BT' => 'dz_BT',
        'BV' => 'und_BV',
        'BW' => 'en_BW',
        'BY' => 'be_BY',
        'BZ' => 'en_BZ',
        'CA' => 'en_CA',
        'CC' => 'ms_CC',
        'CD' => 'sw_CD',
        'CF' => 'fr_CF',
        'CG' => 'fr_CG',
        'CH' => 'de_CH',
        'CI' => 'fr_CI',
        'CK' => 'en_CK',
        'CL' => 'es_CL',
        'CM' => 'fr_CM',
        'CN' => 'zh_Hans_CN',
        'CO' => 'es_CO',
        'CR' => 'es_CR',
        'CU' => 'es_CU',
        'CV' => 'kea_CV',
        'CX' => 'en_CX',
        'CY' => 'el_CY',
        'CZ' => 'cs_CZ',
        'DE' => 'de_DE',
        'DJ' => 'aa_DJ',
        'DK' => 'da_DK',
        'DM' => 'en_DM',
        'DO' => 'es_DO',
        'DZ' => 'ar_DZ',
        'EC' => 'es_EC',
        'EE' => 'et_EE',
        'EG' => 'ar_EG',
        'EH' => 'ar_EH',
        'ER' => 'ti_ER',
        'ES' => 'es_ES',
        'ET' => 'en_ET',
        'FI' => 'fi_FI',
        'FJ' => 'hi_FJ',
        'FK' => 'en_FK',
        'FM' => 'chk_FM',
        'FO' => 'fo_FO',
        'FR' => 'fr_FR',
        'GA' => 'fr_GA',
        'GB' => 'en_GB',
        'GD' => 'en_GD',
        'GE' => 'ka_GE',
        'GF' => 'fr_GF',
        'GG' => 'en_GG',
        'GH' => 'ak_GH',
        'GI' => 'en_GI',
        'GL' => 'iu_GL',
        'GM' => 'en_GM',
        'GN' => 'fr_GN',
        'GP' => 'fr_GP',
        'GQ' => 'fan_GQ',
        'GR' => 'el_GR',
        'GS' => 'und_GS',
        'GT' => 'es_GT',
        'GU' => 'en_GU',
        'GW' => 'pt_GW',
        'GY' => 'en_GY',
        'HK' => 'zh_Hant_HK',
        'HM' => 'und_HM',
        'HN' => 'es_HN',
        'HR' => 'hr_HR',
        'HT' => 'ht_HT',
        'HU' => 'hu_HU',
        'ID' => 'id_ID',
        'IE' => 'en_IE',
        'IL' => 'he_IL',
        'IM' => 'en_IM',
        'IN' => 'hi_IN',
        'IO' => 'und_IO',
        'IQ' => 'ar_IQ',
        'IR' => 'fa_IR',
        'IS' => 'is_IS',
        'IT' => 'it_IT',
        'JE' => 'en_JE',
        'JM' => 'en_JM',
        'JO' => 'ar_JO',
        'JP' => 'ja_JP',
        'KE' => 'en_KE',
        'KG' => 'ky_Cyrl_KG',
        'KH' => 'km_KH',
        'KI' => 'en_KI',
        'KM' => 'ar_KM',
        'KN' => 'en_KN',
        'KP' => 'ko_KP',
        'KR' => 'ko_KR',
        'KW' => 'ar_KW',
        'KY' => 'en_KY',
        'KZ' => 'ru_KZ',
        'LA' => 'lo_LA',
        'LB' => 'ar_LB',
        'LC' => 'en_LC',
        'LI' => 'de_LI',
        'LK' => 'si_LK',
        'LR' => 'en_LR',
        'LS' => 'st_LS',
        'LT' => 'lt_LT',
        'LU' => 'fr_LU',
        'LV' => 'lv_LV',
        'LY' => 'ar_LY',
        'MA' => 'ar_MA',
        'MC' => 'fr_MC',
        'MD' => 'ro_MD',
        'ME' => 'sr_Latn_ME',
        'MF' => 'fr_MF',
        'MG' => 'mg_MG',
        'MH' => 'mh_MH',
        'MK' => 'mk_MK',
        'ML' => 'bm_ML',
        'MM' => 'my_MM',
        'MN' => 'mn_Cyrl_MN',
        'MO' => 'zh_Hant_MO',
        'MP' => 'en_MP',
        'MQ' => 'fr_MQ',
        'MR' => 'ar_MR',
        'MS' => 'en_MS',
        'MT' => 'mt_MT',
        'MU' => 'mfe_MU',
        'MV' => 'dv_MV',
        'MW' => 'ny_MW',
        'MX' => 'es_MX',
        'MY' => 'ms_MY',
        'MZ' => 'pt_MZ',
        'NA' => 'kj_NA',
        'NC' => 'fr_NC',
        'NE' => 'ha_Latn_NE',
        'NF' => 'en_NF',
        'NG' => 'en_NG',
        'NI' => 'es_NI',
        'NL' => 'nl_NL',
        'NO' => 'nb_NO',
        'NP' => 'ne_NP',
        'NR' => 'en_NR',
        'NU' => 'niu_NU',
        'NZ' => 'en_NZ',
        'OM' => 'ar_OM',
        'PA' => 'es_PA',
        'PE' => 'es_PE',
        'PF' => 'fr_PF',
        'PG' => 'tpi_PG',
        'PH' => 'fil_PH',
        'PK' => 'ur_PK',
        'PL' => 'pl_PL',
        'PM' => 'fr_PM',
        'PN' => 'en_PN',
        'PR' => 'es_PR',
        'PS' => 'ar_PS',
        'PT' => 'pt_PT',
        'PW' => 'pau_PW',
        'PY' => 'gn_PY',
        'QA' => 'ar_QA',
        'RE' => 'fr_RE',
        'RO' => 'ro_RO',
        'RS' => 'sr_Cyrl_RS',
        'RU' => 'ru_RU',
        'RW' => 'rw_RW',
        'SA' => 'ar_SA',
        'SB' => 'en_SB',
        'SC' => 'crs_SC',
        'SD' => 'ar_SD',
        'SE' => 'sv_SE',
        'SG' => 'en_SG',
        'SH' => 'en_SH',
        'SI' => 'sl_SI',
        'SJ' => 'nb_SJ',
        'SK' => 'sk_SK',
        'SL' => 'kri_SL',
        'SM' => 'it_SM',
        'SN' => 'fr_SN',
        'SO' => 'sw_SO',
        'SR' => 'srn_SR',
        'ST' => 'pt_ST',
        'SV' => 'es_SV',
        'SY' => 'ar_SY',
        'SZ' => 'en_SZ',
        'TC' => 'en_TC',
        'TD' => 'fr_TD',
        'TF' => 'und_TF',
        'TG' => 'fr_TG',
        'TH' => 'th_TH',
        'TJ' => 'tg_Cyrl_TJ',
        'TK' => 'tkl_TK',
        'TL' => 'pt_TL',
        'TM' => 'tk_TM',
        'TN' => 'ar_TN',
        'TO' => 'to_TO',
        'TR' => 'tr_TR',
        'TT' => 'en_TT',
        'TV' => 'tvl_TV',
        'TW' => 'zh_Hant_TW',
        'TZ' => 'sw_TZ',
        'UA' => 'uk_UA',
        'UG' => 'sw_UG',
        'UM' => 'en_UM',
        'US' => 'en_US',
        'UY' => 'es_UY',
        'UZ' => 'uz_Cyrl_UZ',
        'VA' => 'it_VA',
        'VC' => 'en_VC',
        'VE' => 'es_VE',
        'VG' => 'en_VG',
        'VI' => 'en_VI',
        'VN' => 'vi_VN',
        'VU' => 'bi_VU',
        'WF' => 'wls_WF',
        'WS' => 'sm_WS',
        'YE' => 'ar_YE',
        'YT' => 'swb_YT',
        'ZA' => 'en_ZA',
        'ZM' => 'en_ZM',
        'ZW' => 'sn_ZW'
    );

    /**
     * Autosearch constants
     */
    const BROWSER     = 'browser';
    const ENVIRONMENT = 'environment';
    const ZFDEFAULT   = 'default';

    /**
     * Defines if old behaviour should be supported
     * Old behaviour throws notices and will be deleted in future releases
     *
     * @var boolean
     */
    public static $compatibilityMode = false;

    /**
     * Internal variable
     *
     * @var boolean
     */
    private static $_breakChain = false;

    /**
     * Actual set locale
     *
     * @var string Locale
     */
    protected $_locale;

    /**
     * Automatic detected locale
     *
     * @var string Locales
     */
    protected static $_auto;

    /**
     * Browser detected locale
     *
     * @var string Locales
     */
    protected static $_browser;

    /**
     * Environment detected locale
     *
     * @var string Locales
     */
    protected static $_environment;

    /**
     * Default locale
     *
     * @var string Locales
     */
    protected static $_default = array('en' => true);

    /**
     * Generates a locale object
     * If no locale is given a automatic search is done
     * Then the most probable locale will be automatically set
     * Search order is
     *  1. Given Locale
     *  2. HTTP Client
     *  3. Server Environment
     *  4. Framework Standard
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for parsing input
     * @throws Zend_Locale_Exception When autodetection has been failed
     */
    public function __construct($locale = null)
    {
        $this->setLocale($locale);
    }

    /**
     * Serialization Interface
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return (string) $this->_locale;
    }

    /**
     * Returns a string representation of the object
     * Alias for toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Return the default locale
     *
     * @return array Returns an array of all locale string
     */
    public static function getDefault()
    {
        if ((self::$compatibilityMode === true) or (func_num_args() > 0)) {
            if (!self::$_breakChain) {
                self::$_breakChain = true;
                trigger_error('You are running Zend_Locale in compatibility mode... please migrate your scripts', E_USER_NOTICE);
                $params = func_get_args();
                $param = null;
                if (isset($params[0])) {
                    $param = $params[0];
                }
                return self::getOrder($param);
            }

            self::$_breakChain = false;
        }

        return self::$_default;
    }

    /**
     * Sets a new default locale which will be used when no locale can be detected
     * If provided you can set a quality between 0 and 1 (or 2 and 100)
     * which represents the percent of quality the browser
     * requested within HTTP
     *
     * @param  string|Zend_Locale $locale  Locale to set
     * @param  float              $quality The quality to set from 0 to 1
     * @throws Zend_Locale_Exception When a autolocale was given
     * @throws Zend_Locale_Exception When a unknown locale was given
     * @return void
     */
    public static function setDefault($locale, $quality = 1)
    {
        if (($locale === 'auto') or ($locale === 'root') or ($locale === 'default') or
            ($locale === 'environment') or ($locale === 'browser')) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception('Only full qualified locales can be used as default!');
        }

        if (($quality < 0.1) or ($quality > 100)) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("Quality must be between 0.1 and 100");
        }

        if ($quality > 1) {
            $quality /= 100;
        }

        $locale = self::_prepareLocale($locale);
        if (isset(self::$_localeData[(string) $locale]) === true) {
            self::$_default = array((string) $locale => $quality);
        } else {
            $elocale = explode('_', (string) $locale);
            if (isset(self::$_localeData[$elocale[0]]) === true) {
                self::$_default = array($elocale[0] => $quality);
            } else {
                #require_once 'Zend/Locale/Exception.php';
                throw new Zend_Locale_Exception("Unknown locale '" . (string) $locale . "' can not be set as default!");
            }
        }

        self::$_auto = self::getBrowser() + self::getEnvironment() + self::getDefault();
    }

    /**
     * Expects the Systems standard locale
     *
     * For Windows:
     * f.e.: LC_COLLATE=C;LC_CTYPE=German_Austria.1252;LC_MONETARY=C
     * would be recognised as de_AT
     *
     * @return array
     */
    public static function getEnvironment()
    {
        if (self::$_environment !== null) {
            return self::$_environment;
        }

        #require_once 'Zend/Locale/Data/Translation.php';

        $language      = setlocale(LC_ALL, 0);
        $languages     = explode(';', $language);
        $languagearray = array();

        foreach ($languages as $locale) {
            if (strpos($locale, '=') !== false) {
                $language = substr($locale, strpos($locale, '='));
                $language = substr($language, 1);
            }

            if ($language !== 'C') {
                if (strpos($language, '.') !== false) {
                    $language = substr($language, 0, strpos($language, '.'));
                } else if (strpos($language, '@') !== false) {
                    $language = substr($language, 0, strpos($language, '@'));
                }

                $language = str_ireplace(
                    array_keys(Zend_Locale_Data_Translation::$languageTranslation),
                    array_values(Zend_Locale_Data_Translation::$languageTranslation),
                    (string) $language
                );

                $language = str_ireplace(
                    array_keys(Zend_Locale_Data_Translation::$regionTranslation),
                    array_values(Zend_Locale_Data_Translation::$regionTranslation),
                    $language
                );

                if (isset(self::$_localeData[$language]) === true) {
                    $languagearray[$language] = 1;
                    if (strpos($language, '_') !== false) {
                        $languagearray[substr($language, 0, strpos($language, '_'))] = 1;
                    }
                }
            }
        }

        self::$_environment = $languagearray;
        return $languagearray;
    }

    /**
     * Return an array of all accepted languages of the client
     * Expects RFC compilant Header !!
     *
     * The notation can be :
     * de,en-UK-US;q=0.5,fr-FR;q=0.2
     *
     * @return array - list of accepted languages including quality
     */
    public static function getBrowser()
    {
        if (self::$_browser !== null) {
            return self::$_browser;
        }

        $httplanguages = getenv('HTTP_ACCEPT_LANGUAGE');
        if (empty($httplanguages) && array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $httplanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }

        $languages     = array();
        if (empty($httplanguages)) {
            return $languages;
        }

        $accepted = preg_split('/,\s*/', $httplanguages);

        foreach ($accepted as $accept) {
            $match  = null;
            $result = preg_match('/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i',
                                 $accept, $match);

            if ($result < 1) {
                continue;
            }

            if (isset($match[2]) === true) {
                $quality = (float) $match[2];
            } else {
                $quality = 1.0;
            }

            $countrys = explode('-', $match[1]);
            $region   = array_shift($countrys);

            $country2 = explode('_', $region);
            $region   = array_shift($country2);

            foreach ($countrys as $country) {
                $languages[$region . '_' . strtoupper($country)] = $quality;
            }

            foreach ($country2 as $country) {
                $languages[$region . '_' . strtoupper($country)] = $quality;
            }

            if ((isset($languages[$region]) === false) || ($languages[$region] < $quality)) {
                $languages[$region] = $quality;
            }
        }

        self::$_browser = $languages;
        return $languages;
    }

    /**
     * Sets a new locale
     *
     * @param  string|Zend_Locale $locale (Optional) New locale to set
     * @return void
     */
    public function setLocale($locale = null)
    {
        $locale = self::_prepareLocale($locale);

        if (isset(self::$_localeData[(string) $locale]) === false) {
            // Is it an alias? If so, we can use this locale
            if (isset(self::$_localeAliases[$locale]) === true) {
                $this->_locale = $locale;
                return;
            }

            $region = substr((string) $locale, 0, 3);
            if (isset($region[2]) === true) {
                if (($region[2] === '_') or ($region[2] === '-')) {
                    $region = substr($region, 0, 2);
                }
            }

            if (isset(self::$_localeData[(string) $region]) === true) {
                $this->_locale = $region;
            } else {
                $this->_locale = 'root';
            }
        } else {
            $this->_locale = $locale;
        }
    }

    /**
     * Returns the language part of the locale
     *
     * @return string
     */
    public function getLanguage()
    {
        $locale = explode('_', $this->_locale);
        return $locale[0];
    }

    /**
     * Returns the region part of the locale if available
     *
     * @return string|false - Regionstring
     */
    public function getRegion()
    {
        $locale = explode('_', $this->_locale);
        if (isset($locale[1]) === true) {
            return $locale[1];
        }

        return false;
    }

    /**
     * Return the accepted charset of the client
     *
     * @return string
     */
    public static function getHttpCharset()
    {
        $httpcharsets = getenv('HTTP_ACCEPT_CHARSET');

        $charsets = array();
        if ($httpcharsets === false) {
            return $charsets;
        }

        $accepted = preg_split('/,\s*/', $httpcharsets);
        foreach ($accepted as $accept) {
            if (empty($accept) === true) {
                continue;
            }

            if (strpos($accept, ';') !== false) {
                $quality        = (float) substr($accept, (strpos($accept, '=') + 1));
                $pos            = substr($accept, 0, strpos($accept, ';'));
                $charsets[$pos] = $quality;
            } else {
                $quality           = 1.0;
                $charsets[$accept] = $quality;
            }
        }

        return $charsets;
    }

    /**
     * Returns true if both locales are equal
     *
     * @param  Zend_Locale $object Locale to check for equality
     * @return boolean
     */
    public function equals(Zend_Locale $object)
    {
        if ($object->toString() === $this->toString()) {
            return true;
        }

        return false;
    }

    /**
     * Returns localized informations as array, supported are several
     * types of informations.
     * For detailed information about the types look into the documentation
     *
     * @param  string             $path   (Optional) Type of information to return
     * @param  string|Zend_Locale $locale (Optional) Locale|Language for which this informations should be returned
     * @param  string             $value  (Optional) Value for detail list
     * @return array Array with the wished information in the given language
     */
    public static function getTranslationList($path = null, $locale = null, $value = null)
    {
        #require_once 'Zend/Locale/Data.php';
        $locale = self::findLocale($locale);
        $result = Zend_Locale_Data::getList($locale, $path, $value);
        if (empty($result) === true) {
            return false;
        }

        return $result;
    }

    /**
     * Returns an array with the name of all languages translated to the given language
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for language translation
     * @return array
     * @deprecated
     */
    public static function getLanguageTranslationList($locale = null)
    {
        trigger_error("The method getLanguageTranslationList is deprecated. Use getTranslationList('language', $locale) instead", E_USER_NOTICE);
        return self::getTranslationList('language', $locale);
    }

    /**
     * Returns an array with the name of all scripts translated to the given language
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for script translation
     * @return array
     * @deprecated
     */
    public static function getScriptTranslationList($locale = null)
    {
        trigger_error("The method getScriptTranslationList is deprecated. Use getTranslationList('script', $locale) instead", E_USER_NOTICE);
        return self::getTranslationList('script', $locale);
    }

    /**
     * Returns an array with the name of all countries translated to the given language
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for country translation
     * @return array
     * @deprecated
     */
    public static function getCountryTranslationList($locale = null)
    {
        trigger_error("The method getCountryTranslationList is deprecated. Use getTranslationList('territory', $locale, 2) instead", E_USER_NOTICE);
        return self::getTranslationList('territory', $locale, 2);
    }

    /**
     * Returns an array with the name of all territories translated to the given language
     * All territories contains other countries.
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for territory translation
     * @return array
     * @deprecated
     */
    public static function getTerritoryTranslationList($locale = null)
    {
        trigger_error("The method getTerritoryTranslationList is deprecated. Use getTranslationList('territory', $locale, 1) instead", E_USER_NOTICE);
        return self::getTranslationList('territory', $locale, 1);
    }

    /**
     * Returns a localized information string, supported are several types of informations.
     * For detailed information about the types look into the documentation
     *
     * @param  string             $value  Name to get detailed information about
     * @param  string             $path   (Optional) Type of information to return
     * @param  string|Zend_Locale $locale (Optional) Locale|Language for which this informations should be returned
     * @return string|false The wished information in the given language
     */
    public static function getTranslation($value = null, $path = null, $locale = null)
    {
        #require_once 'Zend/Locale/Data.php';
        $locale = self::findLocale($locale);
        $result = Zend_Locale_Data::getContent($locale, $path, $value);
        if (empty($result) === true && '0' !== $result) {
            return false;
        }

        return $result;
    }

    /**
     * Returns the localized language name
     *
     * @param  string $value  Name to get detailed information about
     * @param  string $locale (Optional) Locale for language translation
     * @return array
     * @deprecated
     */
    public static function getLanguageTranslation($value, $locale = null)
    {
        trigger_error("The method getLanguageTranslation is deprecated. Use getTranslation($value, 'language', $locale) instead", E_USER_NOTICE);
        return self::getTranslation($value, 'language', $locale);
    }

    /**
     * Returns the localized script name
     *
     * @param  string $value  Name to get detailed information about
     * @param  string $locale (Optional) locale for script translation
     * @return array
     * @deprecated
     */
    public static function getScriptTranslation($value, $locale = null)
    {
        trigger_error("The method getScriptTranslation is deprecated. Use getTranslation($value, 'script', $locale) instead", E_USER_NOTICE);
        return self::getTranslation($value, 'script', $locale);
    }

    /**
     * Returns the localized country name
     *
     * @param  string             $value  Name to get detailed information about
     * @param  string|Zend_Locale $locale (Optional) Locale for country translation
     * @return array
     * @deprecated
     */
    public static function getCountryTranslation($value, $locale = null)
    {
        trigger_error("The method getCountryTranslation is deprecated. Use getTranslation($value, 'country', $locale) instead", E_USER_NOTICE);
        return self::getTranslation($value, 'country', $locale);
    }

    /**
     * Returns the localized territory name
     * All territories contains other countries.
     *
     * @param  string             $value  Name to get detailed information about
     * @param  string|Zend_Locale $locale (Optional) Locale for territory translation
     * @return array
     * @deprecated
     */
    public static function getTerritoryTranslation($value, $locale = null)
    {
        trigger_error("The method getTerritoryTranslation is deprecated. Use getTranslation($value, 'territory', $locale) instead", E_USER_NOTICE);
        return self::getTranslation($value, 'territory', $locale);
    }

    /**
     * Returns an array with translated yes strings
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for language translation (defaults to $this locale)
     * @return array
     */
    public static function getQuestion($locale = null)
    {
        #require_once 'Zend/Locale/Data.php';
        $locale            = self::findLocale($locale);
        $quest             = Zend_Locale_Data::getList($locale, 'question');
        $yes               = explode(':', $quest['yes']);
        $no                = explode(':', $quest['no']);
        $quest['yes']      = $yes[0];
        $quest['yesarray'] = $yes;
        $quest['no']       = $no[0];
        $quest['noarray']  = $no;
        $quest['yesexpr']  = self::_prepareQuestionString($yes);
        $quest['noexpr']   = self::_prepareQuestionString($no);

        return $quest;
    }

    /**
     * Internal function for preparing the returned question regex string
     *
     * @param  string $input Regex to parse
     * @return string
     */
    private static function _prepareQuestionString($input)
    {
        $regex = '';
        if (is_array($input) === true) {
            $regex = '^';
            $start = true;
            foreach ($input as $row) {
                if ($start === false) {
                    $regex .= '|';
                }

                $start  = false;
                $regex .= '(';
                $one    = null;
                if (strlen($row) > 2) {
                    $one = true;
                }

                foreach (str_split($row, 1) as $char) {
                    $regex .= '[' . $char;
                    $regex .= strtoupper($char) . ']';
                    if ($one === true) {
                        $one    = false;
                        $regex .= '(';
                    }
                }

                if ($one === false) {
                    $regex .= ')';
                }

                $regex .= '?)';
            }
        }

        return $regex;
    }

    /**
     * Checks if a locale identifier is a real locale or not
     * Examples:
     * "en_XX" refers to "en", which returns true
     * "XX_yy" refers to "root", which returns false
     *
     * @param  string|Zend_Locale $locale     Locale to check for
     * @param  boolean            $strict     (Optional) If true, no rerouting will be done when checking
     * @param  boolean            $compatible (DEPRECATED) Only for internal usage, brakes compatibility mode
     * @return boolean If the locale is known dependend on the settings
     */
    public static function isLocale($locale, $strict = false, $compatible = true)
    {
        if (($locale instanceof Zend_Locale)
            || (is_string($locale) && array_key_exists($locale, self::$_localeData))
        ) {
            return true;
        }

        // Is it an alias?
        if (is_string($locale) && array_key_exists($locale, self::$_localeAliases)) {
            return true;
        }

        if (($locale === null) || (!is_string($locale) and !is_array($locale))) {
            return false;
        }

        try {
            $locale = self::_prepareLocale($locale, $strict);
        } catch (Zend_Locale_Exception $e) {
            return false;
        }

        if (($compatible === true) and (self::$compatibilityMode === true)) {
            trigger_error('You are running Zend_Locale in compatibility mode... please migrate your scripts', E_USER_NOTICE);
            if (isset(self::$_localeData[$locale]) === true) {
                return $locale;
            } else if (!$strict) {
                $locale = explode('_', $locale);
                if (isset(self::$_localeData[$locale[0]]) === true) {
                    return $locale[0];
                }
            }
        } else {
            if (isset(self::$_localeData[$locale]) === true) {
                return true;
            } else if (!$strict) {
                $locale = explode('_', $locale);
                if (isset(self::$_localeData[$locale[0]]) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Finds the proper locale based on the input
     * Checks if it exists, degrades it when necessary
     * Detects registry locale and when all fails tries to detect a automatic locale
     * Returns the found locale as string
     *
     * @param string $locale
     * @throws Zend_Locale_Exception When the given locale is no locale or the autodetection fails
     * @return string
     */
    public static function findLocale($locale = null)
    {
        if ($locale === null) {
            #require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Locale')) {
                $locale = Zend_Registry::get('Zend_Locale');
            }
        }

        if ($locale === null) {
            $locale = new Zend_Locale();
        }

        if (!Zend_Locale::isLocale($locale, true, false)) {
            if (!Zend_Locale::isLocale($locale, false, false)) {
                $locale = Zend_Locale::getLocaleToTerritory($locale);

                if (empty($locale)) {
                    #require_once 'Zend/Locale/Exception.php';
                    throw new Zend_Locale_Exception("The locale '$locale' is no known locale");
                }
            } else {
                $locale = new Zend_Locale($locale);
            }
        }

        $locale = self::_prepareLocale($locale);
        return $locale;
    }

    /**
     * Returns the expected locale for a given territory
     *
     * @param string $territory Territory for which the locale is being searched
     * @return string|null Locale string or null when no locale has been found
     */
    public static function getLocaleToTerritory($territory)
    {
        $territory = strtoupper($territory);
        if (array_key_exists($territory, self::$_territoryData)) {
            return self::$_territoryData[$territory];
        }

        return null;
    }

    /**
     * Returns a list of all known locales where the locale is the key
     * Only real locales are returned, the internal locales 'root', 'auto', 'browser'
     * and 'environment' are suppressed
     *
     * @return array List of all Locales
     */
    public static function getLocaleList()
    {
        $list = self::$_localeData;
        unset($list['root']);
        unset($list['auto']);
        unset($list['browser']);
        unset($list['environment']);
        return $list;
    }

    /**
     * Returns the set cache
     *
     * @return Zend_Cache_Core The set cache
     */
    public static function getCache()
    {
        #require_once 'Zend/Locale/Data.php';
        return Zend_Locale_Data::getCache();
    }

    /**
     * Sets a cache
     *
     * @param  Zend_Cache_Core $cache Cache to set
     * @return void
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        #require_once 'Zend/Locale/Data.php';
        Zend_Locale_Data::setCache($cache);
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        #require_once 'Zend/Locale/Data.php';
        return Zend_Locale_Data::hasCache();
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        #require_once 'Zend/Locale/Data.php';
        Zend_Locale_Data::removeCache();
    }

    /**
     * Clears all set cache data
     *
     * @param string $tag Tag to clear when the default tag name is not used
     * @return void
     */
    public static function clearCache($tag = null)
    {
        #require_once 'Zend/Locale/Data.php';
        Zend_Locale_Data::clearCache($tag);
    }

    /**
     * Disables the set cache
     *
     * @param  boolean $flag True disables any set cache, default is false
     * @return void
     */
    public static function disableCache($flag)
    {
        #require_once 'Zend/Locale/Data.php';
        Zend_Locale_Data::disableCache($flag);
    }

    /**
     * Internal function, returns a single locale on detection
     *
     * @param  string|Zend_Locale $locale (Optional) Locale to work on
     * @param  boolean            $strict (Optional) Strict preparation
     * @throws Zend_Locale_Exception When no locale is set which is only possible when the class was wrong extended
     * @return string
     */
    private static function _prepareLocale($locale, $strict = false)
    {
        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        if (is_array($locale)) {
            return '';
        }

        if (empty(self::$_auto) === true) {
            self::$_browser     = self::getBrowser();
            self::$_environment = self::getEnvironment();
            self::$_breakChain  = true;
            self::$_auto        = self::getBrowser() + self::getEnvironment() + self::getDefault();
        }

        if (!$strict) {
            if ($locale === 'browser') {
                $locale = self::$_browser;
            }

            if ($locale === 'environment') {
                $locale = self::$_environment;
            }

            if ($locale === 'default') {
                $locale = self::$_default;
            }

            if (($locale === 'auto') or ($locale === null)) {
                $locale = self::$_auto;
            }

            if (is_array($locale) === true) {
                $locale = key($locale);
            }
        }

        // This can only happen when someone extends Zend_Locale and erases the default
        if ($locale === null) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception('Autodetection of Locale has been failed!');
        }

        if (strpos($locale, '-') !== false) {
            $locale = strtr($locale, '-', '_');
        }

        $parts = explode('_', $locale);
        if (!isset(self::$_localeData[$parts[0]])) {
            if ((count($parts) == 1) && array_key_exists($parts[0], self::$_territoryData)) {
                return self::$_territoryData[$parts[0]];
            }

            return '';
        }

        foreach($parts as $key => $value) {
            if ((strlen($value) < 2) || (strlen($value) > 3)) {
                unset($parts[$key]);
            }
        }

        $locale = implode('_', $parts);
        return (string) $locale;
    }

    /**
     * Search the locale automatically and return all used locales
     * ordered by quality
     *
     * Standard Searchorder is Browser, Environment, Default
     *
     * @param  string  $searchorder (Optional) Searchorder
     * @return array Returns an array of all detected locales
     */
    public static function getOrder($order = null)
    {
        switch ($order) {
            case self::ENVIRONMENT:
                self::$_breakChain = true;
                $languages         = self::getEnvironment() + self::getBrowser() + self::getDefault();
                break;

            case self::ZFDEFAULT:
                self::$_breakChain = true;
                $languages         = self::getDefault() + self::getEnvironment() + self::getBrowser();
                break;

            default:
                self::$_breakChain = true;
                $languages         = self::getBrowser() + self::getEnvironment() + self::getDefault();
                break;
        }

        return $languages;
    }

    /**
     * Is the given locale in the list of aliases?
     *
     * @param  string|Zend_Locale $locale Locale to work on
     * @return boolean
     */
    public static function isAlias($locale)
    {
        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        return isset(self::$_localeAliases[$locale]);
    }

    /**
     * Return an alias' actual locale.
     *
     * @param  string|Zend_Locale $locale Locale to work on
     * @return string
     */
    public static function getAlias($locale)
    {
        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        if (isset(self::$_localeAliases[$locale]) === true) {
            return self::$_localeAliases[$locale];
        }

        return (string) $locale;
    }
}
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Locale
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @version   $Id$
 */

/**
 * Definition class for all Windows locales
 *
 * Based on this two lists:
 * @link http://msdn.microsoft.com/en-us/library/39cwe7zf.aspx
 * @link http://msdn.microsoft.com/en-us/library/cdax410z.aspx
 * @link http://msdn.microsoft.com/en-us/goglobal/bb964664.aspx
 * @link http://msdn.microsoft.com/en-us/goglobal/bb895996.aspx
 *
 * @category  Zend
 * @package   Zend_Locale
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
class Zend_Locale_Data_Translation
{
    /**
     * Locale Translation for Full Named Locales
     *
     * @var array $localeTranslation
     */
    public static $languageTranslation = array(
        'Afrikaans'         => 'af',
        'Albanian'          => 'sq',
        'Amharic'           => 'am',
        'Arabic'            => 'ar',
        'Armenian'          => 'hy',
        'Assamese'          => 'as',
        'Azeri'             => 'az',
        'Azeri Latin'       => 'az_Latn',
        'Azeri Cyrillic'    => 'az_Cyrl',
        'Basque'            => 'eu',
        'Belarusian'        => 'be',
        'Bengali'           => 'bn',
        'Bengali Latin'     => 'bn_Latn',
        'Bosnian'           => 'bs',
        'Bulgarian'         => 'bg',
        'Burmese'           => 'my',
        'Catalan'           => 'ca',
        'Cherokee'          => 'chr',
        'Chinese'           => 'zh',
        'Croatian'          => 'hr',
        'Czech'             => 'cs',
        'Danish'            => 'da',
        'Divehi'            => 'dv',
        'Dutch'             => 'nl',
        'English'           => 'en',
        'Estonian'          => 'et',
        'Faroese'           => 'fo',
        'Faeroese'          => 'fo',
        'Farsi'             => 'fa',
        'Filipino'          => 'fil',
        'Finnish'           => 'fi',
        'French'            => 'fr',
        'Frisian'           => 'fy',
        'Gaelic'            => 'gd',
        'Galician'          => 'gl',
        'Georgian'          => 'ka',
        'German'            => 'de',
        'Greek'             => 'el',
        'Guarani'           => 'gn',
        'Gujarati'          => 'gu',
        'Hausa'             => 'ha',
        'Hawaiian'          => 'haw',
        'Hebrew'            => 'he',
        'Hindi'             => 'hi',
        'Hungarian'         => 'hu',
        'Icelandic'         => 'is',
        'Igbo'              => 'ig',
        'Indonesian'        => 'id',
        'Inuktitut'         => 'iu',
        'Italian'           => 'it',
        'Japanese'          => 'ja',
        'Kannada'           => 'kn',
        'Kanuri'            => 'kr',
        'Kashmiri'          => 'ks',
        'Kazakh'            => 'kk',
        'Khmer'             => 'km',
        'Konkani'           => 'kok',
        'Korean'            => 'ko',
        'Kyrgyz'            => 'ky',
        'Lao'               => 'lo',
        'Latin'             => 'la',
        'Latvian'           => 'lv',
        'Lithuanian'        => 'lt',
        'Macedonian'        => 'mk',
        'Malay'             => 'ms',
        'Malayalam'         => 'ml',
        'Maltese'           => 'mt',
        'Manipuri'          => 'mni',
        'Maori'             => 'mi',
        'Marathi'           => 'mr',
        'Mongolian'         => 'mn',
        'Nepali'            => 'ne',
        'Norwegian'         => 'no',
        'Norwegian Bokmal'  => 'nb',
        'Norwegian Nynorsk' => 'nn',
        'Oriya'             => 'or',
        'Oromo'             => 'om',
        'Papiamentu'        => 'pap',
        'Pashto'            => 'ps',
        'Polish'            => 'pl',
        'Portuguese'        => 'pt',
        'Punjabi'           => 'pa',
        'Quecha'            => 'qu',
        'Quechua'           => 'qu',
        'Rhaeto-Romanic'    => 'rm',
        'Romanian'          => 'ro',
        'Russian'           => 'ru',
        'Sami'              => 'smi',
        'Sami Inari'        => 'smn',
        'Sami Lule'         => 'smj',
        'Sami Northern'     => 'se',
        'Sami Skolt'        => 'sms',
        'Sami Southern'     => 'sma',
        'Sanskrit'          => 'sa',
        'Serbian'           => 'sr',
        'Serbian Latin'     => 'sr_Latn',
        'Serbian Cyrillic'  => 'sr_Cyrl',
        'Sindhi'            => 'sd',
        'Sinhalese'         => 'si',
        'Slovak'            => 'sk',
        'Slovenian'         => 'sl',
        'Somali'            => 'so',
        'Sorbian'           => 'wen',
        'Spanish'           => 'es',
        'Swahili'           => 'sw',
        'Swedish'           => 'sv',
        'Syriac'            => 'syr',
        'Tajik'             => 'tg',
        'Tamazight'         => 'tmh',
        'Tamil'             => 'ta',
        'Tatar'             => 'tt',
        'Telugu'            => 'te',
        'Thai'              => 'th',
        'Tibetan'           => 'bo',
        'Tigrigna'          => 'ti',
        'Tsonga'            => 'ts',
        'Tswana'            => 'tn',
        'Turkish'           => 'tr',
        'Turkmen'           => 'tk',
        'Uighur'            => 'ug',
        'Ukrainian'         => 'uk',
        'Urdu'              => 'ur',
        'Uzbek'             => 'uz',
        'Uzbek Latin'       => 'uz_Latn',
        'Uzbek Cyrillic'    => 'uz_Cyrl',
        'Venda'             => 've',
        'Vietnamese'        => 'vi',
        'Welsh'             => 'cy',
        'Xhosa'             => 'xh',
        'Yiddish'           => 'yi',
        'Yoruba'            => 'yo',
        'Zulu'              => 'zu',
    );

    public static $regionTranslation = array(
        'Albania'                    => 'AL',
        'Algeria'                    => 'DZ',
        'Argentina'                  => 'AR',
        'Armenia'                    => 'AM',
        'Australia'                  => 'AU',
        'Austria'                    => 'AT',
        'Bahrain'                    => 'BH',
        'Bangladesh'                 => 'BD',
        'Belgium'                    => 'BE',
        'Belize'                     => 'BZ',
        'Bhutan'                     => 'BT',
        'Bolivia'                    => 'BO',
        'Bosnia Herzegovina'         => 'BA',
        'Brazil'                     => 'BR',
        'Brazilian'                  => 'BR',
        'Brunei Darussalam'          => 'BN',
        'Cameroon'                   => 'CM',
        'Canada'                     => 'CA',
        'Chile'                      => 'CL',
        'China'                      => 'CN',
        'Colombia'                   => 'CO',
        'Costa Rica'                 => 'CR',
        "Cote d'Ivoire"              => 'CI',
        'Czech Republic'             => 'CZ',
        'Dominican Republic'         => 'DO',
        'Denmark'                    => 'DK',
        'Ecuador'                    => 'EC',
        'Egypt'                      => 'EG',
        'El Salvador'                => 'SV',
        'Eritrea'                    => 'ER',
        'Ethiopia'                   => 'ET',
        'Finland'                    => 'FI',
        'France'                     => 'FR',
        'Germany'                    => 'DE',
        'Greece'                     => 'GR',
        'Guatemala'                  => 'GT',
        'Haiti'                      => 'HT',
        'Honduras'                   => 'HN',
        'Hong Kong'                  => 'HK',
        'Hong Kong SAR'              => 'HK',
        'Hungary'                    => 'HU',
        'Iceland'                    => 'IS',
        'India'                      => 'IN',
        'Indonesia'                  => 'ID',
        'Iran'                       => 'IR',
        'Iraq'                       => 'IQ',
        'Ireland'                    => 'IE',
        'Italy'                      => 'IT',
        'Jamaica'                    => 'JM',
        'Japan'                      => 'JP',
        'Jordan'                     => 'JO',
        'Korea'                      => 'KR',
        'Kuwait'                     => 'KW',
        'Lebanon'                    => 'LB',
        'Libya'                      => 'LY',
        'Liechtenstein'              => 'LI',
        'Luxembourg'                 => 'LU',
        'Macau'                      => 'MO',
        'Macao SAR'                  => 'MO',
        'Malaysia'                   => 'MY',
        'Mali'                       => 'ML',
        'Mexico'                     => 'MX',
        'Moldava'                    => 'MD',
        'Monaco'                     => 'MC',
        'Morocco'                    => 'MA',
        'Netherlands'                => 'NL',
        'New Zealand'                => 'NZ',
        'Nicaragua'                  => 'NI',
        'Nigeria'                    => 'NG',
        'Norway'                     => 'NO',
        'Oman'                       => 'OM',
        'Pakistan'                   => 'PK',
        'Panama'                     => 'PA',
        'Paraguay'                   => 'PY',
        "People's Republic of China" => 'CN',
        'Peru'                       => 'PE',
        'Philippines'                => 'PH',
        'Poland'                     => 'PL',
        'Portugal'                   => 'PT',
        'PRC'                        => 'CN',
        'Puerto Rico'                => 'PR',
        'Qatar'                      => 'QA',
        'Reunion'                    => 'RE',
        'Russia'                     => 'RU',
        'Saudi Arabia'               => 'SA',
        'Senegal'                    => 'SN',
        'Singapore'                  => 'SG',
        'Slovakia'                   => 'SK',
        'South Africa'               => 'ZA',
        'Spain'                      => 'ES',
        'Sri Lanka'                  => 'LK',
        'Sweden'                     => 'SE',
        'Switzerland'                => 'CH',
        'Syria'                      => 'SY',
        'Taiwan'                     => 'TW',
        'The Netherlands'            => 'NL',
        'Trinidad'                   => 'TT',
        'Tunisia'                    => 'TN',
        'UAE'                        => 'AE',
        'United Kingdom'             => 'GB',
        'United States'              => 'US',
        'Uruguay'                    => 'UY',
        'Venezuela'                  => 'VE',
        'Yemen'                      => 'YE',
        'Zimbabwe'                   => 'ZW',
    );
}
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Format
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * include needed classes
 */
#require_once 'Zend/Locale/Data.php';

/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Format
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale_Format
{
    const STANDARD   = 'auto';

    private static $_options = array('date_format'   => null,
                                     'number_format' => null,
                                     'format_type'   => 'iso',
                                     'fix_date'      => false,
                                     'locale'        => null,
                                     'cache'         => null,
                                     'disableCache'  => null,
                                     'precision'     => null);

    /**
     * Sets class wide options, if no option was given, the actual set options will be returned
     * The 'precision' option of a value is used to truncate or stretch extra digits. -1 means not to touch the extra digits.
     * The 'locale' option helps when parsing numbers and dates using separators and month names.
     * The date format 'format_type' option selects between CLDR/ISO date format specifier tokens and PHP's date() tokens.
     * The 'fix_date' option enables or disables heuristics that attempt to correct invalid dates.
     * The 'number_format' option can be used to specify a default number format string
     * The 'date_format' option can be used to specify a default date format string, but beware of using getDate(),
     * checkDateFormat() and getTime() after using setOptions() with a 'format'.  To use these four methods
     * with the default date format for a locale, use array('date_format' => null, 'locale' => $locale) for their options.
     *
     * @param  array  $options  Array of options, keyed by option name: format_type = 'iso' | 'php', fix_date = true | false,
     *                          locale = Zend_Locale | locale string, precision = whole number between -1 and 30
     * @throws Zend_Locale_Exception
     * @return array if no option was given
     */
    public static function setOptions(array $options = array())
    {
        self::$_options = self::_checkOptions($options) + self::$_options;
        return self::$_options;
    }

    /**
     * Internal function for checking the options array of proper input values
     * See {@link setOptions()} for details.
     *
     * @param  array  $options  Array of options, keyed by option name: format_type = 'iso' | 'php', fix_date = true | false,
     *                          locale = Zend_Locale | locale string, precision = whole number between -1 and 30
     * @throws Zend_Locale_Exception
     * @return array if no option was given
     */
    private static function _checkOptions(array $options = array())
    {
        if (count($options) == 0) {
            return self::$_options;
        }
        foreach ($options as $name => $value) {
            $name  = strtolower($name);
            if ($name !== 'locale') {
                if (gettype($value) === 'string') {
                    $value = strtolower($value);
                }
            }

            switch($name) {
                case 'number_format' :
                    if ($value == Zend_Locale_Format::STANDARD) {
                        $locale = self::$_options['locale'];
                        if (isset($options['locale'])) {
                            $locale = $options['locale'];
                        }
                        $options['number_format'] = Zend_Locale_Data::getContent($locale, 'decimalnumber');
                    } else if ((gettype($value) !== 'string') and ($value !== NULL)) {
                        #require_once 'Zend/Locale/Exception.php';
                        $stringValue = (string)(is_array($value) ? implode(' ', $value) : $value);
                        throw new Zend_Locale_Exception("Unknown number format type '" . gettype($value) . "'. "
                            . "Format '$stringValue' must be a valid number format string.");
                    }
                    break;

                case 'date_format' :
                    if ($value == Zend_Locale_Format::STANDARD) {
                        $locale = self::$_options['locale'];
                        if (isset($options['locale'])) {
                            $locale = $options['locale'];
                        }
                        $options['date_format'] = Zend_Locale_Format::getDateFormat($locale);
                    } else if ((gettype($value) !== 'string') and ($value !== NULL)) {
                        #require_once 'Zend/Locale/Exception.php';
                        $stringValue = (string)(is_array($value) ? implode(' ', $value) : $value);
                        throw new Zend_Locale_Exception("Unknown dateformat type '" . gettype($value) . "'. "
                            . "Format '$stringValue' must be a valid ISO or PHP date format string.");
                    } else {
                        if (((isset($options['format_type']) === true) and ($options['format_type'] == 'php')) or
                            ((isset($options['format_type']) === false) and (self::$_options['format_type'] == 'php'))) {
                            $options['date_format'] = Zend_Locale_Format::convertPhpToIsoFormat($value);
                        }
                    }
                    break;

                case 'format_type' :
                    if (($value != 'php') && ($value != 'iso')) {
                        #require_once 'Zend/Locale/Exception.php';
                        throw new Zend_Locale_Exception("Unknown date format type '$value'. Only 'iso' and 'php'"
                           . " are supported.");
                    }
                    break;

                case 'fix_date' :
                    if (($value !== true) && ($value !== false)) {
                        #require_once 'Zend/Locale/Exception.php';
                        throw new Zend_Locale_Exception("Enabling correction of dates must be either true or false"
                            . "(fix_date='$value').");
                    }
                    break;

                case 'locale' :
                    $options['locale'] = Zend_Locale::findLocale($value);
                    break;

                case 'cache' :
                    if ($value instanceof Zend_Cache_Core) {
                        Zend_Locale_Data::setCache($value);
                    }
                    break;

                case 'disablecache' :
                    if (null !== $value) {
                        Zend_Locale_Data::disableCache($value);
                    }
                    break;

                case 'precision' :
                    if ($value === NULL) {
                        $value = -1;
                    }

                    if (($value < -1) || ($value > 30)) {
                        #require_once 'Zend/Locale/Exception.php';
                        throw new Zend_Locale_Exception("'$value' precision is not a whole number less than 30.");
                    }
                    break;

                default:
                    #require_once 'Zend/Locale/Exception.php';
                    throw new Zend_Locale_Exception("Unknown option: '$name' = '$value'");
                    break;

            }
        }

        return $options;
    }

    /**
     * Changes the numbers/digits within a given string from one script to another
     * 'Decimal' representated the stardard numbers 0-9, if a script does not exist
     * an exception will be thrown.
     *
     * Examples for conversion from Arabic to Latin numerals:
     *   convertNumerals('١١٠ Tests', 'Arab'); -> returns '100 Tests'
     * Example for conversion from Latin to Arabic numerals:
     *   convertNumerals('100 Tests', 'Latn', 'Arab'); -> returns '١١٠ Tests'
     *
     * @param  string  $input  String to convert
     * @param  string  $from   Script to parse, see {@link Zend_Locale::getScriptList()} for details.
     * @param  string  $to     OPTIONAL Script to convert to
     * @return string  Returns the converted input
     * @throws Zend_Locale_Exception
     */
    public static function convertNumerals($input, $from, $to = null)
    {
        if (!self::_getUniCodeSupport()) {
            trigger_error("Sorry, your PCRE extension does not support UTF8 which is needed for the I18N core", E_USER_NOTICE);
        }

        $from   = strtolower($from);
        $source = Zend_Locale_Data::getContent('en', 'numberingsystem', $from);
        if (empty($source)) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("Unknown script '$from'. Use 'Latn' for digits 0,1,2,3,4,5,6,7,8,9.");
        }

        if ($to !== null) {
            $to     = strtolower($to);
            $target = Zend_Locale_Data::getContent('en', 'numberingsystem', $to);
            if (empty($target)) {
                #require_once 'Zend/Locale/Exception.php';
                throw new Zend_Locale_Exception("Unknown script '$to'. Use 'Latn' for digits 0,1,2,3,4,5,6,7,8,9.");
            }
        } else {
            $target = '0123456789';
        }

        for ($x = 0; $x < 10; ++$x) {
            $asource[$x] = "/" . iconv_substr($source, $x, 1, 'UTF-8') . "/u";
            $atarget[$x] = iconv_substr($target, $x, 1, 'UTF-8');
        }

        return preg_replace($asource, $atarget, $input);
    }

    /**
     * Returns the normalized number from a localized one
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '2345.4356,1234' = 23455456.1234
     * '+23,3452.123' = 233452.123
     * '12343 ' = 12343
     * '-9456' = -9456
     * '0' = 0
     *
     * @param  string $input    Input string to parse for numbers
     * @param  array  $options  Options: locale, precision. See {@link setOptions()} for details.
     * @return string Returns the extracted number
     * @throws Zend_Locale_Exception
     */
    public static function getNumber($input, array $options = array())
    {
        $options = self::_checkOptions($options) + self::$_options;
        if (!is_string($input)) {
            return $input;
        }

        if (!self::isNumber($input, $options)) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception('No localized value in ' . $input . ' found, or the given number does not match the localized format');
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getList($options['locale'],'symbols');
        // Change locale input to be default number
        if (($input[0] == $symbols['minus']) && ('-' != $input[0])) {
            $input = '-' . substr($input, 1);
        }

        $input = str_replace($symbols['group'],'', $input);
        if (strpos($input, $symbols['decimal']) !== false) {
            if ($symbols['decimal'] != '.') {
                $input = str_replace($symbols['decimal'], ".", $input);
            }

            $pre = substr($input, strpos($input, '.') + 1);
            if ($options['precision'] === null) {
                $options['precision'] = strlen($pre);
            }

            if (strlen($pre) >= $options['precision']) {
                $input = substr($input, 0, strlen($input) - strlen($pre) + $options['precision']);
            }

            if (($options['precision'] == 0) && ($input[strlen($input) - 1] == '.')) {
                $input = substr($input, 0, -1);
            }
        }

        return $input;
    }

    /**
     * Returns a locale formatted number depending on the given options.
     * The seperation and fraction sign is used from the set locale.
     * ##0.#  -> 12345.12345 -> 12345.12345
     * ##0.00 -> 12345.12345 -> 12345.12
     * ##,##0.00 -> 12345.12345 -> 12,345.12
     *
     * @param   string  $value    Localized number string
     * @param   array   $options  Options: number_format, locale, precision. See {@link setOptions()} for details.
     * @return  string  locale formatted number
     * @throws Zend_Locale_Exception
     */
    public static function toNumber($value, array $options = array())
    {
        // load class within method for speed
        #require_once 'Zend/Locale/Math.php';

        $value             = Zend_Locale_Math::floatalize($value);
        $value             = Zend_Locale_Math::normalize($value);
        $options           = self::_checkOptions($options) + self::$_options;
        $options['locale'] = (string) $options['locale'];

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getList($options['locale'], 'symbols');
        $oenc = self::_getEncoding();
        self::_setEncoding('UTF-8');
        
        // Get format
        $format = $options['number_format'];
        if ($format === null) {
            $format  = Zend_Locale_Data::getContent($options['locale'], 'decimalnumber');
            $format  = self::_seperateFormat($format, $value, $options['precision']);

            if ($options['precision'] !== null) {
                $value   = Zend_Locale_Math::normalize(Zend_Locale_Math::round($value, $options['precision']));
            }
        } else {
            // seperate negative format pattern when available
            $format  = self::_seperateFormat($format, $value, $options['precision']);
            if (strpos($format, '.')) {
                if (is_numeric($options['precision'])) {
                    $value = Zend_Locale_Math::round($value, $options['precision']);
                } else {
                    if (substr($format, iconv_strpos($format, '.') + 1, 3) == '###') {
                        $options['precision'] = null;
                    } else {
                        $options['precision'] = iconv_strlen(iconv_substr($format, iconv_strpos($format, '.') + 1,
                                                             iconv_strrpos($format, '0') - iconv_strpos($format, '.')));
                        $format = iconv_substr($format, 0, iconv_strpos($format, '.') + 1) . '###'
                                . iconv_substr($format, iconv_strrpos($format, '0') + 1);
                    }
                }
            } else {
                $value = Zend_Locale_Math::round($value, 0);
                $options['precision'] = 0;
            }
            $value = Zend_Locale_Math::normalize($value);
        }

        if (iconv_strpos($format, '0') === false) {
            self::_setEncoding($oenc);
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception('Wrong format... missing 0');
        }

        // get number parts
        $pos = iconv_strpos($value, '.');
        if ($pos !== false) {
            if ($options['precision'] === null) {
                $precstr = iconv_substr($value, $pos + 1);
            } else {
                $precstr = iconv_substr($value, $pos + 1, $options['precision']);
                if (iconv_strlen($precstr) < $options['precision']) {
                    $precstr = $precstr . str_pad("0", ($options['precision'] - iconv_strlen($precstr)), "0");
                }
            }
        } else {
            if ($options['precision'] > 0) {
                $precstr = str_pad("0", ($options['precision']), "0");
            }
        }

        if ($options['precision'] === null) {
            if (isset($precstr)) {
                $options['precision'] = iconv_strlen($precstr);
            } else {
                $options['precision'] = 0;
            }
        }

        // get fraction and format lengths
        if (strpos($value, '.') !== false) {
            $number = substr((string) $value, 0, strpos($value, '.'));
        } else {
            $number = $value;
        }

        $prec = call_user_func(Zend_Locale_Math::$sub, $value, $number, $options['precision']);
        $prec = Zend_Locale_Math::floatalize($prec);
        $prec = Zend_Locale_Math::normalize($prec);
        if (iconv_strpos($prec, '-') !== false) {
            $prec = iconv_substr($prec, 1);
        }

        if (($prec == 0) and ($options['precision'] > 0)) {
            $prec = "0.0";
        }

        if (($options['precision'] + 2) > iconv_strlen($prec)) {
            $prec = str_pad((string) $prec, $options['precision'] + 2, "0", STR_PAD_RIGHT);
        }

        if (iconv_strpos($number, '-') !== false) {
            $number = iconv_substr($number, 1);
        }
        $group  = iconv_strrpos($format, ',');
        $group2 = iconv_strpos ($format, ',');
        $point  = iconv_strpos ($format, '0');
        // Add fraction
        $rest = "";
        if (iconv_strpos($format, '.')) {
            $rest   = iconv_substr($format, iconv_strpos($format, '.') + 1);
            $length = iconv_strlen($rest);
            for($x = 0; $x < $length; ++$x) {
                if (($rest[0] == '0') || ($rest[0] == '#')) {
                    $rest = iconv_substr($rest, 1);
                }
            }
            $format = iconv_substr($format, 0, iconv_strlen($format) - iconv_strlen($rest));
        }

        if ($options['precision'] == '0') {
            if (iconv_strrpos($format, '-') != 0) {
                $format = iconv_substr($format, 0, $point)
                        . iconv_substr($format, iconv_strrpos($format, '#') + 2);
            } else {
                $format = iconv_substr($format, 0, $point);
            }
        } else {
            $format = iconv_substr($format, 0, $point) . $symbols['decimal']
                               . iconv_substr($prec, 2);
        }

        $format .= $rest;
        // Add seperation
        if ($group == 0) {
            // no seperation
            $format = $number . iconv_substr($format, $point);
        } else if ($group == $group2) {
            // only 1 seperation
            $seperation = ($point - $group);
            for ($x = iconv_strlen($number); $x > $seperation; $x -= $seperation) {
                if (iconv_substr($number, 0, $x - $seperation) !== "") {
                    $number = iconv_substr($number, 0, $x - $seperation) . $symbols['group']
                            . iconv_substr($number, $x - $seperation);
                }
            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);
        } else {

            // 2 seperations
            if (iconv_strlen($number) > ($point - $group)) {
                $seperation = ($point - $group);
                $number = iconv_substr($number, 0, iconv_strlen($number) - $seperation) . $symbols['group']
                        . iconv_substr($number, iconv_strlen($number) - $seperation);

                if ((iconv_strlen($number) - 1) > ($point - $group + 1)) {
                    $seperation2 = ($group - $group2 - 1);
                    for ($x = iconv_strlen($number) - $seperation2 - 2; $x > $seperation2; $x -= $seperation2) {
                        $number = iconv_substr($number, 0, $x - $seperation2) . $symbols['group']
                                . iconv_substr($number, $x - $seperation2);
                    }
                }

            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);
        }
        // set negative sign
        if (call_user_func(Zend_Locale_Math::$comp, $value, 0, $options['precision']) < 0) {
            if (iconv_strpos($format, '-') === false) {
                $format = $symbols['minus'] . $format;
            } else {
                $format = str_replace('-', $symbols['minus'], $format);
            }
        }

        self::_setEncoding($oenc);
        return (string) $format;
    }

    /**
     * @param string $format
     * @param string $value
     * @param int $precision
     * @return string
     */
    private static function _seperateFormat($format, $value, $precision)
    {
        if (iconv_strpos($format, ';') !== false) {
            if (call_user_func(Zend_Locale_Math::$comp, $value, 0, $precision) < 0) {
                $tmpformat = iconv_substr($format, iconv_strpos($format, ';') + 1);
                if ($tmpformat[0] == '(') {
                    $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
                } else {
                    $format = $tmpformat;
                }
            } else {
                $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
            }
        }

        return $format;
    }


    /**
     * Checks if the input contains a normalized or localized number
     *
     * @param   string  $input    Localized number string
     * @param   array   $options  Options: locale. See {@link setOptions()} for details.
     * @return  boolean           Returns true if a number was found
     */
    public static function isNumber($input, array $options = array())
    {
        if (!self::_getUniCodeSupport()) {
            trigger_error("Sorry, your PCRE extension does not support UTF8 which is needed for the I18N core", E_USER_NOTICE);
        }

        $options = self::_checkOptions($options) + self::$_options;

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getList($options['locale'],'symbols');

        $regexs = Zend_Locale_Format::_getRegexForType('decimalnumber', $options);
        $regexs = array_merge($regexs, Zend_Locale_Format::_getRegexForType('scientificnumber', $options));
        if (!empty($input) && ($input[0] == $symbols['decimal'])) {
            $input = 0 . $input;
        }
        foreach ($regexs as $regex) {
            preg_match($regex, $input, $found);
            if (isset($found[0])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Internal method to convert cldr number syntax into regex
     *
     * @param  string $type
     * @param  array  $options Options: locale. See {@link setOptions()} for details.
     * @return string
     * @throws Zend_Locale_Exception
     */
    private static function _getRegexForType($type, $options)
    {
        $decimal  = Zend_Locale_Data::getContent($options['locale'], $type);
        $decimal  = preg_replace('/[^#0,;\.\-Ee]/u', '',$decimal);
        $patterns = explode(';', $decimal);

        if (count($patterns) == 1) {
            $patterns[1] = '-' . $patterns[0];
        }

        $symbols = Zend_Locale_Data::getList($options['locale'],'symbols');

        foreach($patterns as $pkey => $pattern) {
            $regex[$pkey]  = '/^';
            $rest   = 0;
            $end    = null;
            if (strpos($pattern, '.') !== false) {
                $end     = substr($pattern, strpos($pattern, '.') + 1);
                $pattern = substr($pattern, 0, -strlen($end) - 1);
            }

            if (strpos($pattern, ',') !== false) {
                $parts = explode(',', $pattern);
                $count = count($parts);
                foreach($parts as $key => $part) {
                    switch ($part) {
                        case '#':
                        case '-#':
                            if ($part[0] == '-') {
                                $regex[$pkey] .= '[' . $symbols['minus'] . '-]{0,1}';
                            } else {
                                $regex[$pkey] .= '[' . $symbols['plus'] . '+]{0,1}';
                            }

                            if (($parts[$key + 1]) == '##0')  {
                                $regex[$pkey] .= '[0-9]{1,3}';
                            } else if (($parts[$key + 1]) == '##') {
                                $regex[$pkey] .= '[0-9]{1,2}';
                            } else {
                                throw new Zend_Locale_Exception('Unsupported token for numberformat (Pos 1):"' . $pattern . '"');
                            }
                            break;
                        case '##':
                            if ($parts[$key + 1] == '##0') {
                                $regex[$pkey] .=  '(\\' . $symbols['group'] . '{0,1}[0-9]{2})*';
                            } else {
                                throw new Zend_Locale_Exception('Unsupported token for numberformat (Pos 2):"' . $pattern . '"');
                            }
                            break;
                        case '##0':
                            if ($parts[$key - 1] == '##') {
                                $regex[$pkey] .= '[0-9]';
                            } else if (($parts[$key - 1] == '#') || ($parts[$key - 1] == '-#')) {
                                $regex[$pkey] .= '(\\' . $symbols['group'] . '{0,1}[0-9]{3})*';
                            } else {
                                throw new Zend_Locale_Exception('Unsupported token for numberformat (Pos 3):"' . $pattern . '"');
                            }
                            break;
                        case '#0':
                            if ($key == 0) {
                                $regex[$pkey] .= '[0-9]*';
                            } else {
                                throw new Zend_Locale_Exception('Unsupported token for numberformat (Pos 4):"' . $pattern . '"');
                            }
                            break;
                    }
                }
            }

            if (strpos($pattern, 'E') !== false) {
                if (($pattern == '#E0') || ($pattern == '#E00')) {
                    $regex[$pkey] .= '[' . $symbols['plus']. '+]{0,1}[0-9]{1,}(\\' . $symbols['decimal'] . '[0-9]{1,})*[eE][' . $symbols['plus']. '+]{0,1}[0-9]{1,}';
                } else if (($pattern == '-#E0') || ($pattern == '-#E00')) {
                    $regex[$pkey] .= '[' . $symbols['minus']. '-]{0,1}[0-9]{1,}(\\' . $symbols['decimal'] . '[0-9]{1,})*[eE][' . $symbols['minus']. '-]{0,1}[0-9]{1,}';
                } else {
                    throw new Zend_Locale_Exception('Unsupported token for numberformat (Pos 5):"' . $pattern . '"');
                }
            }

            if (!empty($end)) {
                if ($end == '###') {
                    $regex[$pkey] .= '(\\' . $symbols['decimal'] . '{1}[0-9]{1,}){0,1}';
                } else if ($end == '###-') {
                    $regex[$pkey] .= '(\\' . $symbols['decimal'] . '{1}[0-9]{1,}){0,1}[' . $symbols['minus']. '-]';
                } else {
                    throw new Zend_Locale_Exception('Unsupported token for numberformat (Pos 6):"' . $pattern . '"');
                }
            }

            $regex[$pkey] .= '$/u';
        }

        return $regex;
    }

    /**
     * Alias for getNumber
     *
     * @param   string  $input    Number to localize
     * @param   array   $options  Options: locale, precision. See {@link setOptions()} for details.
     * @return  float
     */
    public static function getFloat($input, array $options = array())
    {
        return floatval(self::getNumber($input, $options));
    }

    /**
     * Returns a locale formatted integer number
     * Alias for toNumber()
     *
     * @param   string  $value    Number to normalize
     * @param   array   $options  Options: locale, precision. See {@link setOptions()} for details.
     * @return  string  Locale formatted number
     */
    public static function toFloat($value, array $options = array())
    {
        $options['number_format'] = Zend_Locale_Format::STANDARD;
        return self::toNumber($value, $options);
    }

    /**
     * Returns if a float was found
     * Alias for isNumber()
     *
     * @param   string $value  Localized number string
     * @param   array $options Options: locale. See {@link setOptions()} for details.
     * @return  boolean        Returns true if a number was found
     */
    public static function isFloat($value, array $options = array())
    {
        return self::isNumber($value, $options);
    }

    /**
     * Returns the first found integer from an string
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '  2345.4356,1234' = 23455456
     * '+23,3452.123' = 233452
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '(-){0,1}(\d+(\.){0,1})*(\,){0,1})\d+'
     *
     * @param   string   $input    Input string to parse for numbers
     * @param   array    $options  Options: locale. See {@link setOptions()} for details.
     * @return  integer            Returns the extracted number
     */
    public static function getInteger($input, array $options = array())
    {
        $options['precision'] = 0;
        return intval(self::getFloat($input, $options));
    }

    /**
     * Returns a localized number
     *
     * @param   string  $value    Number to normalize
     * @param   array   $options  Options: locale. See {@link setOptions()} for details.
     * @return  string            Locale formatted number
     */
    public static function toInteger($value, array $options = array())
    {
        $options['precision'] = 0;
        $options['number_format'] = Zend_Locale_Format::STANDARD;
        return self::toNumber($value, $options);
    }

    /**
     * Returns if a integer was found
     *
     * @param  string $value Localized number string
     * @param  array $options Options: locale. See {@link setOptions()} for details.
     * @return boolean Returns true if a integer was found
     */
    public static function isInteger($value, array $options = array())
    {
        if (!self::isNumber($value, $options)) {
            return false;
        }

        if (self::getInteger($value, $options) == self::getFloat($value, $options)) {
            return true;
        }

        return false;
    }

    /**
     * Converts a format string from PHP's date format to ISO format
     * Remember that Zend Date always returns localized string, so a month name which returns the english
     * month in php's date() will return the translated month name with this function... use 'en' as locale
     * if you are in need of the original english names
     *
     * The conversion has the following restrictions:
     * 'a', 'A' - Meridiem is not explicit upper/lowercase, you have to upper/lowercase the translated value yourself
     *
     * @param  string  $format  Format string in PHP's date format
     * @return string           Format string in ISO format
     */
    public static function convertPhpToIsoFormat($format)
    {
        if ($format === null) {
            return null;
        }

        $convert = array(
            'd' => 'dd'  , 'D' => 'EE'  , 'j' => 'd'   , 'l' => 'EEEE',
            'N' => 'eee' , 'S' => 'SS'  , 'w' => 'e'   , 'z' => 'D'   ,
            'W' => 'ww'  , 'F' => 'MMMM', 'm' => 'MM'  , 'M' => 'MMM' ,
            'n' => 'M'   , 't' => 'ddd' , 'L' => 'l'   , 'o' => 'YYYY',
            'Y' => 'yyyy', 'y' => 'yy'  , 'a' => 'a'   , 'A' => 'a'   ,
            'B' => 'B'   , 'g' => 'h'   , 'G' => 'H'   , 'h' => 'hh'  ,
            'H' => 'HH'  , 'i' => 'mm'  , 's' => 'ss'  , 'e' => 'zzzz',
            'I' => 'I'   , 'O' => 'Z'   , 'P' => 'ZZZZ', 'T' => 'z'   ,
            'Z' => 'X'   , 'c' => 'yyyy-MM-ddTHH:mm:ssZZZZ', 'r' => 'r',
            'U' => 'U',
        );
        $escaped = false;
        $inEscapedString = false;
        $converted = array();
        foreach (str_split($format) as $char) {
            if (!$escaped && $char == '\\') {
                // Next char will be escaped: let's remember it
                $escaped = true;
            } elseif ($escaped) {
                if (!$inEscapedString) {
                    // First escaped string: start the quoted chunk
                    $converted[] = "'";
                    $inEscapedString = true;
                }
                // Since the previous char was a \ and we are in the quoted
                // chunk, let's simply add $char as it is
                $converted[] = $char;
                $escaped = false;
            } elseif ($char == "'") {
                // Single quotes need to be escaped like this
                $converted[] = "''";
            } else {
                if ($inEscapedString) {
                    // Close the single-quoted chunk
                    $converted[] = "'";
                    $inEscapedString = false;
                }
                // Convert the unescaped char if needed
                if (isset($convert[$char])) {
                    $converted[] = $convert[$char];
                } else {
                    $converted[] = $char;
                }
            }
        }

        return implode($converted);
    }

    /**
     * Parse date and split in named array fields
     *
     * @param  string $date    Date string to parse
     * @param  array  $options Options: format_type, fix_date, locale, date_format. See {@link setOptions()} for details.
     * @return array Possible array members: day, month, year, hour, minute, second, fixed, format
     * @throws Zend_Locale_Exception
     */
    private static function _parseDate($date, $options)
    {
        if (!self::_getUniCodeSupport()) {
            trigger_error("Sorry, your PCRE extension does not support UTF8 which is needed for the I18N core", E_USER_NOTICE);
        }

        $options = self::_checkOptions($options) + self::$_options;
        $test = array('h', 'H', 'm', 's', 'y', 'Y', 'M', 'd', 'D', 'E', 'S', 'l', 'B', 'I',
                       'X', 'r', 'U', 'G', 'w', 'e', 'a', 'A', 'Z', 'z', 'v');

        $format = $options['date_format'];
        $number = $date; // working copy
        $result['date_format'] = $format; // save the format used to normalize $number (convenience)
        $result['locale'] = $options['locale']; // save the locale used to normalize $number (convenience)

        $oenc = self::_getEncoding();
        self::_setEncoding('UTF-8');
        $day   = iconv_strpos($format, 'd');
        $month = iconv_strpos($format, 'M');
        $year  = iconv_strpos($format, 'y');
        $hour  = iconv_strpos($format, 'H');
        $min   = iconv_strpos($format, 'm');
        $sec   = iconv_strpos($format, 's');
        $am    = null;
        if ($hour === false) {
            $hour = iconv_strpos($format, 'h');
        }
        if ($year === false) {
            $year = iconv_strpos($format, 'Y');
        }
        if ($day === false) {
            $day = iconv_strpos($format, 'E');
            if ($day === false) {
                $day = iconv_strpos($format, 'D');
            }
        }

        if ($day !== false) {
            $parse[$day]   = 'd';
            if (!empty($options['locale']) && ($options['locale'] !== 'root') &&
                (!is_object($options['locale']) || ((string) $options['locale'] !== 'root'))) {
                // erase day string
                    $daylist = Zend_Locale_Data::getList($options['locale'], 'day');
                foreach($daylist as $key => $name) {
                    if (iconv_strpos($number, $name) !== false) {
                        $number = str_replace($name, "EEEE", $number);
                        break;
                    }
                }
            }
        }
        $position = false;

        if ($month !== false) {
            $parse[$month] = 'M';
            if (!empty($options['locale']) && ($options['locale'] !== 'root') &&
                (!is_object($options['locale']) || ((string) $options['locale'] !== 'root'))) {
                    // prepare to convert month name to their numeric equivalents, if requested,
                    // and we have a $options['locale']
                    $position = self::_replaceMonth($number, Zend_Locale_Data::getList($options['locale'],
                        'month'));
                if ($position === false) {
                    $position = self::_replaceMonth($number, Zend_Locale_Data::getList($options['locale'],
                        'month', array('gregorian', 'format', 'abbreviated')));
                }
            }
        }
        if ($year !== false) {
            $parse[$year]  = 'y';
        }
        if ($hour !== false) {
            $parse[$hour] = 'H';
        }
        if ($min !== false) {
            $parse[$min] = 'm';
        }
        if ($sec !== false) {
            $parse[$sec] = 's';
        }

        if (empty($parse)) {
            self::_setEncoding($oenc);
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("Unknown date format, neither date nor time in '" . $format . "' found");
        }
        ksort($parse);

        // get daytime
        if (iconv_strpos($format, 'a') !== false) {
            if (iconv_strpos(strtoupper($number), strtoupper(Zend_Locale_Data::getContent($options['locale'], 'am'))) !== false) {
                $am = true;
            } else if (iconv_strpos(strtoupper($number), strtoupper(Zend_Locale_Data::getContent($options['locale'], 'pm'))) !== false) {
                $am = false;
            }
        }

        // split number parts
        $split = false;
        preg_match_all('/\d+/u', $number, $splitted);

        if (count($splitted[0]) == 0) {
            self::_setEncoding($oenc);
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("No date part in '$date' found.");
        }
        if (count($splitted[0]) == 1) {
            $split = 0;
        }
        $cnt = 0;
        foreach($parse as $key => $value) {

            switch($value) {
                case 'd':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['day']    = $splitted[0][$cnt];
                        }
                    } else {
                        $result['day'] = iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'M':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['month']  = $splitted[0][$cnt];
                        }
                    } else {
                        $result['month'] = iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'y':
                    $length = 2;
                    if ((iconv_substr($format, $year, 4) == 'yyyy')
                     || (iconv_substr($format, $year, 4) == 'YYYY')) {
                        $length = 4;
                    }

                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['year']   = $splitted[0][$cnt];
                        }
                    } else {
                        $result['year']   = iconv_substr($splitted[0][0], $split, $length);
                        $split += $length;
                    }

                    ++$cnt;
                    break;
                case 'H':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['hour']   = $splitted[0][$cnt];
                        }
                    } else {
                        $result['hour']   = iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'm':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['minute'] = $splitted[0][$cnt];
                        }
                    } else {
                        $result['minute'] = iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 's':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['second'] = $splitted[0][$cnt];
                        }
                    } else {
                        $result['second'] = iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
            }
        }

        // AM/PM correction
        if ($hour !== false) {
            if (($am === true) and ($result['hour'] == 12)){
                $result['hour'] = 0;
            } else if (($am === false) and ($result['hour'] != 12)) {
                $result['hour'] += 12;
            }
        }

        if ($options['fix_date'] === true) {
            $result['fixed'] = 0; // nothing has been "fixed" by swapping date parts around (yet)
        }

        if ($day !== false) {
            // fix false month
            if (isset($result['day']) and isset($result['month'])) {
                if (($position !== false) and ((iconv_strpos($date, $result['day']) === false) or
                                               (isset($result['year']) and (iconv_strpos($date, $result['year']) === false)))) {
                    if ($options['fix_date'] !== true) {
                        self::_setEncoding($oenc);
                        #require_once 'Zend/Locale/Exception.php';
                        throw new Zend_Locale_Exception("Unable to parse date '$date' using '" . $format
                            . "' (false month, $position, $month)");
                    }
                    $temp = $result['day'];
                    $result['day']   = $result['month'];
                    $result['month'] = $temp;
                    $result['fixed'] = 1;
                }
            }

            // fix switched values d <> y
            if (isset($result['day']) and isset($result['year'])) {
                if ($result['day'] > 31) {
                    if ($options['fix_date'] !== true) {
                        self::_setEncoding($oenc);
                        #require_once 'Zend/Locale/Exception.php';
                        throw new Zend_Locale_Exception("Unable to parse date '$date' using '"
                                                      . $format . "' (d <> y)");
                    }
                    $temp = $result['year'];
                    $result['year'] = $result['day'];
                    $result['day']  = $temp;
                    $result['fixed'] = 2;
                }
            }

            // fix switched values M <> y
            if (isset($result['month']) and isset($result['year'])) {
                if ($result['month'] > 31) {
                    if ($options['fix_date'] !== true) {
                        self::_setEncoding($oenc);
                        #require_once 'Zend/Locale/Exception.php';
                        throw new Zend_Locale_Exception("Unable to parse date '$date' using '"
                                                      . $format . "' (M <> y)");
                    }
                    $temp = $result['year'];
                    $result['year']  = $result['month'];
                    $result['month'] = $temp;
                    $result['fixed'] = 3;
                }
            }

            // fix switched values M <> d
            if (isset($result['month']) and isset($result['day'])) {
                if ($result['month'] > 12) {
                    if ($options['fix_date'] !== true || $result['month'] > 31) {
                        self::_setEncoding($oenc);
                        #require_once 'Zend/Locale/Exception.php';
                        throw new Zend_Locale_Exception("Unable to parse date '$date' using '"
                                                      . $format . "' (M <> d)");
                    }
                    $temp = $result['day'];
                    $result['day']   = $result['month'];
                    $result['month'] = $temp;
                    $result['fixed'] = 4;
                }
            }
        }

        if (isset($result['year'])) {
            if (((iconv_strlen($result['year']) == 2) && ($result['year'] < 10)) ||
                (((iconv_strpos($format, 'yy') !== false) && (iconv_strpos($format, 'yyyy') === false)) ||
                ((iconv_strpos($format, 'YY') !== false) && (iconv_strpos($format, 'YYYY') === false)))) {
                if (($result['year'] >= 0) && ($result['year'] < 100)) {
                    if ($result['year'] < 70) {
                        $result['year'] = (int) $result['year'] + 100;
                    }

                    $result['year'] = (int) $result['year'] + 1900;
                }
            }
        }

        self::_setEncoding($oenc);
        return $result;
    }

    /**
     * Search $number for a month name found in $monthlist, and replace if found.
     *
     * @param  string  $number     Date string (modified)
     * @param  array   $monthlist  List of month names
     *
     * @return int|false           Position of replaced string (false if nothing replaced)
     */
    protected static function _replaceMonth(&$number, $monthlist)
    {
        // If $locale was invalid, $monthlist will default to a "root" identity
        // mapping for each month number from 1 to 12.
        // If no $locale was given, or $locale was invalid, do not use this identity mapping to normalize.
        // Otherwise, translate locale aware month names in $number to their numeric equivalents.
        $position = false;
        if ($monthlist && $monthlist[1] != 1) {
            foreach($monthlist as $key => $name) {
                if (($position = iconv_strpos($number, $name, 0, 'UTF-8')) !== false) {
                    $number   = str_ireplace($name, $key, $number);
                    return $position;
                }
            }
        }

        return false;
    }

    /**
     * Returns the default date format for $locale.
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale of $number, possibly in string form (e.g. 'de_AT')
     * @return string  format
     * @throws Zend_Locale_Exception  throws an exception when locale data is broken
     */
    public static function getDateFormat($locale = null)
    {
        $format = Zend_Locale_Data::getContent($locale, 'date');
        if (empty($format)) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("failed to receive data from locale $locale");
        }

        return $format;
    }

    /**
     * Returns an array with the normalized date from an locale date
     * a input of 10.01.2006 without a $locale would return:
     * array ('day' => 10, 'month' => 1, 'year' => 2006)
     * The 'locale' option is only used to convert human readable day
     * and month names to their numeric equivalents.
     * The 'format' option allows specification of self-defined date formats,
     * when not using the default format for the 'locale'.
     *
     * @param   string  $date     Date string
     * @param   array   $options  Options: format_type, fix_date, locale, date_format. See {@link setOptions()} for details.
     * @return  array             Possible array members: day, month, year, hour, minute, second, fixed, format
     */
    public static function getDate($date, array $options = array())
    {
        $options = self::_checkOptions($options) + self::$_options;
        if (empty($options['date_format'])) {
            $options['format_type'] = 'iso';
            $options['date_format'] = self::getDateFormat($options['locale']);
        }

        return self::_parseDate($date, $options);
    }

    /**
     * Returns if the given datestring contains all date parts from the given format.
     * If no format is given, the default date format from the locale is used
     * If you want to check if the date is a proper date you should use Zend_Date::isDate()
     *
     * @param   string  $date     Date string
     * @param   array   $options  Options: format_type, fix_date, locale, date_format. See {@link setOptions()} for details.
     * @return  boolean
     */
    public static function checkDateFormat($date, array $options = array())
    {
        try {
            $date = self::getDate($date, $options);
        } catch (Exception $e) {
            return false;
        }

        if (empty($options['date_format'])) {
            $options['format_type'] = 'iso';
            $options['date_format'] = self::getDateFormat(isset($options['locale']) ? $options['locale'] : null);
        }
        $options = self::_checkOptions($options) + self::$_options;

        // day expected but not parsed
        if ((iconv_strpos($options['date_format'], 'd', 0, 'UTF-8') !== false) and (!isset($date['day']) or ($date['day'] === ""))) {
            return false;
        }

        // month expected but not parsed
        if ((iconv_strpos($options['date_format'], 'M', 0, 'UTF-8') !== false) and (!isset($date['month']) or ($date['month'] === ""))) {
            return false;
        }

        // year expected but not parsed
        if (((iconv_strpos($options['date_format'], 'Y', 0, 'UTF-8') !== false) or
             (iconv_strpos($options['date_format'], 'y', 0, 'UTF-8') !== false)) and (!isset($date['year']) or ($date['year'] === ""))) {
            return false;
        }

        // second expected but not parsed
        if ((iconv_strpos($options['date_format'], 's', 0, 'UTF-8') !== false) and (!isset($date['second']) or ($date['second'] === ""))) {
            return false;
        }

        // minute expected but not parsed
        if ((iconv_strpos($options['date_format'], 'm', 0, 'UTF-8') !== false) and (!isset($date['minute']) or ($date['minute'] === ""))) {
            return false;
        }

        // hour expected but not parsed
        if (((iconv_strpos($options['date_format'], 'H', 0, 'UTF-8') !== false) or
             (iconv_strpos($options['date_format'], 'h', 0, 'UTF-8') !== false)) and (!isset($date['hour']) or ($date['hour'] === ""))) {
            return false;
        }

        return true;
    }

    /**
     * Returns the default time format for $locale.
     *
     * @param  string|Zend_Locale $locale OPTIONAL Locale of $number, possibly in string form (e.g. 'de_AT')
     * @return string  format
     * @throws Zend_Locale_Exception
     */
    public static function getTimeFormat($locale = null)
    {
        $format = Zend_Locale_Data::getContent($locale, 'time');
        if (empty($format)) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("failed to receive data from locale $locale");
        }
        return $format;
    }

    /**
     * Returns an array with 'hour', 'minute', and 'second' elements extracted from $time
     * according to the order described in $format.  For a format of 'H:i:s', and
     * an input of 11:20:55, getTime() would return:
     * array ('hour' => 11, 'minute' => 20, 'second' => 55)
     * The optional $locale parameter may be used to help extract times from strings
     * containing both a time and a day or month name.
     *
     * @param   string  $time     Time string
     * @param   array   $options  Options: format_type, fix_date, locale, date_format. See {@link setOptions()} for details.
     * @return  array             Possible array members: day, month, year, hour, minute, second, fixed, format
     */
    public static function getTime($time, array $options = array())
    {
        $options = self::_checkOptions($options) + self::$_options;
        if (empty($options['date_format'])) {
            $options['format_type'] = 'iso';
            $options['date_format'] = self::getTimeFormat($options['locale']);
        }
        return self::_parseDate($time, $options);
    }

    /**
     * Returns the default datetime format for $locale.
     *
     * @param  string|Zend_Locale $locale OPTIONAL Locale of $number, possibly in string form (e.g. 'de_AT')
     * @return string  format
     * @throws Zend_Locale_Exception
     */
    public static function getDateTimeFormat($locale = null)
    {
        $format = Zend_Locale_Data::getContent($locale, 'datetime');
        if (empty($format)) {
            #require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("failed to receive data from locale $locale");
        }
        return $format;
    }

    /**
     * Returns an array with 'year', 'month', 'day', 'hour', 'minute', and 'second' elements
     * extracted from $datetime according to the order described in $format.  For a format of 'd.M.y H:i:s',
     * and an input of 10.05.1985 11:20:55, getDateTime() would return:
     * array ('year' => 1985, 'month' => 5, 'day' => 10, 'hour' => 11, 'minute' => 20, 'second' => 55)
     * The optional $locale parameter may be used to help extract times from strings
     * containing both a time and a day or month name.
     *
     * @param   string  $datetime DateTime string
     * @param   array   $options  Options: format_type, fix_date, locale, date_format. See {@link setOptions()} for details.
     * @return  array             Possible array members: day, month, year, hour, minute, second, fixed, format
     */
    public static function getDateTime($datetime, array $options = array())
    {
        $options = self::_checkOptions($options) + self::$_options;
        if (empty($options['date_format'])) {
            $options['format_type'] = 'iso';
            $options['date_format'] = self::getDateTimeFormat($options['locale']);
        }
        return self::_parseDate($datetime, $options);
    }

    /**
     * Internal method to detect of Unicode supports UTF8
     * which should be enabled within vanilla php installations
     *
     * @return boolean
     */
    protected static function _getUniCodeSupport()
    {
        return (@preg_match('/\pL/u', 'a')) ? true : false;
    }

    /**
     * Internal method to retrieve the current encoding via the ini setting
     * default_charset for PHP >= 5.6 or iconv_get_encoding otherwise.
     *
     * @return string
     */
    protected static function _getEncoding()
    {
        $oenc = PHP_VERSION_ID < 50600
            ? iconv_get_encoding('internal_encoding')
            : ini_get('default_charset');

        return $oenc;
    }

    /**
     * Internal method to set the encoding via the ini setting
     * default_charset for PHP >= 5.6 or iconv_set_encoding otherwise.
     *
     * @param string $encoding
     * @return void
     */
    protected static function _setEncoding($encoding)
    {
        if (PHP_VERSION_ID < 50600) {
            iconv_set_encoding('internal_encoding', $encoding);
        } else {
            ini_set('default_charset', $encoding);
        }
    }
}
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Utility class for proxying math function to bcmath functions, if present,
 * otherwise to PHP builtin math operators, with limited detection of overflow conditions.
 * Sampling of PHP environments and platforms suggests that at least 80% to 90% support bcmath.
 * Thus, this file should be as light as possible.
 *
 * @category   Zend
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Locale_Math
{
    // support unit testing without using bcmath functions
    public static $_bcmathDisabled = false;

    public static $add   = array('Zend_Locale_Math', 'Add');
    public static $sub   = array('Zend_Locale_Math', 'Sub');
    public static $pow   = array('Zend_Locale_Math', 'Pow');
    public static $mul   = array('Zend_Locale_Math', 'Mul');
    public static $div   = array('Zend_Locale_Math', 'Div');
    public static $comp  = array('Zend_Locale_Math', 'Comp');
    public static $sqrt  = array('Zend_Locale_Math', 'Sqrt');
    public static $mod   = array('Zend_Locale_Math', 'Mod');
    public static $scale = 'bcscale';

    public static function isBcmathDisabled()
    {
        return self::$_bcmathDisabled;
    }

    /**
     * Surprisingly, the results of this implementation of round()
     * prove better than the native PHP round(). For example, try:
     *   round(639.795, 2);
     *   round(267.835, 2);
     *   round(0.302515, 5);
     *   round(0.36665, 4);
     * then try:
     *   Zend_Locale_Math::round('639.795', 2);
     */
    public static function round($op1, $precision = 0)
    {
        if (self::$_bcmathDisabled) {
            $op1 = round($op1, $precision);
            if (strpos((string) $op1, 'E') === false) {
                return self::normalize(round($op1, $precision));
            }
        }

        if (strpos($op1, 'E') !== false) {
            $op1 = self::floatalize($op1);
        }

        $op1    = trim(self::normalize($op1));
        $length = strlen($op1);
        if (($decPos = strpos($op1, '.')) === false) {
            $op1 .= '.0';
            $decPos = $length;
            $length += 2;
        }
        if ($precision < 0 && abs($precision) > $decPos) {
            return '0';
        }

        $digitsBeforeDot = $length - ($decPos + 1);
        if ($precision >= ($length - ($decPos + 1))) {
            return $op1;
        }

        if ($precision === 0) {
            $triggerPos = 1;
            $roundPos   = -1;
        } elseif ($precision > 0) {
            $triggerPos = $precision + 1;
            $roundPos   = $precision;
        } else {
            $triggerPos = $precision;
            $roundPos   = $precision -1;
        }

        $triggerDigit = $op1[$triggerPos + $decPos];
        if ($precision < 0) {
            // zero fill digits to the left of the decimal place
            $op1 = substr($op1, 0, $decPos + $precision) . str_pad('', abs($precision), '0');
        }

        if ($triggerDigit >= '5') {
            if ($roundPos + $decPos == -1) {
                return str_pad('1', $decPos + 1, '0');
            }

            $roundUp = str_pad('', $length, '0');
            $roundUp[$decPos] = '.';
            $roundUp[$roundPos + $decPos] = '1';

            if ($op1 > 0) {
                if (self::$_bcmathDisabled) {
                    return Zend_Locale_Math_PhpMath::Add($op1, $roundUp, $precision);
                }
                return self::Add($op1, $roundUp, $precision);
            } else {
                if (self::$_bcmathDisabled) {
                    return Zend_Locale_Math_PhpMath::Sub($op1, $roundUp, $precision);
                }
                return self::Sub($op1, $roundUp, $precision);
            }
        } elseif ($precision >= 0) {
            return substr($op1, 0, $decPos + ($precision ? $precision + 1: 0));
        }

        return (string) $op1;
    }

    /**
     * Convert a scientific notation to float
     * Additionally fixed a problem with PHP <= 5.2.x with big integers
     *
     * @param string $value
     */
    public static function floatalize($value)
    {
        $value = strtoupper($value);
        if (strpos($value, 'E') === false) {
            return $value;
        }

        $number = substr($value, 0, strpos($value, 'E'));
        if (strpos($number, '.') !== false) {
            $post   = strlen(substr($number, strpos($number, '.') + 1));
            $mantis = substr($value, strpos($value, 'E') + 1);
            if ($mantis < 0) {
                $post += abs((int) $mantis);
            }

            $value = number_format($value, $post, '.', '');
        } else {
            $value = number_format($value, 0, '.', '');
        }

        return $value;
    }

    /**
     * Normalizes an input to standard english notation
     * Fixes a problem of BCMath with setLocale which is PHP related
     *
     * @param   integer  $value  Value to normalize
     * @return  string           Normalized string without BCMath problems
     */
    public static function normalize($value)
    {
        $convert = localeconv();
        $value = str_replace($convert['thousands_sep'], "",(string) $value);
        $value = str_replace($convert['positive_sign'], "", $value);
        $value = str_replace($convert['decimal_point'], ".",$value);
        if (!empty($convert['negative_sign']) and (strpos($value, $convert['negative_sign']))) {
            $value = str_replace($convert['negative_sign'], "", $value);
            $value = "-" . $value;
        }

        return $value;
    }

    /**
     * Localizes an input from standard english notation
     * Fixes a problem of BCMath with setLocale which is PHP related
     *
     * @param   integer  $value  Value to normalize
     * @return  string           Normalized string without BCMath problems
     */
    public static function localize($value)
    {
        $convert = localeconv();
        $value = str_replace(".", $convert['decimal_point'], (string) $value);
        if (!empty($convert['negative_sign']) and (strpos($value, "-"))) {
            $value = str_replace("-", $convert['negative_sign'], $value);
        }
        return $value;
    }

    /**
     * Changes exponential numbers to plain string numbers
     * Fixes a problem of BCMath with numbers containing exponents
     *
     * @param integer $value Value to erase the exponent
     * @param integer $scale (Optional) Scale to use
     * @return string
     */
    public static function exponent($value, $scale = null)
    {
        if (!extension_loaded('bcmath')) {
            return $value;
        }

        $split = explode('e', $value);
        if (count($split) == 1) {
            $split = explode('E', $value);
        }

        if (count($split) > 1) {
            $value = bcmul($split[0], bcpow(10, $split[1], $scale), $scale);
        }

        return $value;
    }

    /**
     * BCAdd - fixes a problem of BCMath and exponential numbers
     *
     * @param  string  $op1
     * @param  string  $op2
     * @param  integer $scale
     * @return string
     */
    public static function Add($op1, $op2, $scale = null)
    {
        $op1 = self::exponent($op1, $scale);
        $op2 = self::exponent($op2, $scale);

        return bcadd($op1, $op2, $scale);
    }

    /**
     * BCSub - fixes a problem of BCMath and exponential numbers
     *
     * @param  string  $op1
     * @param  string  $op2
     * @param  integer $scale
     * @return string
     */
    public static function Sub($op1, $op2, $scale = null)
    {
        $op1 = self::exponent($op1, $scale);
        $op2 = self::exponent($op2, $scale);
        return bcsub($op1, $op2, $scale);
    }

    /**
     * BCPow - fixes a problem of BCMath and exponential numbers
     *
     * @param  string  $op1
     * @param  string  $op2
     * @param  integer $scale
     * @return string
     */
    public static function Pow($op1, $op2, $scale = null)
    {
        $op1 = self::exponent($op1, $scale);
        $op2 = self::exponent($op2, $scale);
        return bcpow($op1, $op2, $scale);
    }

    /**
     * BCMul - fixes a problem of BCMath and exponential numbers
     *
     * @param  string  $op1
     * @param  string  $op2
     * @param  integer $scale
     * @return string
     */
    public static function Mul($op1, $op2, $scale = null)
    {
        $op1 = self::exponent($op1, $scale);
        $op2 = self::exponent($op2, $scale);
        return bcmul($op1, $op2, $scale);
    }

    /**
     * BCDiv - fixes a problem of BCMath and exponential numbers
     *
     * @param  string  $op1
     * @param  string  $op2
     * @param  integer $scale
     * @return string
     */
    public static function Div($op1, $op2, $scale = null)
    {
        $op1 = self::exponent($op1, $scale);
        $op2 = self::exponent($op2, $scale);
        return bcdiv($op1, $op2, $scale);
    }

    /**
     * BCSqrt - fixes a problem of BCMath and exponential numbers
     *
     * @param  string  $op1
     * @param  integer $scale
     * @return string
     */
    public static function Sqrt($op1, $scale = null)
    {
        $op1 = self::exponent($op1, $scale);
        return bcsqrt($op1, $scale);
    }

    /**
     * BCMod - fixes a problem of BCMath and exponential numbers
     *
     * @param  string  $op1
     * @param  string  $op2
     * @return string
     */
    public static function Mod($op1, $op2)
    {
        $op1 = self::exponent($op1);
        $op2 = self::exponent($op2);
        return bcmod($op1, $op2);
    }

    /**
     * BCComp - fixes a problem of BCMath and exponential numbers
     *
     * @param  string  $op1
     * @param  string  $op2
     * @param  integer $scale
     * @return string
     */
    public static function Comp($op1, $op2, $scale = null)
    {
        $op1 = self::exponent($op1, $scale);
        $op2 = self::exponent($op2, $scale);
        return bccomp($op1, $op2, $scale);
    }
}

if (!extension_loaded('bcmath')
    || (defined('TESTS_ZEND_LOCALE_BCMATH_ENABLED') && !TESTS_ZEND_LOCALE_BCMATH_ENABLED)
) {
    require_once 'Zend/Locale/Math/PhpMath.php';
    Zend_Locale_Math_PhpMath::disable();
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity/Attribute/Model - attribute backend default
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Increment extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set new increment id
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Increment
     */
    public function beforeSave($object)
    {
        if (!$object->getId()) {
            $this->getAttribute()->getEntity()->setNewIncrementId($object);
        }

        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store attribute backend
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Store extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }

        if (!$object->hasStoreId()) {
            $object->setStoreId(Mage::app()->getStore()->getId());
        }

        if (!$object->hasData('created_in')) {
            $object->setData('created_in', Mage::app()->getStore($object->getStoreId())->getName());
        }
        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Website attribute backend
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Website extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }
        if (!$object->hasData('website_id')) {
            $object->setData('website_id', Mage::app()->getStore()->getWebsiteId());
        }
        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Boolean customer attribute backend model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Attribute_Backend_Data_Boolean
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data before attribute save
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Attribute_Backend_Data_Boolean
     */
    public function beforeSave($customer)
    {
        $attributeName = $this->getAttribute()->getName();
        $inputValue = $customer->getData($attributeName);
        $sanitizedValue = (!empty($inputValue)) ? '1' : '0';
        $customer->setData($attributeName, $sanitizedValue);
        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote model
 *
 * Supported events:
 *  sales_quote_load_after
 *  sales_quote_save_before
 *  sales_quote_save_after
 *  sales_quote_delete_before
 *  sales_quote_delete_after
 *
 * @method Mage_Sales_Model_Resource_Quote _getResource()
 * @method Mage_Sales_Model_Resource_Quote getResource()
 * @method Mage_Sales_Model_Quote setStoreId(int $value)
 * @method string getCreatedAt()
 * @method Mage_Sales_Model_Quote setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Mage_Sales_Model_Quote setUpdatedAt(string $value)
 * @method string getConvertedAt()
 * @method Mage_Sales_Model_Quote setConvertedAt(string $value)
 * @method int getIsActive()
 * @method Mage_Sales_Model_Quote setIsActive(int $value)
 * @method Mage_Sales_Model_Quote setIsVirtual(int $value)
 * @method int getIsMultiShipping()
 * @method Mage_Sales_Model_Quote setIsMultiShipping(int $value)
 * @method int getItemsCount()
 * @method Mage_Sales_Model_Quote setItemsCount(int $value)
 * @method float getItemsQty()
 * @method Mage_Sales_Model_Quote setItemsQty(float $value)
 * @method int getOrigOrderId()
 * @method Mage_Sales_Model_Quote setOrigOrderId(int $value)
 * @method float getStoreToBaseRate()
 * @method Mage_Sales_Model_Quote setStoreToBaseRate(float $value)
 * @method float getStoreToQuoteRate()
 * @method Mage_Sales_Model_Quote setStoreToQuoteRate(float $value)
 * @method string getBaseCurrencyCode()
 * @method Mage_Sales_Model_Quote setBaseCurrencyCode(string $value)
 * @method string getStoreCurrencyCode()
 * @method Mage_Sales_Model_Quote setStoreCurrencyCode(string $value)
 * @method string getQuoteCurrencyCode()
 * @method Mage_Sales_Model_Quote setQuoteCurrencyCode(string $value)
 * @method float getGrandTotal()
 * @method Mage_Sales_Model_Quote setGrandTotal(float $value)
 * @method float getBaseGrandTotal()
 * @method Mage_Sales_Model_Quote setBaseGrandTotal(float $value)
 * @method Mage_Sales_Model_Quote setCheckoutMethod(string $value)
 * @method int getCustomerId()
 * @method Mage_Sales_Model_Quote setCustomerId(int $value)
 * @method Mage_Sales_Model_Quote setCustomerTaxClassId(int $value)
 * @method Mage_Sales_Model_Quote setCustomerGroupId(int $value)
 * @method string getCustomerEmail()
 * @method Mage_Sales_Model_Quote setCustomerEmail(string $value)
 * @method string getCustomerPrefix()
 * @method Mage_Sales_Model_Quote setCustomerPrefix(string $value)
 * @method string getCustomerFirstname()
 * @method Mage_Sales_Model_Quote setCustomerFirstname(string $value)
 * @method string getCustomerMiddlename()
 * @method Mage_Sales_Model_Quote setCustomerMiddlename(string $value)
 * @method string getCustomerLastname()
 * @method Mage_Sales_Model_Quote setCustomerLastname(string $value)
 * @method string getCustomerSuffix()
 * @method Mage_Sales_Model_Quote setCustomerSuffix(string $value)
 * @method string getCustomerDob()
 * @method Mage_Sales_Model_Quote setCustomerDob(string $value)
 * @method string getCustomerNote()
 * @method Mage_Sales_Model_Quote setCustomerNote(string $value)
 * @method int getCustomerNoteNotify()
 * @method Mage_Sales_Model_Quote setCustomerNoteNotify(int $value)
 * @method int getCustomerIsGuest()
 * @method Mage_Sales_Model_Quote setCustomerIsGuest(int $value)
 * @method string getRemoteIp()
 * @method Mage_Sales_Model_Quote setRemoteIp(string $value)
 * @method string getAppliedRuleIds()
 * @method Mage_Sales_Model_Quote setAppliedRuleIds(string $value)
 * @method string getReservedOrderId()
 * @method Mage_Sales_Model_Quote setReservedOrderId(string $value)
 * @method string getPasswordHash()
 * @method Mage_Sales_Model_Quote setPasswordHash(string $value)
 * @method string getCouponCode()
 * @method Mage_Sales_Model_Quote setCouponCode(string $value)
 * @method string getGlobalCurrencyCode()
 * @method Mage_Sales_Model_Quote setGlobalCurrencyCode(string $value)
 * @method float getBaseToGlobalRate()
 * @method Mage_Sales_Model_Quote setBaseToGlobalRate(float $value)
 * @method float getBaseToQuoteRate()
 * @method Mage_Sales_Model_Quote setBaseToQuoteRate(float $value)
 * @method string getCustomerTaxvat()
 * @method Mage_Sales_Model_Quote setCustomerTaxvat(string $value)
 * @method string getCustomerGender()
 * @method Mage_Sales_Model_Quote setCustomerGender(string $value)
 * @method float getSubtotal()
 * @method Mage_Sales_Model_Quote setSubtotal(float $value)
 * @method float getBaseSubtotal()
 * @method Mage_Sales_Model_Quote setBaseSubtotal(float $value)
 * @method float getSubtotalWithDiscount()
 * @method Mage_Sales_Model_Quote setSubtotalWithDiscount(float $value)
 * @method float getBaseSubtotalWithDiscount()
 * @method Mage_Sales_Model_Quote setBaseSubtotalWithDiscount(float $value)
 * @method int getIsChanged()
 * @method Mage_Sales_Model_Quote setIsChanged(int $value)
 * @method int getTriggerRecollect()
 * @method Mage_Sales_Model_Quote setTriggerRecollect(int $value)
 * @method string getExtShippingInfo()
 * @method Mage_Sales_Model_Quote setExtShippingInfo(string $value)
 * @method int getGiftMessageId()
 * @method Mage_Sales_Model_Quote setGiftMessageId(int $value)
 * @method bool|null getIsPersistent()
 * @method Mage_Sales_Model_Quote setIsPersistent(bool $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Quote extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_quote';
    protected $_eventObject = 'quote';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * When you use true - all cache will be clean
     *
     * @var string || true
     */
    protected $_cacheTag = 'quote';

    /**
     * Quote customer model object
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Quote addresses collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_addresses = null;

    /**
     * Quote items collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_items = null;

    /**
     * Quote payments
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_payments = null;

    /**
     * Different groups of error infos
     *
     * @var array
     */
    protected $_errorInfoGroups = array();

    /**
     * Whether quote should not be saved
     *
     * @var bool
     */
    protected $_preventSaving = false;

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('sales/quote');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @return Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = Mage::helper('sales')->getOldFieldMap('quote');
        return $this;
    }

    /**
     * Get quote store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->hasStoreId()) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_getData('store_id');
    }

    /**
     * Get quote store model object
     *
     * @return  Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    /**
     * Declare quote store model
     *
     * @param   Mage_Core_Model_Store $store
     * @return  Mage_Sales_Model_Quote
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        return $this;
    }

    /**
     * Get all available store ids for quote
     *
     * @return array
     */
    public function getSharedStoreIds()
    {
        $ids = $this->_getData('shared_store_ids');
        if (is_null($ids) || !is_array($ids)) {
            if ($website = $this->getWebsite()) {
                return $website->getStoreIds();
            }
            return $this->getStore()->getWebsite()->getStoreIds();
        }
        return $ids;
    }

    /**
     * Prepare data before save
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _beforeSave()
    {
        /**
         * Currency logic
         *
         * global - currency which is set for default in backend
         * base - currency which is set for current website. all attributes that
         *      have 'base_' prefix saved in this currency
         * store - all the time it was currency of website and all attributes
         *      with 'base_' were saved in this currency. From now on it is
         *      deprecated and will be duplication of base currency code.
         * quote/order - currency which was selected by customer or configured by
         *      admin for current store. currency in which customer sees
         *      price thought all checkout.
         *
         * Rates:
         *      store_to_base & store_to_quote/store_to_order - are deprecated
         *      base_to_global & base_to_quote/base_to_order - must be used instead
         */

        $globalCurrencyCode  = Mage::app()->getBaseCurrencyCode();
        $baseCurrency = $this->getStore()->getBaseCurrency();

        if ($this->hasForcedCurrency()){
            $quoteCurrency = $this->getForcedCurrency();
        } else {
            $quoteCurrency = $this->getStore()->getCurrentCurrency();
        }

        $this->setGlobalCurrencyCode($globalCurrencyCode);
        $this->setBaseCurrencyCode($baseCurrency->getCode());
        $this->setStoreCurrencyCode($baseCurrency->getCode());
        $this->setQuoteCurrencyCode($quoteCurrency->getCode());

        //deprecated, read above
        $this->setStoreToBaseRate($baseCurrency->getRate($globalCurrencyCode));
        $this->setStoreToQuoteRate($baseCurrency->getRate($quoteCurrency));

        $this->setBaseToGlobalRate($baseCurrency->getRate($globalCurrencyCode));
        $this->setBaseToQuoteRate($baseCurrency->getRate($quoteCurrency));

        if (!$this->hasChangedFlag() || $this->getChangedFlag() == true) {
            $this->setIsChanged(1);
        } else {
            $this->setIsChanged(0);
        }

        if ($this->_customer) {
            $this->setCustomerId($this->_customer->getId());
        }

        parent::_beforeSave();
    }

    /**
     * Save related items
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if (null !== $this->_addresses) {
            $this->getAddressesCollection()->save();
        }

        if (null !== $this->_items) {
            $this->getItemsCollection()->save();
        }

        if (null !== $this->_payments) {
            $this->getPaymentsCollection()->save();
        }
        return $this;
    }

    /**
     * Loading quote data by customer
     *
     * @return Mage_Sales_Model_Quote
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customerId = $customer->getId();
        }
        else {
            $customerId = (int) $customer;
        }
        $this->_getResource()->loadByCustomerId($this, $customerId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Loading only active quote
     *
     * @param int $quoteId
     * @return Mage_Sales_Model_Quote
     */
    public function loadActive($quoteId)
    {
        $this->_getResource()->loadActive($this, $quoteId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Loading quote by identifier
     *
     * @param int $quoteId
     * @return Mage_Sales_Model_Quote
     */
    public function loadByIdWithoutStore($quoteId)
    {
        $this->_getResource()->loadByIdWithoutStore($this, $quoteId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Assign customer model object data to quote
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Sales_Model_Quote
     */
    public function assignCustomer(Mage_Customer_Model_Customer $customer)
    {
        return $this->assignCustomerWithAddressChange($customer);
    }

    /**
     * Assign customer model to quote with billing and shipping address change
     *
     * @param  Mage_Customer_Model_Customer    $customer
     * @param  Mage_Sales_Model_Quote_Address  $billingAddress
     * @param  Mage_Sales_Model_Quote_Address  $shippingAddress
     * @return Mage_Sales_Model_Quote
     */
    public function assignCustomerWithAddressChange(
        Mage_Customer_Model_Customer    $customer,
        Mage_Sales_Model_Quote_Address  $billingAddress  = null,
        Mage_Sales_Model_Quote_Address  $shippingAddress = null
    )
    {
        if ($customer->getId()) {
            $this->setCustomer($customer);

            if (!is_null($billingAddress)) {
                $this->setBillingAddress($billingAddress);
            } else {
                $defaultBillingAddress = $customer->getDefaultBillingAddress();
                if ($defaultBillingAddress && $defaultBillingAddress->getId()) {
                    $billingAddress = Mage::getModel('sales/quote_address')
                        ->importCustomerAddress($defaultBillingAddress);
                    $this->setBillingAddress($billingAddress);
                }
            }

            if (is_null($shippingAddress)) {
                $defaultShippingAddress = $customer->getDefaultShippingAddress();
                if ($defaultShippingAddress && $defaultShippingAddress->getId()) {
                    $shippingAddress = Mage::getModel('sales/quote_address')
                        ->importCustomerAddress($defaultShippingAddress);
                } else {
                    $shippingAddress = Mage::getModel('sales/quote_address');
                }
            }
            $this->setShippingAddress($shippingAddress);
        }

        return $this;
    }

    /**
     * Define customer object
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Sales_Model_Quote
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        $this->setCustomerId($customer->getId());
        Mage::helper('core')->copyFieldset('customer_account', 'to_quote', $customer, $this);
        return $this;
    }

    /**
     * Retrieve customer model object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer');
            if ($customerId = $this->getCustomerId()) {
                $this->_customer->load($customerId);
                if (!$this->_customer->getId()) {
                    $this->_customer->setCustomerId(null);
                }
            }
        }
        return $this->_customer;
    }

    /**
     * Retrieve customer group id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->hasData('customer_group_id')) {
            return $this->getData('customer_group_id');
        } else if ($this->getCustomerId()) {
            return $this->getCustomer()->getGroupId();
        } else {
            return Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
        }
    }

    public function getCustomerTaxClassId()
    {
        /*
        * tax class can vary at any time. so instead of using the value from session,
        * we need to retrieve from db every time to get the correct tax class
        */
        //if (!$this->getData('customer_group_id') && !$this->getData('customer_tax_class_id')) {
        $classId = Mage::getModel('customer/group')->getTaxClassId($this->getCustomerGroupId());
        $this->setCustomerTaxClassId($classId);
        //}

        return $this->getData('customer_tax_class_id');
    }

    /**
     * Retrieve quote address collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getModel('sales/quote_address')->getCollection()
                ->setQuoteFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_addresses as $address) {
                    $address->setQuote($this);
                }
            }
        }
        return $this->_addresses;
    }

    /**
     * Retrieve quote address by type
     *
     * @param   string $type
     * @return  Mage_Sales_Model_Quote_Address
     */
    protected function _getAddressByType($type)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == $type && !$address->isDeleted()) {
                return $address;
            }
        }

        $address = Mage::getModel('sales/quote_address')->setAddressType($type);
        $this->addAddress($address);
        return $address;
    }

    /**
     * Retrieve quote billing address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->_getAddressByType(Mage_Sales_Model_Quote_Address::TYPE_BILLING);
    }

    /**
     * Retrieve quote shipping address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->_getAddressByType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);
    }

    public function getAllShippingAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()==Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
                && !$address->isDeleted()) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    public function getAllAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted()) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    /**
     *
     * @param int $addressId
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function getAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && $address->getCustomerAddressId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function getShippingAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && $address->getAddressType()==Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
                && $address->getCustomerAddressId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function removeAddress($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                $address->isDeleted(true);
                break;
            }
        }
        return $this;
    }

    /**
     * Leave no more than one billing and one shipping address, fill them with default data
     *
     * @return Mage_Sales_Model_Quote
     */
    public function removeAllAddresses()
    {
        $addressByType = array();
        $addressesCollection = $this->getAddressesCollection();

        // mark all addresses as deleted
        foreach ($addressesCollection as $address) {
            $type = $address->getAddressType();
            if (!isset($addressByType[$type]) || $addressByType[$type]->getId() > $address->getId()) {
                $addressByType[$type] = $address;
            }
            $address->isDeleted(true);
        }

        // create new billing and shipping addresses filled with default values, set this data to existing records
        foreach ($addressByType as $type => $address) {
            $id = $address->getId();
            $emptyAddress = $this->_getAddressByType($type);
            $address->setData($emptyAddress->getData())->setId($id)->isDeleted(false);
            $emptyAddress->setDeleteImmediately(true);
        }

        // remove newly created billing and shipping addresses from collection to avoid senseless delete queries
        foreach ($addressesCollection as $key => $item) {
            if ($item->getDeleteImmediately()) {
                $addressesCollection->removeItemByKey($key);
            }
        }

        return $this;
    }

    public function addAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setQuote($this);
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote
     */
    public function setBillingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $old = $this->getBillingAddress();

        if (!empty($old)) {
            $old->addData($address->getData());
        } else {
            $this->addAddress($address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING));
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote
     */
    public function setShippingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->getIsMultiShipping()) {
            $this->addAddress($address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING));
        }
        else {
            $old = $this->getShippingAddress();

            if (!empty($old)) {
                $old->addData($address->getData());
            } else {
                $this->addAddress($address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING));
            }
        }
        return $this;
    }

    public function addShippingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->setShippingAddress($address);
        return $this;
    }

    /**
     * Retrieve quote items collection
     *
     * @param   bool $loaded
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getItemsCollection($useCache = true)
    {
        if ($this->hasItemsCollection()) {
            return $this->getData('items_collection');
        }
        if (is_null($this->_items)) {
            $this->_items = Mage::getModel('sales/quote_item')->getCollection();
            $this->_items->setQuote($this);
        }
        return $this->_items;
    }

    /**
     * Retrieve quote items array
     *
     * @return array
     */
    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * Get array of all items what can be display directly
     *
     * @return array
     */
    public function getAllVisibleItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * Checking items availability
     *
     * @return bool
     */
    public function hasItems()
    {
        return sizeof($this->getAllItems())>0;
    }

    /**
     * Checking availability of items with decimal qty
     *
     * @return bool
     */
    public function hasItemsWithDecimalQty()
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getProduct()->getStockItem()
                && $item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checking product exist in Quote
     *
     * @param int $productId
     * @return bool
     */
    public function hasProductId($productId)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getProductId() == $productId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve item model object by item identifier
     *
     * @param   int $itemId
     * @return  Mage_Sales_Model_Quote_Item
     */
    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }

    /**
     * Delete quote item. If it does not have identifier then it will be only removed from collection
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_Sales_Model_Quote
     */
    public function deleteItem(Mage_Sales_Model_Quote_Item $item)
    {
        if ($item->getId()) {
            $this->removeItem($item->getId());
        } else {
            $quoteItems = $this->getItemsCollection();
            $items = array($item);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $items[] = $child;
                }
            }
            foreach ($quoteItems as $key => $quoteItem) {
                foreach ($items as $item) {
                    if ($quoteItem->compare($item)) {
                        $quoteItems->removeItemByKey($key);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Remove quote item by item identifier
     *
     * @param   int $itemId
     * @return  Mage_Sales_Model_Quote
     */
    public function removeItem($itemId)
    {
        $item = $this->getItemById($itemId);

        if ($item) {
            $item->setQuote($this);
            /**
             * If we remove item from quote - we can't use multishipping mode
             */
            $this->setIsMultiShipping(false);
            $item->isDeleted(true);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $child->isDeleted(true);
                }
            }

            $parent = $item->getParentItem();
            if ($parent) {
                $parent->isDeleted(true);
            }

            Mage::dispatchEvent('sales_quote_remove_item', array('quote_item' => $item));
        }

        return $this;
    }

    /**
     * Mark all quote items as deleted (empty quote)
     *
     * @return Mage_Sales_Model_Quote
     */
    public function removeAllItems()
    {
        foreach ($this->getItemsCollection() as $itemId => $item) {
            if (is_null($item->getId())) {
                $this->getItemsCollection()->removeItemByKey($itemId);
            } else {
                $item->isDeleted(true);
            }
        }
        return $this;
    }

    /**
     * Adding new item to quote
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_Sales_Model_Quote
     */
    public function addItem(Mage_Sales_Model_Quote_Item $item)
    {
        /**
         * Temporary workaround for purchase process: it is too dangerous to purchase more than one nominal item
         * or a mixture of nominal and non-nominal items, although technically possible.
         *
         * The problem is that currently it is implemented as sequential submission of nominal items and order, by one click.
         * It makes logically impossible to make the process of the purchase failsafe.
         * Proper solution is to submit items one by one with customer confirmation each time.
         */
        if ($item->isNominal() && $this->hasItems() || $this->hasNominalItems()) {
            Mage::throwException(
                Mage::helper('sales')->__('Nominal item can be purchased standalone only. To proceed please remove other items from the quote.')
            );
        }

        $item->setQuote($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
            Mage::dispatchEvent('sales_quote_add_item', array('quote_item' => $item));
        }
        return $this;
    }

    /**
     * Advanced func to add product to quote - processing mode can be specified there.
     * Returns error message if product type instance can't prepare product.
     *
     * @param mixed $product
     * @param null|float|Varien_Object $request
     * @param null|string $processMode
     * @return Mage_Sales_Model_Quote_Item|string
     */
    public function addProductAdvanced(Mage_Catalog_Model_Product $product, $request = null, $processMode = null)
    {
        if ($request === null) {
            $request = 1;
        }
        if (is_numeric($request)) {
            $request = new Varien_Object(array('qty'=>$request));
        }
        if (!($request instanceof Varien_Object)) {
            Mage::throwException(Mage::helper('sales')->__('Invalid request for adding product to quote.'));
        }

        $cartCandidates = $product->getTypeInstance(true)
            ->prepareForCartAdvanced($request, $product, $processMode);

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = array($cartCandidates);
        }

        $parentItem = null;
        $errors = array();
        $items = array();
        foreach ($cartCandidates as $candidate) {
            // Child items can be sticked together only within their parent
            $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
            $candidate->setStickWithinParent($stickWithinParent);
            $item = $this->_addCatalogProduct($candidate, $candidate->getCartQty());
            if($request->getResetCount() && !$stickWithinParent && $item->getId() === $request->getId()) {
                $item->setData('qty', 0);
            }
            $items[] = $item;

            /**
             * As parent item we should always use the item of first added product
             */
            if (!$parentItem) {
                $parentItem = $item;
            }
            if ($parentItem && $candidate->getParentProductId()) {
                $item->setParentItem($parentItem);
            }

            /**
             * We specify qty after we know about parent (for stock)
             */
            $item->addQty($candidate->getCartQty());

            // collect errors instead of throwing first one
            if ($item->getHasError()) {
                $message = $item->getMessage();
                if (!in_array($message, $errors)) { // filter duplicate messages
                    $errors[] = $message;
                }
            }
        }
        if (!empty($errors)) {
            Mage::throwException(implode("\n", $errors));
        }

        Mage::dispatchEvent('sales_quote_product_add_after', array('items' => $items));

        return $item;
    }


    /**
     * Add product to quote
     *
     * return error message if product type instance can't prepare product
     *
     * @param mixed $product
     * @param null|float|Varien_Object $request
     * @return Mage_Sales_Model_Quote_Item|string
     */
    public function addProduct(Mage_Catalog_Model_Product $product, $request = null)
    {
        return $this->addProductAdvanced(
            $product,
            $request,
            Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL
        );
    }

    /**
     * Adding catalog product object data to quote
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Sales_Model_Quote_Item
     */
    protected function _addCatalogProduct(Mage_Catalog_Model_Product $product, $qty = 1)
    {
        $newItem = false;
        $item = $this->getItemByProduct($product);
        if (!$item) {
            $item = Mage::getModel('sales/quote_item');
            $item->setQuote($this);
            if (Mage::app()->getStore()->isAdmin()) {
                $item->setStoreId($this->getStore()->getId());
            }
            else {
                $item->setStoreId(Mage::app()->getStore()->getId());
            }
            $newItem = true;
        }

        /**
         * We can't modify existing child items
         */
        if ($item->getId() && $product->getParentProductId()) {
            return $item;
        }

        $item->setOptions($product->getCustomOptions())
            ->setProduct($product);

        // Add only item that is not in quote already (there can be other new or already saved item
        if ($newItem) {
            $this->addItem($item);
        }

        return $item;
    }

    /**
     * Updates quote item with new configuration
     *
     * $params sets how current item configuration must be taken into account and additional options.
     * It's passed to Mage_Catalog_Helper_Product->addParamsToBuyRequest() to compose resulting buyRequest.
     *
     * Basically it can hold
     * - 'current_config', Varien_Object or array - current buyRequest that configures product in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file options (file inputs), so they won't
     *   intersect with other submitted options
     *
     * For more options see Mage_Catalog_Helper_Product->addParamsToBuyRequest()
     *
     * @param int $itemId
     * @param Varien_Object $buyRequest
     * @param null|array|Varien_Object $params
     * @return Mage_Sales_Model_Quote_Item
     *
     * @see Mage_Catalog_Helper_Product::addParamsToBuyRequest()
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $item = $this->getItemById($itemId);
        if (!$item) {
            Mage::throwException(Mage::helper('sales')->__('Wrong quote item id to update configuration.'));
        }
        $productId = $item->getProduct()->getId();

        //We need to create new clear product instance with same $productId
        //to set new option values from $buyRequest
        $product = Mage::getModel('catalog/product')
            ->setStoreId($this->getStore()->getId())
            ->load($productId);

        if (!$params) {
            $params = new Varien_Object();
        } else if (is_array($params)) {
            $params = new Varien_Object($params);
        }
        $params->setCurrentConfig($item->getBuyRequest());
        $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest($buyRequest, $params);

        $buyRequest->setResetCount(true);
        $resultItem = $this->addProduct($product, $buyRequest);

        if (is_string($resultItem)) {
            Mage::throwException($resultItem);
        }

        if ($resultItem->getParentItem()) {
            $resultItem = $resultItem->getParentItem();
        }

        if ($resultItem->getId() != $itemId) {
            /*
             * Product configuration didn't stick to original quote item
             * It either has same configuration as some other quote item's product or completely new configuration
             */
            $this->removeItem($itemId);

            $items = $this->getAllItems();
            foreach ($items as $item) {
                if (($item->getProductId() == $productId) && ($item->getId() != $resultItem->getId())) {
                    if ($resultItem->compare($item)) {
                        // Product configuration is same as in other quote item
                        $resultItem->setQty($resultItem->getQty() + $item->getQty());
                        $this->removeItem($item->getId());
                        break;
                    }
                }
            }
        } else {
            $resultItem->setQty($buyRequest->getQty());
        }

        return $resultItem;
    }

    /**
     * Retrieve quote item by product id
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Sales_Model_Quote_Item || false
     */
    public function getItemByProduct($product)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->representProduct($product)) {
                return $item;
            }
        }
        return false;
    }

    public function getItemsSummaryQty()
    {
        $qty = $this->getData('all_items_qty');
        if (is_null($qty)) {
            $qty = 0;
            foreach ($this->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                if (($children = $item->getChildren()) && $item->isShipSeparately()) {
                    foreach ($children as $child) {
                        $qty+= $child->getQty()*$item->getQty();
                    }
                } else {
                    $qty+= $item->getQty();
                }
            }
            $this->setData('all_items_qty', $qty);
        }
        return $qty;
    }

    public function getItemVirtualQty()
    {
        $qty = $this->getData('virtual_items_qty');
        if (is_null($qty)) {
            $qty = 0;
            foreach ($this->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                if (($children = $item->getChildren()) && $item->isShipSeparately()) {
                    foreach ($children as $child) {
                        if ($child->getProduct()->getIsVirtual()) {
                            $qty+= $child->getQty();
                        }
                    }
                } else {
                    if ($item->getProduct()->getIsVirtual()) {
                        $qty+= $item->getQty();
                    }
                }
            }
            $this->setData('virtual_items_qty', $qty);
        }
        return $qty;
    }

    /*********************** PAYMENTS ***************************/
    public function getPaymentsCollection()
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getModel('sales/quote_payment')->getCollection()
                ->setQuoteFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_payments as $payment) {
                    $payment->setQuote($this);
                }
            }
        }
        return $this->_payments;
    }

    /**
     * @return Mage_Sales_Model_Quote_Payment
     */
    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        $payment = Mage::getModel('sales/quote_payment');
        $this->addPayment($payment);
        return $payment;
    }

    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId()==$paymentId) {
                return $payment;
            }
        }
        return false;
    }

    public function addPayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        $payment->setQuote($this);
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        return $this;
    }

    public function setPayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }
        $this->addPayment($payment);

        return $payment;
    }

    public function removePayment()
    {
        $this->getPayment()->isDeleted(true);
        return $this;
    }

    /**
     * Collect totals
     *
     * @return Mage_Sales_Model_Quote
     */
    public function collectTotals()
    {
        /**
         * Protect double totals collection
         */
        if ($this->getTotalsCollectedFlag()) {
            return $this;
        }
        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_before', array($this->_eventObject => $this));

        $this->setSubtotal(0);
        $this->setBaseSubtotal(0);

        $this->setSubtotalWithDiscount(0);
        $this->setBaseSubtotalWithDiscount(0);

        $this->setGrandTotal(0);
        $this->setBaseGrandTotal(0);

        foreach ($this->getAllAddresses() as $address) {
            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);

            $address->collectTotals();

            $this->setSubtotal((float) $this->getSubtotal() + $address->getSubtotal());
            $this->setBaseSubtotal((float) $this->getBaseSubtotal() + $address->getBaseSubtotal());

            $this->setSubtotalWithDiscount(
                (float) $this->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
            );
            $this->setBaseSubtotalWithDiscount(
                (float) $this->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
            );

            $this->setGrandTotal((float) $this->getGrandTotal() + $address->getGrandTotal());
            $this->setBaseGrandTotal((float) $this->getBaseGrandTotal() + $address->getBaseGrandTotal());
        }

        Mage::helper('sales')->checkQuoteAmount($this, $this->getGrandTotal());
        Mage::helper('sales')->checkQuoteAmount($this, $this->getBaseGrandTotal());

        $this->setItemsCount(0);
        $this->setItemsQty(0);
        $this->setVirtualItemsQty(0);

        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $children = $item->getChildren();
            if ($children && $item->isShipSeparately()) {
                foreach ($children as $child) {
                    if ($child->getProduct()->getIsVirtual()) {
                        $this->setVirtualItemsQty($this->getVirtualItemsQty() + $child->getQty()*$item->getQty());
                    }
                }
            }

            if ($item->getProduct()->getIsVirtual()) {
                $this->setVirtualItemsQty($this->getVirtualItemsQty() + $item->getQty());
            }
            $this->setItemsCount($this->getItemsCount()+1);
            $this->setItemsQty((float) $this->getItemsQty()+$item->getQty());
        }

        $this->setData('trigger_recollect', 0);
        $this->_validateCouponCode();

        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_after', array($this->_eventObject => $this));

        $this->setTotalsCollectedFlag(true);
        return $this;
    }

    /**
     * Get all quote totals (sorted by priority)
     * Method process quote states isVirtual and isMultiShipping
     *
     * @return array
     */
    public function getTotals()
    {
        /**
         * If quote is virtual we are using totals of billing address because
         * all items assigned to it
         */
        if ($this->isVirtual()) {
            return $this->getBillingAddress()->getTotals();
        }

        $shippingAddress = $this->getShippingAddress();
        $totals = $shippingAddress->getTotals();
        // Going through all quote addresses and merge their totals
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->isDeleted() || $address === $shippingAddress) {
                continue;
            }
            foreach ($address->getTotals() as $code => $total) {
                if (isset($totals[$code])) {
                    $totals[$code]->merge($total);
                } else {
                    $totals[$code] = $total;
                }
            }
        }

        $sortedTotals = array();
        foreach ($this->getBillingAddress()->getTotalModels() as $total) {
            /* @var $total Mage_Sales_Model_Quote_Address_Total_Abstract */
            if (isset($totals[$total->getCode()])) {
                $sortedTotals[$total->getCode()] = $totals[$total->getCode()];
            }
        }
        return $sortedTotals;
    }

    public function addMessage($message, $index = 'error')
    {
        $messages = $this->getData('messages');
        if (is_null($messages)) {
            $messages = array();
        }

        if (isset($messages[$index])) {
            return $this;
        }

        if (is_string($message)) {
            $message = Mage::getSingleton('core/message')->error($message);
        }

        $messages[$index] = $message;
        $this->setData('messages', $messages);
        return $this;
    }

    /**
     * Retrieve current quote messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->getData('messages');
        if (is_null($messages)) {
            $messages = array();
            $this->setData('messages', $messages);
        }
        return $messages;
    }

    /**
     * Retrieve current quote errors
     *
     * @return array
     */
    public function getErrors()
    {
        $errors = array();
        foreach ($this->getMessages() as $message) {
            /* @var $error Mage_Core_Model_Message_Abstract */
            if ($message->getType() == Mage_Core_Model_Message::ERROR) {
                array_push($errors, $message);
            }
        }
        return $errors;
    }

    /**
     * Sets flag, whether this quote has some error associated with it.
     *
     * @param bool $flag
     * @return Mage_Sales_Model_Quote
     */
    protected function _setHasError($flag)
    {
        return $this->setData('has_error', $flag);
    }

    /**
     * Sets flag, whether this quote has some error associated with it.
     * When TRUE - also adds 'unknown' error information to list of quote errors.
     * When FALSE - clears whole list of quote errors.
     * It's recommended to use addErrorInfo() instead - to be able to remove error statuses later.
     *
     * @param bool $flag
     * @return Mage_Sales_Model_Quote
     * @see addErrorInfo()
     */
    public function setHasError($flag)
    {
        if ($flag) {
            $this->addErrorInfo();
        } else {
            $this->_clearErrorInfo();
        }
        return $this;
    }

    /**
     * Clears list of errors, associated with this quote.
     * Also automatically removes error-flag from oneself.
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _clearErrorInfo()
    {
        $this->_errorInfoGroups = array();
        $this->_setHasError(false);
        return $this;
    }

    /**
     * Adds error information to the quote.
     * Automatically sets error flag.
     *
     * @param string $type An internal error type ('error', 'qty', etc.), passed then to adding messages routine
     * @param string|null $origin Usually a name of module, that embeds error
     * @param int|null $code Error code, unique for origin, that sets it
     * @param string|null $message Error message
     * @param Varien_Object|null $additionalData Any additional data, that caller would like to store
     * @return Mage_Sales_Model_Quote
     */
    public function addErrorInfo($type = 'error', $origin = null, $code = null, $message = null, $additionalData = null)
    {
        if (!isset($this->_errorInfoGroups[$type])) {
            $this->_errorInfoGroups[$type] = Mage::getModel('sales/status_list');
        }

        $this->_errorInfoGroups[$type]->addItem($origin, $code, $message, $additionalData);

        if ($message !== null) {
            $this->addMessage($message, $type);
        }
        $this->_setHasError(true);

        return $this;
    }

    /**
     * Removes error infos, that have parameters equal to passed in $params.
     * $params can have following keys (if not set - then any item is good for this key):
     *   'origin', 'code', 'message'
     *
     * @param string $type An internal error type ('error', 'qty', etc.), passed then to adding messages routine
     * @param array $params
     * @return Mage_Sales_Model_Quote
     */
    public function removeErrorInfosByParams($type = 'error', $params)
    {
        if ($type && !isset($this->_errorInfoGroups[$type])) {
            return $this;
        }

        $errorLists = array();
        if ($type) {
            $errorLists[] = $this->_errorInfoGroups[$type];
        } else {
            $errorLists = $this->_errorInfoGroups;
        }

        foreach ($errorLists as $type => $errorList) {
            $removedItems = $errorList->removeItemsByParams($params);
            foreach ($removedItems as $item) {
                if ($item['message'] !== null) {
                    $this->removeMessageByText($type, $item['message']);
                }
            }
        }

        $errorsExist = false;
        foreach ($this->_errorInfoGroups as $errorListCheck) {
            if ($errorListCheck->getItems()) {
                $errorsExist = true;
                break;
            }
        }
        if (!$errorsExist) {
            $this->_setHasError(false);
        }

        return $this;
    }

    /**
     * Removes message by text
     *
     * @param string $type
     * @param string $text
     * @return Mage_Sales_Model_Quote
     */
    public function removeMessageByText($type = 'error', $text)
    {
        $messages = $this->getData('messages');
        if (is_null($messages)) {
            $messages = array();
        }

        if (!isset($messages[$type])) {
            return $this;
        }

        $message = $messages[$type];
        if ($message instanceof Mage_Core_Model_Message_Abstract) {
            $message = $message->getText();
        } else if (!is_string($message)) {
            return $this;
        }
        if ($message == $text) {
            unset($messages[$type]);
            $this->setData('messages', $messages);
        }
        return $this;
    }

    /**
     * Generate new increment order id and associate it with current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function reserveOrderId()
    {
        if (!$this->getReservedOrderId()) {
            $this->setReservedOrderId($this->_getResource()->getReservedOrderId($this));
        } else {
            //checking if reserved order id was already used for some order
            //if yes reserving new one if not using old one
            if ($this->_getResource()->isOrderIncrementIdUsed($this->getReservedOrderId())) {
                $this->setReservedOrderId($this->_getResource()->getReservedOrderId($this));
            }
        }
        return $this;
    }

    public function validateMinimumAmount($multishipping = false)
    {
        $storeId = $this->getStoreId();
        $minOrderActive = Mage::getStoreConfigFlag('sales/minimum_order/active', $storeId);
        $minOrderMulti  = Mage::getStoreConfigFlag('sales/minimum_order/multi_address', $storeId);
        $minAmount      = Mage::getStoreConfig('sales/minimum_order/amount', $storeId);

        if (!$minOrderActive) {
            return true;
        }

        $addresses = $this->getAllAddresses();

        if ($multishipping) {
            if ($minOrderMulti) {
                foreach ($addresses as $address) {
                    foreach ($address->getQuote()->getItemsCollection() as $item) {
                        $amount = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
                        if ($amount < $minAmount) {
                            return false;
                        }
                    }
                }
            } else {
                $baseTotal = 0;
                foreach ($addresses as $address) {
                    /* @var $address Mage_Sales_Model_Quote_Address */
                    $baseTotal += $address->getBaseSubtotalWithDiscount();
                }
                if ($baseTotal < $minAmount) {
                    return false;
                }
            }
        } else {
            foreach ($addresses as $address) {
                /* @var $address Mage_Sales_Model_Quote_Address */
                if (!$address->validateMinimumAmount()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check quote for virtual product only
     *
     * @return bool
     */
    public function isVirtual()
    {
        $isVirtual = true;
        $countItems = 0;
        foreach ($this->getItemsCollection() as $_item) {
            /* @var $_item Mage_Sales_Model_Quote_Item */
            if ($_item->isDeleted() || $_item->getParentItemId()) {
                continue;
            }
            $countItems ++;
            if (!$_item->getProduct()->getIsVirtual()) {
                $isVirtual = false;
                break;
            }
        }
        return $countItems == 0 ? false : $isVirtual;
    }

    /**
     * Check quote for virtual product only
     *
     * @return bool
     */
    public function getIsVirtual()
    {
        return intval($this->isVirtual());
    }

    /**
     * Has a virtual products on quote
     *
     * @return bool
     */
    public function hasVirtualItems()
    {
        $hasVirtual = false;
        foreach ($this->getItemsCollection() as $_item) {
            if ($_item->getParentItemId()) {
                continue;
            }
            if ($_item->getProduct()->isVirtual()) {
                $hasVirtual = true;
            }
        }
        return $hasVirtual;
    }

    /**
     * Merge quotes
     *
     * @param   Mage_Sales_Model_Quote $quote
     * @return  Mage_Sales_Model_Quote
     */
    public function merge(Mage_Sales_Model_Quote $quote)
    {
        Mage::dispatchEvent(
            $this->_eventPrefix . '_merge_before',
            array(
                 $this->_eventObject=>$this,
                 'source'=>$quote
            )
        );

        foreach ($quote->getAllVisibleItems() as $item) {
            $found = false;
            foreach ($this->getAllItems() as $quoteItem) {
                if ($quoteItem->compare($item)) {
                    $quoteItem->setQty($quoteItem->getQty() + $item->getQty());
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $newItem = clone $item;
                $this->addItem($newItem);
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $newChild = clone $child;
                        $newChild->setParentItem($newItem);
                        $this->addItem($newChild);
                    }
                }
            }
        }

        /**
         * Init shipping and billing address if quote is new
         */
        if (!$this->getId()) {
            $this->getShippingAddress();
            $this->getBillingAddress();
        }

        if ($quote->getCouponCode()) {
            $this->setCouponCode($quote->getCouponCode());
        }

        Mage::dispatchEvent(
            $this->_eventPrefix . '_merge_after',
            array(
                 $this->_eventObject=>$this,
                 'source'=>$quote
            )
        );

        return $this;
    }

    /**
     * Whether there are recurring items
     *
     * @return bool
     */
    public function hasRecurringItems()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getProduct() && $item->getProduct()->isRecurring()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Getter whether quote has nominal items
     * Can bypass treating virtual items as nominal
     *
     * @param bool $countVirtual
     * @return bool
     */
    public function hasNominalItems($countVirtual = true)
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->isNominal()) {
                if ((!$countVirtual) && $item->getProduct()->isVirtual()) {
                    continue;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Whether quote has nominal items only
     *
     * @return bool
     */
    public function isNominal()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if (!$item->isNominal()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Create recurring payment profiles basing on the current items
     *
     * @return array
     */
    public function prepareRecurringPaymentProfiles()
    {
        if (!$this->getTotalsCollectedFlag()) {
            // Whoops! Make sure nominal totals must be calculated here.
            throw new Exception('Quote totals must be collected before this operation.');
        }

        $result = array();
        foreach ($this->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            if (is_object($product) && ($product->isRecurring())
                && $profile = Mage::getModel('sales/recurring_profile')->importProduct($product)
            ) {
                $profile->importQuote($this);
                $profile->importQuoteItem($item);
                $result[] = $profile;
            }
        }
        return $result;
    }

    protected function _validateCouponCode()
    {
        $code = $this->_getData('coupon_code');
        if (strlen($code)) {
            $addressHasCoupon = false;
            $addresses = $this->getAllAddresses();
            if (count($addresses)>0) {
                foreach ($addresses as $address) {
                    if ($address->hasCouponCode()) {
                        $addressHasCoupon = true;
                    }
                }
                if (!$addressHasCoupon) {
                    $this->setCouponCode('');
                }
            }
        }
        return $this;
    }

    /**
     * Trigger collect totals after loading, if required
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _afterLoad()
    {
        // collect totals and save me, if required
        if (1 == $this->getData('trigger_recollect')) {
            $this->collectTotals()->save();
        }
        return parent::_afterLoad();
    }

    /**
     * @deprecated after 1.4 beta1 - one page checkout responsibility
     */
    const CHECKOUT_METHOD_REGISTER  = 'register';
    const CHECKOUT_METHOD_GUEST     = 'guest';
    const CHECKOUT_METHOD_LOGIN_IN  = 'login_in';

    /**
     * Return quote checkout method code
     *
     * @deprecated after 1.4 beta1 it is checkout module responsibility
     * @param boolean $originalMethod if true return defined method from begining
     * @return string
     */
    public function getCheckoutMethod($originalMethod = false)
    {
        if ($this->getCustomerId() && !$originalMethod) {
            return self::CHECKOUT_METHOD_LOGIN_IN;
        }
        return $this->_getData('checkout_method');
    }

    /**
     * Check is allow Guest Checkout
     *
     * @deprecated after 1.4 beta1 it is checkout module responsibility
     * @return bool
     */
    public function isAllowedGuestCheckout()
    {
        return Mage::helper('checkout')->isAllowedGuestCheckout($this, $this->getStoreId());
    }

    /**
     * Prevent quote from saving
     *
     * @return Mage_Sales_Model_Quote
     */
    public function preventSaving()
    {
        $this->_preventSaving = true;
        return $this;
    }

    /**
     * Save quote with prevention checking
     *
     * @return Mage_Sales_Model_Quote
     */
    public function save()
    {
        if ($this->_preventSaving) {
            return $this;
        }
        return parent::save();
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales module base helper
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Maximum available number
     */
    const MAXIMUM_AVAILABLE_NUMBER = 99999999;

    /**
     * Default precision for price calculations
     */
    const PRECISION_VALUE = 0.0001;

    /**
     * Check quote amount
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param decimal $amount
     * @return Mage_Sales_Helper_Data
     */
    public function checkQuoteAmount(Mage_Sales_Model_Quote $quote, $amount)
    {
        if (!$quote->getHasError() && ($amount>=self::MAXIMUM_AVAILABLE_NUMBER)) {
            $quote->setHasError(true);
            $quote->addMessage(
                $this->__('Items maximum quantity or price do not allow checkout.')
            );
        }
        return $this;
    }

    /**
     * Check allow to send new order confirmation email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewOrderConfirmationEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new order email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewOrderEmail($store = null)
    {
        return $this->canSendNewOrderConfirmationEmail($store);
    }

    /**
     * Check allow to send order comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendOrderCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new shipment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewShipmentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send shipment comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendShipmentCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new invoice email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewInvoiceEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send invoice comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendInvoiceCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new creditmemo email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewCreditmemoEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send creditmemo comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendCreditmemoCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Get old field map
     *
     * @param string $entityId
     * @return array
     */
    public function getOldFieldMap($entityId)
    {
        $node = Mage::getConfig()->getNode('global/sales/old_fields_map/' . $entityId);
        if ($node === false) {
            return array();
        }
        return (array) $node;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales abstract resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Prepare data for save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        $data = parent::_prepareDataForSave($object);
        return $data;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Quote resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote extends Mage_Sales_Model_Resource_Abstract
{
    /**
     * Initialize table nad PK name
     *
     */
    protected function _construct()
    {
        $this->_init('sales/quote', 'entity_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select   = parent::_getLoadSelect($field, $value, $object);
        $storeIds = $object->getSharedStoreIds();
        if ($storeIds) {
            $select->where('store_id IN (?)', $storeIds);
        } else {
            /**
             * For empty result
             */
            $select->where('store_id < ?', 0);
        }

        return $select;
    }

    /**
     * Load quote data by customer identifier
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function loadByCustomerId($quote, $customerId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $this->_getLoadSelect('customer_id', $customerId, $quote)
            ->where('is_active = ?', 1)
            ->order('updated_at ' . Varien_Db_Select::SQL_DESC)
            ->limit(1);

        $data    = $adapter->fetchRow($select);

        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }

    /**
     * Load only active quote
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $quoteId
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function loadActive($quote, $quoteId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $this->_getLoadSelect('entity_id', $quoteId, $quote)
            ->where('is_active = ?', 1);

        $data    = $adapter->fetchRow($select);
        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }

    /**
     * Load quote data by identifier without store
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $quoteId
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function loadByIdWithoutStore($quote, $quoteId)
    {
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = parent::_getLoadSelect('entity_id', $quoteId, $quote);

            $data = $read->fetchRow($select);

            if ($data) {
                $quote->setData($data);
            }
        }

        $this->_afterLoad($quote);
        return $this;
    }

    /**
     * Get reserved order id
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return string
     */
    public function getReservedOrderId($quote)
    {
        $storeId = (int)$quote->getStoreId();
        return Mage::getSingleton('eav/config')->getEntityType(Mage_Sales_Model_Order::ENTITY)
            ->fetchNewIncrementId($storeId);
    }

    /**
     * Check is order increment id use in sales/order table
     *
     * @param int $orderIncrementId
     * @return boolean
     */
    public function isOrderIncrementIdUsed($orderIncrementId)
    {
        $adapter   = $this->_getReadAdapter();
        $bind      = array(':increment_id' => (int)$orderIncrementId);
        $select    = $adapter->select();
        $select->from($this->getTable('sales/order'), 'entity_id')
            ->where('increment_id = :increment_id');
        $entity_id = $adapter->fetchOne($select, $bind);
        if ($entity_id > 0) {
            return true;
        }

        return false;
    }

    /**
     * Mark quotes - that depend on catalog price rules - to be recollected on demand
     *
     *  @param  array|null $productIdList
     *
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function markQuotesRecollectByAffectedProduct($productIdList = null)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $select = $writeAdapter->select();
        $subSelect = clone $select;

        $subSelect
            ->distinct()
            ->from(
                   array('qi' => $this->getTable('sales/quote_item')),
                   array('entity_id' => 'quote_id'))
            ->join(
                   array('pp' => $this->getTable('catalogrule/rule_product_price')),
                   'qi.product_id = pp.product_id',
                   array());
        if ($productIdList !== null) {
           $subSelect->where('qi.product_id IN (?)', $productIdList);
        }

        $select
             ->join(
                    array('tmp' => $subSelect),
                    'q.entity_id = tmp.entity_id',
                    array('trigger_recollect' => new Zend_Db_Expr(1)))
             ->where('q.is_active = ?', 1);
        $sql = $writeAdapter->updateFromSelect($select, array('q' => $this->getTable('sales/quote')));
        $writeAdapter->query($sql);

        return $this;
    }

    /**
     * Mark quotes - that depend on catalog price rules - to be recollected on demand
     *
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function markQuotesRecollectOnCatalogRules()
    {
        return $this->markQuotesRecollectByAffectedProduct();
    }

    /**
     * Subtract product from all quotes quantities
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function substractProductFromQuotes($product)
    {
        $productId = (int)$product->getId();
        if (!$productId) {
            return $this;
        }
        $adapter   = $this->_getWriteAdapter();
        $subSelect = $adapter->select();

        $subSelect->from(false, array(
            'items_qty'   => new Zend_Db_Expr(
                $adapter->quoteIdentifier('q.items_qty') . ' - ' . $adapter->quoteIdentifier('qi.qty')),
            'items_count' => new Zend_Db_Expr($adapter->quoteIdentifier('q.items_count') . ' - 1')
        ))
        ->where('q.items_count > 0')
        ->join(
            array('qi' => $this->getTable('sales/quote_item')),
            implode(' AND ', array(
                'q.entity_id = qi.quote_id',
                'qi.parent_item_id IS NULL',
                $adapter->quoteInto('qi.product_id = ?', $productId)
            )),
            array()
        );

        $updateQuery = $adapter->updateFromSelect($subSelect, array('q' => $this->getTable('sales/quote')));

        $adapter->query($updateQuery);

        return $this;
    }

    /**
     * Mark recollect contain product(s) quotes
     *
     * @param array|int|Zend_Db_Expr $productIds
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function markQuotesRecollect($productIds)
    {
        $tableQuote = $this->getTable('sales/quote');
        $tableItem = $this->getTable('sales/quote_item');
        $subSelect = $this->_getReadAdapter()
            ->select()
            ->from($tableItem, array('entity_id' => 'quote_id'))
            ->where('product_id IN (?)', $productIds)
            ->group('quote_id');

        $select = $this->_getReadAdapter()->select()->join(
            array('t2' => $subSelect),
            't1.entity_id = t2.entity_id',
            array('trigger_recollect' => new Zend_Db_Expr('1'))
        );
        $updateQuery = $select->crossUpdateFromSelect(array('t1' => $tableQuote));
        $this->_getWriteAdapter()->query($updateQuery);

        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Session Observer
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Observer_Session
{
    /**
     * Create/Update and Load session when customer log in
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentOnLogin(Varien_Event_Observer $observer)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid (remove persistent cookie for invalid customer)
        if (!$customer || !$customer->getId() || !Mage::helper('persistent/session')->isRememberMeChecked()) {
            Mage::getModel('persistent/session')->removePersistentCookie();
            return;
        }

        $persistentLifeTime = Mage::helper('persistent')->getLifeTime();
        // Delete persistent session, if persistent could not be applied
        if (Mage::helper('persistent')->isEnabled() && ($persistentLifeTime <= 0)) {
            // Remove current customer persistent session
            Mage::getModel('persistent/session')->deleteByCustomerId($customer->getId());
            return;
        }

        /** @var $sessionModel Mage_Persistent_Model_Session */
        $sessionModel = Mage::helper('persistent/session')->getSession();

        // Check if session is wrong or not exists, so create new session
        if (!$sessionModel->getId() || ($sessionModel->getCustomerId() != $customer->getId())) {
            $sessionModel = Mage::getModel('persistent/session')
                ->setLoadExpired()
                ->loadByCustomerId($customer->getId());
            if (!$sessionModel->getId()) {
                $sessionModel = Mage::getModel('persistent/session')
                    ->setCustomerId($customer->getId())
                    ->save();
            }

            Mage::helper('persistent/session')->setSession($sessionModel);
        }

        // Set new cookie
        if ($sessionModel->getId()) {
            Mage::getSingleton('core/cookie')->set(
                Mage_Persistent_Model_Session::COOKIE_NAME,
                $sessionModel->getKey(),
                $persistentLifeTime
            );
        }
    }

    /**
     * Unload persistent session (if set in config)
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentOnLogout(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent')->getClearOnLogout()) {
            return;
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid
        if (!$customer || !$customer->getId()) {
            return;
        }

        Mage::getModel('persistent/session')->removePersistentCookie();

        // Unset persistent session
        Mage::helper('persistent/session')->setSession(null);
    }

    /**
     * Synchronize persistent session info
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentInfo(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent/session')->isPersistent()) {
            return;
        }

        /** @var $sessionModel Mage_Persistent_Model_Session */
        $sessionModel = Mage::helper('persistent/session')->getSession();

        /** @var $request Mage_Core_Controller_Request_Http */
        $request = $observer->getEvent()->getFront()->getRequest();

        // Quote Id could be changed only by logged in customer
        if (Mage::getSingleton('customer/session')->isLoggedIn()
            || ($request && $request->getActionName() == 'logout' && $request->getControllerName() == 'account')
        ) {
            $sessionModel->save();
        }
    }

    /**
     * Set Checked status of "Remember Me"
     *
     * @param Varien_Event_Observer $observer
     */
    public function setRememberMeCheckedStatus(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent')->isRememberMeEnabled()
        ) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if ($controllerAction) {
            $rememberMeCheckbox = $controllerAction->getRequest()->getPost('persistent_remember_me');
            Mage::helper('persistent/session')->setRememberMeChecked((bool)$rememberMeCheckbox);
            if (
                $controllerAction->getFullActionName() == 'checkout_onepage_saveBilling'
                    || $controllerAction->getFullActionName() == 'customer_account_createpost'
            ) {
                Mage::getSingleton('checkout/session')->setRememberMeChecked((bool)$rememberMeCheckbox);
            }
        }
    }

    /**
     * Renew persistent cookie
     *
     * @param Varien_Event_Observer $observer
     */
    public function renewCookie(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent/session')->isPersistent()
        ) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Front_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();

        if (Mage::getSingleton('customer/session')->isLoggedIn()
            || $controllerAction->getFullActionName() == 'customer_account_logout'
        ) {
            Mage::getSingleton('core/cookie')->renew(
                Mage_Persistent_Model_Session::COOKIE_NAME,
                Mage::helper('persistent')->getLifeTime()
            );
        }
    }
}

/**
 *
 * @category   Ebizmarts
 * @package    Ebizmarts_Autoresponder
 * @author     Ebizmarts Team <info@ebizmarts.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_Autoresponder_Model_EventObserver
{
    /**
     * @param Varien_Event_Observer $o
     */
    public function saveConfig(Varien_Event_Observer $o)
    {
        if (Mage::app()->getRequest()->getParam('store')) {
            $scope = 'store';
        } elseif (Mage::app()->getRequest()->getParam('website')) {
            $scope = 'website';
        } else {
            $scope = "default";
        }
        $store = is_null($o->getEvent()->getStore()) ? Mage::app()->getDefaultStoreView()->getCode() : $o->getEvent()->getStore();
        if (!Mage::helper('ebizmarts_mandrill')->useTransactionalService()) {
            $config = Mage::getModel('core/config');
            $config->saveConfig(Ebizmarts_Autoresponder_Model_Config::GENERAL_ACTIVE, false, $scope, $store);
            Mage::getConfig()->cleanCache();
        }
        if (!Mage::helper('ebizmarts_mandrill')->useTransactionalService()) {
            $config = Mage::getModel('core/config');
            $config->saveConfig(Ebizmarts_AbandonedCart_Model_Config::ACTIVE, false, $scope, $store);
            $config->saveConfig(Ebizmarts_AbandonedCart_Model_Config::ENABLE_POPUP, false, $scope, $store);
            Mage::getConfig()->cleanCache();
        }
        if (!Mage::getStoreConfig('customer/address/dob_show')) {
            $config = Mage::getModel('core/config');
            $config->saveConfig(Ebizmarts_Autoresponder_Model_Config::BIRTHDAY_ACTIVE, false, $scope, $store);
            Mage::getConfig()->cleanCache();
        }
        if (!Mage::getStoreConfig('customer/address/dob_show', $store)) {
            $config = Mage::getModel('core/config');
            $config->saveConfig(Ebizmarts_Autoresponder_Model_Config::BIRTHDAY_ACTIVE, false, $scope, $store);
            Mage::getConfig()->cleanCache();
        }
        if (Mage::getStoreConfig('advanced/modules_disable_output/Mage_Wishlist', $store)) {
            $config = Mage::getModel('core/config');
            $config->saveConfig(Ebizmarts_Autoresponder_Model_Config::WISHLIST_ACTIVE, false, $scope, $store);
            Mage::getConfig()->cleanCache();
        }
        if (Mage::getStoreConfig('advanced/modules_disable_output/Mage_Review', $store)) {
            $config = Mage::getModel('core/config');
            $config->saveConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_ACTIVE, false, $scope, $store);
            Mage::getConfig()->cleanCache();
        }
    }

    public function actionAfter(Varien_Event_Observer $o)
    {
        if ($o->getEvent()->getControllerAction()->getFullActionName() == 'review_product_post') {
            Mage::dispatchEvent("review_product_post_after", array('request' => $o->getControllerAction()->getRequest()));
        }
        return $o;
    }

    public function reviewProductPostAfter(Varien_Event_Observer $o)
    {
        $params = Mage::app()->getRequest()->getParams();
        $storeId = Mage::app()->getStore()->getId();
        $customerGroupsCoupon = explode(",", Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_CUSTOMER_GROUP, $storeId));
        $templateId = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_EMAIL, $storeId);
        $mailSubject = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_SUBJECT, $storeId);
        $tags = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_MANDRILL_TAG, $storeId) . "_$storeId";
        $senderId = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::GENERAL_SENDER, $storeId);
        $sender = array('name' => Mage::getStoreConfig("trans_email/ident_$senderId/name", $storeId), 'email' => Mage::getStoreConfig("trans_email/ident_$senderId/email", $storeId));

        if (isset($params['token'])) {
            $token = $params['token'];
            $reviewData = Mage::getModel('ebizmarts_autoresponder/review')->loadByToken($token);
            if ($this->_generateReviewCoupon($reviewData)) {
                //generate coupon
                $customer = Mage::getModel('customer/customer')->load($reviewData->getCustomerId());
                $email = $customer->getEmail();
                $name = $customer->getFirstname() . ' ' . $customer->getLastname();
                if (in_array($customer->getGroupId(), $customerGroupsCoupon)) {
                    if (Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_AUTOMATIC, $storeId) == Ebizmarts_Autoresponder_Model_Config::COUPON_AUTOMATIC) {
                        list($couponcode, $discount, $toDate) = $this->_createNewCoupon($storeId, $email);
                        $vars = array('couponcode' => $couponcode, 'discount' => $discount, 'todate' => $toDate, 'name' => $name, 'tags' => array($tags));
                    } else {
                        $couponcode = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_CODE);
                        $vars = array('couponcode' => $couponcode, 'name' => $name, 'tags' => array($tags));
                    }
                    $translate = Mage::getSingleton('core/translate');
                    $mail = Mage::getModel('core/email_template')->setTemplateSubject($mailSubject)->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
                    $translate->setTranslateInLine(true);
                    Mage::helper('ebizmarts_abandonedcart')->saveMail('review coupon', $email, $name, $couponcode, $storeId);
                }
            }
        }
        return $o;
    }

    protected function _generateReviewCoupon($reviewData)
    {
        $store = Mage::app()->getStore()->getId();
        if (!Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_HAS_COUPON, $store)) {
            return false;
        }
        $rc = false;
        // check if is a registered customer if not, return false
        if (!$reviewData->getCustomerId()) {
            return false;
        }
        // if the customer is registered the counter is in the customer account, so load the customer
        $customer = Mage::getModel('customer/customer')->load($reviewData->getCustomerId());
        $couponTotal = $customer->getEbizmartsReviewsCouponTotal();
        switch (Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_COUNTER, $store)) {
            case Ebizmarts_Autoresponder_Model_Config::COUPON_GENERAL:
                // update the counter
                $counter = $customer->getEbizmartsReviewsCntrTotal();
                $counter++;
                $customer->setEbizmartsReviewsCntrTotal($counter)->save();
                // check if coupon must be generated
                $generalQuantity = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_GENERAL_QUANTITY, $store);
                switch (Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_GENERAL_TYPE)) {
                    case Ebizmarts_Autoresponder_Model_Config::TYPE_EACH:
                        if ($counter && $counter % $generalQuantity) {
                            $rc = true;
                        }
                        break;
                    case Ebizmarts_Autoresponder_Model_Config::TYPE_ONCE:
                        if ($counter == $generalQuantity) {
                            $rc = true;
                        }
                        break;
                    case Ebizmarts_Autoresponder_Model_Config::TYPE_SPECIFIC:
                        if ($counter && $counter % $generalQuantity && $customer->getEbizmartsReviewsCouponTotal() <= Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_SPECIFIC_QUANTITY)) {
                            $rc = true;
                        }
                        break;
                }
                break;
            case Ebizmarts_Autoresponder_Model_Config::COUPON_PER_ORDER:
                // update the counter
                $counter = $reviewData->getCounter();
                $counter++;
                $reviewData->setCounter($counter)->save();
                if (Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_ORDER_MAX) != 0 && $couponTotal >= Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_ORDER_MAX)) {
                    $rc = false;
                } else {
                    if (Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_ORDER_COUNTER, $store) == 0) {
                        if ($counter == $reviewData->getItems()) {
                            $rc = true;
                        } else {
                            $rc = false;
                        }
                    } elseif (Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_ORDER_COUNTER, $store) == $counter) {
                        if ($reviewData->getItems() >= Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_ORDER_ALMOST, $store)) {
                            $rc = true;
                        } else {
                            $rc = false;
                        }
                    }
                }
                break;
        }
        if ($rc) { // increase the count of coupons in the customer
            $customer->setEbizmartsReviewsCouponTotal($couponTotal + 1)->save();
        }
        return $rc;
    }

    protected function _createNewCoupon($store, $email)
    {
        $couponamount = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_DISCOUNT, $store);
        $couponexpiredays = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_EXPIRE, $store);
        $coupontype = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_DISCOUNT_TYPE, $store);
        $couponlength = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_LENGTH, $store);
        $couponlabel = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::REVIEW_COUPON_LABEL, $store);
        $websiteid = Mage::getModel('core/store')->load($store)->getWebsiteId();

        $fromDate = date("Y-m-d");
        $toDate = date('Y-m-d', strtotime($fromDate . " + $couponexpiredays day"));
        if ($coupontype == 1) {
            $action = 'cart_fixed';
            $discount = Mage::app()->getStore($store)->getCurrentCurrencyCode() . "$couponamount";
        } elseif ($coupontype == 2) {
            $action = 'by_percent';
            $discount = "$couponamount%";
        }
        $customer_group = new Mage_Customer_Model_Group();
        $allGroups = $customer_group->getCollection()->toOptionHash();
        $groups = array();
        foreach ($allGroups as $groupid => $name) {
            $groups[] = $groupid;
        }
        $coupon_rule = Mage::getModel('salesrule/rule');
        $coupon_rule->setName("Review coupon $email")
            ->setDescription("Review coupon $email")
            ->setFromDate($fromDate)
            ->setToDate($toDate)
            ->setIsActive(1)
            ->setCouponType(2)
            ->setUsesPerCoupon(1)
            ->setUsesPerCustomer(1)
            ->setCustomerGroupIds($groups)
            ->setProductIds('')
            ->setLengthMin($couponlength)
            ->setLengthMax($couponlength)
            ->setSortOrder(0)
            ->setStoreLabels(array($couponlabel))
            ->setSimpleAction($action)
            ->setDiscountAmount($couponamount)
            ->setDiscountQty(0)
            ->setDiscountStep('0')
            ->setSimpleFreeShipping('0')
            ->setApplyToShipping('0')
            ->setIsRss(0)
            ->setWebsiteIds($websiteid);
        $uniqueId = Mage::getSingleton('salesrule/coupon_codegenerator', array('length' => $couponlength))->generateCode();
        $coupon_rule->setCouponCode($uniqueId);
        $coupon_rule->save();
        return array($uniqueId, $discount, $toDate);
    }

    public function orderSaved(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getOrder()->getStoreId();
        if(Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_ACTIVE, $storeId)) {


            $original_data = $observer->getEvent()->getData('data_object')->getOrigData();
            $new_data = $observer->getEvent()->getData('data_object')->getData();

            $order = $observer->getEvent()->getOrder();
            $configStatuses = explode(',',Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_ORDER_STATUS, $storeId));

            foreach($configStatuses as $status) {
                if (isset($new_data['status']) && isset($original_data['status']) && $original_data['status'] !== $new_data['status'] && $new_data['status'] == $status) {
                    if (Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_ACTIVE, $storeId) && Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_TRIGGER, $storeId) == 1) {
                        $tags = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_MANDRILL_TAG, $storeId) . "_$storeId";
                        $mailSubject = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_SUBJECT, $storeId);
                        $senderId = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::GENERAL_SENDER, $storeId);
                        $sender = array('name' => Mage::getStoreConfig("trans_email/ident_$senderId/name", $storeId), 'email' => Mage::getStoreConfig("trans_email/ident_$senderId/email", $storeId));
                        $templateId = Mage::getStoreConfig(Ebizmarts_Autoresponder_Model_Config::NEWORDER_TEMPLATE, $storeId);

                        //Send email
                        $translate = Mage::getSingleton('core/translate');
                        $email = $order->getCustomerEmail();
                        if (Mage::helper('ebizmarts_autoresponder')->isSubscribed($email, 'neworder', $storeId)) {
                            $name = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
                            $url = Mage::getModel('core/url')->setStore($storeId)->getUrl() . 'ebizautoresponder/autoresponder/unsubscribe?list=neworder&email=' . $email . '&store=' . $storeId;
                            $vars = array('tags' => array($tags), 'url' => $url);
                            $mail = Mage::getModel('core/email_template')->setTemplateSubject($mailSubject)->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
                            $translate->setTranslateInLine(true);
                            Mage::helper('ebizmarts_abandonedcart')->saveMail('new order', $email, $name, "", $storeId);
                        }
                    }
                }
            }
        }

    }

}

/**
 * Ecommerce360 main model
 *
 * @category   Ebizmarts
 * @package    Ebizmarts_MageMonkey
 * @author     Ebizmarts Team <info@ebizmarts.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MageMonkey_Model_Ecommerce360
{

    /**
     * Order information to send to MC
     *
     * @var array
     * @access protected
     */
    protected $_info = array();

    /**
     * @var integer
     * @access protected
     */
    protected $_auxPrice = 0;

    /**
     * Current order
     *
     * @var Mage_Sales_Model_Order
     * @access protected
     */
    protected $_order;

    /**
     * Skip products list
     *
     * @var array
     * @access protected
     */
    protected $_productsToSkip = array(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);

    /**
     * Retrieve Cookie Object
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        return Mage::app()->getCookie();
    }

    /**
     * Check if Ecommerce360 integration is enabled per configuration settings
     *
     * @return bool
     */
    public function isActive()
    {
        return Mage::helper('monkey')->ecommerce360Active();
    }

    /**
     * Add cookie to customer's session
     *
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer
     */
    public function saveCookie(Varien_Event_Observer $observer)
    {
//		if( $this->isActive() ){
//            $request = Mage::app()->getRequest();
//
//			$thirty_days = time()+60*60*24*30;
//	        if ( $request->getParam('mc_cid') ){
//	            $this->getCookie()->set('magemonkey_campaign_id', $request->getParam('mc_cid'), $thirty_days);
//	        }
//	        if ( $request->getParam('mc_eid') ){
//	            $this->getCookie()->set('magemonkey_email_id', $request->getParam('mc_eid'), $thirty_days);
//	        }
//		}
        return $observer;
    }

    /**
     * Process data and send order to MC
     *
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer
     */
    public function run(Varien_Event_Observer $observer)
    {
        $storeId = Mage::app()->getStore()->getId();
        $order = $observer->getEvent()->getOrder();
        $customerEmail = $order->getCustomerEmail();
        $collection = Mage::getModel('monkey/lastorder')->getCollection()
            ->addFieldToFilter('email', array('eq' => $customerEmail));
        if(count($collection) > 0){
            //When saving the new date is automatically placed.
            $item = $collection->getFirstItem();
            $item->save();
        }else{
            Mage::getModel('monkey/lastorder')
                ->setEmail($customerEmail)
                ->save();
        }
        if ((($this->_getCampaignCookie() &&
                    $this->_getEmailCookie()) || Mage::getStoreConfig(Ebizmarts_MageMonkey_Model_Config::ECOMMERCE360_ACTIVE, $storeId) == 2) &&
            $this->isActive()
        ) {
            $this->logSale($order);
        }
        return $observer;
    }

    /**
     * Send order to MailChimp
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool|array
     */
    public function logSale($order)
    {

        $this->_order = $order;
        $api = Mage::getSingleton('monkey/api', array('store' => $this->_order->getStoreId()));
        if (!$api) {
            return false;
        }

        $subtotal = $this->_order->getBaseSubtotal();
        $discount = (float)$this->_order->getBaseDiscountAmount();
        if ($discount != 0) {
            $subtotal = $subtotal + ($discount);
        }
        $this->_info = array(
            'id' => $this->_order->getIncrementId(),
            'total' => $subtotal,
            'shipping' => $this->_order->getBaseShippingAmount(),
            'tax' => $this->_order->getBaseTaxAmount(),
            'store_id' => $this->_order->getStoreId(),
            'store_name' => $this->_order->getStoreName(),
            'order_date' => $this->_order->getCreatedAt(),
            'plugin_id' => 1215,
            'items' => array()
        );




        $emailCookie = $this->_getEmailCookie();
        $campaignCookie = $this->_getCampaignCookie();

        $this->setItemstoSend($this->_order->getStoreId());
        $rs = false;
        if ($emailCookie && $campaignCookie) {
            $this->_info ['email_id'] = $emailCookie;
            $this->_info ['campaign_id'] = $campaignCookie;
            if (Mage::getStoreConfig('monkey/general/checkout_async')) {
                $collection = Mage::getModel('monkey/asyncorders')->getCollection();
                $alreadyOnDb = false;
                foreach ($collection as $order) {
                    $info = unserialize($order->getInfo());
                    if ($info['order_id'] == $this->_order->getId()) {
                        $alreadyOnDb = true;
                    }
                }
                if (!$alreadyOnDb) {
                    $sync = Mage::getModel('monkey/asyncorders');
                    $this->_info['order_id'] = $this->_order->getId();
                    $sync->setInfo(serialize($this->_info))
                        ->setCreatedAt(Mage::getModel('core/date')->gmtDate())
                        ->setProcessed(0)
                        ->save();
                    $rs = true;
                } else {
                    $rs = 'Order already sent or ready to get sent soon';
                }
            } else {
                //Send order to MailChimp
                $rs = $api->campaignEcommOrderAdd($this->_info);
            }
        } else {
            $this->_info ['email'] = $this->_order->getCustomerEmail();
            if (Mage::getStoreConfig('monkey/general/checkout_async')) {
                $collection = Mage::getModel('monkey/asyncorders')->getCollection();
                $alreadyOnDb = false;
                foreach ($collection as $order) {
                    $info = unserialize($order->getInfo());
                    if ($info['order_id'] == $this->_order->getId()) {
                        $alreadyOnDb = true;
                    }
                }
                if (!$alreadyOnDb) {
                    $sync = Mage::getModel('monkey/asyncorders');
                    $this->_info['order_id'] = $this->_order->getId();
                    $sync->setInfo(serialize($this->_info))
                        ->setCreatedAt(Mage::getModel('core/date')->gmtDate())
                        ->setProcessed(0)
                        ->save();
                    $rs = true;
                } else {
                    $rs = 'Order already sent or ready to get sent soon';
                }
            } else {
                $rs = $api->ecommOrderAdd($this->_info);
            }
        }

        if ($rs === TRUE) {
            $this->_logCall();
            return true;
        } else {
            return $rs;
        }

    }

    /**
     * Process order items to send to MailChimp
     *
     * @access private
     * @return Ebizmarts_MageMonkey_Model_Ecommerce360
     */
    private function setItemstoSend($storeId)
    {
        foreach ($this->_order->getAllItems() as $item) {
            $mcitem = array();
            $product = Mage::getModel('catalog/product')->load($item->getProductId());

            if (in_array($product->getTypeId(), $this->_productsToSkip) && $product->getPriceType() == 0) {
                if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $this->_auxPrice = $item->getBasePrice();
                }
                continue;
            }

            $mcitem['product_id'] = $product->getEntityId();
            $mcitem['sku'] = $product->getSku();
            $mcitem['product_name'] = $product->getName();
            $attributesToSend = explode(',', Mage::getStoreConfig(Ebizmarts_MageMonkey_Model_Config::ECOMMERCE360_ATTRIBUTES, $storeId));
            $attributes = $product->getAttributes();
            $productAttributes = '';
            $pipe = false;
            foreach ($attributes as $attribute) {
                if ($pipe) {
                    $productAttributes .= '|';
                }
                if (in_array($attribute->getAttributeCode(), $attributesToSend) && is_string($attribute->getFrontend()->getValue($product)) && trim($attribute->getFrontend()->getValue($product)) != '') {
                    $productAttributes .= $attribute->getAttributeCode() . ':' . $attribute->getFrontend()->getValue($product);
                    $pipe = true;
                } else {
                    $pipe = false;
                }
            }
            if ($productAttributes) {
                $mcitem['product_name'] .= '[' . $productAttributes . ']';
            }

            $names = array();
            $cat_ids = $product->getCategoryIds();

            if (is_array($cat_ids) && count($cat_ids) > 0) {
                $category = Mage::getModel('catalog/category')->load($cat_ids[0]);
                $mcitem['category_id'] = $cat_ids[0];
                $names[] = $category->getName();
                while ($category->getParentId() && $category->getParentId() != 1) {
                    $category = Mage::getModel('catalog/category')->load($category->getParentId());
                    $names[] = $category->getName();
                }
            }
            if (!isset($mcitem['category_id'])) {
                $mcitem['category_id'] = 0;
            }
            $mcitem['category_name'] = (count($names)) ? implode(" - ", array_reverse($names)) : 'None';
            $mcitem['qty'] = $item->getQtyOrdered();
            $mcitem['cost'] = ($this->_auxPrice > 0) ? $this->_auxPrice : $item->getBasePrice();
            $this->_info['items'][] = $mcitem;
            $this->_auxPrice = 0;
        }

        return $this;
    }

    /**
     * Get cookie <magemonkey_email_id> from customer's session
     *
     * @return string|null
     */
    protected function _getEmailCookie()
    {
        return $this->getCookie()->get('magemonkey_email_id');
    }

    /**
     * Get cookie <magemonkey_campaign_id> from customer's session
     *
     * @return string|null
     */
    protected function _getCampaignCookie()
    {
        return $this->getCookie()->get('magemonkey_campaign_id');
    }

    /**
     * Save Api Call on db
     *
     * @return Ebizmarts_MageMonkey_Model_Ecommerce
     */
    protected function _logCall()
    {
        return Mage::getModel('monkey/ecommerce')
            ->setOrderIncrementId($this->_order->getIncrementId())
            ->setOrderId($this->_order->getId())
            ->setMcCampaignId($this->_getCampaignCookie())
            ->setMcEmailId($this->_getEmailCookie())
            ->setCreatedAt(Mage::getModel('core/date')->gmtDate())
            ->setStoreId($this->_order->getStoreId())
            ->save();
    }

    /** Send order to MailChimp Automatically by Order Status
     *
     *
     */
    public function autoExportJobs($storeId)
    {
        $allow_sent = false;
        //Get status options selected in the Configuration
        $states = explode(',', Mage::getStoreConfig(Ebizmarts_MageMonkey_Model_Config::ECOMMERCE360_ORDER_STATUS, $storeId));
        $max = Mage::getStoreConfig(Ebizmarts_MageMonkey_Model_Config::ECOMMERCE360_ORDER_MAX, $storeId);
        $count = 0;
        foreach ($states as $state) {
            if ($max == $count) {
                break;
            }
            $ecommerceTable = Mage::getSingleton('core/resource')->getTableName('monkey/ecommerce');
            if ($state != 'all_status') {
                $orders = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('main_table.store_id', array('eq' => $storeId));
//                $orders->getSelect()->joinLeft(array('ecommerce' => Mage::getSingleton('core/resource')->getTableName('monkey/ecommerce')), 'main_table.entity_id = ecommerce.order_id', 'main_table.*')->where('ecommerce.order_id is null AND main_table.status = \'' . $state . '\'')
//                    ->limit($max - $count);
                $orders->getSelect()->where('main_table.status = \'' . $state . '\' ' .
                    'AND main_table.entity_id NOT IN ' .
                    "(SELECT ecommerce.order_id FROM {$ecommerceTable} AS ecommerce WHERE ecommerce.store_id = {$storeId})")
                    ->limit($max - $count);
            } else {
                $orders = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('main_table.store_id', array('eq' => $storeId));
//                $orders->getSelect()->joinLeft(array('ecommerce' => Mage::getSingleton('core/resource')->getTableName('monkey/ecommerce')), 'main_table.entity_id = ecommerce.order_id', 'main_table.*')->where('ecommerce.order_id is null')
//                    ->limit($max - $count);
                $orders->getSelect()->where('main_table.entity_id NOT IN ' .
                    "(SELECT ecommerce.order_id FROM {$ecommerceTable} AS ecommerce WHERE ecommerce.store_id = {$storeId})")
                    ->limit($max - $count);
            }
            $count += count($orders);
            foreach ($orders as $order) {

                $this->_order = $order;
                $ordersToSend = Mage::getModel('monkey/asyncorders')->getCollection()
                    ->addFieldToFilter('processed', array('eq' => 0));
                foreach ($ordersToSend as $orderToSend) {
                    $info = (array)unserialize($orderToSend->getInfo());
                    if ($this->_order->getIncrementId() == $info['id']) {
                        continue;
                    }
                }

                $api = Mage::getSingleton('monkey/api', array('store' => $this->_order->getStoreId()));
                if (!$api) {
                    return false;
                }

                $subtotal = $this->_order->getBaseSubtotal();
                $discount = (float)$this->_order->getBaseDiscountAmount();
                if ($discount != 0) {
                    $subtotal = $subtotal + ($discount);
                }

                $this->_info = array(
                    'id' => $this->_order->getIncrementId(),
                    'total' => $subtotal,
                    'shipping' => $this->_order->getBaseShippingAmount(),
                    'tax' => $this->_order->getBaseTaxAmount(),
                    'store_id' => $this->_order->getStoreId(),
                    'store_name' => $this->_order->getStoreName(),
                    'order_date' => $this->_order->getCreatedAt(),
                    'plugin_id' => 1215,
                    'items' => array()
                );

                $email = $this->_order->getCustomerEmail();
                $campaign = $this->_order->getEbizmartsMagemonkeyCampaignId();
                $this->setItemstoSend($storeId);
                $rs = false;
                if ($email && $campaign) {
                    $this->_info ['email_id'] = $email;
                    $this->_info ['campaign_id'] = $campaign;

                    if (Mage::getStoreConfig('monkey/general/checkout_async', Mage::app()->getStore()->getId())) {
                        $sync = Mage::getModel('monkey/asyncorders');
                        $this->_info['order_id'] = $this->_order->getId();
                        $sync->setInfo(serialize($this->_info))
                            ->setCreatedAt($this->_order->getCreatedAt())//Mage::getModel('core/date')->gmtDate())
                            ->setProcessed(0)
                            ->save();
                        $rs['complete'] = true;
                    } else {
                        //Send order to MailChimp
                        $rs = $api->campaignEcommOrderAdd($this->_info);
                    }
                } else {
                    $this->_info ['email'] = $email;
                    if (Mage::getStoreConfig('monkey/general/checkout_async', Mage::app()->getStore()->getId())) {
                        $sync = Mage::getModel('monkey/asyncorders');
                        $this->_info['order_id'] = $this->_order->getId();
                        $sync->setInfo(serialize($this->_info))
                            ->setCreatedAt(Mage::getModel('core/date')->gmtDate())
                            ->setProcessed(0)
                            ->save();
                        $rs['complete'] = true;
                    } else {
                        $rs = $api->ecommOrderAdd($this->_info);
                    }
                }
                if (isset($rs['complete']) && $rs['complete'] == TRUE && !Mage::getStoreConfig('monkey/general/checkout_async', Mage::app()->getStore()->getId())) {
                    $order = Mage::getModel('monkey/ecommerce')
                        ->setOrderIncrementId($this->_info['id'])
                        ->setOrderId($this->_info['order_id'])
                        ->setMcEmailId($this->_info ['email'])
                        ->setCreatedAt($this->_order->getCreatedAt())
                        ->setStoreId($this->_info['store_id']);
                    if (isset($this->_info['campaign_id']) && $this->_info['campaign_id']) {
                        $order->setMcCampaignId($this->_info['campaign_id']);
                    }
                    $order->save();
                    //$this->_logCall();
                }
            }
        }
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product type model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type
{
    /**
     * Available product types
     */
    const TYPE_SIMPLE       = 'simple';
    const TYPE_BUNDLE       = 'bundle';
    const TYPE_CONFIGURABLE = 'configurable';
    const TYPE_GROUPED      = 'grouped';
    const TYPE_VIRTUAL      = 'virtual';

    const DEFAULT_TYPE      = 'simple';
    const DEFAULT_TYPE_MODEL    = 'catalog/product_type_simple';
    const DEFAULT_PRICE_MODEL   = 'catalog/product_type_price';

    static protected $_types;
    static protected $_compositeTypes;
    static protected $_priceModels;
    static protected $_typesPriority;

    /**
     * Product type instance factory
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   bool $singleton
     * @return  Mage_Catalog_Model_Product_Type_Abstract
     */
    public static function factory($product, $singleton = false)
    {
        $types = self::getTypes();
        $typeId = $product->getTypeId();

        if (!empty($types[$typeId]['model'])) {
            $typeModelName = $types[$typeId]['model'];
        } else {
            $typeModelName = self::DEFAULT_TYPE_MODEL;
            $typeId = self::DEFAULT_TYPE;
        }

        if ($singleton === true) {
            $typeModel = Mage::getSingleton($typeModelName);
        }
        else {
            $typeModel = Mage::getModel($typeModelName);
            $typeModel->setProduct($product);
        }
        $typeModel->setConfig($types[$typeId]);
        return $typeModel;
    }

    /**
     * Product type price model factory
     *
     * @param   string $productType
     * @return  Mage_Catalog_Model_Product_Type_Price
     */
    public static function priceFactory($productType)
    {
        if (isset(self::$_priceModels[$productType])) {
            return self::$_priceModels[$productType];
        }

        $types = self::getTypes();

        if (!empty($types[$productType]['price_model'])) {
            $priceModelName = $types[$productType]['price_model'];
        } else {
            $priceModelName = self::DEFAULT_PRICE_MODEL;
        }

        self::$_priceModels[$productType] = Mage::getModel($priceModelName);
        return self::$_priceModels[$productType];
    }

    static public function getOptionArray()
    {
        $options = array();
        foreach(self::getTypes() as $typeId=>$type) {
            $options[$typeId] = Mage::helper('catalog')->__($type['label']);
        }

        return $options;
    }

    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=>'');
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    static public function getOptions()
    {
        $res = array();
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    static public function getTypes()
    {
        if (is_null(self::$_types)) {
            $productTypes = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
            foreach ($productTypes as $productKey => $productConfig) {
                $moduleName = 'catalog';
                if (isset($productConfig['@']['module'])) {
                    $moduleName = $productConfig['@']['module'];
                }
                $translatedLabel = Mage::helper($moduleName)->__($productConfig['label']);
                $productTypes[$productKey]['label'] = $translatedLabel;
            }
            self::$_types = $productTypes;
        }

        return self::$_types;
    }

    /**
     * Return composite product type Ids
     *
     * @return array
     */
    static public function getCompositeTypes()
    {
        if (is_null(self::$_compositeTypes)) {
            self::$_compositeTypes = array();
            $types = self::getTypes();
            foreach ($types as $typeId=>$typeInfo) {
                if (array_key_exists('composite', $typeInfo) && $typeInfo['composite']) {
                    self::$_compositeTypes[] = $typeId;
                }
            }
        }
        return self::$_compositeTypes;
    }

    /**
     * Return product types by type indexing priority
     *
     * @return array
     */
    public static function getTypesByPriority()
    {
        if (is_null(self::$_typesPriority)) {
            self::$_typesPriority = array();
            $a = array();
            $b = array();

            $types = self::getTypes();
            foreach ($types as $typeId => $typeInfo) {
                $priority = isset($typeInfo['index_priority']) ? abs(intval($typeInfo['index_priority'])) : 0;
                if (!empty($typeInfo['composite'])) {
                    $b[$typeId] = $priority;
                } else {
                    $a[$typeId] = $priority;
                }
            }

            asort($a, SORT_NUMERIC);
            asort($b, SORT_NUMERIC);

            foreach (array_keys($a) as $typeId) {
                self::$_typesPriority[$typeId] = $types[$typeId];
            }
            foreach (array_keys($b) as $typeId) {
                self::$_typesPriority[$typeId] = $types[$typeId];
            }
        }
        return self::$_typesPriority;
    }
}