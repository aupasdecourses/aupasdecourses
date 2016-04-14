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


class Emagicone_Mobassistantconnector_IndexController extends Mage_Core_Controller_Front_Action
{
    public $CartClass = "";
    public $call_function;
    public $callback;
    public $hash;
    public $def_currency;
    public $currency_code;
    const GSM_URL = 'https://android.googleapis.com/gcm/send';
    const MB_VERSION = '$Revision: 84 $';

    public function indexAction() {
        Mage::app()->cleanCache();
        if(intval(Mage::getStoreConfig('mobassistantconnectorinfosec/emogeneral/status')) != 1) $this->generate_output('module_disabled');

        $this->loadLayout()->renderLayout();

        $def_currency = $this->_get_default_currency();
        $this->def_currency = $def_currency['currency'];

        foreach($this->getRequest()->getParams() as $key=>$value) {
            if(isset($key) && $key == 'callback' && isset($value) && strlen($value) > 0)
                $this->callback = $value;
            if(isset($key) && $key == 'call_function' && isset($value) && strlen($value) > 0)
                $this->call_function = $value;
            if(isset($key) && $key == 'hash' && isset($value) && strlen($value) > 0)
                $this->hash = $value;
        }

        if(!$this->check_auth()) {
           $this->generate_output('auth_error');
        }

        $request_params = Mage::app()->getRequest()->getParams();
        $params = $this->validate_types($request_params, array(
            'show' => 'INT',
            'page' => 'INT',
            'search_order_id' => 'STR',
            'orders_from' => 'STR',
            'orders_to' => 'STR',
            'order_number' => 'STR',
            'customers_from' => 'STR',
            'customers_to' => 'STR',
            'date_from' => 'STR',
            'date_to' => 'STR',
            'graph_from' => 'STR',
            'graph_to' => 'STR',
            'stats_from' => 'STR',
            'stats_to' => 'STR',
            'products_to' => 'STR',
            'products_from' => 'STR',
            'order_id' => 'INT',
            'user_id' => 'INT',
            'params' => 'STR',
            'val' => 'STR',
            'search_val' => 'STR',
            'statuses' => 'STR',
            'sort_by' => 'STR',
            'last_order_id' => 'STR',
            'product_id' => 'INT',
            'get_statuses' => 'INT',
            'cust_with_orders' => 'INT',
            'data_for_widget' => 'INT',
            'registration_id' => 'STR',
            'registration_id_old' => 'STR',
            'registration_id_new' => 'STR',
            'api_key' => 'STR',
            'tracking_number' => 'STR',
            'tracking_title' => 'STR',
            'action' => 'STR',
            'carrier_code' => 'STR',
            'custom_period' => 'INT',
            'group_id' => 'INT',
            'push_new_customer' => 'INT',
            'push_new_order' => 'INT',
            'push_currency_code' => 'STR',
            'app_connection_id' => 'STR',
            'push_store_group_id' => 'STR',
            'push_order_statuses' => 'STR',
            'currency_code' => 'STR',
            'is_mail' => 'INT',
            'store_group_id' => 'INT',
            'carts_from' => 'STR',
            'carts_to' => 'STR',
            'cart_id' => 'STR',
            'search_carts' => 'STR',
            'show_unregistered_customers' => 'INT',
        ));

        foreach($params as $k => $value) {
            $this->{$k} = $value;
        }

        if(empty($this->currency_code) || strval($this->currency_code) == 'base_currency' || strval($this->currency_code) == 'not_set') {
            $this->currency_code = $this->def_currency;
        }

        if($this->call_function == 'test_config') {
            $this->generate_output(array('test' => 1));
        }

        $locale = Mage::app()->getLocale()->getLocaleCode();
        Mage::app()->getTranslator()->setLocale($locale);

        if(!method_exists($this, $this->call_function)) {
            Mage::log(
                "Unknown method call ({$this->call_function})",
                null,
                'emagicone_mobassistantconnector.log'
            );
            $this->generate_output('old_bridge');
        }

        $result = call_user_func(array($this, $this->call_function));

        $this->generate_output($result);
    }

    protected function generate_output($data) {

        $add_bridge_version = false;
        if(in_array($this->call_function, array("test_config", "get_store_title", "get_store_stats", "get_data_graphs"))) {
            if(is_array($data) && $data != 'auth_error' && $data != 'connection_error' && $data != 'old_bridge') {
                $add_bridge_version = true;
            }
        }

        if(!is_array($data)) {
            $data = array($data);
        }

        if(is_array($data)) {
            array_walk_recursive($data, array($this, 'reset_null'));
        }

        if($add_bridge_version) {
            $data['module_version'] = self::MB_VERSION;
        }

//        $data = $this->to_json($data);
        $data = Mage::helper('core')->jsonEncode($data);

        if($this->callback) {
            header('Content-Type: text/javascript;charset=utf-8');
            die($this->callback . '(' . $data . ');');
        } else {
            header('Content-Type: text/javascript;charset=utf-8');
            die($data);
        }
    }

    protected function check_auth() {
        $login = Mage::getStoreConfig('mobassistantconnectorinfosec/emoaccess/login');
        $password = Mage::getStoreConfig('mobassistantconnectorinfosec/emoaccess/password');

        if(md5($login.$password) == $this->hash){
            return true;
        } else {
            Mage::log(
                "Hash accepted ({$this->hash}) is not equal to ({$login}  {$password}) stored",
                null,
                'emagicone_mobassistantconnector.log'
            );
            return false;
        }
    }

