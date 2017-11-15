<?php

class Apdc_Dispatch_Model_Mistral_Ftp extends Mage_Core_Model_Abstract
{
    protected $_path;
    protected $_host;
    protected $_port;
    protected $_ssl;
    protected $_login;
    protected $_pwd;
    protected $_connection;

    protected $_shippingmethods;
    protected $_methodfortournees = 'tablerate';
    protected $_methodforcitycourses = 'flatrate';
    protected $_doforcitycourses = 'APDC5923';
    protected $_dos;
    protected $_maxlengths;
    protected $_creneaux;

    public function __construct()
    {
        if (Mage::getStoreConfig('apdcdispatch/ftp_mistral/ftp_host') != '') {
            $this->_host = Mage::getStoreConfig('apdcdispatch/ftp_mistral/ftp_host');
        } else {
            $this->_host = 'ftporamtl.stars-services.com';
        }

        if (Mage::getStoreConfig('apdcdispatch/ftp_mistral/ftp_port') != '') {
            $this->_port = Mage::getStoreConfig('apdcdispatch/ftp_mistral/ftp_port');
        } else {
            $this->_port = 21;
        }

        $this->_ssl = Mage::getStoreConfig('apdcdispatch/ftp_mistral/use_ssl');

        if (Mage::getStoreConfig('apdcdispatch/ftp_mistral/ftp_login') != '') {
            $this->_login = Mage::getStoreConfig('apdcdispatch/ftp_mistral/ftp_login');
        }

        if (Mage::getStoreConfig('apdcdispatch/ftp_mistral/ftp_pwd') != '') {
            $this->_pwd = Mage::getStoreConfig('apdcdispatch/ftp_mistral/ftp_pwd');
        }

        // $this->_login="ftpapdc";
        // $this->_pwd="ftp.1a";

        $this->_path = Mage::getBaseDir().'/var/tmp/';

        $dos = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();
        $dos = $dos->toArray(array('code_do', 'website_id'))['items'];
        foreach ($dos as $do) {
            $this->_dos[$do['website_id']] = $do['code_do'];
        }

        $this->_maxlengths = [
            1 => 3,
            2 => 10,
            3 => 15,
            4 => 40,
            5 => 64,
            6 => 128,
            7 => 10,
            8 => 20,
            9 => 128,
            10 => 5,
            11 => 60,
            12 => 2,
            13 => 60,
            14 => 10,
            15 => 10,
            16 => 20,
            17 => 10,
            18 => 3,
            19 => 1,
            20 => 10,
            21 => 5,
            22 => 40,
            23 => 5,
            24 => 5,
            25 => 5,
            26 => 15,
            27 => 20,
            28 => 9,
            29 => 9,
            30 => 5,
            31 => 10,
            32 => 3,
            33 => 9,
            34 => 20,
            35 => 1,
            36 => 600,
            37 => 10,
            38 => 40,
            39 => 64,
            40 => 128,
            41 => 10,
            42 => 20,
            43 => 128,
            44 => 5,
            45 => 60,
            46 => 255,
            47 => 60,
            48 => 15,
            49 => 10,
            50 => 1,
            51 => 1,
            52 => 30,
            53 => 30,
            54 => 320,
            55 => 1,
        ];

        $this->_creneaux = [
            'debut' => [
                'Tue' => '17:00',
                'Wed' => '17:00',
                'Thu' => '17:00',
                'Fri' => '17:00',
                'Sat' => '11:00',
            ],
            'fin' => [
                'Tue' => '18:00',
                'Wed' => '18:00',
                'Thu' => '18:00',
                'Fri' => '18:00',
                'Sat' => '12:00',
            ],
        ];

        $this->_shippingmethods = Mage::getSingleton('shipping/config')->getActiveCarriers();
    }

    protected function login($username, $password)
    {
        if (!ftp_login($this->_connection, $username, $password)) {
            throw new Exception("Login failed on {$this->_host}:{$this->_port} with {$username}.");
        }
    }

    protected function pasv($pasv = true)
    {
        ftp_pasv($this->_connection, $pasv);
    }

    protected function put($remote_file, $local_file)
    {
        ftp_put($this->_connection, $remote_file, $local_file, FTP_ASCII);
    }

    protected function cleansemicolons($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = str_replace(';', '', $value);
        }

