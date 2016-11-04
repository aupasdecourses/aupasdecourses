<?php

/**
 * Class Apdc_Commercant_Model_Resource_Commercant
 */
class Apdc_Commercant_Model_Resource_Commercant extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/commercant', 'id_commercant');
    }
}