    protected function push_notification_settings() {
        $result = array();
        $statuses = array();
        $matched = false;
        $deviceActions = array('push_device_id' => '',
                                'push_new_order' => 0,
                                'push_order_statuses' => '',
                                'push_new_customer' => 0,
                                'app_connection_id' => -1,
                                'push_store_group_id' => -1,
                                'push_currency_code' => '');

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
                array_push($deviceIds, $deviceIds[$key]);
                unset($deviceIds[$key]);
            }
        }

        if(strlen($this->registration_id) > 0 && strlen($this->api_key) > 0) {

            if(strlen($this->registration_id ) > 0) {
                $deviceActions['push_device_id'] = $this->registration_id;
            }

            if(strlen($this->push_new_order ) > 0) {
                $deviceActions['push_new_order'] = $this->push_new_order;
            }   else {
                $deviceActions['push_new_order'] = 0;
            }

            if(strlen($this->push_order_statuses) > 0) {
                $deviceActions['push_order_statuses'] = $this->push_order_statuses;
            }   else {
                $deviceActions['push_order_statuses'] = '';
            }

            if(strlen($this->push_new_customer ) > 0) {
                $deviceActions['push_new_customer'] = $this->push_new_customer;
            }   else {
                $deviceActions['push_new_customer'] = 0;
            }

            if(!empty($this->store_group_id)) {
                $deviceActions['push_store_group_id'] = $this->store_group_id;
            }   else {
                $deviceActions['push_store_group_id'] = -1;
            }

            if(!empty($this->app_connection_id)) {
                $deviceActions['app_connection_id'] = $this->app_connection_id;
            }   else {
                $deviceActions['app_connection_id'] = -1;
            }

            if(strlen($this->push_currency_code) > 0) {
                $deviceActions['push_currency_code'] = $this->push_currency_code;
            } else if(strlen($this->currency_code) > 0) {
                $deviceActions['push_currency_code'] = $this->currency_code;
            } else {
                $deviceActions['push_currency_code'] = 'base_currency';
            }

            // Delete empty record
            if((intval($this->push_new_order ) == 0) && (strlen($this->push_order_statuses ) == 0) && (intval($this->push_new_customer ) == 0)) {
                foreach ($deviceIds as $settingNum => $deviceId) {
                    if($deviceId['push_device_id'] == $this->registration_id && (isset($deviceId['app_connection_id']) && $deviceId['app_connection_id'] == $deviceActions['app_connection_id'])) {
                        unset($deviceIds[$settingNum]);
                    } else if($deviceId['push_device_id'] == $this->registration_id && $deviceId['push_store_group_id'] == $this->store_group_id && !isset($deviceId['app_connection_id'])) {
                        unset($deviceIds[$settingNum]);
                    }
                }
            } else {
                // renew old record
                foreach ($deviceIds as $settingNum => $deviceId) {
                    if($deviceId['push_device_id'] == $this->registration_id && (isset($deviceId['app_connection_id']) && $deviceId['app_connection_id'] == $deviceActions['app_connection_id'])) {
                        $deviceIds[$settingNum] = $deviceActions;
                        $matched = true;
                    } else if($deviceId['push_device_id'] == $this->registration_id && $deviceId['push_store_group_id'] == $deviceActions['push_store_group_id'] && !isset($deviceId['app_connection_id'])) {
                        $deviceIds[$settingNum] = $deviceActions;
                        $matched = true;
                    }
                }
                if(!$matched) {
                    // add new record
                    array_push($deviceIds, $deviceActions);
                }
            }

            // Delete old registration id
            if(isset($this->registration_id_old) && strlen($this->registration_id_old) > 0) {
                foreach ($deviceIds as $settingNum => $deviceId) {
                    if($deviceId['push_device_id'] == $this->registration_id_old) {
                        unset($deviceIds[$settingNum]);
                    }
                }
            }

            Mage::getModel('core/config')->saveConfig('mobassistantconnectorinfosec/access/google_ids', serialize($deviceIds) );

            Mage::getModel('core/config')->saveConfig('mobassistantconnectorinfosec/access/api_key', $this->api_key );

            $result = array('success' => 'true');
        } else
            $result = array('error' => $this->__('Can not get configuration value'));

        return $result;
    }

    protected function get_store_title() {
        if(!empty($this->group_id)) {
            try {
                $name = Mage::app()->getGroup($this->group_id)->getName();
            } catch(Exception $e) {
                $name = Mage::app()->getStore()->getFrontendName();
            }
        } else  $name = Mage::app()->getStore()->getFrontendName();
        return array('test' => 1, 'title' => $name);
    }

    protected function is_group_exists($groupId) {
        $exists = false;
        if(isset($groupId)) {
            try {
                $name = Mage::app()->getGroup($groupId)->getName();
                $exists = true;
            } catch(Exception $e) {
                $exists = false;
            }
        }
        return $exists;
    }

    protected function get_stores() {
        $arr_result = array();

        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
//                foreach ($group->getStores() as $store) {
                    $arr_result[] = array(
//                                            'store_id' => $store->getStoreID(),
//                                            'store_name' => $store->getName(),
//                                            'is_store_active' => $store->getIsActive(),
                                            'group_id' => $group->getId(),
                                            'name' => $group->getName(),
                                            'default_store_id' => $group->getDefaultStoreId(),
                                            'website' => $website->getName(),
                                            'website_id' => $website->getWebsiteId(),
                                            'default_group_id' => $website->getDefaultGroupId(),
                                            'website_is_default' => $website->getIsDefault()
//                                            'group_currencies' => $this->get_group_currencies($group->getId())
                                        );
//                }
            }
        }

        return $arr_result;
    }

    protected function get_currencies() {
        $currencies = array();
                    try {
                        $collection = Mage::getModel('core/store')->getCollection(); // ->addFieldToFilter('group_id', $storeId);
                        if(!empty($this->group_id) && $this->group_id != -1) {
                            $collection->addFieldToFilter('group_id', $this->group_id);
                        }

                        foreach ($collection as $store) {
                            $storeId = $store->getStoreId();
                            $CurrencyCode = Mage::getModel('core/config_data')
                                ->getCollection()
                                ->addFieldToFilter('path','currency/options/allow')
                                ->addFieldToFilter('scope_id',$storeId)
                                ->getData();
                            $currencies_array = explode(',',$CurrencyCode[0]['value']);
                            if($currencies_array[0] == '')
                            {
        //                        $currencies_array[] = Mage::app()->getStore($storeId)->getCurrentCurrencyCode();
                                $currencies_array = Mage::app()->getStore($storeId)->getAvailableCurrencyCodes();
                            }

                            foreach ($currencies_array as $curCode) {
                                foreach ($currencies as $currency) {
                                    if($curCode == $currency['code']){
                                        continue 2;
                                    }
                                }

                                $currencySymbol = Mage::app()->getLocale()->currency( $curCode )->getSymbol();
                                $currencyName = Mage::app()->getLocale()->currency( $curCode )->getName();
                                $currencies[] = array('code' => $curCode, 'symbol' => (is_null($currencySymbol) ? $curCode : $currencySymbol), 'name' => $currencyName);
                            }
                        }
                    } catch(Exception $e) {
                        Mage::log(
                            "Error while getting group currencies (". var_export($e->getMessage(), true). ")",
                            null,
                            'emagicone_mobassistantconnector.log'
                        );
                    }

        return $currencies;
    }

    protected function get_store_stats() {
        $data_graphs = '';
        $order_status_stats = array();

        $offset = $this->_get_timezone_offset();
        $store_stats = array('count_orders' => "0", 'total_sales' => "0", 'count_customers' => "0", 'count_products' => "0", "last_order_id" => "0", "new_orders" => "0");

        $today = date("Y-m-d", time());
        $date_from = $date_to = $today;

        if(!empty($this->stats_from)) {
            $date_from = $this->stats_from;
        }

        if(!empty($this->stats_to)) {
            $date_to = $this->stats_to;
        }

        if(isset($this->custom_period) && strlen($this->custom_period) > 0) {
            $custom_period = $this->get_custom_period($this->custom_period);

            $date_from = $custom_period['start_date'];
            $date_to = $custom_period['end_date'];
        }

        if(!empty($date_from)) {
            $date_from .= " 00:00:00";
        }
        if(!empty($date_to)) {
            $date_to .= " 23:59:59";
        }

        $ordersCollection = Mage::getModel('sales/order')->getCollection();
        $ordersCollection->addAttributeToSelect('base_grand_total');
        $ordersCollection->addAttributeToSelect('entity_id');
        $storeTableName = Mage::getSingleton('core/resource')->getTableName('core/store');

        if(strlen($this->statuses) > 0) {
            $this->statuses = '\'' . str_replace('|', '\',\'', $this->statuses) . '\'';
            $ordersCollection->getSelect()->where(new Zend_Db_Expr("main_table.status IN ({$this->statuses})"));
        }

        if (!empty($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $ordersCollection->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = main_table.store_id");
                $ordersCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        if(!empty($date_from)) {
            $ordersCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(main_table.created_at, '+00:00',  '".$offset."')) >= '" . (date('Y-m-d H:i:s', (strtotime($date_from)))) ."'"));
        }
        if(!empty($date_to)) {
            $ordersCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(main_table.created_at, '+00:00',  '".$offset."')) <= '" . (date('Y-m-d H:i:s', (strtotime($date_to)))) ."'"));
        }

        $ordersCollection->getSelect()->columns('SUM(base_grand_total) as sum_base_grand_total,COUNT(*) AS count_orders');

        // $ordersCollection->getSelect()->limit(1);

        $first = $ordersCollection->getFirstItem();

        /*
        if(!empty($date_from) && !empty($date_to)) {
            $ordersCollection->addFieldToFilter('main_table.created_at',
                array('from' => $date_from,
                    'to' => $date_to,
                    'date' => true)  );
        }
        */

        // var_dump($ordersCollection->getSelect()->__toString());die();

        $ordersSum = $first['sum_base_grand_total'];
        // $ordersSum = array_sum($ordersCollection->getColumnValues('base_grand_total'));
        // $row['count_orders'] = $ordersCollection->getSize();
        $row['count_orders'] = $first['count_orders'];

        $nice_nm = $this->bd_nice_number($ordersSum);

        $row['total_sales'] = $this->_price_format($this->def_currency, 1, $nice_nm, $this->currency_code, 0, true);
        $store_stats = array_merge($store_stats, $row);


        $salesCollection = Mage::getModel("sales/order_item")->getCollection()
            ->addFieldToSelect(array('product_id', 'name', 'sku'));

        $orderTableName = Mage::getSingleton('core/resource')->getTableName('sales/order');
        $salesCollection->getSelect()
            ->joinLeft(
                array('order' => $orderTableName),
                "order.entity_id = main_table.order_id",
                array());

        if (!empty($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $salesCollection->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = main_table.store_id");
                $salesCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        if(!empty($date_from)) {
            $salesCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(order.created_at, '+00:00', '".$offset."')) >= '" . (date('Y-m-d H:i:s', (strtotime($date_from)))) ."'"));
        }
        if(!empty($date_to)) {
            $salesCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(order.created_at, '+00:00', '".$offset."')) <= '" . (date('Y-m-d H:i:s', (strtotime($date_to))))  ."'"));
        }

        if(strlen($this->statuses) > 0) {
            $salesCollection->getSelect()->where(new Zend_Db_Expr("order.status IN ({$this->statuses})"));
        }
        $store_stats['count_products'] = $this->bd_nice_number($salesCollection->getSize(), true);

        $salesCollection->setOrder('item_id','DESC');

        if($this->last_order_id != "") {
            $ordersCollection = Mage::getModel('sales/order')->getCollection();

            $ordersCollection->addAttributeToFilter('entity_id', array('gt' => intval($this->last_order_id)));

            $ordersCollection->setOrder('entity_id','DESC');
            $ordersCollection->getSelect()->limit(1);

            $lastOrder = $ordersCollection->getFirstItem();

            $store_stats['last_order_id'] = $this->last_order_id;
            if(intval($lastOrder['entity_id']) > intval($this->last_order_id)) {
                $store_stats['last_order_id'] = intval($lastOrder['entity_id']);
            }

            $store_stats['new_orders'] = $ordersCollection->count();
        }

        $customerCollection = Mage::getModel('customer/customer')->getCollection();

        if(!empty($date_from)) {
            $customerCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(created_at, '+00:00', '".$offset."')) >= '" . date('Y-m-d H:i:s', (strtotime($date_from))) ."'"));
        }
        if(!empty($date_to)) {
            $customerCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(created_at, '+00:00', '".$offset."')) <= '" . date('Y-m-d H:i:s', (strtotime($date_to))) ."'"));
        }

        if (!empty($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $customerCollection->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = e.store_id");
                $customerCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        $row['count_customers'] = $customerCollection->count();
        $store_stats = array_merge($store_stats, $row);

        if(!isset($this->data_for_widget) || empty($this->data_for_widget) || $this->data_for_widget != 1) {
            $data_graphs = $this->get_data_graphs();
        }

        $store_stats['count_orders'] = $this->bd_nice_number($store_stats['count_orders'], true);
        $store_stats['count_customers'] = $this->bd_nice_number($store_stats['count_customers'], true);

//        $result = array_merge($store_stats, array('data_graphs' => $data_graphs));

        $order_status_stats = $this->get_status_stats();

//        $order_status_stats = array(array('code' => 'pending', 'name' => 'Pending', 'count' => 3, 'total' => 'USD33.20'),
//                                 array('code' => 'complete', 'name' => 'Complete', 'count' => 4, 'total' => 'UAH12.20'),
//                                 array('code' => 'holded', 'name' => 'On Hold', 'count' => 4, 'total' => 'USD333.20'),
//                                 array('code' => 'fraud', 'name' => 'Suspected Fraud', 'count' => 0, 'total' => 'USD33.21'),
//                                 array('code' => 'canceled', 'name' => 'Canceled', 'count' => 1, 'total' => 'USD33.00'));


        $result = array_merge($store_stats, array('data_graphs' => $data_graphs), array('order_status_stats' => $order_status_stats));
        return $result;

    }

    protected function get_status_stats() {

        $offset = $this->_get_timezone_offset();
        $store_stats = array('count_orders' => "0", 'total_sales' => "0", 'count_customers' => "0", 'count_products' => "0", "last_order_id" => "0", "new_orders" => "0");

        $today = date("Y-m-d", time());
        $date_from = $date_to = $today;

        if(!empty($this->stats_from)) {
            $date_from = $this->stats_from;
        }

        if(!empty($this->stats_to)) {
            $date_to = $this->stats_to;
        }

        if(isset($this->custom_period) && strlen($this->custom_period) > 0) {
            $custom_period = $this->get_custom_period($this->custom_period);

            $date_from = $custom_period['start_date'];
            $date_to = $custom_period['end_date'];
        }

        if(!empty($date_from)) {
            $date_from .= " 00:00:00";
        }
        if(!empty($date_to)) {
            $date_to .= " 23:59:59";
        }

        $storeTableName = Mage::getSingleton('core/resource')->getTableName('core/store');
        $orderStatusTableName = Mage::getSingleton('core/resource')->getTableName('sales/order_status');

        $salesCollection = Mage::getModel("sales/order")->getCollection()
            ->addFieldToSelect(array('state', 'status',  'store_id', 'base_grand_total', 'base_currency_code', 'order_currency_code'));

        $salesCollection->clear();

        if (!empty($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $salesCollection->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = main_table.store_id");
                $salesCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        $salesCollection->getSelect()
            ->joinLeft(
                array('sos' => $orderStatusTableName),
                "sos.status = main_table.status",
                array('name' => 'sos.label'));

        $salesCollection->getSelect()->columns(array('code' => new Zend_Db_Expr ('main_table.status')));
       $salesCollection->getSelect()->columns(array('count' => new Zend_Db_Expr ('COUNT(main_table.entity_id)')));
        $salesCollection->getSelect()->columns(array('total' => new Zend_Db_Expr ('SUM(main_table.base_grand_total)')));


        if(!empty($date_from)) {
            $salesCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(main_table.created_at, '+00:00', '".$offset."')) >= '" . (date('Y-m-d H:i:s', (strtotime($date_from)))) ."'"));
        }
        if(!empty($date_to)) {
            $salesCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(main_table.created_at, '+00:00', '".$offset."')) <= '" . (date('Y-m-d H:i:s', (strtotime($date_to))))  ."'"));
        }

//        if(strlen($this->statuses) > 0) {
//            $salesCollection->getSelect()->where(new Zend_Db_Expr("main_table.status IN ({$this->statuses})"));
//        }

        $salesCollection->getSelect()->group(new Zend_Db_Expr("main_table.status"));

        $salesCollection->getSelect()->order( new Zend_Db_Expr('count DESC'));

        $orders = array();
        foreach ($salesCollection as $sale) {
            $order = $sale->toArray();
            unset($order['entity_id']);
            unset($order['store_id']);
            unset($order['state']);
            unset($order['base_grand_total']);
            unset($order['base_currency_code']);
            unset($order['order_currency_code']);

            $order['total'] = $this->_price_format($this->def_currency, 3, $order['total'], $this->currency_code, 0, true);

//            $price = $this->_price_format($order['iso_code'], 1, $order['total_paid'], $this->currency_code);
//            $order['customer'] = $order['firstname'] . ' ' . $order['lastname'];
//
//            if($this->currency_code != false){
//                $order['iso_code'] = $this->currency_code;
//            }

            $orders[] = $order;
        }

        return $orders;

    }

    protected function get_data_graphs() {
        $orders = array();
        $customers = array();
        $offset = $this->_get_timezone_offset();
        $average = array('avg_sum_orders' => 0, 'avg_orders' => 0, 'avg_customers' => 0, 'avg_cust_order' => '0.00', 'tot_customers' => 0, 'sum_orders' => 0, 'tot_orders' => 0);

        if(empty($this->graph_from)) {
            $this->graph_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
        }

        if(empty($this->graph_to)) {
            if(!empty($this->stats_to)) {
                $this->graph_to = $this->stats_to;
            } else {
                $this->graph_to = date("Y-m-d", time());
            }
        }
        $startDate = $this->graph_from . " 00:00:00";
        $endDate = $this->graph_to . " 23:59:59";

        $plus_date = "+1 day";
        if(isset($this->custom_period) && strlen($this->custom_period) > 0) {
            $custom_period = $this->get_custom_period($this->custom_period);

            if($this->custom_period == 3) {
                $plus_date = "+3 day";
            } else if($this->custom_period == 4) {
                $plus_date = "+1 week";
            } else if($this->custom_period == 5 || $this->custom_period == 6 || $this->custom_period == 7) {
                $plus_date = "+1 month";
            }

            if($this->custom_period == 6) {
                $ordersCollection = Mage::getModel('sales/order')->getCollection();
                $ordersCollection->getSelect()->reset(Zend_Db_Select::COLUMNS);
                $ordersCollection->getSelect()->columns(array('min_date_add' => new Zend_Db_Expr ("MIN(`created_at`)")));
                $ordersCollection->getSelect()->columns(array('max_date_add' => new Zend_Db_Expr ("MAX(`created_at`)")));

                foreach ($ordersCollection as $order) {
                    $orders_info = $order->toArray();
                    $startDate = $orders_info['min_date_add'];
                    $endDate = $orders_info['max_date_add'];
                }
            } else {
                $startDate = $custom_period['start_date']." 00:00:00";
                $endDate = $custom_period['end_date']." 23:59:59";
            }
        }

        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        $date = $startDate;
        $d = 0;
        $orders = array();

        while ($date <= $endDate) {
            $d++;
            $dateStr = date('Y-m-d H:i:s', ($date));
            $ordersCollection = Mage::getModel('sales/order')->getCollection();
            $storeTableName = Mage::getSingleton('core/resource')->getTableName('core/store');
            $ordersCollection->getSelect()->reset(Zend_Db_Select::COLUMNS);

            if (!empty($this->group_id)) {
                if($this->is_group_exists($this->group_id)) {
                    $ordersCollection->getSelect()
                        ->joinLeft(
                            array('cs' => $storeTableName),
                            "cs.store_id = main_table.store_id",
                            array());
                    $ordersCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
                }
            }

            $ordersCollection->getSelect()->columns(array('date_add' => new Zend_Db_Expr ("(CONVERT_TZ(main_table.created_at, '+00:00', '{$offset}'))")));
            $ordersCollection->getSelect()->columns(array('value' => new Zend_Db_Expr ("SUM(main_table.base_grand_total)")));
            $ordersCollection->getSelect()->columns(array('tot_orders' => new Zend_Db_Expr ("COUNT(main_table.entity_id)")));

            $ordersCollection->getSelect()->where(new Zend_Db_Expr("((CONVERT_TZ(main_table.created_at, '+00:00', '{$offset}' )) >= '{$dateStr}'
                        AND (CONVERT_TZ(main_table.created_at, '+00:00', '{$offset}')) < '".date('Y-m-d H:i:s', (strtotime($plus_date, $date)))."')"));


            if(strlen($this->statuses) > 0) {
                $this->statuses = str_replace('|', '\',\'', $this->statuses);
                $ordersCollection->getSelect()->where(new Zend_Db_Expr("main_table.status IN ({$this->statuses})"));
            }

            $ordersCollection->getSelect()->group(new Zend_Db_Expr("DATE(main_table.created_at)"));
            $ordersCollection->getSelect()->order(new Zend_Db_Expr("main_table.created_at"));

            $total_order_per_day = 0;
            foreach ($ordersCollection as $order) {
                // var_dump($order->getData());die();
                $order['value'] = $this->_price_format($this->def_currency, 2, $order['value'], $this->currency_code, false, false);

                $total_order_per_day += $order['value'];
                $order['date_add'] += strtotime($order['date_add']);
                $average['tot_orders'] += $order['tot_orders'];
                $average['sum_orders'] += $order['value'];
            }

            $total_order_per_day = number_format($total_order_per_day, 2, '.', '');
            $orders[] = array($date*1000, $total_order_per_day);

            $customersCollection = Mage::getResourceModel('customer/customer_collection');
            $customersCollection->addAttributeToSelect('name');

            if (!empty($this->group_id)) {
                if($this->is_group_exists($this->group_id)) {
                    $customersCollection->getSelect()
                        ->joinLeft(
                            array('cs' => $storeTableName),
                            "cs.store_id = e.store_id");
                    $customersCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
                }
            }

//            $customersCollection->addFieldToFilter('created_at',
//                array('from' => $date,
//                    'to' => strtotime($plus_date, $date),
//                    'date' => true)  );


            $customersCollection->getSelect()->columns(array('date_add' => new Zend_Db_Expr ("CONVERT_TZ((created_at, '+00:00', {$offset}))")));
            $customersCollection->getSelect()->where(new Zend_Db_Expr("((CONVERT_TZ(created_at, '+00:00', '{$offset}')) >= '{$dateStr}'
                        AND (CONVERT_TZ(created_at, '+00:00', '{$offset}')) < '".date('Y-m-d H:i:s', (strtotime($plus_date, $date)))."')"));

            $total_customer_per_day = $customersCollection->getSize();
            $average['tot_customers'] += $total_customer_per_day;
            $customers_count = $customersCollection->getSize();

            $customers[] = array($date*1000, $total_customer_per_day);
            $date = strtotime($plus_date, $date);
        }

        $sum = '0';
        $default_currency_sign = $this->_price_format($this->def_currency, 1, $sum, $this->currency_code, true);

        if($d <= 0) $d = 1;
        $average['avg_sum_orders'] = number_format($average['sum_orders']/$d, 2, '.', ' ');
        $average['avg_orders'] = number_format($average['tot_orders']/$d, 1, '.', ' ');
        $average['avg_customers'] = number_format($average['tot_customers']/$d, 1, '.', ' ');

        if($average['tot_customers'] > 0) {
            $average['avg_cust_order'] = number_format($average['sum_orders']/$average['tot_customers'], 1, '.', ' ');
        }
        $average['sum_orders'] = number_format($average['sum_orders'], 2, '.', ' ');
        $average['tot_customers'] = number_format($average['tot_customers'], 1, '.', ' ');
        $average['tot_orders'] = number_format($average['tot_orders'], 1, '.', ' ');

        return array('orders' => $orders, 'customers' => $customers, 'currency_sign' => $default_currency_sign, 'average' => $average);
    }

    protected function get_orders() {
        $offset = $this->_get_timezone_offset();
        $max_date = null;
        $min_date = null;
        $orderStats = null;

        $ordersStatsCollection = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect('entity_id');
        $ordersCollection = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect('entity_id');


        $orderStatusTableName = Mage::getSingleton('core/resource')->getTableName('sales/order_status');
        $storeTableName = Mage::getSingleton('core/resource')->getTableName('core/store');

        $ordersCollection->getSelect()
            ->joinLeft(
                array('sos' => $orderStatusTableName),
                "sos.status = main_table.status",
                array());
        $ordersStatsCollection->getSelect()
            ->joinLeft(
                array('sos' => $orderStatusTableName),
                "sos.status = main_table.status",
                array());

        if (!empty($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $ordersCollection->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = main_table.store_id");
                $ordersStatsCollection->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = main_table.store_id");

                $ordersCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
                $ordersStatsCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        $ordersStatsCollection->getSelect()->columns(array('count_ords' => new Zend_Db_Expr ('COUNT(main_table.entity_id)')));
        $ordersStatsCollection->getSelect()->columns(array('max_date' => new Zend_Db_Expr ('MAX(CONVERT_TZ(main_table.created_at, "+00:00", "'.$offset.'"))')));
        $ordersStatsCollection->getSelect()->columns(array('min_date' => new Zend_Db_Expr ('MIN(CONVERT_TZ(main_table.created_at, "+00:00", "'.$offset.'"))')));
        $ordersStatsCollection->getSelect()->columns(array('orders_total' => new Zend_Db_Expr ('SUM(main_table.base_grand_total)')));

        $ordersCollection->getSelect()->columns(array('store_id' => new Zend_Db_Expr ('main_table.store_id')));
        $ordersCollection->getSelect()->columns(array('id_order' => new Zend_Db_Expr ('main_table.entity_id')));

        $ordersCollection->getSelect()->columns(array('order_number' => new Zend_Db_Expr ('main_table.increment_id')));
        $ordersCollection->getSelect()->columns(array('id_customer' => new Zend_Db_Expr ('main_table.customer_id')));
        $ordersCollection->getSelect()->columns(array('ord_status' => new Zend_Db_Expr ('sos.label')));
        $ordersCollection->getSelect()->columns(array('status_code' => new Zend_Db_Expr ('sos.status')));
        $ordersCollection->getSelect()->columns(array('qty_ordered' => new Zend_Db_Expr ('main_table.total_qty_ordered')));
        $ordersCollection->getSelect()->columns(array('total_paid' => new Zend_Db_Expr ('main_table.base_grand_total')));
        $ordersCollection->getSelect()->columns(array('firstname' => new Zend_Db_Expr ('main_table.customer_firstname')));
        $ordersCollection->getSelect()->columns(array('lastname' => new Zend_Db_Expr ('main_table.customer_lastname')));
        $ordersCollection->getSelect()->columns(array('full_name' => new Zend_Db_Expr ('CONCAT(main_table.customer_firstname, " ", main_table.customer_lastname)')));
        $ordersCollection->getSelect()->columns(array('iso_code' => new Zend_Db_Expr ('main_table.global_currency_code')));
        $ordersCollection->getSelect()->columns(array('date_add' => new Zend_Db_Expr ('CONVERT_TZ(main_table.created_at, "+00:00", "'.$offset.'")')));
        $ordersCollection->getSelect()->columns(array('count_prods' => new Zend_Db_Expr ('main_table.total_item_count')));

        if(empty($this->sort_by)) {
            $this->sort_by = "id";
        }

        switch ($this->sort_by) {
            case 'name':
                $ordersCollection->getSelect()->order( array('main_table.customer_firstname ASC'));
                $ordersCollection->getSelect()->order( array('main_table.customer_lastname ASC'));
                $ordersStatsCollection->getSelect()->order( array('main_table.customer_firstname ASC'));
                $ordersStatsCollection->getSelect()->order( array('main_table.customer_lastname ASC'));
                break;
            case 'date':
                $ordersCollection->getSelect()->order( array('main_table.created_at DESC'));
                $ordersStatsCollection->getSelect()->order( array('main_table.created_at DESC'));
                break;
            case 'id':
                $ordersCollection->getSelect()->order( array('main_table.entity_id DESC'));
                $ordersStatsCollection->getSelect()->order( array('main_table.entity_id DESC'));
                break;
        }

        if(strlen($this->statuses) > 0){
            $this->statuses = '\'' . str_replace('|', '\',\'', $this->statuses) . '\'';
            $ordersCollection->getSelect()->where(new Zend_Db_Expr("main_table.status IN ({$this->statuses})"));
            $ordersStatsCollection->getSelect()->where(new Zend_Db_Expr("main_table.status IN ({$this->statuses})"));
        }

        if(!empty($this->orders_from)) {
            $ordersCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(main_table.created_at, '+00:00',  '".$offset."')) >= '".date('Y-m-d H:i:s', (strtotime(($this->orders_from." 00:00:00")))) ."'"));
            $ordersStatsCollection->getSelect()->where(new Zend_Db_Expr(" (CONVERT_TZ(main_table.created_at, '+00:00',  '".$offset."')) >= '".date('Y-m-d H:i:s', (strtotime(($this->orders_from." 00:00:00")))) ."'"));
        }

        if(!empty($this->orders_to)) {
            $ordersCollection->getSelect()->where(new Zend_Db_Expr("(CONVERT_TZ(main_table.created_at, '+00:00',  '".$offset."')) <= '".date('Y-m-d H:i:s', (strtotime(($this->orders_to." 23:59:59")))) ."'"));
            $ordersStatsCollection->getSelect()->where(new Zend_Db_Expr(" (CONVERT_TZ(main_table.created_at, '+00:00',  '".$offset."')) <= '".date('Y-m-d H:i:s', (strtotime(($this->orders_to." 23:59:59")))) ."'"));
        }

        $query = '';
        if(!empty($this->search_order_id)) {
            $query_where_parts[] = "(main_table.customer_firstname LIKE ('%".$this->search_order_id."%')
                                     OR main_table.customer_lastname LIKE ('%".$this->search_order_id."%')
                                     OR CONCAT(main_table.customer_firstname, ' ', main_table.customer_lastname) LIKE ('%".$this->search_order_id."%'))";
        }
        if(!empty($this->search_order_id) && preg_match('/^\d+(?:,\d+)*$/', $this->search_order_id)) {
            $query_where_parts[] = "(main_table.entity_id IN (".$this->search_order_id.") OR main_table.increment_id IN (".$this->search_order_id."))";
        }

        if (!empty($query_where_parts)) {
            $query .=  "(" .implode(" OR ", $query_where_parts) . ")";
            $ordersCollection->getSelect()->where($query);
            $ordersStatsCollection->getSelect()->where($query);
        }

//        if(!empty($this->search_order_id)) {
//            $ordersCollection->getSelect()->where(new Zend_Db_Expr("(main_table.customer_firstname LIKE ('%".$this->search_order_id."%') OR main_table.customer_lastname LIKE ('%".$this->search_order_id."%'))"));
//            $ordersStatsCollection->getSelect()->where(new Zend_Db_Expr(" (main_table.customer_firstname LIKE ('%".$this->search_order_id."%') OR main_table.customer_lastname LIKE ('%".$this->search_order_id."%'))"));
//        }
//
//        if(!empty($this->search_order_id)) {
//            $ordersCollection->getSelect()->where(new Zend_Db_Expr("(main_table.entity_id IN (".intval($this->search_order_id).") OR main_table.increment_id IN (".intval($this->search_order_id)."))"));
//            $ordersStatsCollection->getSelect()->where(new Zend_Db_Expr(" (main_table.entity_id IN (".intval($this->search_order_id).") OR main_table.increment_id IN (".intval($this->search_order_id)."))"));
//        }

//         echo($ordersCollection->getSelect()->__toString());die();

        if(!empty($this->page) && !empty($this->show)) {
//            $ordersCollection->setPage(($this->page), $this->show);
            $ordersCollection->getSelect()->limit($this->show, ($this->page-1)*$this->show);
        }

        $orders= array();
        foreach ($ordersCollection as $order) {
            $orderArray = $order->toArray();
            $price = $this->_price_format($orderArray['iso_code'], 1, $order['total_paid'], $this->currency_code);
            $orderArray['customer'] = $orderArray['firstname'] . ' ' . $orderArray['lastname'];
            $orderArray['total_paid'] = $price;

            if($this->currency_code != false){
                $orderArray['iso_code'] = $this->currency_code;
            }

            $orderArray['store_id'] = $order->getStore()->getId();
            $orderArray['store_name'] = $order->getStore()->getName();
            $orderArray['store_group_id'] = $order->getStore()->getGroup()->getId();
            $orderArray['store_group_name'] = $order->getStore()->getGroup()->getName();

            $orders[] = $orderArray;
        }

        if($this->page > 1 && intval($this->search_order_id) > 0) {
            $orders = array();
        }

        foreach ($ordersStatsCollection as $orderStats) {
            $orderStats = $orderStats->toArray();

            if($orderStats['count_ords'] > 0) {
                $max_date = date("n/j/Y", strtotime($orderStats['max_date']));
                $min_date = date("n/j/Y", strtotime($orderStats['min_date']));
            }

        }

        $orders_status = null;
        if(isset($this->get_statuses) && $this->get_statuses == 1) {
            $orders_status = $this->get_orders_statuses();
        }

        $orderStats['orders_total'] = $this->_price_format($this->def_currency, 1, $orderStats['orders_total'], $this->currency_code, false);

        return array("orders" => $orders,
            "orders_count" => intval($orderStats['count_ords']),
            "orders_total" => $orderStats['orders_total'],
            "max_date" => $max_date,
            "min_date" => $min_date,
            "orders_status" => $orders_status);
    }

    protected function get_orders_info() {
        $offset = $this->_get_timezone_offset();
        $order_products = array();
        $order_info = array('iso_code' => '', 's_country_id' => '', 'b_country_id' => '', 'telephone' => '', 'shipping_method_mag' => '', 'payment_method_mag' => '');

        $ordersInfoCollection = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect('entity_id');
        $orderAddressTableName = Mage::getSingleton('core/resource')->getTableName('sales/order_address');
        $orderStatusTableName = Mage::getSingleton('core/resource')->getTableName('sales/order_status');

        $ordersInfoCollection->getSelect()
            ->joinLeft(
                array('oa_s' => $orderAddressTableName),
                "oa_s.entity_id = main_table.shipping_address_id",
                array());
        $ordersInfoCollection->getSelect()
            ->joinLeft(
                array('oa_b' => $orderAddressTableName),
                "oa_b.entity_id = main_table.billing_address_id",
                array());
        $ordersInfoCollection->getSelect()
            ->joinLeft(
                array('sos' => $orderStatusTableName),
                "sos.status = main_table.status",
                array());

        $ordersInfoCollection->getSelect()->columns(array('id_order' => new Zend_Db_Expr ('main_table.entity_id')));
        $ordersInfoCollection->getSelect()->columns(array('store_id' => new Zend_Db_Expr ('main_table.store_id')));
        $ordersInfoCollection->getSelect()->columns(array('order_number' => new Zend_Db_Expr ('main_table.increment_id')));
        $ordersInfoCollection->getSelect()->columns(array('status' => new Zend_Db_Expr ('sos.label')));
        $ordersInfoCollection->getSelect()->columns(array('status_code' => new Zend_Db_Expr ('sos.status')));
        $ordersInfoCollection->getSelect()->columns(array('total_paid' => new Zend_Db_Expr ('main_table.base_grand_total')));
        $ordersInfoCollection->getSelect()->columns(array('customer' => new Zend_Db_Expr ('CONCAT(main_table.customer_firstname, " ", main_table.customer_lastname)')));
        $ordersInfoCollection->getSelect()->columns(array('iso_code' => new Zend_Db_Expr ('main_table.global_currency_code')));
        $ordersInfoCollection->getSelect()->columns(array('date_add' => new Zend_Db_Expr ('CONVERT_TZ(main_table.created_at, "+00:00", "'.$offset.'")')));
        $ordersInfoCollection->getSelect()->columns(array('email' => new Zend_Db_Expr ('main_table.customer_email')));
        $ordersInfoCollection->getSelect()->columns(array('id_customer' => new Zend_Db_Expr ('main_table.customer_id')));
        $ordersInfoCollection->getSelect()->columns(array('subtotal' => new Zend_Db_Expr ('main_table.base_subtotal')));
        $ordersInfoCollection->getSelect()->columns(array('sh_amount' => new Zend_Db_Expr ('main_table.base_shipping_amount')));
        $ordersInfoCollection->getSelect()->columns(array('tax_amount' => new Zend_Db_Expr ('main_table.base_tax_amount')));
        $ordersInfoCollection->getSelect()->columns(array('d_amount' => new Zend_Db_Expr ('main_table.discount_amount')));
        $ordersInfoCollection->getSelect()->columns(array('g_total' => new Zend_Db_Expr ('main_table.base_grand_total')));
        $ordersInfoCollection->getSelect()->columns(array('t_paid' => new Zend_Db_Expr ('main_table.base_total_paid')));
        $ordersInfoCollection->getSelect()->columns(array('t_refunded' => new Zend_Db_Expr ('main_table.base_total_refunded')));
        $ordersInfoCollection->getSelect()->columns(array('t_due' => new Zend_Db_Expr ('main_table.base_total_due')));
        $ordersInfoCollection->getSelect()->columns(array('s_name' => new Zend_Db_Expr ('CONCAT(oa_s.firstname, " ", oa_s.lastname)')));
        $ordersInfoCollection->getSelect()->columns(array('s_company' => new Zend_Db_Expr ('oa_s.company')));
        $ordersInfoCollection->getSelect()->columns(array('s_street' => new Zend_Db_Expr ('oa_s.street')));
        $ordersInfoCollection->getSelect()->columns(array('s_city' => new Zend_Db_Expr ('oa_s.city')));
        $ordersInfoCollection->getSelect()->columns(array('s_region' => new Zend_Db_Expr ('oa_s.region')));
        $ordersInfoCollection->getSelect()->columns(array('s_postcode' => new Zend_Db_Expr ('oa_s.postcode')));
        $ordersInfoCollection->getSelect()->columns(array('s_country_id' => new Zend_Db_Expr ('oa_s.country_id')));
        $ordersInfoCollection->getSelect()->columns(array('s_telephone' => new Zend_Db_Expr ('oa_s.telephone')));
        $ordersInfoCollection->getSelect()->columns(array('s_fax' => new Zend_Db_Expr ('oa_s.fax')));
        $ordersInfoCollection->getSelect()->columns(array('b_name' => new Zend_Db_Expr ('CONCAT(oa_b.firstname, " ", oa_b.lastname)')));
        $ordersInfoCollection->getSelect()->columns(array('b_company' => new Zend_Db_Expr ('oa_b.company')));
        $ordersInfoCollection->getSelect()->columns(array('b_street' => new Zend_Db_Expr ('oa_b.street')));
        $ordersInfoCollection->getSelect()->columns(array('b_city' => new Zend_Db_Expr ('oa_b.city')));
        $ordersInfoCollection->getSelect()->columns(array('b_region' => new Zend_Db_Expr ('oa_b.region')));
        $ordersInfoCollection->getSelect()->columns(array('b_postcode' => new Zend_Db_Expr ('oa_b.postcode')));
        $ordersInfoCollection->getSelect()->columns(array('b_country_id' => new Zend_Db_Expr ('oa_b.country_id')));
        $ordersInfoCollection->getSelect()->columns(array('b_telephone' => new Zend_Db_Expr ('oa_b.telephone')));
        $ordersInfoCollection->getSelect()->columns(array('b_fax' => new Zend_Db_Expr ('oa_b.fax')));


        $ordersInfoCollection->addAttributeToFilter('main_table.entity_id', array(
            'eq' => intval($this->order_id),
        ));

        foreach($ordersInfoCollection as $orderInfo) {
            $order_info_array = $orderInfo->toArray();
            $order_info_array['store_id'] = $orderInfo->getStore()->getId();
            $order_info_array['store_name'] = $orderInfo->getStore()->getName();
            $order_info_array['store_group_id'] = $orderInfo->getStore()->getGroup()->getId();
            $order_info_array['store_group_name'] = $orderInfo->getStore()->getGroup()->getName();

            $order_info = $order_info_array;
        }

        $iso_code = $order_info['iso_code'];
        $elements = array('total_paid', 'subtotal', 'sh_amount', 'tax_amount', 'd_amount', 'g_total', 't_paid', 't_refunded', 't_due');
        foreach($elements as $element) {
            $price = $this->_price_format($iso_code, 1, $order_info[$element], $this->currency_code);
            $order_info[$element] = $price;
        }

        if($this->currency_code != false){
            $order_info['iso_code'] = $this->currency_code;
        }

        $order_info['s_country_id'] = Mage::getModel('directory/country')->load($order_info['s_country_id'])->getName();
        $order_info['b_country_id'] = Mage::getModel('directory/country')->load($order_info['b_country_id'])->getName();

        $ordersItemsCollection = Mage::getModel('sales/order_item')->getCollection()->addAttributeToSelect('product_id', 'parent_item_id');
        $orderTableName = Mage::getSingleton('core/resource')->getTableName('sales/order');

        $ordersItemsCollection->getSelect()
            ->joinLeft(
                array('sfo' => $orderTableName),
                "sfo.entity_id = main_table.order_id",
                array());

        $ordersItemsCollection->getSelect()->columns(array('id_order' => new Zend_Db_Expr ('main_table.order_id')));
        $ordersItemsCollection->getSelect()->columns(array('type_id' => new Zend_Db_Expr ('main_table.product_type')));
        $ordersItemsCollection->getSelect()->columns(array('product_name' => new Zend_Db_Expr ('main_table.name')));
        $ordersItemsCollection->getSelect()->columns(array('product_quantity' => new Zend_Db_Expr ('main_table.qty_ordered')));
        $ordersItemsCollection->getSelect()->columns(array('product_price' => new Zend_Db_Expr ('main_table.base_row_total_incl_tax')));
        $ordersItemsCollection->getSelect()->columns(array('sku' => new Zend_Db_Expr ('main_table.sku')));
        $ordersItemsCollection->getSelect()->columns(array('iso_code' => new Zend_Db_Expr ('sfo.global_currency_code')));
        $ordersItemsCollection->getSelect()->columns(array('product_options' => new Zend_Db_Expr ('main_table.product_options')));

        $ordersItemsCollection->addAttributeToFilter('main_table.order_id', array(
            'eq' => intval($this->order_id),
        ));

        $allowedParentId = array(
            array(
                'null' => true
            ),
            array(
                'eq' => 0
            )
        );

        $ordersItemsCollection->addAttributeToFilter('main_table.parent_item_id', $allowedParentId);

        if(empty($this->sort_by)) {
            $this->sort_by = "id";
        }

        switch ($this->sort_by) {
            case 'name':
                $ordersItemsCollection->getSelect()->order( array('full_name ASC'));
                break;
            case 'date':
                $ordersItemsCollection->getSelect()->order( array('date_add DESC'));
                break;
            case 'id':
                $ordersItemsCollection->getSelect()->order( array('id_order DESC'));
                break;
        }

        if(!empty($this->page) && !empty($this->show)) {
            $ordersItemsCollection->setPage($this->page, $this->show);
        }

        $block = Mage::app()->getLayout()->createBlock('sales/order_item_renderer_default');

        foreach($ordersItemsCollection as $orderItem) {
            $block->setItem($orderItem);
            $_options = $block->getItemOptions();

            $thumbnail = (string)Mage::helper('catalog/image')
                ->init($orderItem->getProduct(), 'image')
                ->constrainOnly(TRUE)
                ->keepAspectRatio(TRUE)
                ->resize(150, null);

//            $thumbnail_path = $orderItem->getProduct()->getThumbnail();
//            $thumbnail = $orderItem->getProduct()->getMediaConfig()->getMediaUrl($thumbnail_path);

            if(($thumbnail == 'no_selection') || (!isset($thumbnail))) {
                $thumbnail = '';
            }

            $orderItem = $orderItem->toArray();

            $orderItem['options'] = array();
            if(isset($_options) && count($_options) > 0){
                foreach ($_options as $option) {
                    $orderItem['options'][$option['label']] = $option['value'];
                }
            }

            $orderItem['product_options'] = unserialize($orderItem['product_options']);
            if(isset($orderItem['product_options']['bundle_options'])){
                foreach ($orderItem['product_options']['bundle_options'] as $option) {
                    $orderItem['options'][$option['label']] = $option['value'][0]['qty'].'x '.$option['value'][0]['title'];
                }
            }

            unset($orderItem['product_options']);

            $module_version = explode(' ', self::MB_VERSION);

            if($module_version[1] > 80 && !empty($orderItem['options'])){
                $orderItem['prod_options'] = $orderItem['options'];
                unset($orderItem['options']);
            }

            if(empty($orderItem['options'])) {
                unset($orderItem['options']);
            }

            $orderItem['thumbnail'] = $thumbnail;
            $orderItem['product_price'] = $this->_price_format($order_info['iso_code'], 1, $orderItem['product_price'], $this->currency_code);
            $orderItem['product_quantity'] = intval($orderItem['product_quantity']);
            $orderItem['type_id'] = ucfirst($orderItem['type_id']);
            if($this->currency_code != false){
                $orderItem['iso_code'] = $this->currency_code;
            }
            unset($orderItem['product']);
            $order_products[] = $orderItem;
        }


        $orderCountItemsCollection = Mage::getModel('sales/order_item')->getCollection()->addAttributeToSelect('entity_id');
        $orderCountItemsCollection->getSelect()->columns(array('count_prods' => new Zend_Db_Expr ('COUNT(item_id)')));
        $orderCountItemsCollection->addAttributeToFilter('order_id', array(
            'eq' => intval($this->order_id),
        ));
        $orderCountItemsCollection->addAttributeToFilter('parent_item_id', $allowedParentId);


        $count_prods = $orderCountItemsCollection->getSize();

        if(!empty($this->order_id)) {

            $cur_order = Mage::getModel('sales/order')->load($this->order_id);

            $actions = array();

            if($cur_order->canCancel()) {
                $actions[] = 'cancel';
            }

            if($cur_order->canHold()) {
                $actions[] = 'hold';
            }

            if($cur_order->canUnhold()) {
                $actions[] = 'unhold';
            }

            if($cur_order->canShip()) {
                $actions[] = 'ship';
            }

            if($cur_order->canInvoice()) {
                $actions[] = 'invoice';
            }

            $cus_id = $cur_order->getCustomerId();

            $customer_data = Mage::getModel('customer/customer')->load($cus_id);

            $customerAddressId = $customer_data->getDefaultBilling();
            if($customerAddressId) {
                $address = Mage::getModel('customer/address')->load($customerAddressId)->toArray();
                if(count($address) > 1) {
                    $order_info['telephone'] = $address['telephone'];
                }
            }

            if(empty($order_info['telephone'])) {
                $order_info['telephone'] = '';
            }
            $order_info['shipping_method_mag'] = $cur_order->getShippingDescription();
            $order_info['payment_method_mag'] = $cur_order->getPayment()->getMethodInstance()->getTitle();


            $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                ->setOrderFilter($cur_order)
                ->load();
            $tracks = array();
            foreach ($shipmentCollection as $shipment) {
                foreach($shipment->getAllTracks() as $tracknum)
                {
                    $track['track_number'] = $tracknum->getTrackNumber();
                    $track['title'] = $tracknum->getTitle();
                    $track['carrier_code'] = $tracknum->getCarrierCode();
                    $track['created_at'] = $tracknum->getCreatedAt();

                    $tracks[]=$track;
                }

            }

            $order_full_info = array("order_info" => $order_info, "order_products" => $order_products, "o_products_count" => $count_prods,  "order_tracking" => $tracks, "actions" => $actions);

            return $order_full_info;
        } else return false;
    }

    protected function map_order_statuses($status) {
        return array(
            'st_id' => $status['status'],
            'st_name' => $status['label']
        );
    }

    protected function get_orders_statuses() {
        $statuses = Mage::getModel('sales/order_status')->getResourceCollection()->getData();

        $final_statuses = array_map( array($this, 'map_order_statuses'), $statuses);

        return $final_statuses;
    }

    private function invoice_order() {
        $order = Mage::getModel("sales/order")->load($this->order_id);
        $result = array('error' => $this->__('An error occurred!'));
        try {
            if($order->canInvoice())
            {
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                if ($invoice->getTotalQty()) {
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                    $invoice->register();
                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                    $transactionSave->save();
                    $order->addStatusHistoryComment('Invoice was created from Mobile Assistant.', false);
                    $order->save();
                    $result = array('success' => 'true');
                } else {
                    $order->addStatusHistoryComment('Cannot create an invoice without products.', false);
                    $order->save();
                    $result = array('error' => $this->__('Cannot create an invoice without products!'));
                }
            } else {
                $order->addStatusHistoryComment('Order cannot be invoiced.', false);
                $order->save();
                $result = array('error' => $this->__('Cannot create an invoice!'));
            }
        }
        catch (Mage_Core_Exception $e) {
        }

        return $result;
    }


    private function ship_order() {
        $result = array('error' => $this->__('An error occurred!'));
        $title = '';
        $order = Mage::getModel('sales/order')->load($this->order_id);

        if(isset($this->tracking_title) && strlen($this->tracking_title) > 0) {
            $title = $this->tracking_title;
        } else {
            $carriers = $this->get_carriers();

            foreach($carriers as $carrier) {
                if($carrier['code'] == $this->carrier_code) {
                    $title = $carrier['label'];
                }
            }
        }

        if($order->hasShipments()) {
            foreach($order->getShipmentsCollection() as $shipment)
            {
                $shipmentIncrementId = $shipment->getIncrementId();
                if($shipmentIncrementId) {
                    if(isset($this->tracking_number) && strlen($this->tracking_number) > 0 && isset($this->carrier_code) && strlen($this->carrier_code) > 0) {
                        try {
                            Mage::getModel('sales/order_shipment_api')->addTrack($shipmentIncrementId, $this->carrier_code, $title, $this->tracking_number);

                            // mail customer
                            if ($this->is_mail == 1) {
                                Mage::getModel('sales/order_shipment_api')->sendInfo($shipmentIncrementId);
                            }

                            $result = array('success' => 'true');
                        }
                        catch(Exception $e) {
                            Mage::log(
                                "error: Adding track number: {$e->getMessage()} ({$e->getCustomMessage()})",
                                null,
                                'emagicone_mobassistantconnector.log'
                            );

                            $result = array('error' => $e->getMessage() . ' ('. $e->getCustomMessage() .')');
                        }
                    } else $result = array('error' => $this->__('Empty tracking number!'));
                }
            }
        }
        else if ($order->canShip()) {

            $shipment = new Mage_Sales_Model_Order_Shipment_Api();
            $shipmentId = $shipment->create($order->getIncrementId());

            if(isset($this->tracking_number) && strlen($this->tracking_number) > 0 && isset($this->carrier_code) && strlen($this->carrier_code) > 0) {
                $shipment->addTrack($shipmentId, $this->carrier_code, $title, $this->tracking_number);
            }

            // mail customer
            if ($this->is_mail == 1) {
                $shipment->sendInfo($shipmentId);
            }

            $result = array('success' => 'true');
        } else
            $result = array('error' => $this->__('Current order cannot be shipped!'));

        return $result;
    }

    private function cancel_order() {
        $order = Mage::getModel('sales/order')->load($this->order_id);
        if($order->canCancel()) {
            $order->cancel();
            $order->addStatusHistoryComment('Order was canceled by MA', false);
            $order->save();
            $result = array('success' => 'true');
        } else {
            $order->addStatusHistoryComment('Order cannot be canceled', false);
            $order->save();
            $result = array('error' => $this->__('Current order cannot be canceled!'));
        }

        return $result;
    }

    private function hold_order() {
        $order = Mage::getModel('sales/order')->load($this->order_id);
        if($order->canHold()) {
            $order->hold();
            $order->addStatusHistoryComment('Order was holded by MA', false);
            $order->save();
            $result = array('success' => 'true');
        } else {
            $order->addStatusHistoryComment('Order cannot be holded', false);
            $order->save();
            $result = array('error' => $this->__('Current order cannot be holded!'));
        }

        return $result;
    }

    private function unhold_order() {
        $order = Mage::getModel('sales/order')->load($this->order_id);
        if($order->canUnhold()) {
            $order->unhold();
            $order->addStatusHistoryComment('Order was unholded by MA', false);
            $order->save();
            $result = array('success' => 'true');
        } else {
            $order->addStatusHistoryComment('Order cannot be unholded', false);
            $order->save();
            $result = array('error' => $this->__('Current order cannot be unholded!'));
        }

        return $result;
    }

    private function delete_track_number() {
        $order = Mage::getModel('sales/order')->load($this->order_id);
        $matches = 0;

        $shipCollection = $order->getShipmentsCollection();
        if($shipCollection) {

            foreach($shipCollection as $_ship) {
                $trackingNums = $_ship->getAllTracks();
                $matches  = 0;
                if (count($trackingNums) >= 1) {
                    foreach ($trackingNums as $track) {
                        if ($track->getNumber() == $this->tracking_number) {
                            $track->delete();
                            $matches++;
                        }
                    }
                }
            }
            if($matches > 0) {
                $result = array('success' => 'true');
            } else $result = array('error' => $this->__('Current tracking number was not found'));

        } else
            $result = array('error' => $this->__('Current order does not have shipments!'));

        return $result;
    }

    protected function set_order_action() {
        $result = array('error' => $this->__('An error occurred!'));
        if(isset($this->order_id) && (intval($this->order_id) > 0)) {
            if(isset($this->action) && strlen($this->action) > 0) {
                $order = Mage::getModel('sales/order')->load($this->order_id);
                if ($order->getId()) {
                    switch ($this->action) {
                        case 'cancel':
                            $result = $this->cancel_order();
                            break;
                        case 'hold':
                            $result = $this->hold_order();
                            break;
                        case 'unhold':
                            $result = $this->unhold_order();
                            break;
                        case 'invoice':
                            $result = $this->invoice_order();
                            break;
                        case 'ship':
                            $result = $this->ship_order();
                            break;
                        case 'del_track':
                            $result = $this->delete_track_number();
                            break;
                    }
                } else $result = array('error' => $this->__('No order was found!'));
            } else $result = array('error' => $this->__('Action is not set!'));
        } else $result = array('error' => $this->__('Order id cannot be empty!'));

        return $result;
    }

    protected function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if (in_array($attribute, array('carts', 'orders','ordered_qty'))) {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    protected function get_carriers() {
        $options = array();
        $originalCarriers = Mage::getSingleton('shipping/config')->getAllCarriers();

        if(!$this->group_id) {
            $this->group_id = null;
        }
        $carriers = array();

            if (Mage::helper('core')->isModuleEnabled('Xtento_CustomTrackers')) {
                $disabledCarriers = explode(",", Mage::getStoreConfig('customtrackers/general/disabled_carriers', null));
                foreach ($originalCarriers as $code => $carrierConfig) {
                    if (in_array($code, $disabledCarriers)) {
                        unset($originalCarriers[$code]);
                    }
                }

                if(strval($this->group_id) != '-1') {
                    $collection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('group_id', $this->group_id);
                    $carriers = array();
                    foreach ($collection as $store) {
                        //do something with $store

                        $config = Mage::getStoreConfig('customtrackers', $store->getStoreId());
                        foreach ($config as $code => $carrierConfig) {
                            if ($code == 'general') continue;
                            if ($carrierConfig['active'] == '1') {
                                $carriers[$code] = $carrierConfig['title'];
                            }
                        }
                    }
                } else {
                    foreach (Mage::app()->getWebsites() as $website) {
                        foreach ($website->getGroups() as $group) {
                            foreach ($group->getStores() as $store) {
                                $config = Mage::getStoreConfig('customtrackers', $store->getStoreId());
                                foreach ($config as $code => $carrierConfig) {
                                    if ($code == 'general') continue;
                                    if ($carrierConfig['active'] == '1') {
                                        if((preg_match('/^Custom Tracker \d$/', $carriers[$code]) && ($carriers[$code] != $carrierConfig['title'])) || (is_null($carriers[$code]))) {
                                            $carriers[$code] = $carrierConfig['title'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }


        foreach($originalCarriers as $_code => $_method)
        {
            if($_method->isTrackingAvailable()) {
                if(!$_title = Mage::getStoreConfig("carriers/$_code/title"))
                    $_title = $_code;
                $options[] = array('code' => $_code, 'label' => (strlen($_title)>0) ? $_title : $_title . " ($_code)");
            }
        }


        foreach ($options as $id => $option) {
            if(in_array($option['code'], array_keys($carriers))) {
                $options[$id]['label'] = $carriers[$option['code']];
            }
        }

        return $options;
    }

    protected function get_customers() {
        $offset = $this->_get_timezone_offset();

        $customerCollection = Mage::getModel('customer/customer')->getCollection();
        $storeTableName = Mage::getSingleton('core/resource')->getTableName('core/store');

        $cust_attr_ids = $this->_get_customers_attr();

        if (!empty($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $customerCollection->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = e.store_id");
                $customerCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        $customerCollection->getSelect()->joinLeft(array('firstname' => Mage::getConfig()->getTablePrefix().'customer_entity_varchar'),
            'e.entity_id = firstname.entity_id AND firstname.attribute_id = "'.$cust_attr_ids['firstname'].'"',
            array('firstname' => 'value'));

        $customerCollection->getSelect()->joinLeft(array('middlename' => Mage::getConfig()->getTablePrefix().'customer_entity_varchar'),
            'e.entity_id = middlename.entity_id AND middlename.attribute_id = "'.$cust_attr_ids['middlename'].'"',
            array('middlename' => 'value'));

        $customerCollection->getSelect()->joinLeft(array('lastname' => Mage::getConfig()->getTablePrefix().'customer_entity_varchar'),
            'e.entity_id = lastname.entity_id AND lastname.attribute_id = "'.$cust_attr_ids['lastname'].'"',
            array('lastname' => 'value'));

        $orderTableName = Mage::getSingleton('core/resource')->getTableName('sales/order');
        $customerCollection->getSelect()->joinLeft( array('sfo'=>$orderTableName), 'e.entity_id = sfo.customer_id', array('sale_id' => 'sfo.entity_id'));

        $customerCollection->getSelect()->columns(array('count_ords' => new Zend_Db_Expr ('COUNT(sfo.entity_id)')));

        $customerCollection->getSelect()->group(array('e.entity_id'));
        $customerCollection->getSelect()->columns(array('c_id' => new Zend_Db_Expr ('e.entity_id')));

        $query = '';
        if(!empty($this->customers_from)) {
            $query_where_parts[] = sprintf(" (CONVERT_TZ(e.created_at, '+00:00', '".$offset."')) >= '%s'", (date('Y-m-d H:i:s', (strtotime($this->customers_from." 00:00:00")))));
        }
        if(!empty($this->customers_to)) {
            $query_where_parts[] = sprintf(" (CONVERT_TZ(e.created_at, '+00:00', '".$offset."')) <= '%s'", (date('Y-m-d H:i:s', (strtotime($this->customers_to." 23:59:59")))));
        }
        if (!empty($query_where_parts)) {
            $query .=  implode(" AND ", $query_where_parts);
            $customerCollection->getSelect()->where($query);
        }


        if(!empty($this->search_val)) {
            $customerCollection->getSelect()->where("e.`email` LIKE '%".$this->search_val."%' OR `firstname`.`value` LIKE '%".$this->search_val."%' OR `lastname`.`value` LIKE '%".$this->search_val."%' OR CONCAT(`firstname`.`value`, ' ', `lastname`.`value`) LIKE '%".$this->search_val."%' OR e.entity_id IN (".intval($this->search_val).")");
        }

        if(!empty($this->cust_with_orders)) {
            $customerCollection->getSelect()->having('count_ords > 0');
        }

        if(empty($this->sort_by)) {
            $this->sort_by = "id";
        }

        switch ($this->sort_by) {
            case 'name':
                $customerCollection->getSelect()->order( array('firstname ASC'));
                $customerCollection->getSelect()->order( array('lastname ASC'));
                break;
            case 'date':
                $customerCollection->getSelect()->order( array('e.created_at DESC'));
                break;
            case 'id':
                $customerCollection->getSelect()->order( array('e.entity_id DESC'));
                break;
        }



        $customers_count = count($customerCollection);
        if(!empty($this->page) && !empty($this->show)) {
            $customerCollection->clear();
            $customerCollection->getSelect()->limit($this->show, ($this->page-1)*$this->show);
        }

//        echo($customerCollection->getSelect()->__toString());die();

        $customers= array();

        if($customers_count > ($this->page-1)*$this->show) {
            foreach ($customerCollection as $customer) {

                $reg_date = explode(' ', $customer->getCreatedAt());
                $reg_date = $reg_date[0];

                $cur_customer = array(
                    "id_customer" => $customer->getCId(),
                    "firstname" => $customer->getFirstname(),
                    "middlename" => $customer->getMidlename(),
                    "lastname" => $customer->getLastname(),
                    "full_name" => $customer->getFirstname() . ' ' . $customer->getLastname(),
                    "date_add" => $reg_date,
                    "email" => $customer->getEmail(),
                    "total_orders" => $customer->getCount_ords()
                );

                $cur_customer['total_orders'] = $customer->getCount_ords();
                $customers[] = $cur_customer;
            }
        }

        return array('customers_count' => $customers_count,
            'customers' => $customers);
    }

    protected function get_customers_info() {
        $user_info = array('city' => '', 'postcode' => '', 'phone' => '', 'region' => '', 'country' => '', 'street' => '', 'country_name' => '');

        $cust_attr_ids = $this->_get_customers_attr($this->user_id);
        $offset = $this->_get_timezone_offset();

        $customer = Mage::getModel('customer/customer')->load($this->user_id);

        $user_info['id_customer'] = $customer->getEntityId();
        $user_info['date_add'] = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp($customer->getCreatedAt()));
        $user_info['email'] = $customer->getEmail();
        $user_info['firstname'] = $customer->getFirstname();
        $user_info['middlename'] = $customer->getMiddlename();
        $user_info['lastname'] = $customer->getLastname();


        $customerAddressId = $customer->getDefaultBilling();

        if($customerAddressId) {
            $address = Mage::getModel('customer/address')->load($customerAddressId)->toArray();
            if(count($address) > 1) {
                $user_info['city'] = $address['city'];
                $user_info['postcode'] = $address['postcode'];
                $user_info['phone'] = $address['telephone'];
                $user_info['region'] = $address['region'];
                $user_info['country'] = $address['country_id'];
                $user_info['street'] = $address['street'];
                $user_info['country_name'] = Mage::getModel('directory/country')->load($address['country_id'])->getName();
            }
        }

        $user_info['address'] = $this->split_values($user_info, array('street', 'city', 'region', 'postcode', 'country_name'));
        unset($user_info['country_name']);

        $ordersCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id',$this->user_id);
        $ordersCollection->addAttributeToSelect('base_grand_total');
        $ordersCollection->addAttributeToSelect('entity_id');
        $ordersCollection->addAttributeToSelect('status');
        $ordersCollection->addAttributeToSelect('total_item_count');
        $ordersCollection->addAttributeToSelect('created_at');
        $ordersCollection->addAttributeToSelect('global_currency_code');
        $ordersCollection->addAttributeToSelect('increment_id');
        $ordersCollection->addAttributeToSelect('store_id');

        $ordersSum = array_sum($ordersCollection->getColumnValues('base_grand_total'));
        $row_page['count_ords'] = $ordersCollection->count();

        $ordersSum = $this->bd_nice_number($ordersSum);
        $row_page['sum_ords'] = $this->_price_format($this->def_currency, 1, $ordersSum, $this->currency_code, 0);

        if(!empty($this->page) && !empty($this->show)) {
            $ordersCollection->clear();
            $ordersCollection->getSelect()->limit($this->show, ($this->page-1)*$this->show);
        }

        $customer_orders = null;
        foreach($ordersCollection as $order) {
            $o_status_label = $order->getStatusLabel();
            $c_order['store_id'] = $order->getStore()->getId();
            $c_order['store_name'] = $order->getStore()->getName();
            $c_order['store_group_id'] = $order->getStore()->getGroup()->getId();
            $c_order['store_group_name'] = $order->getStore()->getGroup()->getName();
            $c_order['ord_status_code'] = $order->status;

            $order = $order->toArray();
            // TODO: assign values in simple way
            list($c_order['total_paid'], $c_order['id_order'], $c_order['ord_status'], $c_order['pr_qty'], $c_order['date_add'], $c_order['iso_code']) = array_values($order);

            $c_order['total_paid'] = $this->_price_format($c_order['iso_code'], 1, $c_order['total_paid'], $this->currency_code);
            $c_order['pr_qty'] = intval($c_order['pr_qty']);
            $c_order['ord_status'] = $o_status_label;
            $c_order['order_number'] = $order['increment_id'];

            unset($order['increment']);

            $customer_orders[] = $c_order;
        }

        $this->_price_format($this->def_currency, 1, $row_page['sum_ords'], $this->currency_code, false);

        return array('user_info' => $user_info, 'customer_orders' => $customer_orders, "c_orders_count" => intval($row_page['count_ords']), "sum_ords" => $row_page['sum_ords']);
    }

    protected function _get_customers_attr($user_id = false) {
        $customer_attrs = array('default_billing' => false, 'default_shipping' => false);
        $attributes = Mage::getResourceModel('customer/attribute_collection')->getItems();

        foreach ($attributes as $attribute) {
            $customer_attrs[$attribute->getAttributeCode()] = $attribute->getAttributeId();
        }

        $customer_data = Mage::getModel('customer/customer')->load($user_id);
        $customer_attrs['default_billing'] = $customer_data->getDefaultBilling();
        $customer_attrs['default_shipping'] = $customer_data->getDefaultShipping();

        return $customer_attrs;
    }


    protected function search_products() {
        $products = array();

        $flatHelper = Mage::helper('catalog/product_flat');
        if ($flatHelper->isEnabled()) {
            $emulationModel = Mage::getModel('core/app_emulation');
            $initialEnvironmentInfo = $emulationModel->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMINHTML);
        }

        $collection = Mage::getModel('catalog/product')->getCollection();

        $collection->addAttributeToSelect('*');

        $collection->addAttributeToSelect('name');
        $collection->joinField(
            'qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        $filters = array();
        if(strlen($this->params) > 0){
            $this->params = explode("|", $this->params);

            foreach($this->params as $param) {
                switch ($param) {
                    case 'pr_id':
                        if(isset($this->val) && strlen($this->val) > 0) {
                            $filters[] = array('attribute' => 'entity_id', 'eq' => $this->val );
                        }
                        break;
                    case 'pr_sku':
                        if(isset($this->val) && strlen($this->val) > 0) {
                            $filters[] = array('attribute' => 'sku', 'like' => '%'.$this->val.'%');
                        }
                        break;
                    case 'pr_name':
                        if(isset($this->val) && strlen($this->val) > 0) {
                            $filters[] = array('attribute' => 'name', 'like' => '%'.$this->val.'%');
                        }
                        break;
                    case 'pr_desc':
                        if(isset($this->val) && strlen($this->val) > 0) {
                            $filters[] = array('attribute' => 'description', 'like' => '%'.$this->val.'%');
                        }
                        break;
                    case 'pr_short_desc':
                        if(isset($this->val) && strlen($this->val) > 0) {
                            $filters[] = array('attribute' => 'description', 'like' => '%'.$this->val.'%');
                        }
                        break;
                }
            }

            if(count($filters) > 0) {
                $collection->addFieldToFilter($filters);
            }
        }


        if($this->sort_by == 'name') {
            $filters[] = array('attribute' => 'name', 'like' => '%%');
            $collection->addFieldToFilter($filters);
        }

        if(empty($this->sort_by)) {
            $this->sort_by = "id";
        }

        switch ($this->sort_by) {
            case 'name':
                $collection->getSelect()->order( array('name'));
                break;
            case 'id':
                $collection->getSelect()->order( array('e'.'.entity_id DESC'));
                break;
        }

        $products_count = $collection->getSize();

        if(!empty($this->page) && !empty($this->show)) {
            $collection->getSelect()->limit($this->show, ($this->page-1)*$this->show);
        }

        foreach($collection as $product) {
            $productFinal['main_id'] = $product->getEntityId();
            $productFinal['product_id'] = $product->getEntityId();
            $productFinal['name'] = $product->getName();
            $productFinal['type_id'] = ucfirst($product->getTypeId());
//            Mage::helper('catalog/image')->init($product, 'thumbnail');

            // $thumbnail = $product->getThumbnail();
            // var_dump($product->isVisibleInCatalog());
            // die();

            $thumbnail = (string)Mage::helper('catalog/image')
                ->init($product, 'image')
                ->constrainOnly(TRUE)
                ->keepAspectRatio(TRUE)
                ->resize(150, null);

//            $productFinal['thumbnail'] = $product->getMediaConfig()->getMediaUrl($thumbnail);
            $productFinal['thumbnail'] = $thumbnail;

            if(($thumbnail == 'no_selection') || (!isset($thumbnail))) {
                $productFinal['thumbnail'] = '';
            }

            $productFinal['sku'] = $product->getSku();
            $productFinal['quantity'] = intval($product->getQty());
            $pArr['price'] = $product->getPrice();
            $pArr['spec_price'] = $product->getSpecialPrice();

            $productFinal['price'] = $this->_price_format($this->def_currency, 1, $pArr['price'], $this->currency_code);

            if($pArr['spec_price'] > 0 && $pArr['spec_price'] != '') {
                $productFinal['spec_price'] = $this->_price_format($this->def_currency, 1, $pArr['spec_price'], $this->currency_code);
            } else {
                unset($productFinal['spec_price']);
            }

            $products[] = $productFinal;
        }

        return array('products_count' => $products_count,
            'products' => $products);
    }

    protected function search_products_ordered() {

        $ordered_products = array();
        $max_date = "";
        $min_date = "";
        $offset = $this->_get_timezone_offset();
        //        $storeId = Mage::app()->getStore()->getId();
        $salesCollection = Mage::getModel("sales/order_item")->getCollection()
            ->addFieldToSelect(array('product_id', 'name', 'sku'));

        $salesCollection->getSelect()->columns(array('main_id' => new Zend_Db_Expr ('main_table.order_id')));
        $salesCollection->getSelect()->columns(array('price' => new Zend_Db_Expr ('main_table.base_row_total_incl_tax')));
        $salesCollection->getSelect()->columns(array('orig_price' => new Zend_Db_Expr ('main_table.original_price*main_table.qty_ordered')));
        $salesCollection->getSelect()->columns(array('quantity' => new Zend_Db_Expr ('main_table.qty_ordered')));
        $salesCollection->getSelect()->columns(array('type_id' => new Zend_Db_Expr ('main_table.product_type')));

        $orderItemTableName = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
        $orderTableName = Mage::getSingleton('core/resource')->getTableName('sales/order');
        $storeTableName = Mage::getSingleton('core/resource')->getTableName('core/store');

        $salesCollection->getSelect()
            ->joinLeft(
                array('order' => $orderTableName),
                "order.entity_id = main_table.order_id",
                array());

        if (!empty($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $salesCollection->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = order.store_id",
                        array());
                $salesCollection->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        $salesCollection->getSelect()->columns(array('iso_code' => new Zend_Db_Expr ('order.global_currency_code')));
        $salesCollection->getSelect()->columns(array('status' => new Zend_Db_Expr ('order.status')));
        $salesCollection->getSelect()->columns(array('created_at' => new Zend_Db_Expr ("CONVERT_TZ(order.created_at, '+00:00', '".$offset."' )")));

        if(strlen($this->val) > 0) {
            $filter_cols = array();
            $filters = array();
            if(strlen($this->params) > 0) {
                $this->params = explode("|", $this->params);
                foreach($this->params as $param) {
                    switch ($param) {
                        case 'pr_id':
                            $filter_cols[] = 'main_table'.'.product_id';
                            $filters[] = array('eq' => $this->val);
                            break;
                        case 'pr_sku':
                            $filter_cols[] = 'main_table'.'.sku';
                            $filters[] = array('like' => '%'.$this->val.'%');
                            break;
                        case 'pr_name':
                            $filter_cols[] = 'main_table'.'.name';
                            $filters[] = array('like' => '%'.$this->val.'%');
                            break;
                        case 'order_id':
                            $filter_cols[] = 'main_table'.'.order_id';
                            $filters[] = array('eq' => $this->val);
                            break;
                    }
                }
                $salesCollection->addFieldToFilter($filter_cols, $filters);
            }
        }

        if(!empty($this->products_from)) {
            $this->products_from .= " 00:00:00";
            $date_filter['from'] = $this->products_from;
        }

        if(!empty($this->products_to)) {
            $this->products_to .= " 23:59:59";
            $date_filter['to'] = $this->products_to;
        }

        $date_filter['date'] = true;


        if(!empty($this->products_from) || !empty($this->products_to)) {
            $salesCollection->addFieldToFilter('order'.'.created_at',
                $date_filter  );
        }

        if(!empty($this->statuses)) {
            $this->statuses = explode('|', $this->statuses);
            $salesCollection->addFieldToFilter('order'.'.status',
                array('in' => array($this->statuses)) );
        }

        switch ($this->sort_by) {
            case 'name':
                $salesCollection->getSelect()->order( array('main_table'.'.name ASC'));
                break;
            case 'id':
                $salesCollection->getSelect()->order( array('main_table'.'.product_id DESC'));
                break;
        }

        if(!empty($this->page) && !empty($this->show)) {
            $salesCollection->getSelect()->limit($this->show, ($this->page-1)*$this->show);
        }

        $ordersDates = $salesCollection->getColumnValues('order'.'.created_at');
        $ordersDates = array_map("strtotime", $ordersDates);

        if($salesCollection->count() > 0) {
            $max_date = date("n/j/Y", max(array_values($ordersDates)));
            $min_date = date("n/j/Y", min(array_values($ordersDates)));
        }

        foreach ($salesCollection as $order) {

            $thumbnail = (string)Mage::helper('catalog/image')
                ->init($order->getProduct(), 'image')
                ->constrainOnly(TRUE)
                ->keepAspectRatio(TRUE)
                ->resize(150, null);

            // $thumbnail_path = $order->getProduct()->getThumbnail();
            // $thumbnail = $order->getProduct()->getMediaConfig()->getMediaUrl($thumbnail_path);

            if(($thumbnail == 'no_selection') || (!isset($thumbnail))) {
                $thumbnail = '';
            }

            $ord_prodArr = $order->toArray();
            $ord_prodArr['thumbnail'] = $thumbnail;

            $ord_prodArr['price'] = $this->_price_format($ord_prodArr['iso_code'], 1, $ord_prodArr['price'], $this->currency_code);
            if($ord_prodArr['orig_price'] > 0) {
                $ord_prodArr['orig_price'] = $this->_price_format($ord_prodArr['iso_code'], 1, $ord_prodArr['orig_price'], $this->currency_code);
            } else {
                unset($ord_prodArr['orig_price']);
            }

            unset($ord_prodArr['product']);

            $ord_prodArr['quantity'] = intval($ord_prodArr['quantity']);
            $ord_prodArr['order_id'] = $ord_prodArr['main_id'];
            $ord_prodArr['type_id'] = ucfirst($ord_prodArr['type_id']);

            $ordered_products[] = $ord_prodArr;
        }

        return array(
            "products_count" => $salesCollection->getSize(),
            "products" => $ordered_products,
            "max_date" => $max_date,
            "min_date" => $min_date
        );
    }


    protected function get_products_info() {
        $flatHelper = Mage::helper('catalog/product_flat');
        if ($flatHelper->isEnabled()) {
            $emulationModel = Mage::getModel('core/app_emulation');
            $initialEnvironmentInfo = $emulationModel->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMINHTML);
        }
        $prods_attr_ids = $this->_get_products_attr();
        $row = null;

        $products = array();
        $productsCollection = Mage::getModel('catalog/product')->getCollection();
        $productsCollection->getSelect()->reset(Zend_Db_Select::COLUMNS);

        $productsCollection->getSelect()->columns(array('id_product' => new Zend_Db_Expr ('e.entity_id')));
        $productsCollection->getSelect()->columns(array('type_id' => new Zend_Db_Expr ('e.type_id')));
        $productsCollection->getSelect()->columns(array('name' => new Zend_Db_Expr ('name.value')));
        $productsCollection->getSelect()->columns(array('price' => new Zend_Db_Expr ('price.value')));
        $productsCollection->getSelect()->columns(array('spec_price' => new Zend_Db_Expr ('sp_price.value')));
        $productsCollection->getSelect()->columns(array('quantity' => new Zend_Db_Expr ('qty.qty')));
        $productsCollection->getSelect()->columns(array('sku' => new Zend_Db_Expr ('e.sku')));
        $productsCollection->getSelect()->columns(array('active' => new Zend_Db_Expr ("IF(status.value = 1, 'Enabled', 'Disabled')")));

        $productsCollection->getSelect()->columns(array('total_ordered' => new Zend_Db_Expr ("(SELECT SUM(qty_ordered) FROM ".Mage::getConfig()->getTablePrefix()."sales_flat_order_item WHERE product_id = e.entity_id)")));
        $productsCollection->getSelect()->columns(array('image' => new Zend_Db_Expr ("(SELECT value FROM ".Mage::getConfig()->getTablePrefix()."catalog_product_entity_media_gallery WHERE entity_id = e.entity_id ORDER BY value_id LIMIT 1)")));


        $productsCollection->getSelect()->joinLeft(array('name' => Mage::getConfig()->getTablePrefix().'catalog_product_entity_varchar'),
            'e.entity_id = name.entity_id AND name.attribute_id = "'.$prods_attr_ids['name'].'"',
            array(''));

        $productsCollection->getSelect()->joinLeft(array('price' => Mage::getConfig()->getTablePrefix().'catalog_product_entity_decimal'),
            'e.entity_id = price.entity_id AND price.attribute_id = "'.$prods_attr_ids['price'].'"',
            array('value'));

        $productsCollection->getSelect()->joinLeft(array('sp_price' => Mage::getConfig()->getTablePrefix().'catalog_product_entity_decimal'),
            'e.entity_id = sp_price.entity_id AND sp_price.attribute_id = "'.$prods_attr_ids['special_price'].'"',
            array('value'));

        $productsCollection->getSelect()->joinLeft(array('qty' => Mage::getConfig()->getTablePrefix().'cataloginventory_stock_item'),
            'e.entity_id = qty.product_id',
            array('qty'));

        $productsCollection->getSelect()->joinLeft(array('descr' => Mage::getConfig()->getTablePrefix().'catalog_product_entity_varchar'),
            'e.entity_id = descr.entity_id AND descr.attribute_id = "'.$prods_attr_ids['description'].'"',
            array('value'));

        $productsCollection->getSelect()->joinLeft(array('short_desc' => Mage::getConfig()->getTablePrefix().'catalog_product_entity_text'),
            'e.entity_id = short_desc.entity_id AND short_desc.attribute_id = "'.$prods_attr_ids['short_description'].'"',
            array('value'));

        $productsCollection->getSelect()->joinLeft(array('status' => Mage::getConfig()->getTablePrefix().'catalog_product_entity_int'),
            'e.entity_id = status.entity_id AND status.attribute_id = "'.$prods_attr_ids['status'].'"',
            array('value'));

        $productsCollection->addFieldToFilter('entity_id', array(
            'eq' => $this->product_id,
        ));

        if($productsCollection->getSize() > 0) {
//            echo($productsCollection->getSelect()->__toString());die();
            foreach ($productsCollection as $product) {
                $row = $product->toArray();
                unset($row['stock_item']);
                unset($row['value']);
                unset($row['qty']);

                $row['id_image'] = (string)Mage::helper('catalog/image')->init($product, 'image')->resize(256);
                $row['id_image_large'] = (string)Mage::helper('catalog/image')->init($product, 'image')->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(800);

                $row['type_id'] = ucfirst($row['type_id']);
                $row['price'] = $this->_price_format($this->def_currency, 1, $row['price'], $this->currency_code);
                if($row['spec_price'] > 0 && $row['spec_price'] != '') {
                    $row['spec_price'] = $this->_price_format($this->def_currency, 1, $row['spec_price'], $this->currency_code);
                } else {
                    unset($row['spec_price']);
                }
                $row['quantity'] = intval($row['quantity']);
                $row['total_ordered'] = intval($row['total_ordered']);

            }

            return $row;
        }

        return false;
    }

    protected function _get_products_attr() {
        $products_attrs = array();
        $productAttrs = Mage::getResourceModel('catalog/product_attribute_collection');
        foreach ($productAttrs as $productAttr) {
            $attr_code = $productAttr->getAttributeCode();
            if(in_array($attr_code, array('name', 'price', 'special_price', 'description', 'short_description', 'status'))){
                $products_attrs[$attr_code] = $productAttr->getId();
            }
        }

        return $products_attrs;
    }

    protected function get_products_descr() {
        $product = Mage::getModel('catalog/product')->load($this->product_id);

        return array('descr' => $product->getDescription(), 'short_descr' => $product->getShortDescription());
    }

    protected function isEnabledFlat() {
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        if (!isset($this->_flatEnabled[$this->getStoreId()])) {
            $this->_flatEnabled[$this->getStoreId()] = $this->getFlatHelper()
                ->isEnabled($this->getStoreId());
        }
        return $this->_flatEnabled[$this->getStoreId()];
    }

    protected function get_abandoned_carts_list() {
        $abandoned_carts = array();
        $offset = $this->_get_timezone_offset();


        $storeTableName = Mage::getSingleton('core/resource')->getTableName('core/store');
        $customerTableName = Mage::getSingleton('core/resource')->getTableName('customer_entity');

        $quotes = Mage::getResourceModel('sales/quote_collection');

        if (!isset($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $quotes->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = main_table.store_id",
                        array());
                $quotes->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        if (!empty($this->search_carts)) {
            $this->search_val = $this->search_carts;
        }
        if (!empty($this->search_val) && preg_match('/^\d+(?:,\d+)*$/', $this->search_val)) {
            $quotes->addAttributeToFilter('main_table.entity_id', array('eq' => intval($this->search_val)));
        } else if(!empty($this->search_val)) {
            $quotes->getSelect()->where("main_table.`customer_email` LIKE '%".$this->search_val."%' OR main_table.`customer_firstname` LIKE '%".$this->search_val."%' OR main_table.`customer_lastname` LIKE '%".$this->search_val."%' OR CONCAT(`customer_firstname`, ' ', `customer_lastname`) LIKE '%".$this->search_val."%'");
        }

        if (!empty($this->carts_from)) {
            $this->carts_from = Mage::getModel('core/date')->timestamp(strtotime($this->carts_from));
            $this->carts_from = date('Y-m-d H:i:s', $this->carts_from);
            $quotes->addFieldToFilter('main_table.updated_at', array('from' => $this->carts_from));
        }

        if (!empty($this->carts_to)) {
            $this->carts_to = Mage::getModel('core/date')->timestamp(strtotime($this->carts_to));
            $this->carts_to = date('Y-m-d H:i:s', $this->carts_to);
            $quotes->addFieldToFilter('main_table.updated_at', array('to' => $this->carts_to));
        }

        // if (!empty($this->with_customer_details)) {
        $quotes->addFieldToFilter('main_table.customer_email', array('notnull' => true));
        // }

        if (empty($this->show_unregistered_customers)) {
            $quotes->addFieldToFilter('main_table.customer_id', array('notnull' => true));
            $quotes->getSelect()
                ->joinInner(
                    array('c' => $customerTableName),
                    "c.entity_id = main_table.customer_id",
                    array());
        }

        $quotes->addFieldToFilter('main_table.is_active', array('eq' => 1));
        $quotes->addFieldToFilter('main_table.items_count', array('gt' => 0));

//        $quotes->load();
        $cart_total_res['total_sum'] = $this->_price_format($this->def_currency, 1, array_sum($quotes->getColumnValues('base_grand_total')), $this->currency_code);
        $cart_total_res['total_count'] = $quotes->getSize();
//        $resource = Mage::getSingleton('core/resource');
//        $readConnection = $resource->getConnection('core_read');
//        $cart_total_res['total_count'] = $readConnection->fetchOne($quotes->getSelectCountSql());

//        $quotes->clear();

        if(!empty($this->page) && !empty($this->show)) {
            $quotes->clear();
            $quotes->getSelect()->limit($this->show, ($this->page-1)*$this->show);
        }

        switch ($this->sort_by) {
            case 'id':
                $quotes->getSelect()->order( array('main_table'.'.entity_id DESC'));
                break;
            case 'date':
                $quotes->getSelect()->order( array('main_table'.'.updated_at DESC'));
                break;
            case 'name':
                $quotes->getSelect()->order( array('main_table'.'.customer_firstname ASC'));
                break;
            default:
                $quotes->getSelect()->order( array('main_table'.'.updated_at DESC'));
                break;
        }
        $carts = array();
//        $collection->prepareForAbandonedReport();
        foreach ($quotes as $quote) {
            if (empty($this->show_unregistered_customers)) {
                // Show only real customers
                $customer_id = $quote->getCustomer()->getId();
                if(empty($customer_id)) {
                    continue;
                }
            }

            $cart['id_cart'] = $quote->getEntityId();

            $dateTimestamp = Mage::getModel('core/date')->timestamp(strtotime($quote->getUpdatedAt()));
            $cart['date_add'] = date('Y-m-d H:i:s', $dateTimestamp);

            $cart['id_shop'] = $quote->getStoreId();
            $cart['id_currency'] = $quote->getBaseCurrenceCode();
            $cart['id_customer'] = $quote->getCustomerId();

            $cart['email'] = $quote->getCustomerEmail();
            $cart['customer'] = $quote->getCustomerFirstname() . ' ' .$quote->getCustomerLastname();

            if(!is_null($quote->getCustomer()->getId())){
                $cart['email'] = $quote->getCustomer()->getEmail();
                $cart['customer'] = $quote->getCustomer()->getFirstname() . ' ' .$quote->getCustomer()->getLastname();
            }

            if ($storeName = Mage::getModel('core/store')->load($quote->getStoreId())->getName())
                $cart['shop_name'] = $storeName;
            else
                $cart['shop_name'] = '';

            $cart['carrier_name'] = $quote->getEntityId();
//            $cart['cart_total'] = $quote->getBaseGrandTotal();
            $cart['cart_total'] = $quote->getBaseSubtotalWithDiscount();

            $cart['cart_total'] = $this->_price_format($this->def_currency, 3, $cart['cart_total'], $this->currency_code);

            $cart['cart_count_products'] = $quote->getItemsCount();

            $carts[] = $cart;
        }

        // Sort by name
        if($this->sort_by == 'name') {
            foreach($carts as $cart){
                foreach($cart as $key=>$value){
                    if(!isset($sortArray[$key])){
                        $sortArray[$key] = array();
                    }
                    $sortArray[$key][] = $value;
                }
            }

            $orderby = "customer"; //change this to whatever key you want from the array

            array_multisort($sortArray[$orderby],SORT_ASC,$carts);
        }

        return array('abandoned_carts' => $carts,
            'abandoned_carts_count' => $cart_total_res['total_count'],
            'abandoned_carts_total' => $cart_total_res['total_sum'],
        );

        return $quote;
    }

    protected function get_abandoned_cart_details(){
        $cart_info = array();
        $cart_products = array();

        if ((int)$this->cart_id < 1)
            return false;

        $offset = $this->_get_timezone_offset();

        $storeTableName = Mage::getSingleton('core/resource')->getTableName('core/store');

        $quotes = Mage::getResourceModel('reports/quote_collection');

        if (!isset($this->group_id)) {
            if($this->is_group_exists($this->group_id)) {
                $quotes->getSelect()
                    ->joinLeft(
                        array('cs' => $storeTableName),
                        "cs.store_id = main_table.store_id",
                        array());
                $quotes->getSelect()->where(new Zend_Db_Expr("cs.group_id = " . $this->group_id));
            }
        }

        if (!empty($this->cart_id) && preg_match('/^\d+(?:,\d+)*$/', $this->cart_id)) {
            $quotes->addFieldToFilter('entity_id', array('eq' => intval($this->cart_id)));
        }

//        $quotes->addFieldToFilter('is_active', array('eq' => 1));
//        $quotes->addFieldToFilter('items_count', array('qt' => 1));

        if(!empty($this->page) && !empty($this->show)) {
            $quotes->getSelect()->limit($this->show, ($this->page-1)*$this->show);
        }

        foreach ($quotes as $quote) {
            $cart_info['id_currency'] = $quote->getQuoteCurrencyCode();

            $cart_info['id_cart'] = $quote->getEntityId();
            $cart_info['id_shop'] = $quote->getStoreId();
            $cart_info['id_currency'] = $quote->getQuoteCurrencyCode();

            $created_at_timestamp = Mage::getModel('core/date')->timestamp(strtotime($quote->getCreatedAt()));
            $cart_info['date_add'] = date('Y-m-d H:i:s', $created_at_timestamp);

            $updated_at_timestamp = Mage::getModel('core/date')->timestamp(strtotime($quote->getUpdatedAt()));
            $cart_info['date_up'] = date('Y-m-d H:i:s', $updated_at_timestamp);

            $cart_info['id_customer'] = $quote->getCustomerId();
            $cart_info['email'] = $quote->getCustomerEmail();
            $cart_info['customer'] = $quote->getCustomerFirstname() . ' ' .$quote->getCustomerLastname();

            $cart_info['phone'] = '';

            if(!is_null($quote->getCustomer()->getId())){
                $cart_info['email'] = $quote->getCustomer()->getEmail();
                $cart_info['customer'] = $quote->getCustomer()->getFirstname() . ' ' .$quote->getCustomer()->getLastname();
                $cart_info['account_registered'] = $quote->getCustomer()->getCreatedAt();
                $customer = Mage::getModel('customer/customer')->load($quote->getCustomer()->getId());
                $customerAddressId = $customer->getDefaultBilling();
                if($customerAddressId) {
                    $address = Mage::getModel('customer/address')->load($customerAddressId)->toArray();
                    if(count($address) > 1) {
                        $cart_info['phone'] = $address['telephone'];
                    }
                }
            } else {
                $cart_info['account_registered'] = $quote->getCreatedAt();
            }

            $account_registered_timestamp = Mage::getModel('core/date')->timestamp(strtotime($cart_info['account_registered']));
            $cart_info['account_registered'] = date('Y-m-d H:i:s', $account_registered_timestamp);

            if ($storeName = Mage::getModel('core/store')->load($quote->getStoreId())->getName())
                $cart_info['shop_name'] = $storeName;
            else
                $cart_info['shop_name'] = '';

            $cart_info['carrier_name'] = $quote->getEntityId();
//            $cart_info['cart_total'] = $quote->getBaseGrandTotal();
            $cart_info['cart_total'] = $this->_price_format($this->def_currency, 3, $quote->getBaseSubtotalWithDiscount(), $this->currency_code);
            $cart_info['cart_count_products'] = $quote->getItemsCount();

            $itemsCollection = $quote->getItemsCollection();
            foreach ($itemsCollection as $item) {
                if(!is_null($item->getParentItem())){
                    continue;
                }
                $product = array();
                $product['option_ids'] = array();
                $product['id_product'] = $item->getProduct()->getEntityId();
                $product['product_name'] = $item->getName();
                $product['product_type'] = $item->getProductType();
                $product['product_quantity'] = $item->getQty();
                $product['sku'] = $item->getSku();
                $product['product_price'] = $item->getRowTotal();
                $product['product_price'] = $this->_price_format($this->def_currency, 3, $product['product_price'], $this->currency_code);

                $thumbnail = (string)Mage::helper('catalog/image')
                    ->init($item->getProduct(), 'small_image')
                    ->constrainOnly(TRUE)
                    ->keepAspectRatio(TRUE)
                    ->resize(150, null);

                if(($thumbnail == 'no_selection') || (!isset($thumbnail))) {
                    $thumbnail = '';
                }

                $product['product_image'] = $thumbnail;

                $buy_request = $item->getBuyRequest()->getData();


                if(isset($buy_request['super_attribute'])){
                    $attribute_ids = $buy_request['super_attribute'];
                    foreach ($attribute_ids as $att_id => $opt_id) {
                        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($att_id);
                        foreach($attribute->getSource()->getAllOptions() as $option) {
                            if($option['value'] == $opt_id){
                                $att[$attribute->getName()] = $option['label'];
                            }
                        }
                    }

                    $product['prod_options'] = $att;
                }

                $options = array();
                // Get option ids
                $item_options = $item->getOptions();

                foreach($item_options as $option){
                    $options[$option->getLabel] = $option->getValue();
                    $code = $option->getCode();
                    if($code == 'option_ids'){
                        $dropdown_option_ids = explode(',', $option->getValue());

                        foreach ($dropdown_option_ids as $id) {
                            $product['option_ids'][$id] = '';
                        }
                    }
                }

                // Get option values ids
                foreach ($item_options as $option) {
                    foreach ($product['option_ids'] as $option_id => $value) {
                        if($option->getCode() == 'option_'.$option_id){
                            $product['option_ids'][$option_id] = $option->getValue();
                        }
                    }

                }

                // Get option and values names
                unset($product['options']);
                // $product['prod_options'] = array();
                $product_options = $item->getProduct()->getOptions();
                foreach ($product_options as $option) {
                    foreach ($product['option_ids'] as $option_id => $option_value) {
                        if($option->getOptionId() == $option_id){
                            $product['prod_options'][$option->getTitle()] = '';
                            $option_values = $option->getValues();
                            foreach ($option_values as $value) {
                                if($value->getOptionTypeId() == $option_value){
                                    $product['prod_options'][$option->getTitle()] = $value->getTitle();
//                                    $product['prod_options'][$option->getTitle()]['price'] = $value->getPrice();
                                }
                            }

                        }
                    }
                }
                unset($product['option_ids']);

                $cart_products[] = $product;
            }

        }

        return array(
            'cart_info' => $cart_info,
            'cart_products' => $cart_products,
            'cart_products_count' => $cart_info['cart_count_products'],
        );
    }

    protected function _price_format($iso_code, $curr_format, $price, $convert_to, $force = false, $format = true) {
        $currency_symbol = '';
        $price = str_replace(' ', '', $price);
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();

        if(!in_array(ucwords($convert_to), Mage::getModel('directory/currency')->getConfigAllowCurrencies())) {
            $convert_to = $baseCurrencyCode;
        }

        if(strlen($convert_to) == 3){
            try {
                $price = Mage::helper('directory')->currencyConvert($price, $baseCurrencyCode, $convert_to);
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
        $sign = '<span>' . $currency_symbol . '</span>';
        if($curr_format == 1) {
            $price = $sign . $price;
        } elseif ($curr_format == 2) {
            $price = $price;
        } elseif ($curr_format == 3) {
            $price = $currency_symbol . $price;
        } else {
            $price = $price . ' ' . $sign;
        }

        return $price;
    }


    protected function _get_default_currency() {
        $symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol();
        $currency = Mage::app()->getStore()->getBaseCurrencyCode();
        return array('currency' => $currency, 'symbol' => $symbol);
    }

    protected function _get_store_currencies($storeId) {
        $CurrencyCode = Mage::getModel('core/config_data')
            ->getCollection()
            ->addFieldToFilter('path','currency/options/allow')
            ->addFieldToFilter('scope_id',$storeId)
            ->getData();
        $currencies_array = explode(',',$CurrencyCode[0]['value']);
        if($currencies_array[0] == '')
        {
            $currencies_array[]= Mage::app()->getStore($storeId)->getCurrentCurrencyCode();
        }

        foreach ($currencies_array as $curCode) {
            $currencySymbol = Mage::app()->getLocale()->currency( $curCode )->getSymbol();
            $currencyName = Mage::app()->getLocale()->currency( $curCode )->getName();
            $currencies[] = array('code' => $curCode, 'symbol' => (is_null($currencySymbol) ? $curCode : $currencySymbol), 'name' => $currencyName);
        }

        return $currencies;
    }

    protected function _get_timezone_offset() {
        $timezone = Mage::app()->getStore()->getConfig('general/locale/timezone');


//
//        $timeoffset = Mage::getModel('core/date')->calculateOffset($timezone);
//        $hours = intval($timeoffset / 60 / 60);
//        $mins = $timeoffset / 60 % 60;
//        $offset2 = (($hours >= 0) ? '+'.$hours : $hours) .':'. $mins;



        $origin_dtz = new DateTimeZone("UTC");
        $remote_dtz = new DateTimeZone($timezone);
        $origin_dt = new DateTime("now", $origin_dtz);
        $remote_dt = new DateTime("now", $remote_dtz);
        $offset = $remote_dtz->getOffset($remote_dt) - $origin_dtz->getOffset($origin_dt);

        $hours = intval($offset / 60 / 60);
        $mins = $offset / 60 % 60;
        $offset = (($hours >= 0) ? '+'.$hours : $hours) .':'. $mins;
//
//        $offset =  $offset / 60 / 60;

        return $offset;
    }


    protected function reset_null(&$item) {
        if(empty($item) && $item != 0) {
            $item = '';
        }
        $item = trim($item);
    }


    protected function validate_types(&$array, $names) {
        foreach ($names as $name => $type) {
            if (isset($array["$name"])) {
                switch ($type) {
                    case 'INT':
                        $array["$name"] = intval($array["$name"]);
                        break;
                    case 'FLOAT':
                        $array["$name"] = floatval($array["$name"]);
                        break;
                    case 'STR':
                        $array["$name"] = str_replace(array("\r", "\n"), ' ', addslashes(htmlspecialchars(trim(urldecode($array["$name"])))));
                        break;
                    case 'STR_HTML':
                        $array["$name"] = addslashes(trim($array["$name"]));
                        break;
                    default:
                        $array["$name"] = '';
                }
            } else {
                $array["$name"] = '';
            }
        }

        foreach ($array as $key => $value) {
            if (!isset($names[$key]) && $key != "call_function" && $key != "hash") {
                $array[$key] = "";
            }
        }

        return $array;
    }


    protected function get_custom_period($period = 0) {
        $custom_period = array('start_date' => "", 'end_date' => "");
        $format = "m/d/Y";

        switch($period) {
            case 0: //3 days
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date("m"), date("d")-2, date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date("m"), date("d"), date("Y")));
                break;

            case 1: //7 days
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date("m"), date("d")-6, date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date("m"), date("d"), date("Y")));
                break;

            case 2: //Prev week
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date("n"), date("j")-6, date("Y")) - ((date("N"))*3600*24));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date("n"), date("j"), date("Y")) - ((date("N"))*3600*24));
                break;

            case 3: //Prev month
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date("m")-1, 1, date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date("m"), date("d")-date("j"), date("Y")));
                break;

            case 4: //This quarter
                $m = date("n");
                $start_m = 1;
                $end_m = 3;

                if($m <= 3) {
                    $start_m = 1;
                    $end_m = 3;
                } else if($m >= 4 && $m <= 6) {
                    $start_m = 4;
                    $end_m = 6;
                } else if($m >= 7 && $m <= 9) {
                    $start_m = 7;
                    $end_m = 9;
                } else if($m >= 10) {
                    $start_m = 10;
                    $end_m = 12;
                }

                $custom_period['start_date'] = date($format, mktime(0, 0, 0, $start_m, 1 , date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, $end_m+1, date(1)-1, date("Y")));
                break;

            case 5: //This year
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date(1), date(1), date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date(1), date(1)-1, date("Y")+1));
                break;

            case 7: //Last year
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date(1), date(1), date("Y")-1));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date(1), date(1)-1, date("Y")));
                break;
        }

        return $custom_period;
    }


    protected function bd_nice_number($n, $is_count = false) {
        $n = floatval($n);

        if(!is_numeric($n)) return $n;

        $final_number = $n;
        $number_suf = "";
        // now filter it;
        if($n > 1000000000000000) {
            //return number_format(round(($n / 1000000000000000), 1), 1, '.', ' ') . 'P';
            $final_number = round(($n / 1000000000000000), 2);
            $number_suf = "P";

        } else if($n > 1000000000000) {
            //return number_format(round(($n / 1000000000000),1), 1, '.', ' ') . 'T';
            $final_number = round(($n / 1000000000000), 2);
            $number_suf = "T";

        } else if($n > 1000000000) {
            //return number_format(round(($n / 1000000000),1), 1, '.', ' ') . 'G';
            $final_number = round(($n / 1000000000), 2);
            $number_suf = "G";

        } else if($n > 1000000) {
            //return number_format(round(($n / 1000000),1), 1, '.', ' ') . 'M';
            $final_number = round(($n / 1000000), 2);
            $number_suf = "M";

        } else if($n > 10000) {
            return number_format($n, 0, '', ' ');
        }

        if($is_count) {
            $final_number = intval($final_number);
        } else {
            $final_number = number_format($final_number, 2, '.', ' ') . $number_suf;
        }

        return $final_number;
    }

    protected function split_values($arr, $keys, $sign = ', ') {
        $new_arr = array();
        foreach($keys as $key) {
            if(isset($arr[$key])) {
                if(!is_null($arr[$key]) && $arr[$key] != '') {
                    $new_arr[] = $arr[$key];
                }
            }
        }
        return implode($sign, $new_arr);
    }

}