        return $array;
    }

    protected function co($do, $merchant, $order)
    {
        $co = [
            1 => 'CO2',
            2 => $do,
            3 => 'CLI-'.$order['customer_id'],
            4 => "{$order['increment_id']}-{$merchant['id_attribut_commercant']}",
            5 => '',
            6 => 'Carton',
            7 => 1,
            8 => '',
            9 => '',
            10 => '',
            11 => '',
        ];

        return implode(';', $this->cleansemicolons($co));
    }

    protected function ol($do, $merchant, $order)
    {
        $ol = [
                1 => 'OL',
                2 => $do,
                3 => '',
                4 => 'CLI-'.$order['customer_id'],
                5 => '',
                6 => "{$order['first_name']} {$order['last_name']}",
                7 => '',
                8 => '',
                9 => str_replace(array("\n\r", "\n", "\r",";"), "", $order['street']),
                10 => $order['zipcode'],
                11 => $order['city'],
                12 => 'FR',
                13 => 'FRANCE',
                14 => preg_replace('/ /', '', $order['phone']),
                15 => '',
                16 => $order['batiment'],
                17 => $order['codeporte1'],
                18 => $order['etage'],
                19 => 'N',
                20 => '',
                21 => '',
                22 => "{$order['increment_id']}-{$merchant['id_attribut_commercant']}",
                23 => $order['livraison_debut'],
                24 => $order['livraison_fin'],
                25 => '',
                26 => '',
                27 => '',
                28 => '',
                29 => '',
                30 => '',
                31 => $order['delivery_date'],
                32 => '',
                33 => '',
                34 => 'TRAD',
                35 => 'C',
                36 => "{$order['info']} | Autre Contact: {$order['contact']} {$order['contact_phone']}",
                37 => '',
                38 => $order['increment_id'],
                39 => '',
                40 => '',
                41 => '',
                42 => '',
                43 => '',
                44 => '',
                45 => '',
                46 => '',
                47 => '',
                48 => '',
                49 => '',
                50 => '',
                51 => '',
                52 => '',
                53 => $order['transporter'],
                54 => $order['mail'],
            ];

        foreach ($ol as $key => $value) {
            $ol[$key] = substr($value, 0, $this->_maxlengths[$key]);
        }

        return implode(';', $this->cleansemicolons($ol));
    }

    protected function oe($do, $merchant, $order)
    {
        $oe = [
            1 => 'OE',
            2 => $do,
            3 => '',
            4 => 'COM-'.$merchant['id_attribut_commercant'],
            5 => $merchant['name'],
            6 => $merchant['name'],
            7 => '',
            8 => '',
            9 => $merchant['street'],
            10 => $merchant['postcode'],
            11 => $merchant['city'],
            12 => 'FR',
            13 => 'FRANCE',
            14 => preg_replace('/ /', '', $merchant['phone']),
            15 => preg_replace('/ /', '', $merchant['m_phone']),
            16 => '',
            17 => '',
            18 => '',
            19 => 'N',
            20 => '',
            21 => '',
            22 => "{$order['increment_id']}-{$merchant['id_attribut_commercant']}",
            23 => $order['enlevement_debut'],
            24 => $order['enlevement_fin'],
            25 => '',
            26 => '',
            27 => '',
            28 => '',
            29 => '',
            30 => '',
            31 => $order['delivery_date'],
            32 => '',
            33 => '',
            34 => 'TRAD',
            35 => 'C',
            36 => '',
            37 => '',
            38 => $order['increment_id'],
            39 => '',
            40 => '',
            41 => '',
            42 => '',
            43 => '',
            44 => '',
            45 => '',
            46 => '',
            47 => '',
            48 => '',
            49 => '',
            50 => '',
            51 => '',
            52 => '',
            53 => $order['transporter'],
            54 => 'contact@aupasdecourses.com',
        ];

        foreach ($oe as $key => $value) {
            $oe[$key] = substr($value, 0, $this->_maxlengths[$key]);
        }

        return implode(';', $this->cleansemicolons($oe));
    }

    protected function formatFtpMistral($shops)
    {
        Mage::log("Model Export - start format FTP Mistral",null,"export.log");
        $dos = $this->_dos;
        $out = '';
        foreach ($shops as $store_id => $store) {
            Mage::log("Model Export - format FTP Store ".$store_id,null,"export.log");
            foreach ($store as $id => $shop) {
                foreach ($shop['orders'] as $i => $o) {
                    Mage::log("Model Export - format FTP Order ".$i,null,"export.log");
                    if (isset($shop['infos'])) {
                        $code = explode('_', $o['shipping_method'])[0];
                        $datetime = DateTime::createFromFormat('Y-m-d', $o['delivery_date']);
                        $o['delivery_date'] = $datetime->format('d/m/Y');
                        $day = $datetime->format('D');
                        if ($code == $this->_methodforcitycourses) {
                            Mage::log("Model Export - format FTP Citycourses order ".$i,null,"export.log");
                            $o['transporter'] = 'STARDOM';
                            $o['enlevement_debut'] = '11:00';
                            $o['enlevement_fin'] = '12:00';
                            $o['livraison_debut'] = '12:00';
                            $o['livraison_fin'] = '13:00';
                            $out .= $this->oe($this->_doforcitycourses, $shop['infos'], $o).';0'.PHP_EOL;
                            $out .= $this->ol($this->_doforcitycourses, $shop['infos'], $o).';0'.PHP_EOL;
                            $out .= $this->co($this->_doforcitycourses, $shop['infos'], $o).';0'.PHP_EOL;
                        } else {
                            Mage::log("Model Export - format FTP APDC Tournées order ".$i,null,"export.log");
                            $o['transporter'] = 'LPR';
                            $o['enlevement_debut'] = $this->_creneaux['debut'][$day];
                            $o['enlevement_fin'] = $this->_creneaux['fin'][$day];
                            $o['livraison_debut'] = preg_split('/-/', $o['delivery_time'])[0];
                            $o['livraison_fin'] = preg_split('/-/', $o['delivery_time'])[1];
                            $out .= $this->oe($dos[$store_id], $shop['infos'], $o).';0'.PHP_EOL;
                            $out .= $this->ol($dos[$store_id], $shop['infos'], $o).';0'.PHP_EOL;
                            $out .= $this->co($dos[$store_id], $shop['infos'], $o).';0'.PHP_EOL;
                        }
                    } else {
                        $prods = '';
                        foreach ($o['products'] as $prod) {
                            $prods .= 'commercant. '.$prod['commercant'].', item_id: '.$prod['item_id'].PHP_EOL;
                        }
                        $error[] = [
                            'increment_id' => $i,
                            'products' => $prods,

                        ];
                    }
                }
            }
        }
        if (isset($error)) {
            Mage::getModel('apdcadmin/mail')->warnErrorCommercantNeighborhood($error);
        }

        return $out;
    }

    protected function _processRequestFtp($params)
    {
        $currentTime = Mage::getSingleton('core/date')->timestamp();
        $c_time = date('His', strtotime($currentTime));
        $fileName = str_replace('-', '', $params['c_date'])."_APDC_CDE_{$c_time}.csv";
        $tmpFileName = $this->_path.str_replace('-', '', $params['c_date'])."_APDC_CDE_{$c_time}.csv";
        $out = $this->formatFtpMistral($params['orders']);

        if ($out != '') {
            file_put_contents($tmpFileName, $out);

            if (Mage::getStoreConfig('apdcdispatch/general/mode')&&Mage::getStoreConfig('apdcdispatch/general/mistral_active')) {
                Mage::log("Model Export - start send to Mistral",null,"export.log");
                if (!is_null($this->_host) && !is_null($this->_port)) {
                    $this->_connection = (!$this->_ssl) ? ftp_connect($this->_host, $this->_port) : ftp_ssl_connect($this->_host, $this->_port);
                    if (!$this->_connection) {
                        throw new Exception("Couldn't connect to {$this->_host}:{$this->_port}.");
                    } else {
                        Mage::log('Model Export - processRequestFtp - process request', null, 'export.log');
                        $this->pasv(false);
                        $this->login($this->_login, $this->_pwd);
                        $this->put("IN/{$fileName}", $tmpFileName);
                        Mage::log('Model Export - $currentTime - {$currentTime}', null, 'export.log');
                        Mage::log('Model Export - $c_time - {$c_time}', null, 'export.log');
                        Mage::log('Model Export - sent file - {$fileName}', null, 'export.log');
                        Mage::log('Model Export - processRequestFtp - request done', null, 'export.log');
                    }
                }
            }
        }
    }
}
