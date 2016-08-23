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

class Emagicone_Mobassistantconnector_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function sendPushMessage($messageContent) {
        Mage::app()->cleanCache();
        $apiKey = Mage::getStoreConfig('mobassistantconnectorinfosec/access/api_key');
        $headers = array('Authorization: key=' . $apiKey, 'Content-Type: application/json');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $messageContent);
        $result = curl_exec( $ch );

        if(curl_errno($ch)) {
            Mage::log(
                "Push message error while sending CURL request: {$result}",
                null,
                'emagicone_mobassistantconnector.log'
            );
        }

        curl_close($ch);
        
        return $result;
    }

    public function pushSettingsUpgrade() {
        $deviceIds = Mage::getStoreConfig('mobassistantconnectorinfosec/access/google_ids');

        if(strlen($deviceIds) > 0) {
            $deviceIds = unserialize($deviceIds);
        } else $deviceIds = array();

        foreach(array_keys($deviceIds) as $key) {
            if (!is_int($key)) {
                $deviceIds[$key]['push_device_id'] = $key;
                if(empty($deviceIds[$key]['push_store_group_id'])) {
                    $deviceIds[$key]['push_store_group_id'] = -1;
                }
                if(empty($deviceIds[$key]['push_currency_code'])) {
                    $deviceIds[$key]['push_currency_code'] = 'base_currency';
                }
                if(empty($deviceIds[$key]['app_connection_id'])) {
                    $deviceIds[$key]['app_connection_id'] = -1;
                }
                array_push($deviceIds, $deviceIds[$key]);
                unset($deviceIds[$key]);
            }
        }

        //Check for duplicated records
        foreach ($deviceIds as $a1 => $firstDevice) {
            if(empty($firstDevice['push_currency_code'])) {
                $deviceIds[$a1]['push_currency_code'] = 'base_currency';
            }
            if(empty($deviceIds[$key]['app_connection_id'])) {
                $deviceIds[$key]['app_connection_id'] = -1;
            }
            foreach($deviceIds as $a2 => $secondDevice){
                if(($firstDevice === $secondDevice) && ($a1 != $a2)) {
                    unset($deviceIds[$a1]);
                }
            }
        }

        return $deviceIds;
    }

    public function proceedGoogleResponse($response, $deviceIds, $deviceIdActions) {
        if ($response) {
            $json = json_decode($response, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                $json = array();
            }
        }
        else {
            $json = array();
        }

        $failure = isset($json['failure']) ? $json['failure'] : null;

        $canonicalIds = isset($json['canonical_ids']) ? $json['canonical_ids'] : null;

        if ($failure || $canonicalIds) {
            $results = isset($json['results']) ? $json['results'] : array();
            foreach($results as $id => $result) {
                $newRegId = isset($result['registration_id']) ? $result['registration_id'] : null;
                $error = isset($result['error']) ? $result['error'] : null;
                if ($newRegId) {
                    // It's duplicated deviceId
                    if(in_array($newRegId, $deviceIds) && $newRegId != $deviceIds[$id]) {
                        // Loop through the devices and delete old
                        foreach ($deviceIdActions as $settingNum => $deviceId) {
                            if($deviceId['push_device_id'] == $deviceIds[$id]) {
                                unset($deviceIdActions[$settingNum]);
                            }
                        }
                        // Need to update old deviceId
                    } else if(!in_array($newRegId, $deviceIds)) {
                        foreach ($deviceIdActions as $settingNum => $deviceId) {
                            if($deviceId['push_device_id'] == $deviceIds[$id]) {
                                $deviceIdActions[$settingNum]['push_device_id'] = $newRegId;
                            }
                        }
                    }
                }
                else if ($error) {
                    // Unset not registered device id
                    if ($error == 'NotRegistered' || $error == 'InvalidRegistration') {
                        foreach ($deviceIdActions as $settingNum => $deviceId) {
                            if($deviceId['push_device_id'] == $deviceIds[$id]) {
                                unset($deviceIdActions[$settingNum]);
                            }
                        }
                    }
                }
            }
        }

        return $deviceIdActions;
    }

    public function price_format($iso_code, $curr_format, $price, $convert_to, $force = false, $format = true) {
        $currency_symbol = '';
        $price = str_replace(' ', '', $price);
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();

        if(strlen($convert_to) == 3){
            try {
                $price = Mage::helper('directory')->currencyConvert($price, $baseCurrencyCode, $convert_to);
//                $price = $this->currencyConvert($price, $baseCurrencyCode, $convert_to);
                $iso_code = $convert_to;
            } catch(Exception $e) {
                Mage::log(
                    "Error while currency converting (". var_export($e->getMessage(), true). ")",
                    null,
                    'emagicone_mobassistantconnector.log'
                );
            }

        }

        if($format) {
            $price = number_format(floatval($price), 2, '.', ' ');
        }

        preg_match('/^[a-zA-Z]+$/', $iso_code, $matches);

        if(count($matches) > 0) {
            if(strlen($matches[0]) == 3) {
                $currency_symbol = Mage::app()->getLocale()->currency($iso_code)->getSymbol();
            }
        } else {
            $currency_symbol = $iso_code;
        }

        if($force) {
            return $currency_symbol;
        }
//        $sign = '<span>' . $currency_symbol . '</span>';
        $sign = $currency_symbol;
        if($curr_format == 1) {
            $price = $sign . $price;
        } elseif ($curr_format == 2) {
            $price = $price;
        } else {
            $price = $price . ' ' . $sign;
        }

        return $price;
    }
}