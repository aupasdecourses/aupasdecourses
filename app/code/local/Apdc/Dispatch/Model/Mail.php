<?php

class Apdc_Dispatch_Model_Mail extends Mage_Core_Model_Abstract{

    protected $_amasty_mw_date;
    protected $_c_date;
    protected $_status_no_display;
    protected $_refund_id_limit;

    public function __construct(){
        
        parent::__construct();

        $this->_amasty_mw_date= date('Y-m-d', mktime(0, 0, 0, 1, 20, 2016));
        $this->_c_date=date('Y-m-d');
        $this->_status_no_display=array('complete', 'pending_payment', 'payment_review', 'holded', 'closed', 'canceled');
        $this->_refund_id_limit=2016000249;

    }    

    public function save($filename)
    {
        if (!$this->_finalized) {
            $this->_finalizePdf();
        }
        $this->_pdf->save($filename);
    }

    public function send($smtp_host = 'smtp.mandrillapp.com', $smtp_config = ['auth' => 'login', 'username' => 'pierre@aupasdecourses.com', 'password' => 'suQMuVOzZHE5kc-wmH3oUA', 'port' => 2525, 'return-path' => 'contact@aupasdecourses.com'])
    {
        if (!$this->_finalized) {
            $this->_finalizePdf();
        }
        $pdf = $this->_pdf->render();
        // $tr = new Zend_Mail_Transport_Smtp($smtp_host, $smtp_config);
        // $mail = new Zend_Mail('utf-8');
        // $tmp = [];
        // // foreach ($this->_mails as $m) {
        // //     if ($m != '' && $m != '') {
        // //         $tmp[] = $m;
        // //     }
        // // }
        // //test $tmp="pierre@aupasdecourses.com";
        // $mail->addTo($tmp);
        // $mail->addCc(Mage::getStoreConfig('trans_email/ident_general/email'));
        // $mail->setFrom(Mage::getStoreConfig('trans_email/ident_general/email'), "L'équipe d'Au Pas De Courses");
        // $mail->setSubject("Au Pas De Courses {$this->_orders_count} commandes le {$this->_date}");
        // $mail->setBodyHtml(
        //     Mage::getModel('core/email_template')->loadByCode('APDC::Mail envoi commande commerçants')
        //     ->getProcessedTemplate(['commercant' => $this->_name, 'nbecommande' => $this->_orders_count])
        // );
        // $attach = new Zend_Mime_Part($pdf);
        // $attach->type = 'application/pdf';
        // $attach->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        // $attach->encoding = Zend_Mime::ENCODING_BASE64;
        // $attach->filename = "{$this->_name}_{$this->_date}.pdf";
        // $mail->addAttachment($attach);
        // try {
        //     $mail->send($tr);
        // } catch (Exception $e) {
        //     Mage::log($e, null, 'send_daily_order.log');
        // }
    }

    public function processRequestMail($params){

        foreach($params['orders'] as $store_id => $store){
            foreach($store as $id => $shop) {

                if($shop['orders']<>array()){

                    if(isset($shop['infos'])){
                        $pdf = Mage::getModel('apdcdispatch/mail_pdf', array($shop['infos']['name'], $this->_c_date));
                        foreach($shop['orders'] as $i => $o) {
                            $pdf->addOrder($o);
                        }
                        if($pdf->getOrdersCount()!=0){
                            var_dump("hello");
                            //$pdf->save(Mage::getBaseDir()."/var/tmp/hello.pdf");
                            //       $commercant_pdf->send();
                        }
                    }else{
                        $prods="";
                        foreach($o['products'] as $prod){
                            $prods.="commercant. ".$prod['commercant']. ', item_id: '.$prod['item_id'].PHP_EOL;
                        }
                        $error[]=[
                            'increment_id' => $i,
                            'products'=>$prods,

                        ];                      
                    }
                }
            }
        }

    }
}