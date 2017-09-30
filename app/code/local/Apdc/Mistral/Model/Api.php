<?php

class Apdc_Mistral_Model_Api extends Mage_Core_Model_Abstract
{
    protected $_api_url;
    protected $_request_url;
    protected $_api_token;
    protected $_api_key;
    protected $_body;
    protected $_headers;
    protected $_config;
    protected $_api_verb;

    public function __construct()
    {
        $this->_api_url = 'https://backend.stars-services.com/WebServices/V1/';
        $this->_api_token = 'APDC2712A9B6';
        $this->_api_key = '6AA0A660A6B647C39F4CE6CED09621A2';
        $this->_headers = array('Content-Type: application/json');
        $this->_config = array(
                    'timeout' => 2,
            );
    }

    protected function getAuthorizedActions()
    {
        return array('getpictures', 'getOrder', 'importOrder', 'cancelOrder');
    }

    protected function setAction($data)
    {
        $this->_request_url = $this->_api_url.$data['action'];

        switch ($data['action']) {
            case 'getOrder':
                $this->_api_verb = Zend_Http_Client::POST;
                $this->_body = array(
                    'ApiKey' => $this->_api_token,
                    'PartnerRef' => $data['PartnerRef'],
                    'OrderRef' => $data['OrderRef'],
                );
                break;
            case 'importOrder':
                $this->_api_verb = Zend_Http_Client::POST;
                $this->_body = array(
                    'ApiKey' => $this->_api_token,
                    'PartnerRef' => $data['PartnerRef'],
                    'Order' => array(
                        'Items' => [],
                        'Billing' => [],
                        'CashOnDelivery' => 0,
                        'Parcels' => [],
                        'Contact' => '',
                        'ContactMails' => '',
                        'ContactPhones' => '',
                        'Delivery' => [],
                        'PartnerCode' => '',
                        'FlowTypeCode' => '',
                        'ActionDate' => '',
                        'OrderAmount' => 0,
                        'Reference' => '',
                        'TotalBoxCount' => 0,
                        'PartnerFlowCode' => '',
                        'PaymentMode' => '',
                        'Pick' => [],
                        'TourReference' => '',
                    ),
                );
                break;
            case 'cancelOrder':
                $this->_api_verb = Zend_Http_Client::POST;
                $this->_body = array(
                    'ApiKey' => $this->_api_token,
                    'PartnerRef' => $data['PartnerRef'],
                    'OrderRef' => $data['OrderRef'],
                );
                break;
            case 'getpictures':
                $this->_api_verb = Zend_Http_Client::POST;
                $this->_body = array(
                    'Token' => $this->_api_token,
                    'PartnerRef' => $data['PartnerRef'],
                    'OrderRef' => $data['OrderRef'],
                );
                break;
            default:
                break;
        }
    }

    protected function getBody()
    {
        return json_encode($this->_body);
    }

    public function processRequest(array $data)
    {
        if (isset($data['action']) && in_array($data['action'], $this->getAuthorizedActions())) {
            $this->setAction($data);
            $curl = new Varien_Http_Adapter_Curl();
            $curl->setConfig($this->getConfig());
            $curl->write($this->_api_verb, $this->_request_url, '1.1', $this->_headers, $this->getBody());
            $response = $curl->read();
            if ($response === false) {
                return false;
            }
            $response = preg_split('/^\r?$/m', $response, 2);
            $response = trim($response[1]);
            $curl->close();

            try {
                $result = json_decode($response, true);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            $result = 'error';
        }

        return $result;
    }
}
