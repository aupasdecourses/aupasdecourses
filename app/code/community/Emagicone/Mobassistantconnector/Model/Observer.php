<?php
/**
 *    This file is part of Mobile Assistant Connector.
 *
 *   Mobile Assistant Connector is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   Mobile Assistant Connector is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with Mobile Assistant Connector.  If not, see <http://www.gnu.org/licenses/>.
 */

class Emagicone_Mobassistantconnector_Model_Observer
{
    public function checkOrder($observer) {
        Mage::app()->cleanCache();
        if(intval(Mage::getStoreConfig('mobassistantconnectorinfosec/emogeneral/status')) == 1) {
            $order = $observer->getEvent()->getOrder();
            $groupId = $_storeId = Mage::app()->getStore(intval($order->getStoreId()))->getGroupId();
            $state = $order->getState();
            // $state = $observer->getEvent()->getState();
            // $status = $observer->getEvent()->getStatus();
            $status = $order->getStatus();
            $comment = $observer->getEvent()->getComment();
            $statusLabel = $status;
            $statuses = array();
            $deviceIds = array();
            $deviceIdsByCCode = array();
            $deviceArResult = array();
            $type = '';

            $statuses = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
            foreach($statuses as $st) {
                if($st['status'] == $status) {
                    $statusLabel = $st['label'];
                }
            }

            $deviceIdActions = Mage::helper('mobassistantconnector')->pushSettingsUpgrade();

            if(count($deviceIdActions) > 0 && !is_null($state)) {


                foreach ($deviceIdActions as $settingNum => $deviceId) {

                    Mage::log(
                        "push_new_order: ". $deviceId['push_store_group_id'].
                        "\n groupId: ". $groupId.
                        "\n state: ". $state.
                        "\n push_device_id: ". $deviceId['push_device_id']
                        ,
                        null,
                        'emagicone_mobassistantconnector.log'
                    );

                    if($deviceId['push_store_group_id'] == $groupId || $deviceId['push_store_group_id'] == -1) {
                        if(intval($deviceId['push_new_order']) == 1  /**  && ( ($order->getCreatedAt() == $order->getUpdatedAt())  || $order->getCustomerIsGuest() == true) */ && $state == 'new' && !is_null($deviceId['push_device_id'])){
                            array_push($deviceIds, $deviceId);
//                            $deviceIdsByCCode[$deviceId['push_currency_code']][] = $deviceId['push_device_id'];
                            $type = 'new_order';
                        }
                        if(strlen($deviceId['push_order_statuses']) > 0 && (!in_array($deviceId['push_device_id'], $deviceIds)) && !($order->getCreatedAt() == $order->getUpdatedAt()) ) {
                            $statuses =  explode('|', $deviceId['push_order_statuses']);
                            if(in_array($status, $statuses) || intval($deviceId['push_order_statuses']) == -1) {
                                if (!in_array($deviceId['push_device_id'], $deviceIds)) {
                                    array_push($deviceIds, $deviceId);
//                                    $deviceIdsByCCode[$deviceId['push_currency_code']][] = $deviceId['push_device_id'];
//                                    $deviceIdsByCCode[$deviceId['push_currency_code']]['push_device_id'][] = $deviceId['push_device_id'];
//                                    $deviceIdsByCCode[$deviceId['push_currency_code']]['app_connection_id'][] = $deviceId['app_connection_id'];
                                    $type = 'order_changed';
                                }
                            }
                        }
                    }
                }

            }

            Mage::log(
                "******* \n Push message: Type: {$type}; All ids: ". count($deviceIdActions). "; Accepted to current event: {". count($deviceIds). "};  ",
                null,
                'emagicone_mobassistantconnector.log'
            );

//            foreach($deviceIdsByCCode as $deviceCurrencyCode => $deviceIds) {
                if(count($deviceIds) > 0) {
                    foreach ($deviceIds as $key => $value) {
                        $currency_code = $order->getGlobalCurrencyCode();
                        $currency_symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();

                        $deviceCurrencyCode = $value['push_currency_code'];
                        $app_connection_id = $value['app_connection_id'];

                        $total = $order->getBaseGrandTotal();
                        // $total = $order->getSubtotalInclTax();
                        $total = number_format(floatval($total), 2, '.', ' ');

                        if(empty($deviceCurrencyCode) || strval($deviceCurrencyCode) == 'base_currency') {
                            $deviceCurrencyCode = $currency_code;
                        }

                        $total = Mage::helper('mobassistantconnector')->price_format($currency_code, 1, $total, $deviceCurrencyCode, 0, true);

                        $storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                        $storeUrl = str_replace('http://', '',  $storeUrl);
                        $storeUrl = str_replace('https://', '',  $storeUrl);

                        preg_replace('/\/*$/i', '', $storeUrl);

                        $fields = array(
                            'registration_ids' => array(0 => $value['push_device_id']),
                            'data' => array( "message" => array("push_notif_type" => $type, "email" => $order->getCustomerEmail(), 'customer_name' => $order->getCustomerFirstname().'  '.$order->getCustomerLastname(),
                                "order_id" => $order->getId(), "total" => $total, "store_url" => $storeUrl, "new_status" => $statusLabel, "group_id" => $groupId, 'app_connection_id' => $app_connection_id) ),
                        );

                        if($type === 'new_order') {
                            unset($fields['data']['new_status']);
                        }

                        $fields_log = var_export($fields, true);

                        $send_data = Mage::helper('core')->jsonEncode($fields['data']);

                        Mage::log(
                            "Push message: Type: {$type}; data: " . var_export(json_encode($fields), true) ,
                            null,
                            'emagicone_mobassistantconnector.log'
                        );

                        $fields = Mage::helper('core')->jsonEncode($fields);

                        $response = Mage::helper('mobassistantconnector')->sendPushMessage($fields);

                        $deviceArResult = Mage::helper('mobassistantconnector')->proceedGoogleResponse($response, $value['push_device_id'], $deviceIdActions);

                        Mage::getModel('core/config')->saveConfig('mobassistantconnectorinfosec/access/google_ids', serialize($deviceArResult) );

                        $d_r = Mage::helper('core')->jsonDecode($response, Zend_Json::TYPE_OBJECT);

                        Mage::log(
                            "Google response: (multicast_id = {$d_r->multicast_id}, success = {$d_r->success}, failure = {$d_r->failure}, canonical_ids = {$d_r->canonical_ids})",
                            null,
                            'emagicone_mobassistantconnector.log'
                        );
                    }
//                }
            }
        }
    }

