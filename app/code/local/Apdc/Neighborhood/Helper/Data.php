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
/**
 * Apdc_Neighborhood_Helper_Data 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Core_Helper_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * getPostcodeByWebsite 
     * 
     * @param Mage_Core_Model_Website $website website 
     * 
     * @return string
     */
    public function getNeighborhoodInfosByWebsite($website=null)
    {
        $infos = [
            'postcode' => '',
            'name' => ''
        ];

        if (is_null($website)) {
            $website = Mage::app()->getWebsite();
        }
        $allZipCodes = $this->getAllZipCode();
        foreach ($allZipCodes as $postcode => $data) {
            if ($data[0]['website_id'] == $website->getId()) {
                $infos['postcode'] = $postcode;
                $infos['name'] = $data[0]['name'];
                break;
            }
        }
        return $infos;

    }

    /**
     * getNeighborhoodByPostcode 
     * 
     * @param string       $postcode : postcode 
     * @param boolean|null $isActive : get only active neighborhoods or not
     * 
     * @return Apdc_Neighborhood_Model_Neighborhood | null
     */
    public function getNeighborhoodByPostcode($postcode)
    {
        $allZipCodes = $this->getAllZipCode();
        if (isset($allZipCodes[$postcode]) && !empty($allZipCodes[$postcode])) {
            $websiteId = $allZipCodes[$postcode][0]['website_id'];
            $website = Mage::app()->getWebsite($websiteId);
            if ($website) {
                return $website->getDefaultStore();
            }
        }
        return null;
    }

    /**
     * sendChangeNeighborhoodAdminNotification 
     * 
     * @param Mage_Customer_Model_Customer $customer customer 
     * @param Apdc_Neighborhood_Model_Neighborhood $oldNeighborhood oldNeighborhood 
     * @param Apdc_Neighborhood_Model_Neighborhood $newNeighborhood newNeighborhood 
     * 
     * @return void
     */
    public function sendChangeNeighborhoodAdminNotification($customer, $oldNeighborhood, $newNeighborhood)
    {
        try {
            $templateId = Mage::getStoreConfig('apdc_neighborhood/notifications/template_notification_change_neighborhood');
            if (!$templateId) {
                $templateId = 'apdc_neighborhood_notifications_template_notification_change_neighborhood';
            }
            $vars = array(
                'customer' => $customer,
                'oldNeighborhood' => $oldNeighborhood,
                'newNeighborhood' => $newNeighborhood
            );
            $sender = array(
                'name' => Mage::getStoreConfig('trans_email/ident_general/name'),
                'email' => Mage::getStoreConfig('trans_email/ident_general/email')
            );
            $emailTo = Mage::getStoreConfig('apdc_neighborhood/notifications/sent_to_email');
            $nameTo = Mage::getStoreConfig('apdc_neighborhood/notifications/sent_to_name');
            $emailTemplate = Mage::getModel('core/email_template');
            $emailTemplate->sendTransactional($templateId, $sender, $emailTo, $nameTo, $vars);
            if (!$emailTemplate->getSentSuccess()) {
                Mage::throwException('Impossible d\'envoyer la notification de changement de quartier pour le client : ' . $customer->getEmail());
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * getAllZipCode 
     * 
     * @return array
     */
    public function getAllZipCode()
    {
        $allZipCodes = [];
        $shippingRestrictions = Mage::getModel('amshiprestriction/rule')->getCollection()
            ->addFieldToFilter('is_active', 1);

        $stores = Mage::app()->getStores();
        foreach ($shippingRestrictions as $shipRestriction) {
            $shipStores = explode(',', trim($shipRestriction->getStores(), ','));
            if (count($shipStores) == 1) {
                $storeId = reset($shipStores);
                if (isset($stores[$storeId])) {
                    $conditions = unserialize($shipRestriction->getConditionsSerialized());
                    if (isset($conditions['conditions'])) {
                        foreach ($conditions['conditions'] as $condition) {
                            if (
                                $condition['type'] == 'amshiprestriction/rule_condition_address' &&
                                isset($condition['attribute']) &&
                                $condition['attribute'] == 'postcode'
                            ) {
                                if (!isset($allZipCodes[$condition['value']])) {
                                    $allZipCodes[$condition['value']] = [];
                                }
                                $allZipCodes[$condition['value']][] = [
                                    'name' => $stores[$storeId]->getName(),
                                    'website_id' => $stores[$storeId]->getWebsiteId()
                                ];
                            }

                        }
                    }
                }
            }
        }

        asort($allZipCodes);
        return $allZipCodes;
    }

    /**
     * getAllWebsites 
     * 
     * @return 
     */
    public function getAllWebsites()
    {
        $allWebsites = [];
        $shippingRestrictions = Mage::getModel('amshiprestriction/rule')->getCollection()
            ->addFieldToFilter('is_active', 1);

        $stores = Mage::app()->getStores();
        foreach ($shippingRestrictions as $shipRestriction) {
            $shipStores = explode(',', trim($shipRestriction->getStores(), ','));
            if (count($shipStores) == 1) {
                $storeId = reset($shipStores);
                if (isset($stores[$storeId]) && $stores[$storeId]->getWebsiteId() != 2) {
                    $allWebsites[$stores[$storeId]->getName()] = [
                        'name' => $stores[$storeId]->getName(),
                        'website_id' => $stores[$storeId]->getWebsiteId()
                    ];
                }
            }
        }

        ksort($allWebsites, SORT_NATURAL);

        return $allWebsites;
    }


    /**
     * getCurrentNeighborhood 
     * 
     * @return Apdc_Neighborhood_Model_Neighborhood | null
     */
    public function getCurrentNeighborhood()
    {
        $neighborhood = Mage::getSingleton('customer/session')->getNeighborhood();
        if ($neighborhood && $neighborhood->getId()) {
            return $neighborhood;
        }
        return null;
    }

    /**
     * getCustomer 
     * 
     * @return Mage_Customer_Model_Customer | null
     */
    public function getCustomer()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getSingleton('customer/session')->getCustomer();
        }
        return null;
    }

    /**
     * getNeighborhoodImageUrl 
     * 
     * @return string
     */
    public function getNeighborhoodImageUrl()
    {
        $imageUrl = '';

        $storeCode = Mage::app()->getStore()->getCode();
        $path = 'wysiwyg' . DS . 'neighborhood' . DS . $storeCode . '.jpg';
        if (file_exists(Mage::getBaseDir('media') . DS . $path)) {
            $imageUrl = Mage::getBaseUrl('media') . $path;
        }

        return $imageUrl;
    }

    /**
     * getCustomerDefaultPostcode 
     * 
     * @param Mage_Customer_Model_Customer|null $customer customer 
     * 
     * @return string
     */
    public function getCustomerDefaultPostcode($customer=null)
    {
        $defaultPostcode = '';
        if ($this->getCustomer()) {
            $address = $this->getCustomer()->getPrimaryShippingAddress();
            if (!$address) {
                $address = $this->getCustomer()->getPrimaryBillingAddress();
            }
            if (!$address) {
                $address = $this->getCustomer()->getAddressesCollection()->getFirstItem();
            }
            if ($address) {
                $defaultPostcode = $address->getPostcode();
            }
        }
        return $defaultPostcode;
    }

    /**
     * setNeighborhood
     * 
     * @param Mage_Core_Model_Store $neighborhood neighborhood
     * 
     * @return void
     */
    public function setNeighborhood($neighborhood)
    {
        Mage::getSingleton('customer/session')->setNeighborhood($neighborhood);
        if ($this->getCustomer()) {
            $this->getCustomer()->setCustomerNeighborhood($neighborhood->getId());
        }
        return $this;
    }

    /**
     * getCustomerNeighborhoodStoreId 
     * 
     * @param Mage_Customer_Model_Customer $customer customer 
     * 
     * @return int
     * @return string
     */
    public function getCustomerNeighborhoodStoreId($customer)
    {
        if ($customer->getCustomerNeighborhood()) {
            $store = Mage::app()->getStore($customer->getCustomerNeighborhood());
            if ($store && $store->getId()) {
                return $store->getId();
            }
        }
        $websiteId = $customer->getWebsiteId();
        return Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();
    }

    /**
     * getCustomerNeighborhoodStore 
     * 
     * @param Mage_Customer_Model_Customer $customer customer 
     * 
     * @return Mage_Core_Model_Store
     */
    public function getCustomerNeighborhoodStore($customer)
    {
        if ($customer->getCustomerNeighborhood()) {
            $store = Mage::app()->getStore($customer->getCustomerNeighborhood());
            if ($store && $store->getId()) {
                return $store;
            }
        }
        return Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore();
    }

    /**
     * getNeighborhoodsByWebsiteId 
     * 
     * @param int          $websiteId : websiteId 
     * @param boolean|null $isActive  : get only active neighborhoods or not
     * 
     * @return Apdc_Neighborhood_Model_Resource_Neighborhood_Collection
     */
    public function getNeighborhoodsByWebsiteId($websiteId, $isActive=null)
    {
        $neighborhoods = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection()
            ->addFieldToFilter('website_id', $websiteId);
        if ($isActive === true) {
            $neighborhoods->addFieldToFilter('is_active', 1);
        }
        $neighborhoods->getSelect()->order('sort_order ASC');
        return $neighborhoods;
    }
}
