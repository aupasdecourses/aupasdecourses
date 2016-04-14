<?php

class Pmainguet_Customhandles_Model_Observer
{
    /**
     * Converts attribute set name of current product to nice name ([a-z0-9_]+).
     * Adds layout handle PRODUCT_ATTRIBUTE_SET_<attribute_set_nicename> after
     * PRODUCT_TYPE_<product_type_id> handle
     *
     * Event: controller_action_layout_load_before
     *
     * @param Varien_Event_Observer $observer
     */

    /*Add custom handles for Commerçant page only*/
    
    public function addCustomhandle(Varien_Event_Observer $observer)
    {
            $type=Mage::app()->getFrontController()->getRequest()->getControllerName();

            if ($type=='category'){
                $layout = $observer->getEvent()->getLayout();
                $id = Mage::registry('current_category')->getId();
                $estcom=Mage::getResourceModel('catalog/category')->getAttributeRawValue($id, "estcom_commercant", Mage::app()->getStore()->getId());
                if($estcom){
                    $layout->getUpdate()->addHandle('PAGE_COMMERCANT');
                }
            }
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
 * @category   BL
 * @package    BL_CustomGrid
 * @copyright  Copyright (c) 2012 Benoît Leulliette <benoit.leulliette@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class BL_CustomGrid_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function implodeArray($array, $glue=',')
    {
        return (is_array($array) ? implode($glue, $array) : '');
    }
    
    public function unserializeArray($array)
    {
        return (is_array($array = @unserialize($array)) ? $array : array());
    }
    
    protected function _parseIntValue($value)
    {
        return ($value !== '' ? intval($value) : null);
    }
    
    public function parseCsvIntArray($string, $unique=true, $sorted=false, $min=null, $max=null)
    {
        $values = array_map(array($this, '_parseIntValue'), explode(',', $string));
        $filterCodes = array('!is_null($v)');
        
        if ($unique) {
            $values = array_unique($values);
        }
        if (!is_null($min)) {
            $filterCodes[] = '($v >= '.intval($min).')';
        }
        if (!is_null($max)) {
            $filterCodes[] = '($v <= '.intval($max).')';
        }
        
        $filterCode = 'return ('.implode(' && ', $filterCodes).');';
        $values = array_filter($values, create_function('$v', $filterCode));
        
        if ($sorted) {
            sort($values, SORT_NUMERIC);
        }
        
        return $values;
    }
    
    public function getOptionsHashFromOptionsArray(array $optionsArray, $withEmpty=false)
    {
        $optionsHash = array();
        
        foreach ($optionsArray as $key => $value) {
            if (is_array($value)) {
                if (isset($value['value']) && isset($value['label'])) {
                    if ($withEmpty || ($value['value'] !== '')) {
                        $optionsHash[$value['value']] = $value['label'];
                    }
                }
            } else {
                // Seems to already be an options hash
                $optionsHash[$key] = $value;
            }
        }
        
        return $optionsHash;
    }
    
    public function getOptionsArrayFromOptionsHash(array $optionsHash, $withEmpty=false)
    {
        $optionsArray = array();
        
        foreach ($optionsHash as $key => $value) {
            if (!is_array($value)) {
                if ($withEmpty || ($key !== '')) {
                    $optionsArray[] = array(
                        'value' => $key,
                        'label' => $value,
                    );
                }
            } elseif (isset($value['value']) && isset($value['label'])) {
                // Seems to already be an options array, remove anyway empty values if needed
                if ($withEmpty || ($value['value'] !== '')) {
                    $optionsArray[] = $value;
                }
            }
        }
        
        return $optionsArray;
    }
    
    public function getColumnHeaderName($key)
    {
        // Beautify column key
        $key = trim(str_replace('_', ' ', strtolower($key)));
        
        // Play on words case for translation
        // Try three of the whole possibilities, which should represent most of the successfull ones
        $helper = Mage::helper('adminhtml');
        
        if (($key === ($result = $helper->__($key)))
            && (ucfirst($key) === ($result = $helper->__(ucfirst($key))))
            && (uc_words($key, ' ', ' ') === ($result = $helper->__(uc_words($key, ' ', ' '))))) {
            // Use basic key if no translation succeeded
            $result = uc_words($key, ' ', ' ');
        }
        
        return $result;
    }
    
    public function isMageVersion($major, $minor, $revision=null)
    {
        $infos = Mage::getVersionInfo();
        return (($infos['major'] == $major)
                 && ($infos['minor'] == $minor)
                 && (is_null($revision) || ($infos['revision'] == $revision)));
    }
    
    public function isMageVersionGreaterThan($major, $minor, $revision=null)
    {
        $infos  = Mage::getVersionInfo();
        
        if (($iMajor = intval($infos['major'])) > $major) {
            return true;
        } elseif ($iMajor == $major) {
            if (($iMinor = intval($infos['minor'])) > $minor) {
                return true;
            } elseif (($iMinor == $minor) && !is_null($revision)) {
                return (intval($infos['revision']) > $revision);
            }
        }
        
        return false;
    }
    
    public function isMageVersionLesserThan($major, $minor, $revision=null)
    {
        $infos  = Mage::getVersionInfo();
        
        if (($iMajor = intval($infos['major'])) < $major) {
            return true;
        } elseif ($iMajor == $major) {
            if (($iMinor = intval($infos['minor'])) < $minor) {
                return true;
            } elseif (($iMinor == $minor) && !is_null($revision)) {
                return (intval($infos['revision']) < $revision);
            }
        }
        
        return false;
    }
    
    public function isMageVersion14()
    {
        return $this->isMageVersion(1, 4);
    }
    
    public function isMageVersion15()
    {
        return $this->isMageVersion(1, 5);
    }
    
    public function isMageVersion16()
    {
        return $this->isMageVersion(1, 6);
    }
    
    public function isMageVersion17()
    {
        return $this->isMageVersion(1, 7);
    }
    
    public function getMageVersionRevision()
    {
        $infos = Mage::getVersionInfo();
        return $infos['revision'];
    }
    
    public function isRewritedGrid($block)
    {
        if ($class = get_class($block)) {
            return (bool) preg_match('#^BL_CustomGrid_Block_Rewrite_.+$#', $class);
        }
        return false;
    }
    
    public function isAjaxRequest()
    {
        return $this->_getRequest()->isAjax();
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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Template extends Mage_Core_Block_Template
{
    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'adminhtml/url';
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * Check whether or not the module output is enabled
     *
     * Because many module blocks belong to Adminhtml module,
     * the feature "Disable module output" doesn't cover Admin area
     *
     * @param string $moduleName Full module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->getModuleName();
        }
        return !Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName);
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_block_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}

/**
 * Mage Monkey default helper
 *
 * @category   Ebizmarts
 * @package    Ebizmarts_MageMonkey
 * @author     Ebizmarts Team <info@ebizmarts.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MageMonkey_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Utility to check if admin is logged in
     *
     * @return bool
     */
    public function isAdmin()
    {
        return Mage::getSingleton('admin/session')->isLoggedIn();
    }

    /**
     * Check if Magento is EE
     *
     * @return bool
     */
    public function isEnterprise()
    {
        return is_object(Mage::getConfig()->getNode('global/models/enterprise_enterprise'));
    }


    /**
     * Whether Admin Notifications should be displayed or not in backend Admin
     *
     * @return bool
     */
    public function isAdminNotificationEnabled()
    {
        return $this->config('adminhtml_notification');
    }

    /**
     * Return Webhooks security key for given store
     *
     * @param mixed $store Store object, or Id, or code
     * @param string $listId Optional listid to retrieve store code from it
     * @return string
     */
    public function getWebhooksKey($store = null, $listId = null)
    {
        if (!is_null($listId)) {
            $store = $this->getStoreByList($listId, TRUE);
        }

        $crypt = md5((string)Mage::getConfig()->getNode('global/crypt/key'));
        $key = substr($crypt, 0, (strlen($crypt) / 2));

        // Prevent most cases to attach default in webhook url
        if (!$store || $store == 'default') $store = '';

        return ($key . $store);
    }

    public function filterShowGroupings($interestGroupings)
    {
        if (is_array($interestGroupings)) {

            $customGroupings = (array)Mage::getConfig()->getNode('default/monkey/custom_groupings');
            foreach ($interestGroupings as $key => $group) {

                if (TRUE === in_array($group['name'], $customGroupings)) {
                    unset($interestGroupings[$key]);
                }

            }
        }

        return $interestGroupings;
    }

    /**
     * Check if CustomerGroup grouping already exists on MC
     *
     * @param array $groupings
     * @return bool
     */
    public function customerGroupGroupingExists($interestGroupings)
    {
        $exists = FALSE;
        if (is_array($interestGroupings)) {
            foreach ($interestGroupings as $group) {
                if ($group['name'] == $this->getCustomerGroupingName()) {
                    $exists = TRUE;
                    break;
                }
            }
        }

        return $exists;
    }

    /**
     * Return customer groping name to be used when creating a grouping to store
     * Magento customer groups
     *
     * @return string
     */
    public function getCustomerGroupingName()
    {
        return (string)Mage::getConfig()->getNode('default/monkey/custom_groupings/customer_grouping_name');
    }

    /**
     * Get module User-Agent to use on API requests
     *
     * @return string
     */
    public function getUserAgent()
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;

