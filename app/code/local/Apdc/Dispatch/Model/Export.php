<?php

class Apdc_Dispatch_Model_Export extends Apdc_Dispatch_Model_Mistral_Ftp
{
    public function processRequest($params)
    {
        if (!isset($params) || !isset($params['medium'])) {
            Mage::log('params are not set!', null, 'dispatch.log');
            break;
        }

        //$c_date = date("Y-m-d",mktime(0, 0, 0, 2, 23, 2017));

        $c_date = date('Y-m-d');
        $to = date('Y-m-d', strtotime('+5 day'));

        try {
            $params['c_date'] = $c_date;

            switch ($params['medium']) {
                case 'ftp':
                    $params['orders'] = Mage::getModel('pmainguet_delivery/orders_shop')->getShopsOrdersAction($c_date, $to);
                    if (Mage::getStoreConfig('apdcdispatch/general/mistral_active')) {
                        Mage::getModel('apdcdispatch/mistral_ftp')->_processRequestFtp($params);
                    } else {
                        Mage::log('Export via FTP est désactivé', null, 'export.log');
                        Mage::getModel('apdcadmin/mail')->warnMistralDeactivated();
                    }
                    break;
                case 'api':
                    break;
                case 'mail':
                    //send only for current day
                    $params['orders'] = Mage::getModel('pmainguet_delivery/orders_shop')->getShopsOrdersAction($c_date, $c_date);
                    if (Mage::getStoreConfig('apdcdispatch/general/mail_active')) {
                        Mage::getModel('apdcdispatch/mail')->processRequestMail($params);
                    } else {
                        Mage::log('Envoi par mail des commerçants est désactivé', null, 'export.log');
                        Mage::getModel('apdcadmin/mail')->warnMailShopDeactivated();
                    }
                default:
                    break;
            }
        } catch (Exception $e) {
            Mage::log($e, null, 'dispatch.log');
            Mage::getModel('apdcadmin/mail')->warnErrorMistral($e->getMessage());
        }
    }

    public function processCronMistral()
    {
        $params['medium'] = 'ftp';
        $this->processRequest($params);
    }

    public function processCronShops()
    {
        $params['medium'] = 'mail';
        $this->processRequest($params);
    }
}
