<?php

namespace Apdc\ApdcBundle\Services;

include_once '../../app/Mage.php';

class Pdforder
{

    public function __construct()
    {   
        \Mage::app();
    }   


    public function sendPdfByMerchant($merchant)
    {   
//      if (\Mage::getStoreConfig('apdcdispatch/general/mail_active')) {
            \Mage::getModel('apdcdispatch/mail')->sendPdfByMerchant($merchant);
//      } else {
//          \Mage::log('L\'envoi par mail des commercants est désactivé', null, 'export.log');
//      }
    }   
}
