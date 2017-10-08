<?php

class Apdc_Dispatch_Model_Mail extends Mage_Core_Model_Abstract
{
    private $_transport;

    protected $_amasty_mw_date;
    protected $_c_date;
    protected $_status_no_display;
    protected $_refund_id_limit;

    public function __construct()
    {
        parent::__construct();

        $this->_amasty_mw_date = date('Y-m-d', mktime(0, 0, 0, 1, 20, 2016));

        $currentTime = Mage::getSingleton('core/date')->timestamp();
        $this->_c_date = date('Y-m-d', $currentTime);

        $this->_status_no_display = array('complete', 'pending_payment', 'payment_review', 'holded', 'closed', 'canceled');
        $this->_refund_id_limit = 2016000249;
    }

    public function getEmails($infos)
    {

        //Mode production ou test
        Mage::log(Mage::getStoreConfig('apdcdispatch/general/mode'), null, 'export.log');
        if (Mage::getStoreConfig('apdcdispatch/general/mode')) {
            $mails['m_email'] = $infos['m_email'];
            if ($infos['e1_email'] != null) {
                $mails['e1_email'] = $infos['e1_email'];
            }
            if ($infos['e2_email'] != null) {
                $mails['e2_email'] = $infos['e2_email'];
            }
        } else {
            $mails = [
               'm_email' => 'pierre@aupasdecourses.com',
            ];
        }

        return $mails;
    }

    public function processRequestMail($params,$getByStore=true)
    {
        if($getByStore){
            foreach ($params['orders'] as $store_id => $store) {
                foreach ($store as $id => $shop) {
                    if ($shop['orders'] != array()) {
                        if (isset($shop['infos'])) {
                            $pdf = Mage::getModel('apdcdispatch/mail_pdf', array($shop['infos']['name'], $this->_c_date));
                            foreach ($shop['orders'] as $i => $o) {
                                $pdf->addOrder($o);
                            }
                            if ($pdf->getOrdersCount() != 0) {
                                $attach = $pdf->render();
                                $emails = $this->getEmails($shop['infos']);

                                $mail = new Mandrill_Message(Mage::getStoreConfig(Ebizmarts_Mandrill_Model_System_Config::APIKEY));

                                $mail->addTo($emails);
                                //$mail->addBcc(Mage::getStoreConfig('trans_email/ident_general/email'));
                                $mail->setFrom(Mage::getStoreConfig('trans_email/ident_general/email'), "L'équipe d'Au Pas De Courses");
                                $mail->setSubject("Au Pas De Courses {$pdf->getOrdersCount()} commandes le {$this->_c_date}");
                                $mail->setBodyHtml(
                                    Mage::getModel('core/email_template')->loadByCode('APDC::Mail envoi commande commerçants')
                                    ->getProcessedTemplate(['commercant' => $shop['infos']['name'], 'nbecommande' => $pdf->getOrdersCount()])
                                );

                                $mail->createAttachment($attach, 'application/pdf', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, "{$shop['infos']['name']} - {$this->_c_date}.pdf");

                                try {
                                    $mail->send($this->_transport);
                                } catch (Exception $e) {
                                    Mage::log($e->getMessage(), null, 'send_daily_order.log');
                                }
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
        }else{
            foreach ($params['orders'] as $id => $shop) {
                if ($shop['orders'] != array()) {
                    if (isset($shop['infos'])) {
                        $pdf = Mage::getModel('apdcdispatch/mail_pdf', array($shop['infos']['name'], $this->_c_date));
                        foreach ($shop['orders'] as $i => $o) {
                            $pdf->addOrder($o);
                        }
                        if ($pdf->getOrdersCount() != 0) {
                            $attach = $pdf->render();
                            $emails = $this->getEmails($shop['infos']);

                            $mail = new Mandrill_Message(Mage::getStoreConfig(Ebizmarts_Mandrill_Model_System_Config::APIKEY));

                            $mail->addTo($emails);
                            $mail->addBcc(Mage::getStoreConfig('trans_email/ident_general/email'));
                            $mail->setFrom(Mage::getStoreConfig('trans_email/ident_general/email'), "L'équipe d'Au Pas De Courses");
                            $mail->setSubject("Au Pas De Courses {$pdf->getOrdersCount()} commandes le {$this->_c_date}");
                            $mail->setBodyHtml(
                                Mage::getModel('core/email_template')->loadByCode('APDC::Mail envoi commande commerçants')
                                ->getProcessedTemplate(['commercant' => $shop['infos']['name'], 'nbecommande' => $pdf->getOrdersCount()])
                            );

                            $mail->createAttachment($attach, 'application/pdf', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, "{$shop['infos']['name']} - {$this->_c_date}.pdf");

                            try {
                                $mail->send($this->_transport);
                            } catch (Exception $e) {
                                Mage::log($e->getMessage(), null, 'send_daily_order.log');
                            }
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
    }
}
