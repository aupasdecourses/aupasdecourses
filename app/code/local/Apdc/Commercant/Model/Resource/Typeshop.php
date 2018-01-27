<?php

/**
 * Class Apdc_Commercant_Model_Resource_Shop
 */
class Apdc_Commercant_Model_Resource_Typeshop extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('apdc_commercant/typeshop', 'id_type_shop');
    }

}
