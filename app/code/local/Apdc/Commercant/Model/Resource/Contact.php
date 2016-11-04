<?php

/**
 * Class Apdc_Commercant_Model_Resource_Contact
 */
class Apdc_Commercant_Model_Resource_Contact extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/contact', 'id_contact');
    }
}
