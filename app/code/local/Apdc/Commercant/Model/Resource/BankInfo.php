<?php

/**
 * Class Apdc_Commercant_Model_Resource_BankInfo
 */
class Apdc_Commercant_Model_Resource_BankInfo extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/bankInfo', 'id_bank_information');
    }
}