    public function customerRegisterSuccess($observer) {
        $statuses = array();
        $deviceIds = array();
        $deviceArResult = array();
        $type = 'new_customer';


        $customer = $observer->getEvent()->getCustomer();
        $groupId = $_storeId = Mage::app()->getStore(intval($customer->getStoreId()))->getGroupId();

        Mage::app()->cleanCache();
        if(intval(Mage::getStoreConfig('mobassistantconnectorinfosec/emogeneral/status')) == 1) {

            $deviceIdActions = Mage::helper('mobassistantconnector')->pushSettingsUpgrade();

            if(count($deviceIdActions) > 0) {
                foreach ($deviceIdActions as $settingNum => $deviceId) {
                    if(($groupId == $deviceId['push_store_group_id']) || $deviceId['push_store_group_id'] == -1) {
                        if(intval($deviceId['push_new_customer'] == 1)) {
                            array_push($deviceIds, $deviceId);
                        }
                    }
                }
            }

            Mage::log(
                "******* \n Push message: Type: {$type}; All ids: ". count($deviceIdActions). "; Accepted to current event: {". count($deviceIds). "};  ",
                null,
                'emagicone_mobassistantconnector.log'
            );

            if(count($deviceIds) > 0) {
                foreach ($deviceIds as $key => $value) {
                    $fields = array(
                        'registration_ids' => array(0 => $value['push_device_id']),
                        'data' => array( "message" => array("push_notif_type" => $type, "email" => $customer->getEmail(), 'customer_name' => $customer->getFirstname().'  '.$customer->getLastname(),
                            "customer_id" => $customer->getId(), "store_url" => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), "group_id" => $groupId, 'app_connection_id' => $value['app_connection_id']))
                    );

                    $send_data = Mage::helper('core')->jsonEncode($fields['data']);
                    $fields = Mage::helper('core')->jsonEncode($fields);


                    Mage::log(
                        "Data: {$fields} ",
                        null,
                        'emagicone_mobassistantconnector.log'
                    );

                    $response = Mage::helper('mobassistantconnector')->sendPushMessage($fields);

    //                $success = true;
                    $deviceArResult = Mage::helper('mobassistantconnector')->proceedGoogleResponse($response, $deviceIds, $deviceIdActions);

                    Mage::getModel('core/config')->saveConfig('mobassistantconnectorinfosec/access/google_ids', serialize($deviceArResult) );

                    $d_r = Mage::helper('core')->jsonDecode($response, Zend_Json::TYPE_OBJECT);

                    Mage::log(
                        "Google response: (multicast_id = {$d_r->multicast_id}, success = {$d_r->success}, failure = {$d_r->failure}, canonical_ids = {$d_r->canonical_ids})",
                        null,
                        'emagicone_mobassistantconnector.log'
                    );
                }
            }
        }
    }
}