        $aux = (array_key_exists('Enterprise_Enterprise', $modulesArray)) ? 'EE' : 'CE';
        $v = (string)Mage::getConfig()->getNode('modules/Ebizmarts_MageMonkey/version');
        $version = strpos(Mage::getVersion(), '-') ? substr(Mage::getVersion(), 0, strpos(Mage::getVersion(), '-')) : Mage::getVersion();
        return (string)'MageMonkey' . $v . '/Mage' . $aux . $version;
    }

    /**
     * Return Mandrill API key
     *
     * @param string $store
     * @return string Api Key
     */
    public function getMandrillApiKey($store = null)
    {
        if (is_null($store)) {
            $key = $this->config('mandrill_apikey');
        } else {
            $curstore = Mage::app()->getStore();
            Mage::app()->setCurrentStore($store);
            $key = $this->config('mandrill_apikey', $store);
            Mage::app()->setCurrentStore($curstore);
        }

        return $key;
    }

    /**
     * Return MC API key for given store, if none is given
     * default key is returned
     *
     * @param string $store
     * @return string Api Key
     */
    public function getApiKey($store = null)
    {
        if (is_null($store)) {
            $key = $this->config('apikey');
        } else {
            $curstore = Mage::app()->getStore();
            Mage::app()->setCurrentStore($store);
            $key = $this->config('apikey', $store);
            Mage::app()->setCurrentStore($curstore);
        }

        return $key;
    }

    /**
     * Logging facility
     *
     * @param mixed $data Message to save to file
     * @param string $filename log filename, default is <Monkey.log>
     * @return Mage_Core_Model_Log_Adapter
     */
    public function log($data, $filename = 'Monkey.log')
    {
        if ($this->config('enable_log') != 0) {
            return Mage::getModel('core/log_adapter', $filename)->log($data);
        }
    }

    /**
     * Get module configuration value
     *
     * @param string $value
     * @param string $store
     * @return mixed Configuration setting
     */
    public function config($value, $store = null)
    {
        $store = is_null($store) ? Mage::app()->getStore() : $store;

        $configscope = Mage::app()->getRequest()->getParam('store');
        if ($configscope && ($configscope !== 'undefined') && !is_array($configscope)) {
            if (is_array($configscope) && isset($configscope['code'])) {
                $store = $configscope['code'];
            } else {
                $store = $configscope;
            }
        }

        return Mage::getStoreConfig("monkey/general/$value", $store);
    }

    /**
     * Check if config setting <checkout_subscribe> is enabled
     *
     * @return bool
     */
    public function canCheckoutSubscribe()
    {
        return $this->config('checkout_subscribe');
    }

    /**
     * Check if an email is subscribed on MailChimp
     *
     * @param string $email
     * @param string $listId
     * @return bool
     */
    public function subscribedToList($email, $listId = null)
    {
        $on = FALSE;

        if ($email) {
            $member = Mage::getSingleton('monkey/api')
                ->listMemberInfo($listId, $email);

            if (!is_string($member) && $member['success'] && ($member['data'][0]['status'] == 'subscribed' || $member['data'][0]['status'] == 'pending')) {
                $on = TRUE;
            }
        }

        return $on;
    }

    /**
     * Check if Ecommerce360 integration is enabled
     *
     * @return bool
     */
    public function ecommerce360Active()
    {
        $storeId = Mage::app()->getStore()->getId();
        return (bool)(Mage::getStoreConfig(Ebizmarts_MageMonkey_Model_Config::ECOMMERCE360_ACTIVE, $storeId) != 0);
    }

    /**
     * Check if Transactional Email via MC is enabled
     *
     * @return bool
     */
    public function useTransactionalService()
    {
        return Mage::getStoreConfigFlag("monkey/general/transactional_emails");
    }

    /**
     * Check if Ebizmarts_MageMonkey module is enabled
     *
     * @return bool
     */
    public function canMonkey()
    {
        return (bool)((int)$this->config('active') !== 0);
    }

    /**
     * Get default MC listId for given storeId
     *
     * @param string $store
     * @return string $list
     */
    public function getDefaultList($store)
    {
        $curstore = Mage::app()->getStore();
        Mage::app()->setCurrentStore($store);
        $list = $this->config('list', $store);
        Mage::app()->setCurrentStore($curstore);
        return $list;
    }

    /**
     * Get additional Lists by storeId
     *
     * @param string $store
     * @return string $list
     */
    public function getAdditionalList($store)
    {
        $curstore = Mage::app()->getStore();
        Mage::app()->setCurrentStore($store);
        $list = $this->config('additional_lists', $store);
        Mage::app()->setCurrentStore($curstore);
        return $list;
    }

    /**
     * Get which store is associated to given $mcListId
     *
     * @param string $mcListId
     * @param bool $includeDefault Include <default> store or not on result
     * @return string $store
     */
    public function getStoreByList($mcListId, $includeDefault = FALSE)
    {
        $list = Mage::getModel('core/config_data')->getCollection()
            ->addValueFilter($mcListId)->getFirstItem();

        $store = null;
        if ($list->getId()) {

            //$isDefault = (bool)($list->getScope() == 'default');
            $isDefault = (bool)($list->getScope() == Mage::app()->getDefaultStoreView()->getCode());
            if (!$isDefault && !$includeDefault) {
                $store = (string)Mage::app()->getStore($list->getScopeId())->getCode();
            } else {
                $store = $list->getScope();
            }

        }

        return $store;
    }

    /**
     * Check if current request is a Webhooks request
     *
     * @return bool
     */
    public function isWebhookRequest()
    {
        $rq = Mage::app()->getRequest();
        $monkeyRequest = (string)'monkeywebhookindex';
        $thisRequest = (string)($rq->getRequestedRouteName() . $rq->getRequestedControllerName() . $rq->getRequestedActionName());

        return (bool)($monkeyRequest === $thisRequest);
    }

    /**
     * Get config setting <map_fields>
     *
     * @return array|FALSE
     */
    public function getMergeMaps($storeId)
    {
        return unserialize($this->config('map_fields', $storeId));
    }

    /**
     * Get progress bar HTML code
     *
     * @param integer $complete Processed qty so far
     * @param integer $total Total qty to process
     * @return string
     */
    public function progressbar($complete, $total)
    {
        if ($total == 0) {
            return;
        }
        $percentage = round(($complete * 100) / $total, 0);

        $barStyle = '';
        if ($percentage > 0) {
            $barStyle = " style=\"width: $percentage%\"";
        }

        $html = "<div id=\"bar-progress-bar\" class=\"bar-all-rounded\">\n";
        $html .= "<div id=\"bar-progress-bar-percentage\" class=\"bar-all-rounded\"$barStyle>";
        $html .= "$percentage% ($complete of $total)";
        //<progress value="75" max="100">3/4 complete</progress>
        //if ($percentage > 5) {$html .= "$percentage% ($complete of $total)";} else {$html .= "<div class=\"bar-spacer\">&nbsp;</div>";}
        $html .= "</div></div>";

        return $html;
    }

    /**
     * Return Merge Fields mapped to Magento attributes
     *
     * @param object $customer
     * @param bool $includeEmail
     * @param integer $websiteId
     * @return array
     */
    public function getMergeVars($customer, $includeEmail = FALSE, $websiteId = NULL)
    {
        $merge_vars = array();
        $maps = $this->getMergeMaps($customer->getStoreId());

        if (!$maps && !$customer->getListGroups()) {
            return;
        }

        $request = Mage::app()->getRequest();

        //Add Customer data to Subscriber if is Newsletter_Subscriber is Customer
        if (!$customer->getDefaultShipping() && $customer->getEntityId()) {
            $customer->addData(Mage::getModel('customer/customer')->load($customer->getEntityId())
                ->setStoreId($customer->getStoreId())
                ->toArray());
        } elseif ($customer->getCustomerId()) {
            $customer->addData(Mage::getModel('customer/customer')->load($customer->getCustomerId())
                ->setStoreId($customer->getStoreId())
                ->toArray());
        }

        $merge_vars = $this->_setMaps($maps,$customer,$merge_vars, $websiteId);

        //GUEST
        $guestFirstName = '';
        if (!$customer->getId() && !$request->getPost('firstname')) {
            if($this->config('guest_name', $customer->getStoreId())){
                $guestFirstName = $this->config('guest_name', $customer->getStoreId());
            }elseif($customer->getSubscriberFirstname()) {
                $guestFirstName = $customer->getSubscriberFirstname();
            }

            if ($guestFirstName) {
                $merge_vars['FNAME'] = $guestFirstName;
            }
        }
        $guestLastName = '';
        if (!$customer->getId() && !$request->getPost('lastname')) {
            if($this->config('guest_lastname', $customer->getStoreId())){
                $guestLastName = $this->config('guest_lastname', $customer->getStoreId());
            }elseif($customer->getSubscriberLastname()){
                $guestLastName = $customer->getSubscriberLastname();
            }

            if ($guestLastName) {
                $merge_vars['LNAME'] = $guestLastName;
            }
        }
        //GUEST

        if ($includeEmail) {
            $merge_vars['EMAIL'] = $customer->getEmail();
        }

        $groups = $customer->getListGroups();
        $groupings = array();

        if (is_array($groups) && count($groups)) {
            foreach ($groups as $groupId => $grupoptions) {
                if (is_array($grupoptions)) {
                    $grupOptionsEscaped = array();
                    foreach ($grupoptions as $gopt) {
                        $gopt = str_replace(",", "%C%", $gopt);
                        $grupOptionsEscaped[] = $gopt;
                    }
                    $groupings[] = array(
                        'id' => $groupId,
                        'groups' => str_replace('%C%', '\\,', implode(', ', $grupOptionsEscaped))
                    );
                } else {
                    $groupings[] = array(
                        'id' => $groupId,
                        'groups' => str_replace(',', '\\,', $grupoptions)
                    );
                }
            }
        }

        $merge_vars['GROUPINGS'] = $groupings;

        //magemonkey_mergevars_after
        $blank = new Varien_Object;
        Mage::dispatchEvent('magemonkey_mergevars_after',
            array('vars' => $merge_vars, 'customer' => $customer, 'newvars' => $blank));
        if ($blank->hasData()) {
            $merge_vars = array_merge($merge_vars, $blank->toArray());
        }
        //magemonkey_mergevars_after
        return $merge_vars;
    }
    private function _setMaps($maps,$customer,$merge_vars, $websiteId)
    {
        foreach ($maps as $map) {
            $request = Mage::app()->getRequest();

            $customAtt = $map['magento'];
            $chimpTag = $map['mailchimp'];

            if ($chimpTag && $customAtt) {

                $key = strtoupper($chimpTag);

                switch ($customAtt) {
                    case 'gender':
                        $val = (int)$customer->getData(strtolower($customAtt));
                        if ($val == 1) {
                            $merge_vars[$key] = 'Male';
                        } elseif ($val == 2) {
                            $merge_vars[$key] = 'Female';
                        }
                        break;
                    case 'dob':
                        $dob = (string)$customer->getData(strtolower($customAtt));
                        if ($dob) {
                            $merge_vars[$key] = (substr($dob, 5, 2) . '/' . substr($dob, 8, 2));
                        }
                        break;
                    case 'billing_address':
                    case 'shipping_address':
                        $merge_vars = array_merge($merge_vars, $this->_setAddress($customAtt,$merge_vars, $customer, $key));
                        break;
                    case 'date_of_purchase':

                        $last_order = Mage::getModel('monkey/lastorder')
                            ->getCollection()
                            ->addFieldToFilter('email', array('eq' => $customer->getEmail()))
                            ->getFirstItem();
                        if ($last_order->getId()) {
                            $merge_vars[$key] = $last_order->getDate();
                        }

                        break;
                    case 'ee_customer_balance':

                        $merge_vars[$key] = '';

                        if ($this->isEnterprise() && $customer->getId()) {

                            $_customer = Mage::getModel('customer/customer')->load($customer->getId());
                            if ($_customer->getId()) {
                                if (Mage::app()->getStore()->isAdmin()) {
                                    $websiteId = is_null($websiteId) ? Mage::app()->getStore()->getWebsiteId() : $websiteId;
                                }

                                $balance = Mage::getModel('enterprise_customerbalance/balance')
                                    ->setWebsiteId($websiteId)
                                    ->setCustomerId($_customer->getId())
                                    ->loadByCustomer();

                                $merge_vars[$key] = $balance->getAmount();
                            }

                        }

                        break;
                    case 'group_id':
                        $group_id = (int)$customer->getData(strtolower($customAtt));
                        $customerGroup = Mage::helper('customer')->getGroups()->toOptionHash();
                        if ($group_id == 0) {
                            $merge_vars[$key] = 'NOT LOGGED IN';
                        } else {
                            $merge_vars[$key] = $customerGroup[$group_id];
                        }
                        break;
                    case 'store_code':
                        $storeId = (string)$customer->getData('store_id');
                        $storeCode = Mage::getModel('core/store')->load($storeId)->getCode();
                        if ($storeCode) {
                            $merge_vars[$key] = $storeCode;
                        }
                        break;
                    default:
                        if (($value = (string)$customer->getData(strtolower($customAtt)))
                            OR ($value = (string)$request->getPost(strtolower($customAtt)))
                        ) {
                            $merge_vars[$key] = $value;
                        }

                        break;
                }

            }
        }
        return $merge_vars;
    }
    protected function _setAddress($customAtt,$merge_vars, $customer, $key)
    {
        $addr = explode('_', $customAtt);
        $address = $customer->{'getPrimary' . ucfirst($addr[0]) . 'Address'}();
        if (!$address) {
            if ($customer->{'getDefault' . ucfirst($addr[0])}()) {
                $address = Mage::getModel('customer/address')->load($customer->{'getDefault' . ucfirst($addr[0])}());
            }
        }
        if ($address) {
            $merge_vars[$key] = array(
                'addr1' => $address->getStreet(1),
                'addr2' => $address->getStreet(2),
                'city' => $address->getCity(),
                'state' => (!$address->getRegion() ? $address->getCity() : $address->getRegion()),
                'zip' => $address->getPostcode(),
                'country' => $address->getCountryId()
            );
            $telephone = $address->getTelephone();
            if ($telephone) {
                $merge_vars['TELEPHONE'] = $telephone;
            }
            $company = $address->getCompany();
            if ($company) {
                $merge_vars['COMPANY'] = $company;
            }
            $country = $address->getCountryId();
            if ($country) {
                $merge_vars['COUNTRY'] = $country;
            }
        }
        return $merge_vars;
    }
    /**
     * Get Mergevars
     *
     * @param null|Mage_Customer_Model_Customer $object
     * @param bool $includeEmail
     * @return array
     */
    public function mergeVars($object = NULL, $includeEmail = FALSE, $currentList = NULL)
    {
        //Initialize as GUEST customer
        $customer = new Varien_Object;

        $regCustomer = Mage::registry('current_customer');
        $guestCustomer = Mage::registry('mc_guest_customer');

        if (Mage::helper('customer')->isLoggedIn()) {
            $customer = Mage::helper('customer')->getCustomer();
        } elseif ($regCustomer) {
            $customer = $regCustomer;
        } elseif ($guestCustomer) {
            $customer = $guestCustomer;
        } else {
            if (is_null($object)) {
                $customer->setEmail($object->getSubscriberEmail())
                    ->setStoreId($object->getStoreId());
            } else {
                $customer = $object;
            }
        }

        if (is_object($object)) {
            if ($object->getListGroups()) {
                $customer->setListGroups($object->getListGroups());
            }

            if ($object->getMcListId()) {
                $customer->setMcListId($object->getMcListId());
            }
        }

        $mergeVars = Mage::helper('monkey')->getMergeVars($customer, $includeEmail);
        // add groups
        $monkeyPost = Mage::getSingleton('core/session')->getMonkeyPost();
        $request = Mage::app()->getRequest();
        $post = $request->getPost();
        if ($monkeyPost) {
            $post = unserialize($monkeyPost);
        }
        //if post exists && is not admin backend subscription && not footer subscription
        $mergeVars = array_merge($this->_checkGrouping($post,$currentList, $object), $mergeVars);

        return $mergeVars;
    }
    private function _checkGrouping($post,$currentList, $object)
    {
        $mergeVars = array();
        $request = Mage::app()->getRequest();
        $adminSubscription = $request->getActionName() == 'save' && $request->getControllerName() == 'customer' && $request->getModuleName() == (string)Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        $footerSubscription = $request->getActionName() == 'new' && $request->getControllerName() == 'subscriber' && $request->getModuleName() == 'newsletter';
        $customerSubscription = $request->getActionName() == 'saveadditional';
        $customerCreateAccountSubscription = $request->getActionName() == 'createpost';
        if ($post && !$adminSubscription && !$customerSubscription && !$customerCreateAccountSubscription || Mage::getSingleton('core/session')->getIsOneStepCheckout()) {
            $defaultList = Mage::helper('monkey')->config('list');
            //if can change customer set the groups set by customer else set the groups on MailChimp config
            $canChangeGroups = Mage::getStoreConfig('monkey/general/changecustomergroup', $object->getStoreId());
            if ($currentList && ($currentList != $defaultList || $canChangeGroups && !$footerSubscription) && isset($post['list'][$currentList])) {
                $subscribeGroups = array(0 => array());
                foreach ($post['list'][$currentList] as $toGroups => $value) {
                    if (is_numeric($toGroups)) {
                        $subscribeGroups[0]['id'] = $toGroups;
                        $subscribeGroups[0]['groups'] = implode(', ', array_unique($post['list'][$currentList][$subscribeGroups[0]['id']]));
                    }
                }
                $groups = NULL;
            } elseif ($currentList == $defaultList) {
                $groups = Mage::getStoreConfig('monkey/general/cutomergroup', $object->getStoreId());
                $groups = explode(",", $groups);
                if (isset($groups[0]) && $groups[0]) {
                    $subscribeGroups = array();
                    $_prevGroup = null;
                    $checkboxes = array();
                    foreach ($groups as $group) {
                        $item = explode("_", $group);
                        if ($item[0]) {
                            $currentGroup = $item[0];
                            if ($currentGroup == $_prevGroup || $_prevGroup == null) {
                                $checkboxes[] = $item[1];
                                $_prevGroup = $currentGroup;
                            } else {
                                $subscribeGroups[] = array('id' => $_prevGroup, "groups" => str_replace('%C%', '\\,', implode(', ', $checkboxes)));
                                $checkboxes = array();
                                $_prevGroup = $currentGroup;
                                $checkboxes[] = $item[1];
                            }
                        }
                    }
                    if ($currentGroup) {
                        $subscribeGroups[] = array('id' => $currentGroup, "groups" => str_replace('%C%', '\\,', implode(', ', $checkboxes)));
                    }

                }

                $force = Mage::getStoreConfig('monkey/general/checkout_subscribe', $object->getStoreId());
                $map = Mage::getStoreConfig('monkey/general/markfield', $object->getStoreId());
                if (isset($post['magemonkey_subscribe']) && $map != "") {
                    $listsChecked = explode(',', $post['magemonkey_subscribe']);
                    $hasClicked = in_array($currentList, $listsChecked);
                    if ($hasClicked && $force != 3) {
                        $mergeVars[$map] = "Yes";
                    } else {
                        $mergeVars[$map] = "No";
                    }
                } elseif (Mage::getSingleton('core/session')->getIsOneStepCheckout()) {
                    $post2 = $request->getPost();
                    if (isset($post['subscribe_newsletter']) || isset($post2['subscribe_newsletter'])) {
                        $mergeVars[$map] = "Yes";
                    } elseif (Mage::helper('monkey')->config('checkout_subscribe') > 2) {
                        $mergeVars[$map] = "No";
                    }
                } elseif ($request->getModuleName() == 'checkout') {
                    $mergeVars[$map] = "No";
                }
            } else {
                $map = Mage::getStoreConfig('monkey/general/markfield', $object->getStoreId());
                $mergeVars[$map] = "Yes";
            }
            if (isset($subscribeGroups[0]['id']) && $subscribeGroups[0]['id'] != -1) {
                $mergeVars["GROUPINGS"] = $subscribeGroups;
            }
        }
        return $mergeVars;
    }
    /**
     * Register on Magento's registry GUEST customer data for MergeVars for on checkout subscribe
     *
     * @param Mage_Sales_Model_Order $order
     * @return void
     */
    public function registerGuestCustomer($order)
    {

        if (Mage::registry('mc_guest_customer')) {
            return;
        }

        $customer = new Varien_Object;

        $customer->setId('guest' . time());
        $customer->setEmail($order->getBillingAddress()->getEmail());
        $customer->setStoreId($order->getStoreId());
        $customer->setFirstname($order->getBillingAddress()->getFirstname());
        $customer->setLastname($order->getBillingAddress()->getLastname());
        $customer->setPrimaryBillingAddress($order->getBillingAddress());
        $customer->setPrimaryShippingAddress($order->getShippingAddress());

        Mage::register('mc_guest_customer', $customer, TRUE);

    }


    /**
     * Create a Magento's customer account for given data
     *
     * @param array $accountData
     * @param integer $websiteId ID of website to associate customer to
     * @return Mage_Customer_Model_Customer
     */
    public function createCustomerAccount($accountData, $websiteId)
    {
        $customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId);

        if (!isset($accountData['firstname']) OR empty($accountData['firstname'])) {
            $accountData['firstname'] = $this->__('Store');
        }
        if (!isset($accountData['lastname']) OR empty($accountData['lastname'])) {
            $accountData['lastname'] = $this->__('Guest');
        }

        $customerForm = Mage::getModel('customer/form');
        $customerForm->setFormCode('customer_account_create')
            ->setEntity($customer)
            ->initDefaultValues();
        // emulate request
        $request = $customerForm->prepareRequest($accountData);
        $customerData = $customerForm->extractData($request);
        $customerForm->restoreData($customerData);

        $customerErrors = $customerForm->validateData($customerData);

        if ($customerErrors) {
            $customerForm->compactData($customerData);

            $pwd = $customer->generatePassword(8);
            $customer->setPassword($pwd);
            try {
                $customer->save();

                if ($customer->isConfirmationRequired()) {
                    $customer->sendNewAccountEmail('confirmation');
                }
                /**
                 * Handle Address related Data
                 */
                $billing = $shipping = null;
                if (isset($accountData['billing_address']) && !empty($accountData['billing_address'])) {
                    $this->_McAddressToMage($accountData, 'billing', $customer);
                }
                if (isset($accountData['shipping_address']) && !empty($accountData['shipping_address'])) {
                    $this->_McAddressToMage($accountData, 'shipping', $customer);
                }
            } catch (Exception $ex) {
                $this->log($ex->getMessage(), 'Monkey.log');
            }
        }

        return $customer;
    }

    /**
     * Parse MailChimp <address> MergeField type to Magento's address object
     *
     * @param array $data MC address data
     * @param string $type billing or shipping
     * @param Mage_Customer_Model_Customer $customer
     * @return array Empty if noy errors, or a list of errors in an Array
     */
    protected function _McAddressToMage(array $data, $type, $customer)
    {
        $addressData = $data["{$type}_address"];
        $address = explode(str_repeat(' ', 2), $addressData);
        list($addr1, $addr2, $city, $state, $zip, $country) = $address;

        $region = Mage::getModel('directory/region')->loadByName($state, $country);

        $mgAddress = array(
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'street' => array($addr1, $addr2),
            'city' => $city,
            'country_id' => $country,
            'region' => $state,
            'region_id' => (!is_null($region->getId()) ? $region->getId() : null),
            'postcode' => $zip,
            'telephone' => 'not_provided',
        );

        /* @var $address Mage_Customer_Model_Address */
        $address = Mage::getModel('customer/address');
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_register_address')
            ->setEntity($address);

        $addrrequest = $addressForm->prepareRequest($mgAddress);
        $addressData = $addressForm->extractData($addrrequest);
        $addressErrors = $addressForm->validateData($addressData);

        $errors = array();
        if ($addressErrors === true) {
            $address->setId(null)
                ->setData("is_default_{$type}", TRUE);
            $addressForm->compactData($addressData);
            $customer->addAddress($address);

            $addressErrors = $address->validate();
            if (is_array($addressErrors)) {
                $errors = array_merge($errors, $addressErrors);
            }
        } else {
            $errors = array_merge($errors, $addressErrors);
        }

        return $errors;
    }

    /**
     * handles subscription to any list on post
     *
     * @param $object
     * @param $db
     */
    public function listsSubscription($object, $db)
    {
        $monkeyPost = Mage::getSingleton('core/session')->getMonkeyPost();
        $post = unserialize($monkeyPost);
        if (isset($post['magemonkey_force'])) {
            foreach ($post['list'] as $list) {
                $listId = $list['subscribed'];
                $this->subscribeToList($object, $db, $listId);
            }
        } elseif (isset($post['magemonkey_subscribe']) && $post['magemonkey_subscribe']) {
            $lists = explode(',', $post['magemonkey_subscribe']);
            foreach ($lists as $listId) {
                $this->subscribeToList($object, $db, $listId);
            }
            //Subscription for One Step Checkout with force subscription
        } elseif (Mage::getSingleton('core/session')->getIsOneStepCheckout() && Mage::helper('monkey')->config('checkout_subscribe') > 2 && !Mage::getSingleton('core/session')->getIsUpdateCustomer()) {
            $this->subscribeToList($object, $db);
        } elseif(!Mage::getSingleton('core/session')->getMonkeyCheckout()){
            $this->subscribeToList($object, $db, NULL, TRUE);
        }

    }

    /**
     * Subscribe to list by listId
     *
     * @param $object
     * @param $db
     * @param null $listId
     */
    public function subscribeToList($object, $db, $listId = NULL, $forceSubscribe = FALSE)
    {
        $email = $object->getEmail();
        $storeId = $object->getStoreId();
        if ($object instanceof Mage_Customer_Model_Customer) {
            $subscriber = Mage::getModel('newsletter/subscriber')
                ->setSubscriberEmail($email);
        } else {
            $subscriber = $object;
        }

        $defaultList = Mage::getStoreConfig(Ebizmarts_MageMonkey_Model_Config::GENERAL_LIST, $storeId);
        if(!$listId){
            $listId = $defaultList;
        }
        $alreadySubscribed = Mage::getSingleton('newsletter/subscriber')->loadByEmail($email);
        if ($listId == $defaultList && !Mage::getSingleton('core/session')->getIsHandleSubscriber() && !$forceSubscribe && !$alreadySubscribed) {
            $subscriber->subscribe($email);
        } else {
            $alreadyOnList = Mage::getSingleton('monkey/asyncsubscribers')->getCollection()
                ->addFieldToFilter('lists', $listId)
                ->addFieldToFilter('email', $email)
                ->addFieldToFilter('processed', 0);
            //if not in magemonkey_async_subscribers with processed 0 add list
            if (count($alreadyOnList) == 0) {
                $isConfirmNeed = FALSE;
                if (!Mage::helper('monkey')->isAdmin() &&
                    (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_CONFIRMATION_FLAG, $object->getStoreId()) == 1 && !Mage::getStoreConfig(Ebizmarts_MageMonkey_Model_Config::GENERAL_CONFIRMATION_EMAIL, $object->getStoreId()) || $forceSubscribe && Mage::getSingleton('core/session')->getMonkeyCheckout())
                ) {
                    $isConfirmNeed = TRUE;
                }

                $isOnMailChimp = Mage::helper('monkey')->subscribedToList($email, $listId);
                //if( TRUE === $subscriber->getIsStatusChanged() ){
                if ($isOnMailChimp == 1) {
                    if(Mage::getSingleton('core/session')->getIsOneStepCheckout() || Mage::getSingleton('core/session')->getMonkeyCheckout()) {
                        $mergeVars = Mage::helper('monkey')->mergeVars($object, FALSE, $listId);
                        $this->_subscribe($listId, $email, $mergeVars, 0, 1);
                    }
                    return;
                }

                if ($isConfirmNeed) {
                    $subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED);
                }

                $mergeVars = Mage::helper('monkey')->mergeVars($object, FALSE, $listId);
                $this->_subscribe($listId, $email, $mergeVars, $isConfirmNeed, $db);
            }
        }

    }

    /**
     * Subscribe to list only on MailChimp side
     *
     * @param $listId
     * @param $email
     * @param $mergeVars
     * @param $isConfirmNeed
     * @param $db
     */
    public function _subscribe($listId, $email, $mergeVars, $isConfirmNeed, $db)
    {
        if ($db) {
            if ($isConfirmNeed) {
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('monkey')->__('Confirmation request will be sent soon.'));
            }
            $subs = Mage::getModel('monkey/asyncsubscribers');
            $subs->setMapfields(serialize($mergeVars))
                ->setEmail($email)
                ->setLists($listId)
                ->setConfirm($isConfirmNeed)
                ->setProcessed(0)
                ->setCreatedAt(Mage::getModel('core/date')->gmtDate())
                ->save();
        } else {
            if ($isConfirmNeed) {
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('monkey')->__('Confirmation request has been sent.'));
            }
            Mage::getSingleton('monkey/api')->listSubscribe($listId, $email, $mergeVars, 'html', $isConfirmNeed, TRUE);
        }
    }

    /**
     * Handle subscription on customer account
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param string $guestEmail
     * @return void
     */
    public function handlePost($request, $guestEmail)
    {
        //<state> param is an html serialized field containing the default form state
        //before submission, we need to parse it as a request in order to save it to $odata and process it
//        parse_str($request->getPost('state'), $odata);
        $m = explode('&',$request->getPost('state'));
        $odata = array();
        $list = array();
        foreach($m as $v) {

            $g = explode('=',$v);
            $u = explode('%5B',$v);
            if($u[0] == 'list') {
                $suffixListId = $u[1];
                $listId = substr($u[1], 0, (strlen($suffixListId)-3));
                $list[$listId] = array();
                $listIdArray = $list[$listId];
                $tail = explode('%5D',$u[2]);
                $subscribed = $tail[0];
                $listIdArray[$subscribed] = $g[1];
                $list[$listId] = $listIdArray;
                $odata['list'] = $list;
            }else {
                $odata[$g[0]] = $g[1];
            }
        }
        $lists = $request->getPost('list', array());


        $curlists = (TRUE === array_key_exists('list', $odata)) ? $odata['list'] : array();
        $defaultList = $this->getDefaultList(Mage::app()->getStore());

        $api = Mage::getSingleton('monkey/api');
        $loggedIn = Mage::helper('customer')->isLoggedIn();
        if ($loggedIn) {
            $customer = Mage::helper('customer')->getCustomer();
        } else {
            $customer = Mage::registry('mc_guest_customer');
        }
        $email = $guestEmail ? $guestEmail : $customer->getEmail();
        if (!empty($curlists)) {
            //Handle Unsubscribe and groups update actions
            foreach ($curlists as $listId => $list) {

                if (FALSE === array_key_exists($listId, $lists)) {
                    //Unsubscribe Email

                    $item = Mage::getModel('monkey/monkey')->loadByEmail($email);
                    if (!$item->getId()) {
                        $item = Mage::getModel('newsletter/subscriber')
                            ->loadByEmail($email);
                    }
                    if ($item->getSubscriberEmail()) {
                        $item->unsubscribe();
                    }

                    //Unsubscribe Email
                    $alreadyOnDb = Mage::getSingleton('monkey/asyncsubscribers')->getCollection()
                        ->addFieldToFilter('lists', $listId)
                        ->addFieldToFilter('email', $email)
                        ->addFieldToFilter('processed', 0);

                    if(count($alreadyOnDb) > 0) {
                        foreach ($alreadyOnDb as $listToDelete) {
                            $toDelete = Mage::getModel('monkey/asyncsubscribers')->load($listToDelete->getId());
                            $toDelete->delete();
                        }
                        Mage::getSingleton('core/session')
                            ->addSuccess($this->__('You have been removed from Newsletter.'));
                    } else {
                        $api->listUnsubscribe($listId, $email);
                        Mage::getSingleton('core/session')
                            ->addSuccess($this->__('You have been removed from Newsletter.'));
                    }

                } else {

                    $groupings = $lists[$listId];
                    unset($groupings['subscribed']);
                    $customerLists = $api->listMemberInfo($listId, $email);
                    $customerLists = isset($customerLists['data'][0]['merges']['GROUPINGS']) ? $customerLists['data'][0]['merges']['GROUPINGS'] : array();

                    foreach ($customerLists as $clkey => $cl) {
                        if (!isset($groupings[$cl['id']])) {
                            $groupings[$cl['id']][] = '';
                        }
                    }

                    $customer->setMcListId($listId);
                    $customer->setListGroups($groupings);
                    $mergeVars = Mage::helper('monkey')->getMergeVars($customer);

                    //Handle groups update
                    $api->listUpdateMember($listId, $email, $mergeVars);
                    Mage::getSingleton('core/session')
                        ->addSuccess($this->__('Your profile has been updated!'));

                }

            }

        }

        //Subscribe to new lists
        if (is_array($lists) && is_array($curlists)) {
            $subscribe = array_diff_key($lists, $curlists);
            if (!empty($subscribe)) {
                foreach ($subscribe as $listId => $slist) {
                    if (!isset($slist['subscribed'])) {
                        continue;
                    }

                    $groupings = $lists[$listId];
                    unset($groupings['subscribed']);
                    if ($defaultList == $listId) {
                        $subscriber = Mage::getModel('newsletter/subscriber');
                        $subscriber->setListGroups($groupings);
                        $subscriber->setMcListId($listId);
                        $subscriber->setMcStoreId(Mage::app()->getStore()->getId());
                        $subscriber->subscribe($email);
                    } else {
                        $customer->setListGroups($groupings);
                        $customer->setMcListId($listId);
                        $subscriber = Mage::getModel('newsletter/subscriber')
                            ->setSubscriberEmail($email);
                        $this->subscribeToList($subscriber, 0, $listId);

                    }
                }
            }
        }
    }

    public function getThisStore()
    {
        $store = Mage::app()->getStore();

        $configscope = Mage::app()->getRequest()->getParam('store');
        if ($configscope && ($configscope !== 'undefined')) {
            $store = $configscope;
        }
        return $store;
    }

    public function getCanShowCampaignJs()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        if (Mage::getStoreConfig(Ebizmarts_MageMonkey_Model_Config::ECOMMERCE360_ACTIVE, $storeId) && Mage::helper('monkey')->canMonkey()) {
            return 'ebizmarts/magemonkey/campaignCatcher.js';
        }
    }
}
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Model_Observer
{
    protected $_attributes = null;
    protected $_permissibleActions = array('index', 'grid', 'exportCsv', 'exportExcel');
    protected $_exportActions = array('exportCsv', 'exportExcel');
    protected $_controllerNames = array('sales_', 'orderspro_');
    protected $_otherClasses = array('Mage_Adminhtml_Block_Sales_Order_Grid',
                                     'EM_DeleteOrder_Block_Adminhtml_Sales_Order_Grid',
                                     'MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid',
                                     'Excellence_Salesgrid_Block_Adminhtml_Sales_Order_Grid',
                                     'AW_Ordertags_Block_Adminhtml_Sales_Order_Grid');
    
    protected function _prepareOrderAttributes()
    {
        if (Mage::app()->getRequest()->getPost('amorderattr'))
        {
            $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
            $orderAttributes = $session->getAmastyOrderAttributes();
            if (!$orderAttributes)
            {
                $orderAttributes = array();
            }
            if (!Mage::registry('attributeClear')){
                $orderAttributes = array_merge($orderAttributes, Mage::app()->getRequest()->getPost('amorderattr'));
            }
            $session->setAmastyOrderAttributes($orderAttributes);
        }
    }
    
    public function onSalesQuoteSaveAfter($observer)
    {
        $this->_prepareOrderAttributes();
    }
    
    public function onCheckoutTypeOnepageSaveOrderAfter($observer)
    {
        if(!Mage::registry('amorderattr_saved')){
            $this->_prepareOrderAttributes();
            $order = $observer->getOrder();
            $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
            $orderAttributes = $session->getAmastyOrderAttributes();
            $attributes = Mage::getModel('amorderattr/attribute');
            $attributes->load($order->getId(), 'order_id');
            if ($attributes->getId())
            {
                return false;
            }
           
            if (is_array($orderAttributes) && !empty($orderAttributes))
            {
                $collection = Mage::getModel('eav/entity_attribute')->getCollection();
                $collection->addFieldToFilter('is_visible_on_front', 1);
                $collection->addFieldToFilter('entity_type_id',Mage::getModel('eav/entity')->setType('order')->getTypeId());
                $attributesList = $collection->load();
                
                foreach ($attributesList as $attribute)
                {
                   
                    if ('checkboxes' == $attribute->getFrontend()->getInputType())
                    {
                       if (array_key_exists($attribute->getAttributeCode(), $orderAttributes)) {
                           $orderAttributes[$attribute->getAttributeCode()] = implode(',', $orderAttributes[$attribute->getAttributeCode()]);
                       }
                       
                    }
                    if ('radios' == $attribute->getFrontend()->getInputType()){
                        $orderAttributes[$attribute->getAttributeCode()] = $orderAttributes[$attribute->getAttributeCode()][0];
                    }
                }
                $attributes->addData($orderAttributes);  
            }
            
            $attributes->setData('order_id', $order->getId());
            $this->_applyDefaultValues($order, $attributes);
            $attributes->save();
            Mage::register('amorderattr_saved', true, true);
            $session->setAmastyOrderAttributes(array());
            Mage::register('attributeClear',true, true);
        }
    }

    public function onSalesOrderSaveBefore($observer)
    {
        if (false !== strpos(Mage::app()->getRequest()->getControllerName(), 'sales_order')
            && 'save' == Mage::app()->getRequest()->getActionName()
            && !Mage::registry('amorderattr_saved')
            && $orderAttributes = Mage::app()->getRequest()->getPost('amorderattr')) {
            Mage::getSingleton('adminhtml/session')->setAmastyOrderAttributes($orderAttributes);
        }
    }
    
    // this will be used when creating/editing order in the backend
    public function onSalesOrderSaveAfter($observer)
    {
        if (false !== strpos(Mage::app()->getRequest()->getControllerName(), 'sales_order') && 'save' == Mage::app()->getRequest()->getActionName() && !Mage::registry('amorderattr_saved'))
        {
            $order = $observer->getOrder();
            $orderAttributes = Mage::app()->getRequest()->getPost('amorderattr');
            
            $attributes = Mage::getModel('amorderattr/attribute');
            $attributes->load($order->getId(), 'order_id');
            if ($attributes->getId())
            {
                return false;
            }
            
            if (is_array($orderAttributes) && !empty($orderAttributes))
            {
                foreach ($orderAttributes as $key => $val)
                {
                    if ($val)
                    {
                        if (is_array($val)){
                           $val=implode(', ',$val);
                        }
                        $attributes->setData($key, $val);
                    }
                }
            }
           
            $attributes->setData('order_id', $order->getId());
            $this->_applyDefaultValues($order, $attributes); // $attributes might be modified in that function
            $attributes->save();
            Mage::register('amorderattr_saved', true, true);
            Mage::getSingleton('adminhtml/session')->setAmastyOrderAttributes(null);
        }
    }
    
    protected function _applyDefaultValues($order, $attributes)
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                        ->setEntityTypeFilter(Mage::getModel('eav/entity')->setType('order')->getTypeId());
                             
        $collection->getSelect()
            ->where('main_table.is_user_defined = ?', 1)
            ->where('main_table.apply_default = ?', 1);
            
        if ($collection->getSize() > 0)
        {
            foreach ($collection as $attributeToApply)
            {
                if (!$attributes->getData($attributeToApply->getAttributeCode()) && $attributeToApply->getDefaultValue())
                {
                   $attributes->setData($attributeToApply->getAttributeCode(), $attributeToApply->getDefaultValue());
                }
            }
        }
    }
    
    protected function _getAttributes()
    {
        if (is_null($this->_attributes)) {
            $attributes = Mage::getModel('eav/entity_attribute')->getCollection();
            $attributes->addFieldToFilter('entity_type_id', Mage::getModel('eav/entity')->setType('order')->getTypeId());
            $attributes->addFieldToFilter('show_on_grid', 1);
            $this->_attributes = $attributes;
        }
        return $this->_attributes;
    }
    
    protected function _prepareCollection($collection, $place = 'order', $column = 'entity_id')
    {
        if ($this->_isJoined($collection->getSelect()->getPart('from')))
            return $collection;
            
        if (!$this->_isControllerName($place))
            return $collection;
        
        $attributes = $this->_getAttributes();
        if ($attributes->getSize()) {
            $fields = array();
            foreach ($attributes as $attribute) {
                $fields[] = $attribute->getAttributeCode();
            }
            
            $isVersion14 = ! Mage::helper('ambase')->isVersionLessThan(1,4);
            
            $alias = $isVersion14 ? 'main_table' : 'e';
            $collection->getSelect()
                       ->joinLeft(
                            array('custom_attributes' => Mage::getModel('amorderattr/attribute')->getResource()->getTable('amorderattr/order_attribute')),
                            "$alias.$column = custom_attributes.order_id",
                            $fields
                       );
        }
        return $collection;
    }
    
    protected function _isControllerName($place)
    {
        $found = false;
        foreach ($this->_controllerNames as $controllerName) {
            if (false !== strpos(Mage::app()->getRequest()->getControllerName(), $controllerName . $place)) {
                $found = true;
            }
        }
        return $found;
    }
    
    protected function _prepareColumns(&$grid, $export = false, $place = 'order', $after = 'grand_total')
    {
        if (!$this->_isControllerName($place) || 
            !in_array(Mage::app()->getRequest()->getActionName(), $this->_permissibleActions) )
            return $grid;
        
        $attributes = $this->_getAttributes();
        if ($attributes->getSize() > 0) {
            foreach ($attributes as $attribute) {
                $column = array();
                switch ($attribute->getFrontendInput())
                {
                    case 'date':
                            if ('time' == $attribute->getNote())
                            {
                                $column = array(
                                    'header'       => Mage::helper('amorderattr')->__($attribute->getFrontend()->getLabel()),
                                    'type'         => 'datetime',
                                    'align'        => 'center',
                                    'index'        => $attribute->getAttributeCode(),
                                    'filter_index' => 'custom_attributes.'.$attribute->getAttributeCode(),
                                    'gmtoffset'    => false,
                                    'renderer'     => 'amorderattr/adminhtml_order_grid_renderer_datetime',
                                );
                            } else 
                            {
                                $column = array(
                                    'header'       => Mage::helper('amorderattr')->__($attribute->getFrontend()->getLabel()),
                                    'type'         => 'date',
                                    'align'        => 'center',
                                    'index'        => $attribute->getAttributeCode(),
                                    'filter_index' => 'custom_attributes.'.$attribute->getAttributeCode(),
                                    'gmtoffset'    => false,
                                    'renderer'     => 'amorderattr/adminhtml_order_grid_renderer_datetime',
                                );
                            }
                            
                            break;
                        case 'text':
                        case 'textarea':
                            $column = array(
                                'header'       => Mage::helper('amorderattr')->__($attribute->getFrontend()->getLabel()),
                                'index'        => $attribute->getAttributeCode(),
                                'filter_index' => 'custom_attributes.'.$attribute->getAttributeCode(),
                                'filter'       => 'adminhtml/widget_grid_column_filter_text',
                                'sortable'     => true,
                                'renderer'     => 'amorderattr/adminhtml_order_grid_renderer_text' . ($export ? '_export' : ''),
                            );
                            break;
                        case 'boolean':
                            $options = array();
                            foreach ($attribute->getSource()->getAllOptions(false, true) as $option)
                            {
                                $options[$option['value']] = $option['label'];
                            }
                            $column = array(
                                'header'       =>  Mage::helper('amorderattr')->__($attribute->getFrontend()->getLabel()),
                                'index'        =>  $attribute->getAttributeCode(),
                                'align'        => 'center',
                                'filter_index' => 'custom_attributes.'.$attribute->getAttributeCode(),
                                'type'         => 'options',
                                'options'      =>  $options,
                                'filter'       => 'adminhtml/widget_grid_column_filter_select',                                
                            );                         
                            break;                                                    
                        case 'select':
                            $options = array();
                            foreach ($attribute->getSource()->getAllOptions(false, true) as $option) {
                                $options[$option['value']] = $option['label'];
                            }
                            $column = array(
                                'header'       =>  Mage::helper('amorderattr')->__($attribute->getFrontend()->getLabel()),
                                'index'        =>  $attribute->getAttributeCode(),
                                'filter_index' => 'custom_attributes.'.$attribute->getAttributeCode(),
                                'align'        => 'center',
                                'type'         => 'options',
                                'options'      =>  $options,
                            );
                            break;
                        case 'multiselect':
                            $options = array();
                            foreach ($attribute->getSource()->getAllOptions(false, true) as $option) {
                                $options[$option['value']] = $option['label'];
                            }
                            $column = array(
                                'header'       =>  Mage::helper('amorderattr')->__($attribute->getFrontend()->getLabel()),
                                'index'        =>  $attribute->getAttributeCode(),
                                'align'        => 'center',
                                'filter_index' => 'custom_attributes.'.$attribute->getAttributeCode(),
                                'type'         => 'options',
                                'options'      =>  $options,
                            );
                            break;
                         case 'checkboxes':
                            $options = array();
                            foreach ($attribute->getSource()->getAllOptions(false, true) as $option)
                            {
                                $options[$option['value']] = $option['label'];
                            }
                            $column = array(
                                'header'       =>  Mage::helper('amorderattr')->__($attribute->getFrontend()->getLabel()),
                                'type'         => 'options',
                                'align'        => 'center',
                                'options'      =>  $options,
                                'index'        =>  $attribute->getAttributeCode(),
                                'filter_index' => 'custom_attributes.'.$attribute->getAttributeCode(),
                                'filter'       => 'amorderattr/adminhtml_order_grid_filter_checkboxes',
                                'renderer'     => 'amorderattr/adminhtml_order_grid_renderer_checkboxes',
                            );
                            break;
                    case 'radios':
                        $options = array();
                        foreach ($attribute->getSource()->getAllOptions(false, true) as $option)
                        {
                            $options[$option['value']] = $option['label'];
                        }
                        $column = array(
                            'header'       =>  Mage::helper('amorderattr')->__($attribute->getFrontend()->getLabel()),
                            'type'         => 'options',
                            'align'        => 'center',
                            'options'      =>  $options,
                            'index'        =>  $attribute->getAttributeCode(),
                            'filter_index' => 'custom_attributes.'.$attribute->getAttributeCode()
                        );
                        break;
                }
                $grid->addColumnAfter($column['index'], $column, $after);
                $after = $column['index'];
            }
        }
        return $grid;
    }
    
    public function onSalesOrderGridCollectionLoadBefore($observer)
    {
        $collection = $this->_prepareCollection($observer->getOrderGridCollection());
    }
    
    public function onSalesOrderInvoiceGridCollectionLoadBefore($observer)
    {
        if (!Mage::getStoreConfig('amorderattr/invoices_shipments/invoice_grid'))
            return;
        
        $collection = $this->_prepareCollection($observer->getOrderInvoiceGridCollection(), 'invoice', 'order_id');
    }
    
    public function onSalesOrderShipmentGridCollectionLoadBefore($observer)
    {
        if (!Mage::getStoreConfig('amorderattr/invoices_shipments/shipment_grid')) 
            return;
            
        $collection = $this->_prepareCollection($observer->getOrderShipmentGridCollection(), 'shipment', 'order_id');
    }
    
    protected function _isInstanceOf($block)
    {
        $found = false;
        foreach ($this->_otherClasses as $className) {
            if ($block instanceof $className) {
                $found = true;
                break;
            }
        }
        return $found;
    }
    
    public function onCoreLayoutBlockCreateAfter($observer)
    {
        $block = $observer->getBlock();
        // Order Grid
        if ($this->_isInstanceOf($block)) {
            $this->_prepareColumns($block, in_array(Mage::app()->getRequest()->getActionName(), $this->_exportActions));
        }
        /*if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid || $block instanceof EM_DeleteOrder_Block_Adminhtml_Sales_Order_Grid) {
            $this->_prepareColumns($block, in_array(Mage::app()->getRequest()->getActionName(), $this->_exportActions));
        }*/
        // Invoice Grid
        if ($block instanceof Mage_Adminhtml_Block_Sales_Invoice_Grid && Mage::getStoreConfig('amorderattr/invoices_shipments/invoice_grid')) {
            $this->_prepareColumns($block, in_array(Mage::app()->getRequest()->getActionName(), $this->_exportActions), 'invoice');
        }
        // Shipment Grid
        if ($block instanceof Mage_Adminhtml_Block_Sales_Shipment_Grid && Mage::getStoreConfig('amorderattr/invoices_shipments/shipment_grid')) {
            $this->_prepareColumns($block, in_array(Mage::app()->getRequest()->getActionName(), $this->_exportActions), 'shipment', 'total_qty');
        }
    }
    
    protected function _isJoined($from)
    {
        $found = false;
        foreach ($from as $alias => $data) {
            if ('custom_attributes' === $alias) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    protected function _prepareBackendHtml($html)
    {
        if (false === strpos($html, 'BEGIN `Amasty: Order Attributes`')) {
            $list = Mage::app()->getLayout()->createBlock('amorderattr/adminhtml_order_attribute_view_list');
            if (false === strpos($html, 'BEGIN `Amasty: Delivery Date`')) {
                $html = preg_replace('@<div class="entry-edit">(\s*)<div class="entry-edit-head">(\s*)(.*?)head-products@', 
                                 $list->toHtml() .'<div class="entry-edit"><div class="entry-edit-head">$3head-products', $html, 1);
            } else {
                $pos = strpos($html, '<!-- BEGIN `Amasty: Delivery Date` -->');
                $html = substr_replace($html, $list->toHtml(), $pos-1, 0);
            }
        }
        return $html;
    }
    
    protected function _prepareFrontendHtml($transport, $fields, $where = '<div class="buttons-set"', $begin = true)
    {
        $html = $transport->getHtml();
        if (false === strpos($html, 'amorderattr')) {
            if ($begin) {
                $pos = strpos($html, $where);
                $pos--;
            } else {
                $pos = strrpos($html, $where);
                $pos += 7;
            }
            $insert = Mage::helper('amorderattr')->fields($fields);
            if ('review' == $fields
                && Mage::helper('core')->isModuleEnabled('Amasty_Scheckout')) {
                $insert = str_replace('<ul class="form-list">', '', $insert);
                $insert = str_replace('<li class="fields">', '', $insert);
                $insert = str_replace('</li>', '', $insert);
                $insert = str_replace('</ul>', '', $insert);
                $insert = str_replace('<form id="form_review">', '', $insert);
                $insert = str_replace('</form>', '', $insert);
            }
            $html = substr_replace($html, $insert, $pos, 0);
            $transport->setHtml($html);
        }
        return $html;
    }
    
    public function handleBlockOutput($observer)
    {
        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        
        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Info) {
            $html = $this->_prepareBackendHtml($html);
        }
        
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Invoice_View) {
            if (Mage::getStoreConfig('amorderattr/invoices_shipments/invoice_view')) {
                $html = $this->_prepareBackendHtml($html);
            }
        }
        
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Shipment_View) {
            if (Mage::getStoreConfig('amorderattr/invoices_shipments/shipment_view')) {
                $html = $this->_prepareBackendHtml($html);
            }
        }

        $blockClass = Mage::getConfig()->getBlockClassName('checkout/onepage_billing');
        //if ($block instanceof Mage_Checkout_Block_Onepage_Billing) {
        if ($blockClass == get_class($block)) {
            if (Mage::helper('core')->isModuleEnabled('Amasty_Scheckout')) {
                $html = $this->_prepareFrontendHtml($transport, 'billing', '</div>', false);
            } else {
                $html = $this->_prepareFrontendHtml($transport, 'billing');
            }
        }

        $blockClass = Mage::getConfig()->getBlockClassName('checkout/onepage_shipping');
        //if ($block instanceof Mage_Checkout_Block_Onepage_Shipping) {
        if ($blockClass == get_class($block)) {
            if (Mage::helper('core')->isModuleEnabled('Amasty_Scheckout')) {
                $html = $this->_prepareFrontendHtml($transport, 'shipping', '</div>', false);
            } else {
                $html = $this->_prepareFrontendHtml($transport, 'shipping');
            }
        }

        $blockClass = Mage::getConfig()->getBlockClassName('checkout/onepage_shipping_method');
        //if ($block instanceof Mage_Checkout_Block_Onepage_Shipping_Method) {
        if ($blockClass == get_class($block)) {
            if (Mage::helper('core')->isModuleEnabled('Amasty_Scheckout')) {
                $html = $this->_prepareFrontendHtml($transport, 'shipping_method', '</div>', false);
            } else {
                $html = $this->_prepareFrontendHtml($transport, 'shipping_method');
            }
        }

        $blockClass = Mage::getConfig()->getBlockClassName('checkout/onepage_payment');
        //if ($block instanceof Mage_Checkout_Block_Onepage_Payment) {
        if ($blockClass == get_class($block)) {
            if (Mage::helper('core')->isModuleEnabled('Amasty_Scheckout')) {
                $html = $this->_prepareFrontendHtml($transport, 'payment', '</div>', false);
            } else {
                $html = $this->_prepareFrontendHtml($transport, 'payment', '</form>');
            }
        }

        $blockClass = Mage::getConfig()->getBlockClassName('checkout/onepage_review_info');
        //if ($block instanceof Mage_Checkout_Block_Onepage_Review_Info) {
        if ($blockClass == get_class($block)) {
            $html = $this->_prepareFrontendHtml($transport, 'review', '</tfoot>');
        }

        if ($block instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available){
            $html .= '<script>if (typeof(amOrderattrConditionObj) != "undefined"){amOrderattrConditionObj.check();}</script>';
        }

        $blockClass = Mage::getConfig()->getBlockClassName('sales/order_print');
        if ($blockClass == get_class($block)) {
            $pos = strripos($html, '<h2');
            $insert = Mage::app()->getLayout()->createBlock('amorderattr/sales_order_print_attributes');
            $insert->setOrder($block->getOrder());
            $html = substr_replace($html, $insert->toHtml(), $pos-1, 0);
        }
        
        // Order View Page
        $blockClass = Mage::getConfig()->getBlockClassName('sales/order_info');
        if ($blockClass == get_class($block)) {
            $deliveryDate = false;
            if (false !== strpos($html, '<!-- BEGIN Order View `Amasty: Delivery Date` -->')) {
                $pos = strpos($html, '<!-- BEGIN Order View `Amasty: Delivery Date` -->');
                $pos = strpos($html, '<div class="col-1">', $pos);
                $deliveryDate = true;
            } else {
                $pos = strpos($html, '<div class="order-items');
            }
            $insert = Mage::app()->getLayout()->createBlock('amorderattr/sales_order_view_attributes');
            if ($insert) {
                if ($deliveryDate) {
                    $html = substr_replace($html, $insert->toHtml() . '<div class="col-2">', $pos, 19);
                } else {
                    $html = substr_replace($html, '<div class="col2-set order-info-box">' . $insert->toHtml() . '</div>', $pos - 1, 0);
                }
            }
        }
        
        $transport->setHtml($html);
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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base widget class
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget extends Mage_Adminhtml_Block_Template
{
    public function getId()
    {
        if ($this->getData('id')===null) {
            $this->setData('id', Mage::helper('core')->uniqHash('id_'));
        }
        return $this->getData('id');
    }

    public function getHtmlId()
    {
        return $this->getId();
    }

    /**
     * Get current url
     *
     * @param array $params url parameters
     * @return string current url
     */
    public function getCurrentUrl($params = array())
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }
        return $this->getUrl('*/*/*', $params);
    }

    protected function _addBreadcrumb($label, $title=null, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
    }

    /**
     * Create buttonn and return its html
     *
     * @param string $label
     * @param string $onclick
     * @param string $class
     * @param string $id
     * @return string
     */
    public function getButtonHtml($label, $onclick, $class='', $id=null) {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => $label,
                'onclick'   => $onclick,
                'class'     => $class,
                'type'      => 'button',
                'id'        => $id,
            ))
            ->toHtml();
    }

    public function getGlobalIcon()
    {
        return '<img src="'.$this->getSkinUrl('images/fam_link.gif').'" alt="'.$this->__('Global Attribute').'" title="'.$this->__('This attribute shares the same value in all the stores').'" class="attribute-global"/>';
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
 * @category    Varien
 * @package     Varien_Data
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form field renderer
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element);
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
 * @package     Mage_Widget
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget Data helper
 *
 * @category   Mage
 * @package    Mage_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Helper_Data extends Mage_Core_Helper_Abstract
{
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
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_ConfigurableSwatches_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_BASE = 'configswatches';
    const CONFIG_PATH_ENABLED = 'configswatches/general/enabled';
    const CONFIG_PATH_SWATCH_ATTRIBUTES = 'configswatches/general/swatch_attributes';
    const CONFIG_PATH_LIST_SWATCH_ATTRIBUTE = 'configswatches/general/product_list_attribute';


    protected $_enabled = null;
    protected $_configAttributeIds = null;

    /**
     * Is the extension enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (is_null($this->_enabled)) {
            $this->_enabled = (
                (bool) Mage::getStoreConfig(self::CONFIG_PATH_ENABLED)
                && Mage::helper('configurableswatches/productlist')->getSwatchAttribute()
            );
        }
        return $this->_enabled;
    }

    /**
     * Return the formatted hyphenated string
     *
     * @param string $str
     * @return string
     */
    public function getHyphenatedString($str)
    {
        $result = false;
        if (function_exists('iconv')) {
            $result = @iconv('UTF-8', 'ASCII//TRANSLIT', $str); // will issue a notice on failure, we handle failure
        }

        if (!$result) {
            $result = dechex(crc32(self::normalizeKey($str)));
        }

        return preg_replace('/([^a-z0-9]+)/', '-', self::normalizeKey($result));
    }

    /**
     * Trims and lower-cases strings used as array indexes in json and for string matching in a
     * multi-byte compatible way if the mbstring module is available.
     *
     * @param $key
     * @return string
     */
    public static function normalizeKey($key) {
        if (function_exists('mb_strtolower')) {
            return trim(mb_strtolower($key, 'UTF-8'));
        }
        return trim(strtolower($key));
    }

    /**
     * Get list of attributes that should use swatches
     *
     * @return array
     */
    public function getSwatchAttributeIds()
    {
        if (is_null($this->_configAttributeIds)) {
            $this->_configAttributeIds = explode(',', Mage::getStoreConfig(self::CONFIG_PATH_SWATCH_ATTRIBUTES));
        }
        return $this->_configAttributeIds;
    }

    /**
     * Determine if an attribute should be a swatch
     *
     * @param int|Mage_Eav_Model_Attribute $attr
     * @return bool
     */
    public function attrIsSwatchType($attr)
    {
        if ($attr instanceof Varien_Object) {
            $attr = $attr->getId();
        }
        $configAttrs = $this->getSwatchAttributeIds();
        return in_array($attr, $configAttrs);
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
 * Date conversion model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Date
{
    /**
     * Current config offset in seconds
     *
     * @var int
     */
    private $_offset = 0;

    /**
     * Current system offset in seconds
     *
     * @var int
     */
    private $_systemOffset = 0;

    /**
     * Init offset
     *
     */
    public function __construct()
    {
        $this->_offset = $this->calculateOffset($this->_getConfigTimezone());
        $this->_systemOffset = $this->calculateOffset();
    }

    /**
     * Gets the store config timezone
     *
     * @return string
     */
    protected function _getConfigTimezone()
    {
        return Mage::app()->getStore()->getConfig('general/locale/timezone');
    }

    /**
     * Calculates timezone offset
     *
     * @param  string $timezone
     * @return int offset between timezone and gmt
     */
    public function calculateOffset($timezone = null)
    {
        $result = true;
        $offset = 0;

        if (!is_null($timezone)){
            $oldzone = @date_default_timezone_get();
            $result = date_default_timezone_set($timezone);
        }

        if ($result === true) {
            $offset = (int)date('Z');
        }

        if (!is_null($timezone)){
            date_default_timezone_set($oldzone);
        }

        return $offset;
    }

    /**
     * Forms GMT date
     *
     * @param  string $format
     * @param  int|string $input date in current timezone
     * @return string
     */
    public function gmtDate($format = null, $input = null)
    {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $date = $this->gmtTimestamp($input);

        if ($date === false) {
            return false;
        }

        $result = date($format, $date);
        return $result;
    }

    /**
     * Converts input date into date with timezone offset
     * Input date must be in GMT timezone
     *
     * @param  string $format
     * @param  int|string $input date in GMT timezone
     * @return string
     */
    public function date($format = null, $input = null)
    {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $result = date($format, $this->timestamp($input));
        return $result;
    }

    /**
     * Forms GMT timestamp
     *
     * @param  int|string $input date in current timezone
     * @return int
     */
    public function gmtTimestamp($input = null)
    {
        if (is_null($input)) {
            return gmdate('U');
        } else if (is_numeric($input)) {
            $result = $input;
        } else {
            $result = strtotime($input);
        }

        if ($result === false) {
            // strtotime() unable to parse string (it's not a date or has incorrect format)
            return false;
        }

        $date      = Mage::app()->getLocale()->date($result);
        $timestamp = $date->get(Zend_Date::TIMESTAMP) - $date->get(Zend_Date::TIMEZONE_SECS);

        unset($date);
        return $timestamp;

    }

    /**
     * Converts input date into timestamp with timezone offset
     * Input date must be in GMT timezone
     *
     * @param  int|string $input date in GMT timezone
     * @return int
     */
    public function timestamp($input = null)
    {
        if (is_null($input)) {
            $result = $this->gmtTimestamp();
        } else if (is_numeric($input)) {
            $result = $input;
        } else {
            $result = strtotime($input);
        }

        $date      = Mage::app()->getLocale()->date($result);
        $timestamp = $date->get(Zend_Date::TIMESTAMP) + $date->get(Zend_Date::TIMEZONE_SECS);

        unset($date);
        return $timestamp;
    }

    /**
     * Get current timezone offset in seconds/minutes/hours
     *
     * @param  string $type
     * @return int
     */
    public function getGmtOffset($type = 'seconds')
    {
        $result = $this->_offset;
        switch ($type) {
            case 'seconds':
            default:
                break;

            case 'minutes':
                $result = $result / 60;
                break;

            case 'hours':
                $result = $result / 60 / 60;
                break;
        }
        return $result;
    }

    /**
     * Deprecated since 1.1.7
     */
    public function checkDateTime($year, $month, $day, $hour = 0, $minute = 0, $second = 0)
    {
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        foreach (array('hour' => 23, 'minute' => 59, 'second' => 59) as $var => $maxValue) {
            $value = (int)$$var;
            if (($value < 0) || ($value > $maxValue)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Deprecated since 1.1.7
     */
    public function parseDateTime($dateTimeString, $dateTimeFormat)
    {
        // look for supported format
        $isSupportedFormatFound = false;

        $formats = array(
            // priority is important!
            '%m/%d/%y %I:%M' => array(
                '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2})/',
                array('y' => 3, 'm' => 1, 'd' => 2, 'h' => 4, 'i' => 5)
            ),
            'm/d/y h:i' => array(
                '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2})/',
                array('y' => 3, 'm' => 1, 'd' => 2, 'h' => 4, 'i' => 5)
            ),
            '%m/%d/%y' => array('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2})/', array('y' => 3, 'm' => 1, 'd' => 2)),
            'm/d/y' => array('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2})/', array('y' => 3, 'm' => 1, 'd' => 2)),
        );

        foreach ($formats as $supportedFormat => $regRule) {
            if (false !== strpos($dateTimeFormat, $supportedFormat, 0)) {
                $isSupportedFormatFound = true;
                break;
            }
        }
        if (!$isSupportedFormatFound) {
            Mage::throwException(Mage::helper('core')->__('Date/time format "%s" is not supported.', $dateTimeFormat));
        }

        // apply reg rule to found format
        $regex = array_shift($regRule);
        $mask  = array_shift($regRule);
        if (!preg_match($regex, $dateTimeString, $matches)) {
            Mage::throwException(Mage::helper('core')->__('Specified date/time "%1$s" do not match format "%2$s".', $dateTimeString, $dateTimeFormat));
        }

        // make result
        $result = array();
        foreach (array('y', 'm', 'd', 'h', 'i', 's') as $key) {
            $value = 0;
            if (isset($mask[$key]) && isset($matches[$mask[$key]])) {
                $value = (int)$matches[$mask[$key]];
            }
            $result[] = $value;
        }

        // make sure to return full year
        if ($result[0] < 100) {
            $result[0] = 2000 + $result[0];
        }

        return $result;
    }
}
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */
class Amasty_Deliverydate_Helper_Data extends Mage_Core_Helper_Abstract
{
    private static $_dictionary = array(
        'dd'   => '%d',
        'd'    => '%j',
        'MM'   => '%m',
        'M'    => '%n',
        'yyyy' => '%Y',
        'yy'   => '%y',
    );
    
    public function getYears($year = 0, $isGrid = false, $model = 'holidays', $column = 'from_year')
    {
        $years = array(0 => $this->__('Each year'));
        if ($isGrid) { // dropdown for grid
            $collection = Mage::getModel('amdeliverydate/' . $model)->getCollection();
            foreach ($collection as $item) {
                if ('holidays' == $model) {
                    if ($item->getYear() && !in_array($item->getYear(), $years)) {
                        $years[$item->getYear()] = $item->getYear();
                    }
                } else {
                    if ($item->getData($column) && !in_array($item->getData($column), $years)) {
                        $years[$item->getData($column)] = $item->getData($column);
                    }
                }
            }
        } else { // dropdown for edit page
            $curYear = date('Y');
            if ($year && $year < $curYear) {
                $years[$year] = $year;
            }
            for ($i = 0; $i <= 4; $i++) {
                $years[$curYear + $i] = $curYear + $i;
            }
        }
        return $years;
    }
    
    public function getMonths()
    {
        return array(
            0  => $this->__('Each month'),
            1  => $this->__('January'),
            2  => $this->__('February'),
            3  => $this->__('March'),
            4  => $this->__('April'),
            5  => $this->__('May'),
            6  => $this->__('June'),
            7  => $this->__('July'),
            8  => $this->__('August'),
            9  => $this->__('September'),
            10 => $this->__('October'),
            11 => $this->__('November'),
            12 => $this->__('December')
        );
    }
    
    public function getDays()
    {
        $days = array();
        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }
        return $days;
    }
    
    public function getAlignForColumn($pointer)
    {
        $align = '';
        switch ($pointer) {
            case '0':
                $align = 'right';
                break;
            case '1':
                $align = 'center';
                break;
            case '2':
                $align = 'left';
                break;
        }
        return $align;
    }
    
    public function whatShow($place = 'order_grid', $storeId = 0, $include = 'show')
    {
        $fields = array();
        
        if (in_array($place, explode(',', Mage::getStoreConfig('amdeliverydate/date_field/' . $include, $storeId)))) {
            $fields[] = 'date';
        }
        if (in_array($place, explode(',', Mage::getStoreConfig('amdeliverydate/time_field/' . $include, $storeId)))) {
            $fields[] = 'time';
        }
        if (in_array($place, explode(',', Mage::getStoreConfig('amdeliverydate/comment_field/' . $include, $storeId)))) {
            $fields[] = 'comment';
        }
        return $fields;
    }
    
    public function getTIntervals($currentStore = 0)
    {
        $tIntervals = array('' => '');
        
        $collection = Mage::getModel('amdeliverydate/tinterval')->getCollection();
        $collection->getSelect()->order('sorting_order');

        foreach ($collection as $tInterval) {
            $storeIds = trim($tInterval->getData('store_ids'), ',');
            $storeIds = explode(',', $storeIds);
            if (!in_array($currentStore, $storeIds) && !in_array(0, $storeIds)) {
                continue;
            }
            
            $value = $tInterval->getData('from') . ' - ' . $tInterval->getData('to');
            $tIntervals[$value] = $value;
        }
        return $tIntervals;
    }
    
    public function getPhpFormat($storeId = 0)
    {
        return str_replace('%', '', $this->_convert(Mage::getStoreConfig('amdeliverydate/date_field/format', $storeId)));
    }
    
    private function _convert($value)
    {
        foreach (self::$_dictionary as $search => $replace) {
            $value = preg_replace('/(^|[^%])' . $search . '/', '$1' . $replace, $value);
        }
        return $value;
    }
    
    public function checkDefault($default, $currentStore, $now)
    {
        $default = date('Y-m-d', strtotime($default));
        list($y, $m, $d) = explode('-', $default);
        
        // min and max day intervals
        if (Mage::getStoreConfig('amdeliverydate/date_field/default', $currentStore) < Mage::getStoreConfig('amdeliverydate/general/min_days', $currentStore)
        || (Mage::getStoreConfig('amdeliverydate/general/max_days', $currentStore)
            && Mage::getStoreConfig('amdeliverydate/date_field/default', $currentStore) > Mage::getStoreConfig('amdeliverydate/general/max_days', $currentStore))) {
            return false;
        }
        // same day
        if (Mage::getStoreConfig('amdeliverydate/general/enabled_same_day', $currentStore)
        && 0 == Mage::getStoreConfig('amdeliverydate/date_field/default', $currentStore)) {
            list($h, $m, $s) = explode(',', Mage::getStoreConfig('amdeliverydate/general/same_day', $currentStore));
            $disableAfterSrc = date('Y', $now) . '-' . date('m', $now) . '-' . date('d', $now) . ' ' . $h . ':' . $m . ':' . $s;
            $disableAfter = strtotime($disableAfterSrc);
            if ($disableAfter <= $now) {
                return false;
            }
        }
        // next day
        if (Mage::getStoreConfig('amdeliverydate/general/enabled_next_day', $currentStore)
        && 1 == Mage::getStoreConfig('amdeliverydate/date_field/default', $currentStore)) {
            list($h, $m, $s) = explode(',', Mage::getStoreConfig('amdeliverydate/general/next_day', $currentStore));
            $disableAfterSrc = date('Y', $now) . '-' . date('m', $now) . '-' . date('d', $now) . ' ' . $h . ':' . $m . ':' . $s;
            $disableAfter = strtotime($disableAfterSrc);
            if ($disableAfter <= $now) {
                return false;
            }
        }
        // days of week
        $daysOfWeek = explode(',', Mage::getStoreConfig('amdeliverydate/general/disabled_days', $currentStore));
        $dayOfWeek = date('N', strtotime($default)) + 1;
        if (8 == $dayOfWeek) {
            $dayOfWeek = 1;
        }
        if (in_array($dayOfWeek, $daysOfWeek)) {
            return false;
        }
        // date intervals
        $collection = Mage::getModel('amdeliverydate/dinterval')->getCollection();
        if (0 < $collection->getSize()) {
            foreach ($collection as $interval) {
                $storeIds = trim($interval->getStoreIds(), ',');
                $storeIds = explode(',', $storeIds);
                if (!in_array($currentStore, $storeIds) && !in_array(0, $storeIds)) {
                    continue;
                }
                // from date
                if (0 == $interval->getFromYear()) {
                    $fromY = date('Y', strtotime($default));
                } else {
                    $fromY = $interval->getFromYear();
                }
                if (0 == $interval->getFromMonth()) {
                    $fromM = date('m', strtotime($default));
                } else {
                    $fromM = $interval->getFromMonth();
                }
                $fromDate = strtotime($fromY . '-' . $fromM . '-' . $interval->getFromDay());
                // to date
                if (0 == $interval->getToYear()) {
                    $toY = date('Y', strtotime($default));
                } else {
                    $toY = $interval->getToYear();
                }
                if (0 == $interval->getToMonth()) {
                    $toM = date('m', strtotime($default));
                } else {
                    $toM = $interval->getToMonth();
                }
                $toDate = strtotime($toY . '-' . $toM . '-' . $interval->getToDay());
                if ($fromDate <= strtotime($default)
                && $toDate >= strtotime($default)) {
                    return false;
                }
            }
        }
        // holidays
        $holidays = Mage::getModel('amdeliverydate/holidays')->getCollection();
        if (0 < $holidays->getSize()) {
            foreach ($holidays as $holiday) {
                $storeIds = trim($holiday->getStoreIds(), ',');
                $storeIds = explode(',', $storeIds);
                if (!in_array($currentStore, $storeIds) && !in_array(0, $storeIds)) {
                    continue;
                }
                if ((($y == $holiday->getYear()) && ($m == $holiday->getMonth()) && ($d == $holiday->getDay())) // fixed date
                || ((0 == $holiday->getYear()) && ($m == $holiday->getMonth()) && ($d == $holiday->getDay())) // each year
                || (($y == $holiday->getYear()) && (0 == $holiday->getMonth()) && ($d == $holiday->getDay())) // each month
                || ((0 == $holiday->getYear()) && (0 == $holiday->getMonth()) && ($d == $holiday->getDay()))) { // each year and each month
                    return false;
                }
            }
        }
        return true;
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
 * @package     Mage_Compiler
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Compiler main helper
 *
 * @category   Mage
 * @package    Mage_Compiler
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Compiler_Helper_Data extends Mage_Core_Helper_Abstract
{
} // Class Mage_Api_Helper_Data End
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
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog data helper
 */
class Mage_Tax_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Price conversion constant for positive
     */
    const PRICE_CONVERSION_PLUS = 1;

    /**
     * Price conversion constat for negative
     */
    const PRICE_CONVERSION_MINUS = 2;

    /**
     * Tax configuration object
     *
     * @var Mage_Tax_Model_Config
     */
    protected $_config = null;

    /**
     * Tax calculator
     *
     * @var Mage_Tac_Model_Calculation
     */
    protected $_calculator = null;

    /**
     * Display tax column
     *
     * @var bool
     */
    protected $_displayTaxColumn;

    /**
     * Tax data
     *
     * @var mixed
     */
    protected $_taxData;

    /**
     * Price includes tax
     *
     * @var bool
     */
    protected $_priceIncludesTax;

    /**
     * Shipping price includes tax
     *
     * @var bool
     */
    protected $_shippingPriceIncludesTax;

    /**
     * Apply tax after discount
     *
     * @var bool
     */
    protected $_applyTaxAfterDiscount;

    /**
     * Price display type
     *
     * @var int
     */
    protected $_priceDisplayType;

    /**
     * Shipping price display type
     *
     * @var int
     */
    protected $_shippingPriceDisplayType;

    /**
     * Postcode cut to this length when creating search templates
     *
     * @var integer
     */
    protected $_postCodeSubStringLength = 10;

    /**
     * Application instance
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Initialize helper instance
     *
     * @param array $args
     */
    public function  __construct(array $args = array())
    {
        $this->_config = Mage::getSingleton('tax/config');
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
    }

    /**
     * Return max postcode length to create search templates
     *
     * @return integer  $len
     */
    public function getPostCodeSubStringLength()
    {
        $len = (int)$this->_postCodeSubStringLength;
        if ($len <= 0) {
            $len = 10;
        }
        return $len;
    }

    /**
     * Get tax configuration object
     *
     * @return Mage_Tax_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Get tax calculation object
     *
     * @return  Mage_Tac_Model_Calculation
     */
    public function getCalculator()
    {
        if ($this->_calculator === null) {
            $this->_calculator = Mage::getSingleton('tax/calculation');
        }
        return $this->_calculator;
    }

    /**
     * Get product price including store conversion rate
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   null|string $format
     * @return  float|string
     */
    public function getProductPrice($product, $format = null)
    {
        try {
            $value = $product->getPrice();
            $value = $this->_app->getStore()->convertPrice($value, $format);
        } catch (Exception $e) {
            $value = $e->getMessage();
        }
        return $value;
    }

    /**
     * Check if product prices inputted include tax
     *
     * @param   mix $store
     * @return  bool
     */
    public function priceIncludesTax($store = null)
    {
        return $this->_config->priceIncludesTax($store) || $this->_config->getNeedUseShippingExcludeTax();
    }

    /**
     * Check what taxes should be applied after discount
     *
     * @param   mixed $store
     * @return  bool
     */
    public function applyTaxAfterDiscount($store = null)
    {
        return $this->_config->applyTaxAfterDiscount($store);
    }

    /**
     * Output
     *
     * @param bool $flag
     * @param mixed $store
     * @return string
     */
    public function getIncExcText($flag, $store = null)
    {
        if ($flag) {
            $s = $this->__('Incl. Tax');
        } else {
            $s = $this->__('Excl. Tax');
        }
        return $s;
    }

    /**
     * Get product price display type
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param   mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return $this->_config->getPriceDisplayType($store);
    }

    /**
     * Check if necessary do product price conversion
     * If it necessary will be returned conversion type (minus or plus)
     *
     * @param   mixed $store
     * @return  false | int
     */
    public function needPriceConversion($store = null)
    {
        $res = false;
        if ($this->priceIncludesTax($store)) {
            switch ($this->getPriceDisplayType($store)) {
                case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    return self::PRICE_CONVERSION_MINUS;
                case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                    $res = true;
            }
        } else {
            switch ($this->getPriceDisplayType($store)) {
                case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    return self::PRICE_CONVERSION_PLUS;
                case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    $res = false;
            }
        }

        if ($res === false) {
            $res = $this->displayTaxColumn($store);
        }
        return $res;
    }

    /**
     * Check if need display full tax summary information in totals block
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayFullSummary($store = null)
    {
        return $this->_config->displayCartFullSummary($store);
    }

    /**
     * Check if need display zero tax in subtotal
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayZeroTax($store = null)
    {
        return $this->_config->displayCartZeroTax($store);
    }

    /**
     * Check if need display cart prices included tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartPriceInclTax($store = null)
    {
        return $this->_config->displayCartPricesInclTax($store);
    }

    /**
     * Check if need display cart prices excluding price
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartPriceExclTax($store = null)
    {
        return $this->_config->displayCartPricesExclTax($store);
    }

    /**
     * Check if need display cart prices excluding and including tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartBothPrices($store = null)
    {
        return $this->_config->displayCartPricesBoth($store);
    }

    /**
     * Check if need display order prices included tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesPriceInclTax($store = null)
    {
        return $this->_config->displaySalesPricesInclTax($store);
    }

    /**
     * Check if need display order prices excluding price
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesPriceExclTax($store = null)
    {
        return $this->_config->displaySalesPricesExclTax($store);
    }

    /**
     * Check if need display order prices excluding and including tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesBothPrices($store = null)
    {
        return $this->_config->displaySalesPricesBoth($store);
    }


    /**
     * Check if we need display price include and exclude tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalBoth($store = null)
    {
        return $this->_config->displaySalesSubtotalBoth($store);
    }

    /**
     * Check if we need display price include tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalInclTax($store = null)
    {
        return $this->_config->displaySalesSubtotalInclTax($store);
    }

    /**
     * Check if we need display price exclude tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalExclTax($store = null)
    {
        return $this->_config->displaySalesSubtotalExclTax($store);
    }

    /**
     * Check if need display tax column in for shopping cart/order items
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayTaxColumn($store = null)
    {
        return $this->_config->displayCartPricesBoth();
    }

    /**
     * Get prices javascript format json
     *
     * @param   mixed $store
     * @return  string
     */
    public function getPriceFormat($store = null)
    {
        $this->_app->getLocale()->emulate($store);
        $priceFormat = $this->_app->getLocale()->getJsPriceFormat();
        $this->_app->getLocale()->revert();
        if ($store) {
            $priceFormat['pattern'] = $this->_app->getStore($store)->getCurrentCurrency()->getOutputFormat();
        }
        return Mage::helper('core')->jsonEncode($priceFormat);
    }

    /**
     * Get all tax rates JSON for all product tax classes
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     * @deprecated after 1.4 - please use getAllRatesByProductClass
     * @return string
     */
    public function getTaxRatesByProductClass()
    {
        return $this->_getAllRatesByProductClass();
    }

    /**
     * Get all tax rates JSON for all product tax classes of specific store
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     *
     * @param mixed $store
     * @return string
     */
    public function getAllRatesByProductClass($store = null)
    {
        return $this->_getAllRatesByProductClass($store);
    }


    /**
     * Get all tax rates JSON for all product tax classes of specific store
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     *
     * @param mixed $store
     * @return string
     */
    protected function _getAllRatesByProductClass($store = null)
    {
        $result = array();
        $calc = Mage::getSingleton('tax/calculation');
        $rates = $calc->getRatesForAllProductTaxClasses($calc->getDefaultRateRequest($store));

        foreach ($rates as $class => $rate) {
            $result["value_{$class}"] = $rate;
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * Get product price with all tax settings processing
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   float $price inputed product price
     * @param   bool $includingTax return price include tax flag
     * @param   null|Mage_Customer_Model_Address $shippingAddress
     * @param   null|Mage_Customer_Model_Address $billingAddress
     * @param   null|int $ctc customer tax class
     * @param   null|Mage_Core_Model_Store $store
     * @param   bool $priceIncludesTax flag what price parameter contain tax
     * @return  float
     */
    public function getPrice($product, $price, $includingTax = null, $shippingAddress = null, $billingAddress = null,
                             $ctc = null, $store = null, $priceIncludesTax = null, $roundPrice = true)
    {
        if (!$price) {
            return $price;
        }
        $store = $this->_app->getStore($store);
        if (!$this->needPriceConversion($store)) {
            return $store->roundPrice($price);
        }
        if (is_null($priceIncludesTax)) {
            $priceIncludesTax = $this->priceIncludesTax($store);
        }

        $percent = $product->getTaxPercent();
        $includingPercent = null;

        $taxClassId = $product->getTaxClassId();
        if (is_null($percent)) {
            if ($taxClassId) {
                $request = Mage::getSingleton('tax/calculation')
                    ->getRateRequest($shippingAddress, $billingAddress, $ctc, $store);
                $percent = Mage::getSingleton('tax/calculation')
                    ->getRate($request->setProductClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludesTax) {
            if ($this->isCrossBorderTradeEnabled($store)) {
                $includingPercent = $percent;
            } else {
                $request = Mage::getSingleton('tax/calculation')->getRateOriginRequest($store);
                $includingPercent = Mage::getSingleton('tax/calculation')
                    ->getRate($request->setProductClassId($taxClassId));
            }
        }

        if ($percent === false || is_null($percent)) {
            if ($priceIncludesTax && !$includingPercent) {
                return $price;
            }
        }

        $product->setTaxPercent($percent);
        if ($product->getAppliedRates() == null) {
            $request = Mage::getSingleton('tax/calculation')
                    ->getRateRequest($shippingAddress, $billingAddress, $ctc, $store);
            $request->setProductClassId($taxClassId);
            $appliedRates =  Mage::getSingleton('tax/calculation')->getAppliedRates($request);
            $product->setAppliedRates($appliedRates);
        }

        if (!is_null($includingTax)) {
            if ($priceIncludesTax) {
                if ($includingTax) {
                    /**
                     * Recalculate price include tax in case of different rates.  Otherwise price remains the same.
                     */
                    if ($includingPercent != $percent) {
                        // determine the customer's price that includes tax
                        $price = $this->_calculatePriceInclTax($price, $includingPercent, $percent, $store);
                    }
                } else {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                }
            } else {
                if ($includingTax) {
                    $appliedRates = $product->getAppliedRates();
                    if (count($appliedRates) > 1) {
                        $price = $this->_calculatePriceInclTaxWithMultipleRates($price, $appliedRates);
                    } else {
                        $price = $this->_calculatePrice($price, $percent, true);
                    }
                }
            }
        } else {
            if ($priceIncludesTax) {
                switch ($this->getPriceDisplayType($store)) {
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                        if ($includingPercent != $percent) {
                            // determine the customer's price that includes tax
                            $taxablePrice = $this->_calculatePriceInclTax($price, $includingPercent, $percent, $store);
                            // determine the customer's tax amount,
                            // round tax unless $roundPrice is set explicitly to false
                            $tax = $this->getCalculator()->calcTaxAmount($taxablePrice, $percent, true, $roundPrice);
                            // determine the customer's price without taxes
                            $price = $taxablePrice - $tax;
                        } else {
                            //round tax first unless $roundPrice is set to false explicitly
                            $price = $this->_calculatePrice($price, $includingPercent, false, $roundPrice);
                        }
                        break;

                    case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        $price = $this->_calculatePrice($price, $percent, true);
                        break;
                }
            } else {
                switch ($this->getPriceDisplayType($store)) {
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                        $appliedRates = $product->getAppliedRates();
                        if (count($appliedRates) > 1) {
                            $price = $this->_calculatePriceInclTaxWithMultipleRates($price, $appliedRates);
                        } else {
                            $price = $this->_calculatePrice($price, $percent, true);
                        }
                        break;

                    case Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    case Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                        break;
                }
            }
        }
        if ($roundPrice) {
            return $store->roundPrice($price);
        } else {
            return $price;
        }
    }

    /**
     * Given a store price that includes tax at the store rate, this function will back out the store's tax, and add in
     * the customer's tax.  Returns this new price which is the customer's price including tax.
     *
     * @param float $storePriceInclTax
     * @param float $storePercent
     * @param float $customerPercent
     * @param Mage_Core_Model_Store $store
     * @return float
     */
    protected function _calculatePriceInclTax($storePriceInclTax, $storePercent, $customerPercent, $store)
    {
        $priceExclTax         = $this->_calculatePrice($storePriceInclTax, $storePercent, false, false);
        $customerTax          = $this->getCalculator()->calcTaxAmount($priceExclTax, $customerPercent, false, false);
        $customerPriceInclTax = $store->roundPrice($priceExclTax + $customerTax);
        return $customerPriceInclTax;
    }

    /**
     * Check if we have display in catalog prices including tax
     *
     * @return bool
     */
    public function displayPriceIncludingTax()
    {
        return $this->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if we have display in catalog prices excluding tax
     *
     * @return bool
     */
    public function displayPriceExcludingTax()
    {
        return $this->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if we have display in catalog prices including and excluding tax
     *
     * @param int $store
     * @return bool
     */
    public function displayBothPrices($store = null)
    {
        return $this->getPriceDisplayType($store) == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Calculate price imcluding/excluding tax base on tax rate percent
     *
     * @param   float $price
     * @param   float $percent
     * @param   bool $type true - to calculate the price including tax and false if calculating price to exclude tax
     * @param   bool $roundTaxFirst
     * @return  float
     */
    protected function _calculatePrice($price, $percent, $type, $roundTaxFirst = false)
    {
        $calculator = $this->getCalculator();
        if ($type) {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, false, $roundTaxFirst);
            return $price + $taxAmount;
        } else {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, true, $roundTaxFirst);
            return $price - $taxAmount;
        }
    }

    /**
     * Calculate price including tax when multiple taxes is applied and rounded
     * independently.
     *
     * @param foat $price
     * @param array $appliedRates
     * @return float
     */
    protected function _calculatePriceInclTaxWithMultipleRates($price, $appliedRates)
    {
        $calculator = $this->getCalculator();
        $tax = 0;
        foreach ($appliedRates as $appliedRate) {
            $taxRate = $appliedRate['percent'];
            $tax += $calculator->round($price * $taxRate / 100);
        }
        return $tax + $price;
    }

    /**
     * Returns the include / exclude tax label
     *
     * @param bool $flag
     * @return string
     */
    public function getIncExcTaxLabel($flag)
    {
        $text = $this->getIncExcText($flag);
        return $text ? ' <span class="tax-flag">(' . $text . ')</span>' : '';
    }

    /**
     * Check if shipping prices include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function shippingPriceIncludesTax($store = null)
    {
        return $this->_config->shippingPriceIncludesTax($store);
    }

    /**
     * Get shipping methods prices display type
     *
     * @param mixed $store
     * @return int
     */
    public function getShippingPriceDisplayType($store = null)
    {
        return $this->_config->getShippingPriceDisplayType($store);
    }

    /**
     * Returns whether the shipping price should display with taxes included
     *
     * @return bool
     */
    public function displayShippingPriceIncludingTax()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Returns whether the shipping price should display without taxes
     *
     * @return bool
     */
    public function displayShippingPriceExcludingTax()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Returns whether the shipping price should display both with and without taxes
     *
     * @return bool
     */
    public function displayShippingBothPrices()
    {
        return $this->getShippingPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Get tax class id specified for shipping tax estimation
     *
     * @param mixed $store
     * @return int
     */
    public function getShippingTaxClass($store)
    {
        return $this->_config->getShippingTaxClass($store);
    }

    /**
     * Get Shipping Price
     *
     * @param float $price
     * @param null|bool $includingTax
     * @param mixed $shippingAddress
     * @param mixed $ctc
     * @param mixed $store
     * @return float
     */
    public function getShippingPrice($price, $includingTax = null, $shippingAddress = null, $ctc = null, $store = null)
    {
        $pseudoProduct = new Varien_Object();
        $pseudoProduct->setTaxClassId($this->getShippingTaxClass($store));

        $billingAddress = false;
        if ($shippingAddress && $shippingAddress->getQuote() && $shippingAddress->getQuote()->getBillingAddress()) {
            $billingAddress = $shippingAddress->getQuote()->getBillingAddress();
        }

        $price = $this->getPrice(
            $pseudoProduct,
            $price,
            $includingTax,
            $shippingAddress,
            $billingAddress,
            $ctc,
            $store,
            $this->shippingPriceIncludesTax($store)
        );
        return $price;
    }

    /**
     * Returns the SQL for the price tax
     *
     * @param string $priceField
     * @param string $taxClassField
     * @return string
     */
    public function getPriceTaxSql($priceField, $taxClassField)
    {
        if (!$this->priceIncludesTax() && $this->displayPriceExcludingTax()) {
            return '';
        }

        $request = Mage::getSingleton('tax/calculation')->getDefaultRateRequest();
        $defaultTaxes = Mage::getSingleton('tax/calculation')->getRatesForAllProductTaxClasses($request);

        $request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $currentTaxes = Mage::getSingleton('tax/calculation')->getRatesForAllProductTaxClasses($request);

        $defaultTaxString = $currentTaxString = '';

        $rateToVariable = array(
            'defaultTaxString' => 'defaultTaxes',
            'currentTaxString' => 'currentTaxes',
        );
        foreach ($rateToVariable as $rateVariable => $rateArray) {
            if ($$rateArray && is_array($$rateArray)) {
                $$rateVariable = '';
                foreach ($$rateArray as $classId => $rate) {
                    if ($rate) {
                        $$rateVariable .= sprintf("WHEN %d THEN %12.4f ", $classId, $rate / 100);
                    }
                }
                if ($$rateVariable) {
                    $$rateVariable = "CASE {$taxClassField} {$$rateVariable} ELSE 0 END";
                }
            }
        }

        $result = '';

        if ($this->priceIncludesTax()) {
            if ($defaultTaxString) {
                $result = "-({$priceField}/(1+({$defaultTaxString}))*{$defaultTaxString})";
            }
            if (!$this->displayPriceExcludingTax() && $currentTaxString) {
                $result .= "+(({$priceField}{$result})*{$currentTaxString})";
            }
        } else {
            if ($this->displayPriceIncludingTax()) {
                if ($currentTaxString) {
                    $result .= "+({$priceField}*{$currentTaxString})";
                }
            }
        }
        return $result;
    }

    /**
     * Join tax class
     * @param Varien_Db_Select $select
     * @param int $storeId
     * @param string $priceTable
     * @return Mage_Tax_Helper_Data
     */
    public function joinTaxClass($select, $storeId, $priceTable = 'main_table')
    {
        $taxClassAttribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'tax_class_id');
        $joinConditionD = implode(' AND ', array(
            "tax_class_d.entity_id = {$priceTable}.entity_id",
            $select->getAdapter()->quoteInto('tax_class_d.attribute_id = ?', (int)$taxClassAttribute->getId()),
            'tax_class_d.store_id = 0'
        ));
        $joinConditionC = implode(' AND ', array(
            "tax_class_c.entity_id = {$priceTable}.entity_id",
            $select->getAdapter()->quoteInto('tax_class_c.attribute_id = ?', (int)$taxClassAttribute->getId()),
            $select->getAdapter()->quoteInto('tax_class_c.store_id = ?', (int)$storeId)
        ));
        $select
            ->joinLeft(
                array('tax_class_d' => $taxClassAttribute->getBackend()->getTable()),
                $joinConditionD,
                array())
            ->joinLeft(
                array('tax_class_c' => $taxClassAttribute->getBackend()->getTable()),
                $joinConditionC,
                array());

        return $this;
    }

    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     *
     * @param   null|int $store
     * @return  0|1
     */
    public function discountTax($store = null)
    {
        return $this->_config->discountTax($store);
    }

    /**
     * Get value of "Apply Tax On" custom/original price configuration settings.
     * Result is 0 or 1
     *
     * @param mixed $store
     * @return mixed
     */
    public function getTaxBasedOn($store = null)
    {
        return Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store);
    }

    /**
     * Check if tax can be applied to custom price
     *
     * @param $store
     * @return bool
     */
    public function applyTaxOnCustomPrice($store = null)
    {
        return ((int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_ON, $store) == 0);
    }

    /**
     * Check if tax should be applied just to original price
     *
     * @param $store
     * @return bool
     */
    public function applyTaxOnOriginalPrice($store = null)
    {
        return ((int)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_ON, $store) == 1);
    }

    /**
     * Get taxes/discounts calculation sequence.
     * This sequence depends on "Catalog price include tax", "Apply Tax After Discount"
     * and "Apply Discount On Prices Including Tax" configuration options.
     *
     * @param   null|int|string|Mage_Core_Model_Store $store
     * @return  string
     */
    public function getCalculationSequence($store = null)
    {
        return $this->_config->getCalculationSequence($store);
    }

    /**
     * Get tax calculation algorithm code
     *
     * @param   null|int $store
     * @return  string
     */
    public function getCalculationAgorithm($store = null)
    {
        return $this->_config->getAlgorithm($store);
    }

    /**
     * Get calculated taxes for each tax class
     *
     * This method returns array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'hidden_tax_amount' => $hiddenTaxAmount,
     *      'title'             => $title,
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param Mage_Sales_Model_Order $source
     * @return array
     */
    public function getCalculatedTaxes($source)
    {
        if ($this->_getFromRegistry('current_invoice')) {
            $current = $this->_getFromRegistry('current_invoice');
        } elseif ($this->_getFromRegistry('current_creditmemo')) {
            $current = $this->_getFromRegistry('current_creditmemo');
        } else {
            $current = $source;
        }

        $taxClassAmount = array();
        if ($current && $source) {
            if ($current == $source) {
                // use the actuals
                $rates = $this->_getTaxRateSubtotals($source);
                foreach ($rates['items'] as $rate) {
                    $taxClassId = $rate['tax_id'];
                    $taxClassAmount[$taxClassId]['tax_amount'] = $rate['amount'];
                    $taxClassAmount[$taxClassId]['base_tax_amount'] = $rate['base_amount'];
                    $taxClassAmount[$taxClassId]['title'] = $rate['title'];
                    $taxClassAmount[$taxClassId]['percent'] = $rate['percent'];
                }
            } else {
                // regenerate tax subtotals
                // Calculate taxes for shipping
                $shippingTaxAmount = $current->getShippingTaxAmount();
                if ($shippingTaxAmount) {
                    $shippingTax    = Mage::helper('tax')->getShippingTax($current);
                    $taxClassAmount = array_merge($taxClassAmount, $shippingTax);
                }

                foreach ($current->getItemsCollection() as $item) {
                    $taxCollection = Mage::getResourceModel('tax/sales_order_tax_item')
                        ->getTaxItemsByItemId(
                            $item->getOrderItemId() ? $item->getOrderItemId() : $item->getItemId()
                        );

                    foreach ($taxCollection as $tax) {
                        $taxClassId = $tax['tax_id'];
                        $percent = $tax['tax_percent'];

                        $price = $item->getRowTotal();
                        $basePrice = $item->getBaseRowTotal();
                        if ($this->applyTaxAfterDiscount($item->getStoreId())) {
                            $price = $price - $item->getDiscountAmount() + $item->getHiddenTaxAmount();
                            $basePrice = $basePrice - $item->getBaseDiscountAmount() + $item->getBaseHiddenTaxAmount();
                        }
                        $tax_amount = $price * $percent / 100;
                        $base_tax_amount = $basePrice * $percent / 100;

                        if (isset($taxClassAmount[$taxClassId])) {
                            $taxClassAmount[$taxClassId]['tax_amount'] += $tax_amount;
                            $taxClassAmount[$taxClassId]['base_tax_amount'] += $base_tax_amount;
                        } else {
                            $taxClassAmount[$taxClassId]['tax_amount'] = $tax_amount;
                            $taxClassAmount[$taxClassId]['base_tax_amount'] = $base_tax_amount;
                            $taxClassAmount[$taxClassId]['title'] = $tax['title'];
                            $taxClassAmount[$taxClassId]['percent'] = $tax['percent'];
                        }
                    }
                }
            }

            foreach ($taxClassAmount as $key => $tax) {
                if ($tax['tax_amount'] == 0 && $tax['base_tax_amount'] == 0) {
                    unset($taxClassAmount[$key]);
                }
            }

            $taxClassAmount = array_values($taxClassAmount);
        }

        return $taxClassAmount;
    }

    /**
     * Returns the array of tax rates for the order
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    protected function _getTaxRateSubtotals($order)
    {
        return Mage::getModel('tax/sales_order_tax')->getCollection()->loadByOrder($order)->toArray();
    }

    /**
     * Retrieve a value from registry by a key
     *
     * @param string $key
     * @return mixed
     */
    protected function _getFromRegistry($key)
    {
        return Mage::registry($key);
    }

    /**
     * Get calculated Shipping & Handling Tax
     *
     * This method returns array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'hidden_tax_amount' => $hiddenTaxAmount
     *      'title'             => $title
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param Mage_Sales_Model_Order $source
     * @return array
     */
    public function getShippingTax($source)
    {
        if (Mage::registry('current_invoice')) {
            $current = Mage::registry('current_invoice');
        } elseif (Mage::registry('current_creditmemo')) {
            $current = Mage::registry('current_creditmemo');
        } else {
            $current = $source;
        }

        $taxClassAmount = array();
        if ($current && $source) {
            if ($current->getShippingTaxAmount() != 0 && $current->getBaseShippingTaxAmount() != 0) {
                $taxClassAmount[0]['tax_amount'] = $current->getShippingTaxAmount();
                $taxClassAmount[0]['base_tax_amount'] = $current->getBaseShippingTaxAmount();
                if ($current->getShippingHiddenTaxAmount() > 0) {
                    $taxClassAmount[0]['hidden_tax_amount'] = $current->getShippingHiddenTaxAmount();
                }
                $taxClassAmount[0]['title'] = $this->__('Shipping & Handling Tax');
                $taxClassAmount[0]['percent'] = NULL;
            }
        }

        return $taxClassAmount;
    }

    /**
     * Get all FPTs
     *
     * @return array
     */
    public function getAllWeee($source = null)
    {
        $allWeee = array();
        $store = $this->_app->getStore();

        if (Mage::registry('current_invoice')) {
            $source = Mage::registry('current_invoice');
        } elseif (Mage::registry('current_creditmemo')) {
            $source = Mage::registry('current_creditmemo');
        } elseif ($source == null) {
            $source = $this->_app->getOrder();
        }

        $helper = Mage::helper('weee');
        if (!$helper->includeInSubtotal($store)) {
            foreach ($source->getAllItems() as $item) {
                foreach ($helper->getApplied($item) as $tax) {
                    $weeeDiscount = isset($tax['weee_discount']) ? $tax['weee_discount'] : 0;
                    $title = $tax['title'];

                    $rowAmount = isset($tax['row_amount']) ? $tax['row_amount'] : 0;
                    $rowAmountInclTax = isset($tax['row_amount_incl_tax']) ? $tax['row_amount_incl_tax'] : 0;
                    $amountDisplayed = ($helper->isTaxIncluded()) ? $rowAmountInclTax : $rowAmount;

                    if (array_key_exists($title, $allWeee)) {
                        $allWeee[$title] = $allWeee[$title] + $amountDisplayed - $weeeDiscount;
                    } else {
                        $allWeee[$title] = $amountDisplayed - $weeeDiscount;
                    }
                }
            }
        }

        return $allWeee;
    }

    /**
     * Check if do not show notification about wrong display settings
     *
     * @return bool
     */
    public function isWrongDisplaySettingsIgnored()
    {
        return (bool)$this->_app->getStore()->getConfig(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_PRICE_DISPLAY);
    }

    /**
     * Check if do not show notification about wrong discount settings
     *
     * @return bool
     */
    public function isWrongDiscountSettingsIgnored()
    {
        return (bool)$this->_app->getStore()->getConfig(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_DISCOUNT);
    }

    /**
     * Check if warning about conflicting FPT configuration should be shown
     *
     * @return bool
     */
    public function isConflictingFptTaxConfigurationSettingsIgnored()
    {
        return (bool) $this->_app->getStore()
            ->getConfig(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_FPT_CONFIGURATION);
    }

    /**
     * Return whether cross border trade is enabled or not
     *
     * @param   null|int $store
     * @return boolean
     */
    public function isCrossBorderTradeEnabled($store = null)
    {
        return (bool)$this->_config->crossBorderTradeEnabled($store);
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
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration paths storage
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Config
{
    /**#@+
     * Paths to tax notification configs
     */
    const XML_PATH_TAX_NOTIFICATION_DISCOUNT = 'tax/ignore_notification/discount';
    const XML_PATH_TAX_NOTIFICATION_PRICE_DISPLAY = 'tax/ignore_notification/price_display';
    const XML_PATH_TAX_NOTIFICATION_FPT_CONFIGURATION = 'tax/ignore_notification/fpt_configuration';
    const XML_PATH_TAX_NOTIFICATION_URL = 'tax/notification/url';
    /**#@-*/

    /**
     * Tax classes
     */
    const CONFIG_XML_PATH_SHIPPING_TAX_CLASS = 'tax/classes/shipping_tax_class';

    /**#@+
     * Paths to tax calculation configs
     */
    const CONFIG_XML_PATH_PRICE_INCLUDES_TAX = 'tax/calculation/price_includes_tax';
    const CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX = 'tax/calculation/shipping_includes_tax';
    const CONFIG_XML_PATH_BASED_ON = 'tax/calculation/based_on';
    const CONFIG_XML_PATH_APPLY_ON = 'tax/calculation/apply_tax_on';
    const CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT = 'tax/calculation/apply_after_discount';
    const CONFIG_XML_PATH_DISCOUNT_TAX = 'tax/calculation/discount_tax';
    const XML_PATH_ALGORITHM = 'tax/calculation/algorithm';
    const CONFIG_XML_PATH_CROSS_BORDER_TRADE_ENABLED = 'tax/calculation/cross_border_trade_enabled';
    /**#@-*/

    /**#@+
     * Paths to tax defaults configs
     */
    const CONFIG_XML_PATH_DEFAULT_COUNTRY = 'tax/defaults/country';
    const CONFIG_XML_PATH_DEFAULT_REGION = 'tax/defaults/region';
    const CONFIG_XML_PATH_DEFAULT_POSTCODE = 'tax/defaults/postcode';
    /**#@-*/

    /**#@+
     * Prices display settings
     */
    const CONFIG_XML_PATH_PRICE_DISPLAY_TYPE = 'tax/display/type';
    const CONFIG_XML_PATH_DISPLAY_SHIPPING = 'tax/display/shipping';
    /**#@-*/

    /**#@+
     * Shopping cart display settings
     */
    const XML_PATH_DISPLAY_CART_PRICE = 'tax/cart_display/price';
    const XML_PATH_DISPLAY_CART_SUBTOTAL = 'tax/cart_display/subtotal';
    const XML_PATH_DISPLAY_CART_SHIPPING = 'tax/cart_display/shipping';
    const XML_PATH_DISPLAY_CART_DISCOUNT = 'tax/cart_display/discount';
    const XML_PATH_DISPLAY_CART_GRANDTOTAL = 'tax/cart_display/grandtotal';
    const XML_PATH_DISPLAY_CART_FULL_SUMMARY = 'tax/cart_display/full_summary';
    const XML_PATH_DISPLAY_CART_ZERO_TAX = 'tax/cart_display/zero_tax';
    /**#@-*/

    /**#@+
     * Shopping cart display settings
     */
    const XML_PATH_DISPLAY_SALES_PRICE = 'tax/sales_display/price';
    const XML_PATH_DISPLAY_SALES_SUBTOTAL = 'tax/sales_display/subtotal';
    const XML_PATH_DISPLAY_SALES_SHIPPING = 'tax/sales_display/shipping';
    const XML_PATH_DISPLAY_SALES_DISCOUNT = 'tax/sales_display/discount';
    const XML_PATH_DISPLAY_SALES_GRANDTOTAL = 'tax/sales_display/grandtotal';
    const XML_PATH_DISPLAY_SALES_FULL_SUMMARY = 'tax/sales_display/full_summary';
    const XML_PATH_DISPLAY_SALES_ZERO_TAX = 'tax/sales_display/zero_tax';
    /**#@-*/

    /**
     * String separator
     */
    const CALCULATION_STRING_SEPARATOR = '|';

    /**#@+
     * Indexes for tax display types
     */
    const DISPLAY_TYPE_EXCLUDING_TAX = 1;
    const DISPLAY_TYPE_INCLUDING_TAX = 2;
    const DISPLAY_TYPE_BOTH = 3;
    /**#@-*/

    /**#@+
     * Indexes for FPT Configuration Types
     */
    const FPT_NOT_TAXED = 0;
    const FPT_TAXED = 1;
    const FPT_LOADED_DISPLAY_WITH_TAX = 2;
    /**#@-*/

    /**#@+
     * @deprecated
     */
    const CONFIG_XML_PATH_SHOW_IN_CATALOG = 'tax/display/show_in_catalog';
    const CONFIG_XML_PATH_DEFAULT_PRODUCT_TAX_GROUP = 'catalog/product/default_tax_group';
    const CONFIG_XML_PATH_DISPLAY_TAX_COLUMN = 'tax/display/column_in_summary';
    const CONFIG_XML_PATH_DISPLAY_FULL_SUMMARY = 'tax/display/full_summary';
    const CONFIG_XML_PATH_DISPLAY_ZERO_TAX = 'tax/display/zero_tax';
    /**#@-*/

    /**
     * Flag which notify what we need use prices exclude tax for calculations
     *
     * @var bool
     */
    protected $_needUsePriceExcludeTax = false;

    /**
     * Flag which notify what we need use shipping prices exclude tax for calculations
     *
     * @var bool
     */
    protected $_needUseShippingExcludeTax = false;

    /**
     * @var $_shippingPriceIncludeTax bool
     */
    protected $_shippingPriceIncludeTax = null;

    /**
     * Retrieve config value for store by path
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    protected function _getStoreConfig($path, $store)
    {
        return Mage::getStoreConfig($path, $store);
    }

    /**
     * Check if product prices inputed include tax
     *
     * @param   mix $store
     * @return  bool
     */
    public function priceIncludesTax($store = null)
    {
        if ($this->_needUsePriceExcludeTax) {
            return false;
        }
        return (bool)$this->_getStoreConfig(self::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $store);
    }

    /**
     * Check what taxes should be applied after discount
     *
     * @param   mixed $store
     * @return  bool
     */
    public function applyTaxAfterDiscount($store = null)
    {
        return (bool)$this->_getStoreConfig(self::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $store);
    }

    /**
     * Get product price display type
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param   mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return (int)$this->_getStoreConfig(self::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, $store);
    }

    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     *
     * @param   null|int $store
     * @return  0|1
     */
    public function discountTax($store = null)
    {
        return ((int)$this->_getStoreConfig(self::CONFIG_XML_PATH_DISCOUNT_TAX, $store) == 1);
    }

    /**
     * Get taxes/discounts calculation sequence.
     * This sequence depends on "Apply Customer Tax" and "Apply Discount On Prices" configuration options.
     *
     * @param   null|int|string|Mage_Core_Model_Store $store
     * @return  string
     */
    public function getCalculationSequence($store = null)
    {
        if ($this->applyTaxAfterDiscount($store)) {
            if ($this->discountTax($store)) {
                $seq = Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL;
            } else {
                $seq = Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL;
            }
        } else {
            if ($this->discountTax($store)) {
                $seq = Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL;
            } else {
                $seq = Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL;
            }
        }
        return $seq;
    }

    /**
     * Specify flag what we need use price exclude tax
     *
     * @param   bool $flag
     * @return  Mage_Tax_Model_Config
     */
    public function setNeedUsePriceExcludeTax($flag)
    {
        $this->_needUsePriceExcludeTax = $flag;
        return $this;
    }

    /**
     * Get flag what we need use price exclude tax
     *
     * @return bool $flag
     */
    public function getNeedUsePriceExcludeTax()
    {
        return $this->_needUsePriceExcludeTax;
    }

    /**
     * Specify flag what we need use shipping price exclude tax
     *
     * @param   bool $flag
     * @return  Mage_Tax_Model_Config
     */
    public function setNeedUseShippingExcludeTax($flag)
    {
        $this->_needUseShippingExcludeTax = $flag;
        return $this;
    }

    /**
     * Get flag what we need use shipping price exclude tax
     *
     * @return bool $flag
     */
    public function getNeedUseShippingExcludeTax()
    {
        return $this->_needUseShippingExcludeTax;
    }


    /**
     * Get defined tax calculation agorithm
     *
     * @param   store $store
     * @return  string
     */
    public function getAlgorithm($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_ALGORITHM, $store);
    }

    /**
     * Get tax class id specified for shipping tax estimation
     *
     * @param   store $store
     * @return  int
     */
    public function getShippingTaxClass($store = null)
    {
        return (int)$this->_getStoreConfig(self::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
    }

    /**
     * Get shipping methods prices display type
     *
     * @param   store $store
     * @return  int
     */
    public function getShippingPriceDisplayType($store = null)
    {
        return (int)$this->_getStoreConfig(self::CONFIG_XML_PATH_DISPLAY_SHIPPING, $store);
    }

    /**
     * Check if shipping prices include tax
     *
     * @param   store $store
     * @return  bool
     */
    public function shippingPriceIncludesTax($store = null)
    {
        if ($this->_shippingPriceIncludeTax === null) {
            $this->_shippingPriceIncludeTax = (bool)$this->_getStoreConfig(
                self::CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX,
                $store
            );
        }
        return $this->_shippingPriceIncludeTax;
    }

    /**
     * Declare shipping prices type
     * @param bool $flag
     * @return Mage_Tax_Model_Config
     */
    public function setShippingPriceIncludeTax($flag)
    {
        $this->_shippingPriceIncludeTax = $flag;
        return $this;
    }


    /**
     * Check if need display full tax summary information in totals block
     *
     * @deprecated please use displayCartFullSummary or displaySalesFullSummary
     * @param   mixed $store
     * @return  bool
     */
    public function displayFullSummary($store = null)
    {
        return $this->displayCartFullSummary($store);
    }

    /**
     * Check if need display zero tax in subtotal
     *
     * @deprecated please use displayCartZeroTax or displaySalesZeroTax
     * @param   mixed $store
     * @return  bool
     */
    public function displayZeroTax($store = null)
    {
        return $this->displayCartZeroTax($store);
    }

    /**
     * Get shopping cart prices display type
     *
     * @deprecated please use displayCartPrice or displaySalesZeroTax
     * @param   mixed $store
     * @return  bool
     */
    public function displayTaxColumn($store = null)
    {
        return (bool)$this->_getStoreConfig(self::CONFIG_XML_PATH_DISPLAY_TAX_COLUMN, $store);
    }

    /**
     * Check if display cart prices included tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartPricesInclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE, $store) == self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if display cart prices excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartPricesExclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE, $store) == self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if display cart prices included and excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartPricesBoth($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE, $store) == self::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check if display cart subtotal included tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartSubtotalInclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL, $store) == self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if display cart subtotal excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartSubtotalExclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL, $store) == self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if display cart subtotal included and excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartSubtotalBoth($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL, $store) == self::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check if display cart shipping included tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartShippingInclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_SHIPPING, $store) == self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if display cart shipping excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartShippingExclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_SHIPPING, $store) == self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if display cart shipping included and excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartShippingBoth($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_SHIPPING, $store) == self::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check if display cart discount included tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartDiscountInclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_DISCOUNT, $store) == self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if display cart discount excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartDiscountExclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_DISCOUNT, $store) == self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if display cart discount included and excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartDiscountBoth($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_DISCOUNT, $store) == self::DISPLAY_TYPE_BOTH;
    }

    /**
     * Get display cart tax with grand total
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartTaxWithGrandTotal($store = null)
    {
        return (bool)$this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_GRANDTOTAL, $store);
    }

    /**
     * Get display cart full summary
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartFullSummary($store = null)
    {
        return (bool)$this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_FULL_SUMMARY, $store);
    }

    /**
     * Get display cart zero tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartZeroTax($store = null)
    {
        return (bool)$this->_getStoreConfig(self::XML_PATH_DISPLAY_CART_ZERO_TAX, $store);
    }

    /**
     * Check if display sales prices include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesPricesInclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_PRICE, $store) == self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if display sales prices exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesPricesExclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_PRICE, $store) == self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if display sales prices include and exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesPricesBoth($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_PRICE, $store) == self::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check if display sales subtotal include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalInclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_SUBTOTAL, $store)
            == self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if display sales subtotal exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalExclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_SUBTOTAL, $store)
            == self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if display sales subtotal include and exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalBoth($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_SUBTOTAL, $store) == self::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check if display sales shipping include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesShippingInclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_SHIPPING, $store)
            == self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if display sales shipping exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesShippingExclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_SHIPPING, $store)
            == self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if display sales shipping include and exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesShippingBoth($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_SHIPPING, $store) == self::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check if display sales discount include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesDiscountInclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_DISCOUNT, $store)
            == self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if display sales discount exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalestDiscountExclTax($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_DISCOUNT, $store)
            == self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if display sales discount include and exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesDiscountBoth($store = null)
    {
        return $this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_DISCOUNT, $store) == self::DISPLAY_TYPE_BOTH;
    }

    /**
     * Get display sales tax with grand total
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesTaxWithGrandTotal($store = null)
    {
        return (bool)$this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_GRANDTOTAL, $store);
    }

    /**
     * Get display sales full summary
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesFullSummary($store = null)
    {
        return (bool)$this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_FULL_SUMMARY, $store);
    }

    /**
     * Get display sales zero tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesZeroTax($store = null)
    {
        return (bool)$this->_getStoreConfig(self::XML_PATH_DISPLAY_SALES_ZERO_TAX, $store);
    }

    /**
     * Check if tax calculation type and price display settings are compatible
     *
     * invalid settings if
     *      Tax Calculation Method Based On 'Total' or 'Row'
     *      and at least one Price Display Settings has 'Including and Excluding Tax' value
     *
     * @param mixed $store
     * @return bool
     */
    public function checkDisplaySettings($store = null)
    {
        if ($this->getAlgorithm($store) == Mage_Tax_Model_Calculation::CALC_UNIT_BASE) {
            return true;
        }
        return $this->getPriceDisplayType($store) != self::DISPLAY_TYPE_BOTH
            && $this->getShippingPriceDisplayType($store) != self::DISPLAY_TYPE_BOTH
            && !$this->displayCartPricesBoth($store)
            && !$this->displayCartSubtotalBoth($store)
            && !$this->displayCartShippingBoth($store)
            && !$this->displaySalesPricesBoth($store)
            && !$this->displaySalesSubtotalBoth($store)
            && !$this->displaySalesShippingBoth($store);
    }

    /**
     * Check if tax discount settings are compatible
     *
     * Matrix for invalid discount settings is as follows:
     *      Before Discount / Excluding Tax
     *      Before Discount / Including Tax
     *
     * @param mixed $store
     * @return bool
     */
    public function checkDiscountSettings($store = null)
    {
        return $this->applyTaxAfterDiscount($store);
    }

    /**
     * Return the config value for self::CONFIG_XML_PATH_CROSS_BORDER_TRADE_ENABLED
     *
     * @param int|null $store
     * @return int
     */
    public function crossBorderTradeEnabled($store = null)
    {
        return $this->_getStoreConfig(self::CONFIG_XML_PATH_CROSS_BORDER_TRADE_ENABLED, $store);
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
 * Core config data resource model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Config_Data extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }

    /**
     * Convert array to comma separated value
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Config_Data
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $this->_checkUnique($object);
        }

        if (is_array($object->getValue())) {
            $object->setValue(join(',', $object->getValue()));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Validate unique configuration data before save
     * Set id to object if exists configuration instead of throw exception
     *
     * @param Mage_Core_Model_Config_Data $object
     * @return Mage_Core_Model_Resource_Config_Data
     */
    protected function _checkUnique(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array($this->getIdFieldName()))
            ->where('scope = :scope')
            ->where('scope_id = :scope_id')
            ->where('path = :path');
        $bind   = array(
            'scope'     => $object->getScope(),
            'scope_id'  => $object->getScopeId(),
            'path'      => $object->getPath()
        );

        $configId = $this->_getReadAdapter()->fetchOne($select, $bind);
        if ($configId) {
            $object->setId($configId);
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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Config data collection
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Config_Data_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('core/config_data');
    }

    /**
     * Add scope filter to collection
     *
     * @param string $scope
     * @param int $scopeId
     * @param string $section
     * @return Mage_Core_Model_Resource_Config_Data_Collection
     */
    public function addScopeFilter($scope, $scopeId, $section)
    {
        $this->addFieldToFilter('scope', $scope);
        $this->addFieldToFilter('scope_id', $scopeId);
        $this->addFieldToFilter('path', array('like' => $section . '/%'));
        return $this;
    }

    /**
     *  Add path filter
     *
     * @param string $section
     * @return Mage_Core_Model_Resource_Config_Data_Collection
     */
    public function addPathFilter($section)
    {
        $this->addFieldToFilter('path', array('like' => $section . '/%'));
        return $this;
    }

    /**
     * Add value filter
     *
     * @param int|string $value
     * @return Mage_Core_Model_Resource_Config_Data_Collection
     */
    public function addValueFilter($value)
    {
        $this->addFieldToFilter('value', array('like' => $value));
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
 * @package     Mage_Index
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Indexer strategy
 */
class Mage_Index_Model_Indexer
{
    /**
     * Collection of available processes
     *
     * @var Mage_Index_Model_Resource_Process_Collection
     */
    protected $_processesCollection;

    /**
     * Indexer processes lock flag
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_lockFlag = false;

    /**
     * Whether table changes are allowed
     *
     * @var bool
     */
    protected $_allowTableChanges = true;

    /**
     * Current processing event(s)
     * In array case it should be array(Entity type, Event type)
     *
     * @var null|Mage_Index_Model_Event|array
     */
    protected $_currentEvent = null;

    /**
     * Array of errors
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Class constructor. Initialize index processes based on configuration
     */
    public function __construct()
    {
        $this->getProcessesCollection();
    }

    /**
     * Get collection of all available processes
     *
     * @return Mage_Index_Model_Resource_Process_Collection
     */
    public function getProcessesCollection()
    {
        if (is_null($this->_processesCollection)) {
            $this->_processesCollection = Mage::getResourceModel('index/process_collection');
        }
        return $this->_processesCollection;
    }

    /**
     * Get index process by specific id
     *
     * @param int $processId
     * @return Mage_Index_Model_Process | false
     */
    public function getProcessById($processId)
    {
        foreach ($this->getProcessesCollection() as $process) {
            if ($process->getId() == $processId) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Get index process by specific code
     *
     * @param string $code
     * @return Mage_Index_Model_Process | false
     */
    public function getProcessByCode($code)
    {
        foreach ($this->getProcessesCollection() as $process) {
            if ($process->getIndexerCode() == $code) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Function returns array of indexer's process with order by sort_order field
     *
     * @param array $codes
     * @return array
     */
    public function getProcessesCollectionByCodes(array $codes)
    {
        $processes = array();
        $this->_errors = array();
        foreach($codes as $code) {
            $process = $this->getProcessByCode($code);
            if (!$process) {
                $this->_errors[] = sprintf('Warning: Unknown indexer with code %s', trim($code));
                continue;
            }
            $processes[$process->getIndexerCode()] = $process;
        }
        return $processes;
    }

    /**
     * Return true if model has errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (bool)count($this->_errors);
    }

    /**
     * Return array of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Lock indexer actions
     * @deprecated after 1.6.1.0
     *
     * @return Mage_Index_Model_Indexer
     */
    public function lockIndexer()
    {
        $this->_lockFlag = true;
        return $this;
    }

    /**
     * Unlock indexer actions
     * @deprecated after 1.6.1.0
     *
     * @return Mage_Index_Model_Indexer
     */
    public function unlockIndexer()
    {
        $this->_lockFlag = false;
        return $this;
    }

    /**
     * Check if onject actions are locked
     *
     * @deprecated after 1.6.1.0
     * @return bool
     */
    public function isLocked()
    {
        return $this->_lockFlag;
    }

    /**
     * Indexing all pending events.
     * Events set can be limited by event entity and type
     *
     * @param   null | string $entity
     * @param   null | string $type
     * @throws Exception
     * @return  Mage_Index_Model_Indexer
     */
    public function indexEvents($entity=null, $type=null)
    {
        Mage::dispatchEvent('start_index_events' . $this->_getEventTypeName($entity, $type));

        /** @var $resourceModel Mage_Index_Model_Resource_Process */
        $resourceModel = Mage::getResourceSingleton('index/process');

        $allowTableChanges = $this->_allowTableChanges && !$resourceModel->isInTransaction();
        if ($allowTableChanges) {
            $this->_currentEvent = array($entity, $type);
            $this->_changeKeyStatus(false);
        }

        $resourceModel->beginTransaction();
        $this->_allowTableChanges = false;
        try {
            $this->_runAll('indexEvents', array($entity, $type));
            $resourceModel->commit();
        } catch (Exception $e) {
            $resourceModel->rollBack();
            throw $e;
        }
        if ($allowTableChanges) {
            $this->_allowTableChanges = true;
            $this->_changeKeyStatus(true);
            $this->_currentEvent = null;
        }
        Mage::dispatchEvent('end_index_events' . $this->_getEventTypeName($entity, $type));
        return $this;
    }

    /**
     * Index one event by all processes
     *
     * @param   Mage_Index_Model_Event $event
     * @return  Mage_Index_Model_Indexer
     */
    public function indexEvent(Mage_Index_Model_Event $event)
    {
        $this->_runAll('safeProcessEvent', array($event));
        return $this;
    }

    /**
     * Register event in each indexing process process
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Index_Model_Indexer
     */
    public function registerEvent(Mage_Index_Model_Event $event)
    {
        $this->_runAll('register', array($event));
        return $this;
    }

    /**
     * Create new event log and register event in all processes
     *
     * @param   Varien_Object $entity
     * @param   string $entityType
     * @param   string $eventType
     * @param   bool $doSave
     * @return  Mage_Index_Model_Event
     */
    public function logEvent(Varien_Object $entity, $entityType, $eventType, $doSave=true)
    {
        $event = Mage::getModel('index/event')
            ->setEntity($entityType)
            ->setType($eventType)
            ->setDataObject($entity)
            ->setEntityPk($entity->getId());

        $this->registerEvent($event);
        if ($doSave) {
            $event->save();
        }
        return $event;
    }

    /**
     * Create new event log and register event in all processes.
     * Initiate events indexing procedure.
     *
     * @param   Varien_Object $entity
     * @param   string $entityType
     * @param   string $eventType
     * @throws Exception
     * @return  Mage_Index_Model_Indexer
     */
    public function processEntityAction(Varien_Object $entity, $entityType, $eventType)
    {
        $event = $this->logEvent($entity, $entityType, $eventType, false);
        /**
         * Index and save event just in case if some process matched it
         */
        if ($event->getProcessIds()) {
            Mage::dispatchEvent('start_process_event' . $this->_getEventTypeName($entityType, $eventType));

            /** @var $resourceModel Mage_Index_Model_Resource_Process */
            $resourceModel = Mage::getResourceSingleton('index/process');

            $allowTableChanges = $this->_allowTableChanges && !$resourceModel->isInTransaction();
            if ($allowTableChanges) {
                $this->_currentEvent = $event;
                $this->_changeKeyStatus(false);
            }

            $resourceModel->beginTransaction();
            $this->_allowTableChanges = false;
            try {
                $this->indexEvent($event);
                $resourceModel->commit();
            } catch (Exception $e) {
                $resourceModel->rollBack();
                if ($allowTableChanges) {
                    $this->_allowTableChanges = true;
                    $this->_changeKeyStatus(true);
                    $this->_currentEvent = null;
                }
                throw $e;
            }
            if ($allowTableChanges) {
                $this->_allowTableChanges = true;
                $this->_changeKeyStatus(true);
                $this->_currentEvent = null;
            }
            $event->save();
            Mage::dispatchEvent('end_process_event' . $this->_getEventTypeName($entityType, $eventType));
        }
        return $this;
    }

    /**
     * Run all processes method with parameters
     * Run by depends priority
     * Not recursive call is not implement
     *
     * @param string $method
     * @param array $args
     * @return Mage_Index_Model_Indexer
     */
    protected function _runAll($method, $args)
    {
        $checkLocks = $method != 'register';
        $processed = array();
        foreach ($this->getProcessesCollection() as $process) {
            $code = $process->getIndexerCode();
            if (in_array($code, $processed)) {
                continue;
            }
            $hasLocks = false;

            if ($process->getDepends()) {
                foreach ($process->getDepends() as $processCode) {
                    $dependProcess = $this->getProcessByCode($processCode);
                    if ($dependProcess && !in_array($processCode, $processed)) {
                        if ($checkLocks && $dependProcess->isLocked()) {
                            $hasLocks = true;
                        } else {
                            call_user_func_array(array($dependProcess, $method), $args);
                            if ($checkLocks && $dependProcess->getMode() == Mage_Index_Model_Process::MODE_MANUAL) {
                                $hasLocks = true;
                            } else {
                                $processed[] = $processCode;
                            }
                        }
                    }
                }
            }

            if (!$hasLocks) {
                call_user_func_array(array($process, $method), $args);
                $processed[] = $code;
            }
        }
    }

    /**
     * Enable/Disable keys in index tables
     *
     * @param bool $enable
     * @return Mage_Index_Model_Indexer
     */
    protected function _changeKeyStatus($enable = true)
    {
        $processed = array();
        foreach ($this->getProcessesCollection() as $process) {
            $code = $process->getIndexerCode();
            if (in_array($code, $processed)) {
                continue;
            }

            if ($process->getDepends()) {
                foreach ($process->getDepends() as $processCode) {
                    $dependProcess = $this->getProcessByCode($processCode);
                    if ($dependProcess && !in_array($processCode, $processed)) {
                        if ($this->_changeProcessKeyStatus($dependProcess, $enable)) {
                            $processed[] = $processCode;
                        }
                    }
                }
            }

            if ($this->_changeProcessKeyStatus($process, $enable)) {
                $processed[] = $code;
            }
        }

        return $this;
    }

    /**
     * Check if the event will be processed and disable/enable keys in index tables
     *
     * @param mixed|Mage_Index_Model_Process $process
     * @param bool $enable
     * @return bool
     */
    protected function _changeProcessKeyStatus($process, $enable = true)
    {
        $event = $this->_currentEvent;
        if ($process instanceof Mage_Index_Model_Process
            && $process->getMode() !== Mage_Index_Model_Process::MODE_MANUAL
            && !$process->isLocked()
            && (is_null($event)
                || ($event instanceof Mage_Index_Model_Event && $process->matchEvent($event))
                || (is_array($event) && $process->matchEntityAndType($event[0], $event[1]))
        )) {
            if ($enable) {
                $process->enableIndexerKeys();
            } else {
                $process->disableIndexerKeys();
            }
            return true;
        }
        return false;
    }

    /**
     * Allow DDL operations while indexing
     *
     * @return Mage_Index_Model_Indexer
     */
    public function allowTableChanges()
    {
        $this->_allowTableChanges = true;
        return $this;
    }

    /**
     * Disallow DDL operations while indexing
     *
     * @return Mage_Index_Model_Indexer
     */
    public function disallowTableChanges()
    {
        $this->_allowTableChanges = false;
        return $this;
    }

    /**
     * Get event type name
     *
     * @param null|string $entityType
     * @param null|string $eventType
     * @return string
     */
    protected function _getEventTypeName($entityType = null, $eventType = null)
    {
        $eventName = $entityType . '_' . $eventType;
        $eventName = trim($eventName, '_');
        if (!empty($eventName)) {
            $eventName = '_' . $eventName;
        }
        return $eventName;
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
 * @package     Mage_Index
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Index Process Collection
 *
 * @category    Mage
 * @package     Mage_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Index_Model_Resource_Process_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = 'process_collection';

    /**
     * Event prefix name
     *
     * @var string
     */
    protected $_eventPrefix = 'process_collection';

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('index/process');
    }

    /**
     * Add count of unprocessed events to process collection
     *
     * @return Mage_Index_Model_Resource_Process_Collection
     */
    public function addEventsStats()
    {
        $countsSelect = $this->getConnection()
            ->select()
            ->from($this->getTable('index/process_event'), array('process_id', 'events' => 'COUNT(*)'))
            ->where('status=?', Mage_Index_Model_Process::EVENT_STATUS_NEW)
            ->group('process_id');
        $this->getSelect()
            ->joinLeft(
                array('e' => $countsSelect),
                'e.process_id=main_table.process_id',
                array('events' => $this->getConnection()->getCheckSql(
                    $this->getConnection()->prepareSqlCondition('e.events', array('null' => null)), 0, 'e.events'
                ))
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
 * @package     Mage_Index
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Index Process Resource Model
 *
 * @category    Mage
 * @package     Mage_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Index_Model_Resource_Process extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize  table and table pk
     *
     */
    protected function _construct()
    {
        $this->_init('index/process', 'process_id');
    }

    /**
     * Update process/event association row status
     *
     * @param int $processId
     * @param int $eventId
     * @param string $status
     * @return Mage_Index_Model_Resource_Process
     */
    public function updateEventStatus($processId, $eventId, $status)
    {
        $adapter = $this->_getWriteAdapter();
        $condition = array(
            'process_id = ?' => $processId,
            'event_id = ?'   => $eventId
        );
        $adapter->update($this->getTable('index/process_event'), array('status' => $status), $condition);
        return $this;
    }

    /**
     * Register process end
     *
     * @param Mage_Index_Model_Process $process
     * @return Mage_Index_Model_Resource_Process
     */
    public function endProcess(Mage_Index_Model_Process $process)
    {
        $data = array(
            'status'    => Mage_Index_Model_Process::STATUS_PENDING,
            'ended_at'  => $this->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Register process start
     *
     * @param Mage_Index_Model_Process $process
     * @return Mage_Index_Model_Resource_Process
     */
    public function startProcess(Mage_Index_Model_Process $process)
    {
        $data = array(
            'status'        => Mage_Index_Model_Process::STATUS_RUNNING,
            'started_at'    => $this->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Register process fail
     *
     * @param Mage_Index_Model_Process $process
     * @return Mage_Index_Model_Resource_Process
     */
    public function failProcess(Mage_Index_Model_Process $process)
    {
        $data = array(
            'status'   => Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX,
            'ended_at' => $this->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Update process status field
     *
     *
     * @param Mage_Index_Model_Process $process
     * @param string $status
     * @return Mage_Index_Model_Resource_Process
     */
    public function updateStatus($process, $status)
    {
        $data = array('status' => $status);
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Updates process data
     * @param int $processId
     * @param array $data
     * @return Mage_Index_Model_Resource_Process
     */
    protected function _updateProcessData($processId, $data)
    {
        $bind = array('process_id=?' => $processId);
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $bind);

        return $this;
    }

    /**
     * Update process start date
     *
     * @param Mage_Index_Model_Process $process
     * @return Mage_Index_Model_Resource_Process
     */
    public function updateProcessStartDate(Mage_Index_Model_Process $process)
    {
        $this->_updateProcessData($process->getId(), array('started_at' => $this->formatDate(time())));
        return $this;
    }

    /**
     * Update process end date
     *
     * @param Mage_Index_Model_Process $process
     * @return Mage_Index_Model_Resource_Process
     */
    public function updateProcessEndDate(Mage_Index_Model_Process $process)
    {
        $this->_updateProcessData($process->getId(), array('ended_at' => $this->formatDate(time())));
        return $this;
    }

    /**
     * Whether transaction is already started
     *
     * @return bool
     */
    public function isInTransaction()
    {
        return $this->_getWriteAdapter()->getTransactionLevel() > 0;
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
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Calculation Model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Calculation extends Mage_Core_Model_Abstract
{
    /*
     * Identifier constant for Tax calculation before discount excluding TAX
     */
    const CALC_TAX_BEFORE_DISCOUNT_ON_EXCL      = '0_0';
    /***/

    /**
     * Identifier constant for Tax calculation before discount including TAX
     */
    const CALC_TAX_BEFORE_DISCOUNT_ON_INCL      = '0_1';


    /**
     * Identifier constant for Tax calculation after discount excluding TAX
     */
    const CALC_TAX_AFTER_DISCOUNT_ON_EXCL       = '1_0';

    /**
     * Identifier constant for Tax calculation after discount including TAX
     */
    const CALC_TAX_AFTER_DISCOUNT_ON_INCL       = '1_1';


    /**
     * Identifier constant for unit based calculation
     */
    protected $_rates                           = array();
    /**
     * Identifier constant for row based calculation
     */
    protected $_ctc                             = array();
    /**
     * Identifier constant for total based calculation
     */
    protected $_ptc                             = array();

    /**
     * CALC_UNIT_BASE
     */
    const CALC_UNIT_BASE = 'UNIT_BASE_CALCULATION';

    /**
     * CALC_ROW_BASE
     */
    const CALC_ROW_BASE = 'ROW_BASE_CALCULATION';

    /**
     * CALC_TOTAL_BASE
     */
    const CALC_TOTAL_BASE = 'TOTAL_BASE_CALCULATION';

    /**
     * Cache to hold the rates
     *
     * @var array
     */
    protected $_rateCache = array();

    /**
     * Store the rate calculation process
     *
     * @var array
     */
    protected $_rateCalculationProcess = array();

    /**
     * Hold the customer
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * Customer group
     *
     * @var string
     */
    protected $_defaultCustomerTaxClass = null;

    /**
     * Tax helper
     *
     * @var Mage_Tax_Helper_Data
     */
    protected $_taxHelper;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('tax/calculation');
    }

    /**
     * Initialize tax helper
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        parent::__construct();
        $this->_taxHelper = !empty($args['helper']) ? $args['helper'] : Mage::helper('tax');
    }

    /**
     * Specify customer object which can be used for rate calculation
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Tax_Model_Calculation
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Get the customer default customer class
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getDefaultCustomerTaxClass($store = null)
    {
        if ($this->_defaultCustomerTaxClass === null) {
            $defaultCustomerGroup = Mage::helper('customer')->getDefaultCustomerGroupId($store);
            $this->_defaultCustomerTaxClass = Mage::getModel('customer/group')->getTaxClassId($defaultCustomerGroup);
        }
        return $this->_defaultCustomerTaxClass;
    }

    /**
     * Get customer object
     *
     * @return  Mage_Customer_Model_Customer | false
     */
    public function getCustomer()
    {
        if ($this->_customer === null) {
            $session = Mage::getSingleton('customer/session');
            if ($session->isLoggedIn()) {
                $this->_customer = $session->getCustomer();
            } elseif ($session->getCustomerId()) {
                $this->_customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
            } else {
                $this->_customer = false;
            }
        }
        return $this->_customer;
    }

    /**
     * Delete calculation settings by rule id
     *
     * @param   int $ruleId
     * @return  Mage_Tax_Model_Calculation
     */
    public function deleteByRuleId($ruleId)
    {
        $this->_getResource()->deleteByRuleId($ruleId);
        return $this;
    }

    /**
     * Get calculation rates by rule id
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getRates($ruleId)
    {
        if (!isset($this->_rates[$ruleId])) {
            $this->_rates[$ruleId] = $this->_getResource()->getDistinct('tax_calculation_rate_id', $ruleId);
        }
        return $this->_rates[$ruleId];
    }

    /**
     * Get allowed customer tax classes by rule id
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getCustomerTaxClasses($ruleId)
    {
        if (!isset($this->_ctc[$ruleId])) {
            $this->_ctc[$ruleId] = $this->_getResource()->getDistinct('customer_tax_class_id', $ruleId);
        }
        return $this->_ctc[$ruleId];
    }

    /**
     * Get allowed product tax classes by rule id
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getProductTaxClasses($ruleId)
    {
        if (!isset($this->_ptc[$ruleId])) {
            $this->_ptc[$ruleId] = $this->getResource()->getDistinct('product_tax_class_id', $ruleId);
        }
        return $this->_ptc[$ruleId];
    }

    /**
     * Aggregate tax calculation data to array
     *
     * @return array
     */
    protected function _formCalculationProcess()
    {
        $title = $this->getRateTitle();
        $value = $this->getRateValue();
        $id = $this->getRateId();

        $rate = array(
            'code' => $title, 'title' => $title, 'percent' => $value, 'position' => 1, 'priority' => 1);

        $process = array();
        $process['percent'] = $value;
        $process['id'] = "{$id}-{$value}";
        $process['rates'][] = $rate;

        return array($process);
    }

    /**
     * Get calculation tax rate by specific request
     *
     * @param   Varien_Object $request
     * @return  float
     */
    public function getRate($request)
    {
        if (!$request->getCountryId() || !$request->getCustomerClassId() || !$request->getProductClassId()) {
            return 0;
        }

        $cacheKey = $this->_getRequestCacheKey($request);
        if (!isset($this->_rateCache[$cacheKey])) {
            $this->unsRateValue();
            $this->unsCalculationProcess();
            $this->unsEventModuleId();
            Mage::dispatchEvent('tax_rate_data_fetch', array(
                'request' => $request));
            if (!$this->hasRateValue()) {
                $rateInfo = $this->_getResource()->getRateInfo($request);
                $this->setCalculationProcess($rateInfo['process']);
                $this->setRateValue($rateInfo['value']);
            } else {
                $this->setCalculationProcess($this->_formCalculationProcess());
            }
            $this->_rateCache[$cacheKey] = $this->getRateValue();
            $this->_rateCalculationProcess[$cacheKey] = $this->getCalculationProcess();
        }
        return $this->_rateCache[$cacheKey];
    }

    /**
     * Get cache key value for specific tax rate request
     *
     * @param   $request
     * @return  string
     */
    protected function _getRequestCacheKey($request)
    {
        $key = $request->getStore() ? $request->getStore()->getId() . '|' : '';
        $key .= $request->getProductClassId() . '|' . $request->getCustomerClassId() . '|'
            . $request->getCountryId() . '|' . $request->getRegionId() . '|' . $request->getPostcode();
        return $key;
    }

    /**
     * Get tax rate based on store shipping origin address settings
     * This rate can be used for conversion store price including tax to
     * store price excluding tax
     *
     * @param   Varien_Object $request
     * @return  float
     */
    public function getStoreRate($request, $store = null)
    {
        $storeRequest = $this->getRateOriginRequest($store)
            ->setProductClassId($request->getProductClassId());
        return $this->getRate($storeRequest);
    }

    /**
     * Get tax rate based on store shipping origin address settings
     * This rate can be used for conversion store price including tax to
     * store price excluding tax
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param null|Mage_Core_Model_Store $store
     * @return float
     */
    public function getStoreRateForItem($item, $store = null)
    {
        $storeRequest = $this->getRateOriginRequest($store)
            ->setProductClassId($item->getProduct()->getTaxClassId());
        return $this->getRate($storeRequest);
    }

    /**
     * Get request object for getting tax rate based on store shippig original address
     *
     * @param   null|store $store
     * @return  Varien_Object
     */
    public function getRateOriginRequest($store = null)
    {
        $request = new Varien_Object();
        $request->setCountryId(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_COUNTRY_ID, $store))
            ->setRegionId(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_REGION_ID, $store))
            ->setPostcode(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_POSTCODE, $store))
            ->setCustomerClassId($this->getDefaultCustomerTaxClass($store))
            ->setStore($store);
        return $request;
    }

    /**
     * Return the default rate request. It can be either based on store address or customer address
     *
     * @param type $store
     * @return \Varien_Object
     */
    public function getDefaultRateRequest($store =null)
    {
        if ($this->_taxHelper->isCrossBorderTradeEnabled($store)) {
            //If cross border trade is enabled, we will use customer tax rate as store tax rate
            return $this->getRateRequest(null, null, null, $store);
        } else {
            return $this->getRateOriginRequest($store);
        }
    }

    /**
     * Get request object with information necessary for getting tax rate
     * Request object contain:
     *  country_id (->getCountryId())
     *  region_id (->getRegionId())
     *  postcode (->getPostcode())
     *  customer_class_id (->getCustomerClassId())
     *  store (->getStore())
     *
     * @param   null|false|Varien_Object $shippingAddress
     * @param   null|false|Varien_Object $billingAddress
     * @param   null|int $customerTaxClass
     * @param   null|int $store
     * @return  Varien_Object
     */
    public function getRateRequest(
        $shippingAddress = null,
        $billingAddress = null,
        $customerTaxClass = null,
        $store = null)
    {
        if ($shippingAddress === false && $billingAddress === false && $customerTaxClass === false) {
            return $this->getRateOriginRequest($store);
        }
        $address = new Varien_Object();
        $customer = $this->getCustomer();
        $basedOn = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store);

        if (($shippingAddress === false && $basedOn == 'shipping')
            || ($billingAddress === false && $basedOn == 'billing')
        ) {
            $basedOn = 'default';
        } else {
            if ((($billingAddress === false || is_null($billingAddress) || !$billingAddress->getCountryId())
                && $basedOn == 'billing')
                || (($shippingAddress === false || is_null($shippingAddress) || !$shippingAddress->getCountryId())
                    && $basedOn == 'shipping')
            ) {
                if ($customer) {
                    $defBilling = $customer->getDefaultBillingAddress();
                    $defShipping = $customer->getDefaultShippingAddress();

                    if ($basedOn == 'billing' && $defBilling && $defBilling->getCountryId()) {
                        $billingAddress = $defBilling;
                    } else if ($basedOn == 'shipping' && $defShipping && $defShipping->getCountryId()) {
                        $shippingAddress = $defShipping;
                    } else {
                        $basedOn = 'default';
                    }
                } else {
                    $basedOn = 'default';
                }
            }
        }

        switch ($basedOn) {
            case 'billing':
                $address = $billingAddress;
                break;
            case 'shipping':
                $address = $shippingAddress;
                break;
            case 'origin':
                $address = $this->getRateOriginRequest($store);
                break;
            case 'default':
                $address
                    ->setCountryId(Mage::getStoreConfig(
                    Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                    $store))
                    ->setRegionId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION, $store))
                    ->setPostcode(Mage::getStoreConfig(
                    Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_POSTCODE,
                    $store));
                break;
        }

        if (is_null($customerTaxClass) && $customer) {
            $customerTaxClass = $customer->getTaxClassId();
        } elseif (($customerTaxClass === false) || !$customer) {
            $customerTaxClass = Mage::getModel('customer/group')
                    ->getTaxClassId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }

        $request = new Varien_Object();
        $request
            ->setCountryId($address->getCountryId())
            ->setRegionId($address->getRegionId())
            ->setPostcode($address->getPostcode())
            ->setStore($store)
            ->setCustomerClassId($customerTaxClass);
        return $request;
    }

    /**
     * Compare data and rates for two tax rate requests for same products (product tax class ids).
     * Returns true if requests are similar (i.e. equal taxes rates will be applied to them)
     *
     * Notice:
     * a) productClassId MUST be identical for both requests, because we intend to check selling SAME products to DIFFERENT locations
     * b) due to optimization productClassId can be array of ids, not only single id
     *
     * @param   Varien_Object $first
     * @param   Varien_Object $second
     * @return  bool
     */
    public function compareRequests($first, $second)
    {
        $country = $first->getCountryId() == $second->getCountryId();
        // "0" support for admin dropdown with --please select--
        $region  = (int)$first->getRegionId() == (int)$second->getRegionId();
        $postcode = $first-> getPostcode() == $second-> getPostcode();
        $taxClass = $first-> getCustomerClassId() == $second-> getCustomerClassId();

        if ($country && $region && $postcode && $taxClass) {
            return true;
        }
        /**
         * Compare available tax rates for both requests
         */
        $firstReqRates = $this->_getResource()->getRateIds($first);
        $secondReqRates = $this->_getResource()->getRateIds($second);
        if ($firstReqRates === $secondReqRates) {
            return true;
        }

        /**
         * If rates are not equal by ids then compare actual values
         * All product classes must have same rates to assume requests been similar
         */
        $productClassId1 = $first->getProductClassId(); // Save to set it back later
        $productClassId2 = $second->getProductClassId(); // Save to set it back later

        // Ids are equal for both requests, so take any of them to process
        $ids = is_array($productClassId1) ? $productClassId1 : array($productClassId1);
        $identical = true;
        foreach ($ids as $productClassId) {
            $first->setProductClassId($productClassId);
            $rate1 = $this->getRate($first);

            $second->setProductClassId($productClassId);
            $rate2 = $this->getRate($second);

            if ($rate1 != $rate2) {
                $identical = false;
                break;
            }
        }

        $first->setProductClassId($productClassId1);
        $second->setProductClassId($productClassId2);

        return $identical;
    }

    /**
     * Gets the tax rates by type
     *
     * @param Varien_Object $request
     * @param string $fieldName
     * @param string $type
     * @return array
     */
    protected function _getRates($request, $fieldName, $type)
    {
        $result = array();
        $classes = Mage::getModel('tax/class')->getCollection()
            ->addFieldToFilter('class_type', $type)
            ->load();
        foreach ($classes as $class) {
            $request->setData($fieldName, $class->getId());
            $result[$class->getId()] = $this->getRate($request);
        }

        return $result;
    }

    /**
     * Gets rates for all the product tax classes
     *
     * @param Varien_Object $request
     * @return array
     */
    public function getRatesForAllProductTaxClasses($request)
    {
        return $this->_getRates($request, 'product_class_id', Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
    }

    /**
     * Gets rates for all the customer tax classes
     *
     * @param Varien_Object $request
     * @return array
     */
    public function getRatesForAllCustomerTaxClasses($request)
    {
        return $this->_getRates($request, 'customer_class_id', Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER);
    }

    /**
     * Get information about tax rates applied to request
     *
     * @param   Varien_Object $request
     * @return  array
     */
    public function getAppliedRates($request)
    {
        if (!$request->getCountryId() || !$request->getCustomerClassId() || !$request->getProductClassId()) {
            return array();
        }

        $cacheKey = $this->_getRequestCacheKey($request);
        if (!isset($this->_rateCalculationProcess[$cacheKey])) {
            $this->_rateCalculationProcess[$cacheKey] = $this->_getResource()->getCalculationProcess($request);
        }
        return $this->_rateCalculationProcess[$cacheKey];
    }

    /**
     * Get rate ids applicable for some address
     *
     * @param Varien_Object $request
     * @return array
     */
    public function getApplicableRateIds($request)
    {
        return $this->_getResource()->getApplicableRateIds($request);
    }

    /**
     * Get the calculation process
     *
     * @param array $rates
     * @return mixed
     */
    public function reproduceProcess($rates)
    {
        return $this->getResource()->getCalculationProcess(null, $rates);
    }

    /**
     * Get rates by customer tax class
     *
     * @param int $customerTaxClass
     * @return mixed
     */
    public function getRatesByCustomerTaxClass($customerTaxClass)
    {
        return $this->getResource()->getRatesByCustomerTaxClass($customerTaxClass);
    }

    /**
     * Get rates by customer and product classes
     *
     * @param int $customerTaxClass
     * @param int $productTaxClass
     * @return mixed
     */
    public function getRatesByCustomerAndProductTaxClasses($customerTaxClass, $productTaxClass)
    {
        return $this->getResource()->getRatesByCustomerTaxClass($customerTaxClass, $productTaxClass);
    }

    /**
     * Calculate rated tax abount based on price and tax rate.
     * If you are using price including tax $priceIncludeTax should be true.
     *
     * @param   float $price
     * @param   float $taxRate
     * @param   boolean $priceIncludeTax
     * @param   boolean $round
     * @return  float
     */
    public function calcTaxAmount($price, $taxRate, $priceIncludeTax = false, $round = true)
    {
        $taxRate = $taxRate / 100;

        if ($priceIncludeTax) {
            $amount = $price * (1 - 1 / (1 + $taxRate));
        } else {
            $amount = $price * $taxRate;
        }

        if ($round) {
            return $this->round($amount);
        }

        return $amount;
    }

    /**
     * Truncate number to specified precision
     *
     * @param   float $price
     * @param   int $precision
     * @return  float
     */
    public function truncate($price, $precision = 4)
    {
        $exp = pow(10, $precision);
        $price = floor($price * $exp) / $exp;
        return $price;
    }

    /**
     * Round tax amount
     *
     * @param   float $price
     * @return  float
     */
    public function round($price)
    {
        return Mage::app()->getStore()->roundPrice($price);
    }

    /**
     * Round price up
     *
     * @param   float $price
     * @return  float
     */
    public function roundUp($price)
    {
        return ceil($price * 100) / 100;
    }

    /**
     * Round price down
     *
     * @param   float $price
     * @return  float
     */
    public function roundDown($price)
    {
        return floor($price * 100) / 100;
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
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WEEE data helper
 *
 * @category Mage
 * @package  Mage_Weee
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Weee_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Config Path for FPT
     */
    const XML_PATH_FPT_ENABLED = 'tax/weee/enable';

    /**
     *'FPT Tax Configuration' for TAXED
     */
    const TAXED = '1';

    /**
     *'FPT Tax Configuration' for LOADED_AND_DISPLAY_WITH_TAX
     */
    const LOADED_AND_DISPLAY_WITH_TAX = '2';

    /**
     * Current store, in the case of backend order, it could be different from admin store
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * @var array
     */
    protected $_storeDisplayConfig   = array();

    /**
     * Get weee amount display type on product view page
     *
     * @param   mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display', $store);
    }

    /**
     * Get weee amount display type on product list page
     *
     * @param   mixed $store
     * @return  int
     */
    public function getListPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_list', $store);
    }

    /**
     * Get weee amount display type in sales modules
     *
     * @param   mixed $store
     * @return  int
     */
    public function getSalesPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_sales', $store);
    }

    /**
     * Get weee amount display type in email templates
     *
     * @param   mixed $store
     * @return  int
     */
    public function getEmailPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_email', $store);
    }

    /**
     * Check if weee tax amount should be discounted
     *
     * @param   mixed $store
     * @return  bool
     */
    public function isDiscounted($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/discount', $store);
    }

    /**
     * Check if weee tax amount should be taxable
     *
     * @param   mixed $store
     * @return  bool
     */
    public function isTaxable($store = null)
    {
        return Mage::getStoreConfig('tax/weee/apply_vat', $store) == self::TAXED ||
            Mage::getStoreConfig('tax/weee/apply_vat', $store) == self::LOADED_AND_DISPLAY_WITH_TAX;
    }

    /**
     * Returns true if default store tax is already applied to the FPT(weee)
     *
     * @param mixed $store
     * @return bool
     */
    public function isTaxIncluded($store = null)
    {
        return Mage::getStoreConfig('tax/weee/apply_vat', $store) == self::LOADED_AND_DISPLAY_WITH_TAX;
    }

    /**
     * Get Weee Tax Configuration Type
     *
     * @param   mixed $store
     * @return  int
     */
    public function getTaxType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/apply_vat', $store);
    }

    /**
     * Check if weee tax amount should be included to subtotal
     *
     * @param   mixed $store
     * @return  bool
     */
    public function includeInSubtotal($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/include_in_subtotal', $store);
    }

    /**
     * Get weee tax amount for product based on shipping and billing addresses, website and tax settings
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   null|Mage_Customer_Model_Address_Abstract $shipping
     * @param   null|Mage_Customer_Model_Address_Abstract $billing
     * @param   mixed $website
     * @param   bool $calculateTaxes
     * @return  float
     */
    public function getAmount($product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false)
    {
        if ($this->isEnabled()) {
            return Mage::getSingleton('weee/tax')->
                getWeeeAmount($product, $shipping, $billing, $website, $calculateTaxes);
        }
        return 0;
    }

    /**
     * Returns diaplay type for price accordingly to current zone
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array|null                 $compareTo
     * @param string                     $zone
     * @param Mage_Core_Model_Store      $store
     * @return bool|int
     */
    public function typeOfDisplay($product, $compareTo = null, $zone = null, $store = null)
    {
        if (!$this->isEnabled($store)) {
            return false;
        }
        switch ($zone) {
            case 'product_view':
                $type = $this->getPriceDisplayType($store);
                break;
            case 'product_list':
                $type = $this->getListPriceDisplayType($store);
                break;
            case 'sales':
                $type = $this->getSalesPriceDisplayType($store);
                break;
            case 'email':
                $type = $this->getEmailPriceDisplayType($store);
                break;
            default:
                if (Mage::registry('current_product')) {
                    $type = $this->getPriceDisplayType($store);
                } else {
                    $type = $this->getListPriceDisplayType($store);
                }
                break;
        }

        if (is_null($compareTo)) {
            return $type;
        } else {
            if (is_array($compareTo)) {
                return in_array($type, $compareTo);
            } else {
                return $type == $compareTo;
            }
        }
    }

    /**
     * Proxy for Mage_Weee_Model_Tax::getProductWeeeAttributes()
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|false|Varien_Object   $shipping
     * @param null|false|Varien_Object   $billing
     * @param Mage_Core_Model_Website    $website
     * @param bool                       $calculateTaxes
     * @return array
     */
    public function getProductWeeeAttributes($product, $shipping = null, $billing = null,
                                             $website = null, $calculateTaxes = false)
    {
        return Mage::getSingleton('weee/tax')
            ->getProductWeeeAttributes($product, $shipping, $billing, $website, $calculateTaxes);
    }

    /**
     * Returns applied weee taxes
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return array
     */
    public function getApplied($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item_Abstract) {
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $result = array();
                foreach ($item->getChildren() as $child) {
                    $childData = $this->getApplied($child);
                    if (is_array($childData)) {
                        $result = array_merge($result, $childData);
                    }
                }
                return $result;
            }
        }

        /**
         * if order item data is old enough then weee_tax_applied cab be
         * not valid serialized data
         */
        $data = $item->getWeeeTaxApplied();
        if (empty($data)) {
            return array();
        }
        return unserialize($item->getWeeeTaxApplied());
    }

    /**
     * Sets applied weee taxes
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param array                                $value
     * @return Mage_Weee_Helper_Data
     */
    public function setApplied($item, $value)
    {
        $item->setWeeeTaxApplied(serialize($value));
        return $this;
    }

    /**
     * Returns array of weee attributes allowed for display
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductWeeeAttributesForDisplay($product)
    {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes($product, null, null, null, $this->typeOfDisplay($product, 1));
        }
        return array();
    }

    /**
     * Get Product Weee attributes for price renderer
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|false|Varien_Object $shipping Shipping Address
     * @param null|false|Varien_Object $billing Billing Address
     * @param null|Mage_Core_Model_Website $website
     * @param mixed $calculateTaxes
     * @return array
     */
    public function getProductWeeeAttributesForRenderer($product, $shipping = null, $billing = null,
                                                        $website = null, $calculateTaxes = false)
    {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes(
                $product,
                $shipping,
                $billing,
                $website,
                $calculateTaxes ? $calculateTaxes : $this->typeOfDisplay($product, 1)
            );
        }
        return array();
    }

    /**
     * Returns amount to display excluding taxes
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getAmountForDisplay($product)
    {
        if ($this->isEnabled()) {
            $attributes = $this->getProductWeeeAttributesForRenderer($product,
                null, null, null, true);

            if (is_array($attributes)) {
                $amount = 0;
                foreach ($attributes as $attribute) {
                    /* @var $attribute Varien_Object */
                    $amount += $attribute->getAmount();
                }
                return $amount;
            }
        }
        return 0;
    }

    /**
     * Returns amount to display including taxes
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getAmountForDisplayInclTaxes($product)
    {
        if ($this->isEnabled()) {
            $attributes = $this->getProductWeeeAttributesForRenderer($product,
                null, null, null, true);
            return $this->getAmountInclTaxes($attributes);
        }
        return 0;
    }

    /**
     * Returns original amount
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    public function getOriginalAmount($product)
    {
        if ($this->isEnabled()) {
            return Mage::getModel('weee/tax')->getWeeeAmount($product, null, null, null, false, true);
        }
        return 0;
    }

    /**
     * Adds HTML containers and formats tier prices accordingly to the currency used
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array                      $tierPrices
     * @param boolean                    $includeIndex
     * @return Mage_Weee_Helper_Data
     */
    public function processTierPrices($product, &$tierPrices, $includeIndex = true)
    {
        $weeeAmountInclTax = $this->getAmountForDisplayInclTaxes($product);
        $weeeAmount = $this->getAmountForDisplay($product);
        $store = Mage::app()->getStore();
        foreach ($tierPrices as $index => &$tier) {
            $spanTag = '<span class="price tier-' . ($includeIndex ? $index : 'fixed');
            $html = $store->formatPrice($store->convertPrice(
                Mage::helper('tax')->getPrice($product, $tier['website_price'], true) + $weeeAmountInclTax), false);
            $tier['formated_price_incl_weee'] = $spanTag . '-incl-tax">' . $html . '</span>';
            $html = $store->formatPrice($store->convertPrice(
                Mage::helper('tax')->getPrice($product, $tier['website_price']) + $weeeAmount), false);
            $tier['formated_price_incl_weee_only'] = $spanTag . '">' . $html . '</span>';
            $tier['formated_weee'] = $store->formatPrice($store->convertPrice($weeeAmount));
        }
        return $this;
    }

    /**
     * Check if fixed taxes are used in system
     *
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        if ($store == null && $this->_store) {
            //This is needed when order is created from backend
            $store = $this->_store;
        }
        return Mage::getStoreConfig(self::XML_PATH_FPT_ENABLED, $store);
    }

    /**
     * Set the store for the current quote
     *
     * @param Mage_Core_Model_Store $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * Returns all summed weee taxes with all local taxes applied
     *
     * @throws Mage_Exception
     * @param array $attributes Array of Varien_Object, result from getProductWeeeAttributes()
     * @return float
     */
    public function getAmountInclTaxes($attributes)
    {
        if (is_array($attributes)) {
            $amount = 0;
            foreach ($attributes as $attribute) {
                /* @var $attribute Varien_Object */
                $amount += $attribute->getAmount() + $attribute->getTaxAmount();
            }
        } else {
            throw new Mage_Exception('$attributes must be an array');
        }

        return (float)$amount;
    }

    /**
     * Check if the configuration for the particular store causes conflicts
     *
     * @param Mage_Core_Model_Store|null $store
     * @return boolean
     */
    public function validateCatalogPricesAndFptConfiguration($store = null)
    {
        // Check the configuration - Weee enabled and catalog display
        $priceIncludesTax = $this->_getHelper('tax')->priceIncludesTax($store);
        // $priceIncludesTax = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $store);
        $fptTaxConfig = $this->getTaxType($store);

        // If FPT == Including tax & Catalog Prices Excluding Tax or
        // FPT = Taxed (Meaning - go ahead and calculate tax on fpt and Catalog Prices Include tax)
        return (($fptTaxConfig == Mage_Tax_Model_Config::FPT_LOADED_DISPLAY_WITH_TAX && !$priceIncludesTax)
            || ($fptTaxConfig == Mage_Tax_Model_Config::FPT_TAXED && $priceIncludesTax));
    }

    /**
     * Set a value to a specific property searching FPT by title for the Item
     *
     * @param Mage_Core_Model_Abstract $item
     * @param string $title
     * @param string $property
     * @param string $value
     */
    public function setWeeeTaxesAppliedProperty($item, $title, $property, $value)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        foreach ($weeeTaxAppliedAmounts as &$weeeTaxAppliedAmount) {
            //if the title is not set we set the value to all fields
            if (isset($title)) {
                if ($weeeTaxAppliedAmount['title'] == $title) {
                    $weeeTaxAppliedAmount[$property] = $value;
                }
            } else {
                $weeeTaxAppliedAmount[$property] = $value;
            }
        }
        $item->setWeeeTaxApplied(serialize($weeeTaxAppliedAmounts));
    }

    /**
     * Get the total weee tax
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getWeeeTaxInclTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalWeeeTaxIncTaxApplied = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalWeeeTaxIncTaxApplied += max($weeeTaxAppliedAmount['amount_incl_tax'], 0);
        }
        return $totalWeeeTaxIncTaxApplied;
    }

    /**
     * Get the total base weee tax
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getBaseWeeeTaxInclTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalBaseWeeeTaxIncTaxApplied = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalBaseWeeeTaxIncTaxApplied += max($weeeTaxAppliedAmount['base_amount_incl_tax'], 0);
        }
        return $totalBaseWeeeTaxIncTaxApplied;
    }

    /**
     * Get the total weee including tax by row
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getRowWeeeTaxInclTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalWeeeTaxIncTaxApplied = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalWeeeTaxIncTaxApplied += max($weeeTaxAppliedAmount['row_amount_incl_tax'], 0);
        }
        return $totalWeeeTaxIncTaxApplied;
    }

    /**
     * Get the total base weee including tax by row
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getBaseRowWeeeTaxInclTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalWeeeTaxIncTaxApplied = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalWeeeTaxIncTaxApplied += max($weeeTaxAppliedAmount['base_row_amount_incl_tax'], 0);
        }
        return $totalWeeeTaxIncTaxApplied;
    }

    /**
     * Get the total tax applied on weee by unit
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getTotalTaxAppliedForWeeeTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalTaxForWeeeTax = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalTaxForWeeeTax += max($weeeTaxAppliedAmount['amount_incl_tax']
                - $weeeTaxAppliedAmount['amount'], 0);
        }
        return $totalTaxForWeeeTax;
    }

    /**
     * Get the total tax applied on weee by unit
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getBaseTotalTaxAppliedForWeeeTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalTaxForWeeeTax = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalTaxForWeeeTax += max($weeeTaxAppliedAmount['base_amount_incl_tax']
                - $weeeTaxAppliedAmount['base_amount'], 0);
        }
        return $totalTaxForWeeeTax;
    }

    /**
     * Get the Total tax applied for Weee
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getTotalRowTaxAppliedForWeeeTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalTaxForWeeeTax = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalTaxForWeeeTax += max($weeeTaxAppliedAmount['row_amount_incl_tax']
                - $weeeTaxAppliedAmount['row_amount'], 0);
        }
        return $totalTaxForWeeeTax;
    }

    /**
     * Get the Total tax applied in base for Weee
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getBaseTotalRowTaxAppliedForWeeeTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalTaxForWeeeTax = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalTaxForWeeeTax += max($weeeTaxAppliedAmount['base_row_amount_incl_tax']
                - $weeeTaxAppliedAmount['base_row_amount'], 0);
        }
        return $totalTaxForWeeeTax;
    }

    /**
     * Calculate row weee amount for an order, invoice or credit memo item
     * The returned value may contain discount if the discount is not included in
     * the discount for subtotal
     *
     * @param mixed $item
     * @return float
     */
    public function getRowWeeeAmountAfterDiscount($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $weeeAmountInclDiscount = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $weeeAmountInclDiscount += $weeeTaxAppliedAmount['row_amount'];
            if (!$this->includeInSubtotal()) {
                $weeeAmountInclDiscount -= isset($weeeTaxAppliedAmount['weee_discount'])
                    ? $weeeTaxAppliedAmount['weee_discount'] : 0;
            }
        }
        return $weeeAmountInclDiscount;
    }

    /**
     * Calculate base row weee amount for an order, invoice or credit memo item
     * The returned value may contain discount if the discount is not included in
     * the discount for subtotal
     *
     * @param mixed $item
     * @return float
     */
    public function getBaseRowWeeeAmountAfterDiscount($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $baseWeeeAmountInclDiscount = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $baseWeeeAmountInclDiscount += $weeeTaxAppliedAmount['base_row_amount'];
            if (!$this->includeInSubtotal()) {
                $baseWeeeAmountInclDiscount -= isset($weeeTaxAppliedAmount['base_weee_discount'])
                    ? $weeeTaxAppliedAmount['base_weee_discount'] : 0;
            }
        }
        return $baseWeeeAmountInclDiscount;
    }

    /**
     * Get The Helper with the name provider
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
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
 * HTML select element block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Html_Select extends Mage_Core_Block_Abstract
{

    protected $_options = array();

    /**
     * Get options of the element
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set options for the HTML select
     *
     * @param array $options
     * @return Mage_Core_Block_Html_Select
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Add an option to HTML select
     *
     * @param string $value  HTML value
     * @param string $label  HTML label
     * @param array  $params HTML attributes
     * @return Mage_Core_Block_Html_Select
     */
    public function addOption($value, $label, $params=array())
    {
        $this->_options[] = array('value' => $value, 'label' => $label, 'params' => $params);
        return $this;
    }

    /**
     * Set element's HTML ID
     *
     * @param string $id ID
     * @return Mage_Core_Block_Html_Select
     */
    public function setId($id)
    {
        $this->setData('id', $id);
        return $this;
    }

    /**
     * Set element's CSS class
     *
     * @param string $class Class
     * @return Mage_Core_Block_Html_Select
     */
    public function setClass($class)
    {
        $this->setData('class', $class);
        return $this;
    }

    /**
     * Set element's HTML title
     *
     * @param string $title Title
     * @return Mage_Core_Block_Html_Select
     */
    public function setTitle($title)
    {
        $this->setData('title', $title);
        return $this;
    }

    /**
     * HTML ID of the element
     *
     * @return string
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * CSS class of the element
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getData('class');
    }

    /**
     * Returns HTML title of the element
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Render HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = '<select name="' . $this->getName() . '" id="' . $this->getId() . '" class="'
            . $this->getClass() . '" title="' . $this->getTitle() . '" ' . $this->getExtraParams() . '>';
        $values = $this->getValue();

        if (!is_array($values)){
            if (!is_null($values)) {
                $values = array($values);
            } else {
                $values = array();
            }
        }

        $isArrayOption = true;
        foreach ($this->getOptions() as $key => $option) {
            if ($isArrayOption && is_array($option)) {
                $value  = $option['value'];
                $label  = (string)$option['label'];
                $params = (!empty($option['params'])) ? $option['params'] : array();
            } else {
                $value = (string)$key;
                $label = (string)$option;
                $isArrayOption = false;
                $params = array();
            }

            if (is_array($value)) {
                $html .= '<optgroup label="' . $label . '">';
                foreach ($value as $keyGroup => $optionGroup) {
                    if (!is_array($optionGroup)) {
                        $optionGroup = array(
                            'value' => $keyGroup,
                            'label' => $optionGroup
                        );
                    }
                    $html .= $this->_optionToHtml(
                        $optionGroup,
                        in_array($optionGroup['value'], $values)
                    );
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_optionToHtml(
                    array(
                        'value' => $value,
                        'label' => $label,
                        'params' => $params
                    ),
                    in_array($value, $values)
                );
            }
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * Return option HTML node
     *
     * @param array $option
     * @param boolean $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected = false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' #{option_extra_attr_' . self::calcOptionHash($option['value']) . '}';
        }

        $params = '';
        if (!empty($option['params']) && is_array($option['params'])) {
            foreach ($option['params'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $keyMulti => $valueMulti) {
                        $params .= sprintf(' %s="%s" ', $keyMulti, $valueMulti);
                    }
                } else {
                    $params .= sprintf(' %s="%s" ', $key, $value);
                }
            }
        }

        return sprintf('<option value="%s"%s %s>%s</option>',
            $this->escapeHtml($option['value']),
            $selectedHtml,
            $params,
            $this->escapeHtml($option['label']));
    }

    /**
     * Alias for toHtml()
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }

    /**
     * Calculate CRC32 hash for option value
     *
     * @param string $optionValue Value of the option
     * @return string
     */
    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   BL
 * @package    BL_CustomGrid
 * @copyright  Copyright (c) 2012 Benoît Leulliette <benoit.leulliette@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class BL_CustomGrid_Model_Config_Abstract extends Varien_Object
{
    protected $_acceptParameters = false;
    
    abstract public function getConfigType();
    
    public function getXmlConfig()
    {
        return Mage::getSingleton('customgrid/config')->getXmlConfig($this->getConfigType());
    }
    
    public function getXmlElementByCode($code)
    {
        $elements = $this->getXmlConfig()->getXpath($code);
        if (is_array($elements) && isset($elements[0]) 
            && ($elements[0] instanceof Varien_Simplexml_Element)) {
            return $elements[0];
        }
        return null;
    }
    
    public function getConfigAsXml($code)
    {
        return $this->getXmlElementByCode($code);
    }
    
    public function getElementsXml()
    {
        return $this->getXmlConfig()->getNode();
    }
    
    public function getConfigAsObject($code)
    {
        $xml = $this->getConfigAsXml($code);
        $object = new Varien_Object();
        
        if ($xml === null) {
            return $object;
        }
        
        // Save all nodes to object data
        $object->setCode($code);
        $object->setData($xml->asCanonicalArray());
        
        // Set module for translations etc..
        $module = $object->getData('@/module');
        $object->setModule($module ? $module : 'customgrid');
        
        // Set type
        $type = $object->getData('@/type');
        $object->setType($type);
        
        // Translate name, description and help
        $helper = Mage::helper($object->getModule());
        
        if ($object->hasName()) {
            $object->setName($helper->__((string)$object->getName()));
        }
        if ($object->hasDescription()) {
            $object->setDescription($helper->__((string)$object->getDescription()));
        }
        if ($object->hasHelp()) {
            $object->setHelp($helper->__((string)$object->getHelp()));
        }
        
        if ($this->_acceptParameters) {
            // Correct element parameters and convert its data to objects if needed
            $params = $object->getData('parameters');
            $newParams = array();
            
            if (is_array($params)) {
                $sortOrder = 0;
                foreach ($params as $key => $data) {
                    if (is_array($data)) {
                        $data['key'] = $key;
                        $data['sort_order'] = (isset($data['sort_order']) ? (int)$data['sort_order'] : $sortOrder);
                        
                        // Prepare values (for dropdowns) specified directly in configuration
                        $values = array();
                        if (isset($data['values']) && is_array($data['values'])) {
                            foreach ($data['values'] as $value) {
                                if (isset($value['label']) && isset($value['value'])) {
                                    $values[] = $value;
                                }
                            }
                        }
                        $data['values'] = $values;
                        
                        // Prepare helper block object
                        if (isset($data['helper_block'])) {
                            $helper = new Varien_Object();
                            if (isset($data['helper_block']['data']) && is_array($data['helper_block']['data'])) {
                                $helper->addData($data['helper_block']['data']);
                            }
                            if (isset($data['helper_block']['type'])) {
                                $helper->setType($data['helper_block']['type']);
                            }
                            $data['helper_block'] = $helper;
                        }
                        
                        $newParams[$key] = new Varien_Object($data);
                        $sortOrder++;
                    }
                }
            }
            
            uasort($newParams, array($this, '_sortParameters'));
            $object->setData('parameters', $newParams);
        }
        
        return $object;
    }
    
    public function getElementArrayValues($element, $values, $helper)
    {
        return array();
    }
    
    public function getElementsArray()
    {
        if (!$this->_getData('elements_array')) {
            $result = array();
            
            if ($this->getElementsXml()) {
                foreach ($this->getElementsXml()->children() as $element) {
                    $helper = ($element->getAttribute('module') ? $element->getAttribute('module') : 'customgrid');
                    $helper = Mage::helper($helper);
                    
                    $values = array(
                        'code' => $element->getName(),
                        'type' => $element->getAttribute('type'),
                        'name' => $helper->__((string)$element->name),
                        'help' => $helper->__((string)$element->help),
                        'sort_order'  => (int)$element->sort_order,
                        'description' => $helper->__((string)$element->description),
                        'is_customizable' => $this->_acceptParameters,
                    );
                    
                    $result[$element->getName()] = array_merge(
                        $values,
                        $this->getElementArrayValues($element, $values, $helper)
                    );
                }
            }
            
            uasort($result, array($this, '_sortElements'));
            $this->setData('elements_array', $result);
        }
        return $this->_getData('elements_array');
    }
    
    public function getElementInstanceByCode($code, $params=null)
    {
        if ($element = $this->getXmlElementByCode($code)) {
            if (!$this->_acceptParameters) {
                $instance = Mage::getSingleton($element->getAttribute('type'));
            } else {
                $instance = Mage::getModel($element->getAttribute('type'));
                if ($instance && !is_null($params)) {
                    if (is_array($params = $this->decodeParameters($params))) {
                        $instance->addData($params);
                    }
                }
            }
            
            $helper = ($element->getAttribute('module') ? $element->getAttribute('module') : 'customgrid');
            $helper = Mage::helper($helper);
            $instance->setCode($code);
            $instance->setName($helper->__((string)$element->name));
            
            return $instance;
        }
        return null;
    }
    
    public function encodeParameters($parameters)
    {
        if (is_array($parameters)) {
            return serialize($parameters);
        }
        return $parameters;
    }
    
    public function decodeParameters($parameters, $forceArray=false)
    {
        if (is_string($parameters)) {
            $parameters = unserialize($parameters);
        }
        return ($forceArray && !is_array($parameters) ? array() : $parameters);
    }
    
    protected function _sortElements($a, $b)
    {
        $aOrder = $a['sort_order'];
        $bOrder = $b['sort_order'];
        return ($aOrder < $bOrder 
            ? -1 : ($aOrder > $bOrder ? 1 : strcmp($a['name'], $b['name'])));
    }
    
    protected function _sortParameters($a, $b)
    {
        $aOrder = (int)$a->getData('sort_order');
        $bOrder = (int)$b->getData('sort_order');
        return ($aOrder < $bOrder ? -1 : ($aOrder > $bOrder ? 1 : 0));
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
 * @category   BL
 * @package    BL_CustomGrid
 * @copyright  Copyright (c) 2012 Benoît Leulliette <benoit.leulliette@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class BL_CustomGrid_Model_Config extends Varien_Object
{
    const CACHE_KEY = 'bl_customgrid_config';
    
    const TYPE_GRID_TYPES = 'grid_types';
    const TYPE_COLUMN_RENDERERS_COLLECTION = 'column_renderers_collection';
    const TYPE_COLUMN_RENDERERS_ATTRIBUTE  = 'column_renderers_attribute';
    
    protected $_xmlConfigs = null;
    
    /**
    * Load whole customgrid configuration, retrieve a sub part
    * 
    * @param string $type Configuration part type
    * @return Varien_Simplexml_Config
    */
    public function getXmlConfig($type)
    {
        if (is_null($this->_xmlConfigs)) {
            $cachedXml = Mage::app()->loadCache(self::CACHE_KEY);
            if ($cachedXml) {
                $xmlConfig = new Varien_Simplexml_Config($cachedXml);
            } else {
                $config = new Varien_Simplexml_Config();
                $config->loadString('<?xml version="1.0"?><customgrid></customgrid>');
                Mage::getConfig()->loadModulesConfiguration('customgrid.xml', $config);
                $xmlConfig = $config;
                if (Mage::app()->useCache('config')) {
                    Mage::app()->saveCache(
                        $config->getXmlString(),
                        self::CACHE_KEY,
                        array(Mage_Core_Model_Config::CACHE_TAG)
                    );
                }
            }
            // Split config in main parts
            $this->_xmlConfigs = array(
                self::TYPE_GRID_TYPES 
                    => new Varien_Simplexml_Config($xmlConfig->getNode('grid_types')),
                self::TYPE_COLUMN_RENDERERS_COLLECTION
                    => new Varien_Simplexml_Config($xmlConfig->getNode('column_renderers/collection')),
                self::TYPE_COLUMN_RENDERERS_ATTRIBUTE 
                    => new Varien_Simplexml_Config($xmlConfig->getNode('column_renderers/attribute')),
            );
        }
        return (isset($this->_xmlConfigs[$type]) ? $this->_xmlConfigs[$type] : null);
    }
}