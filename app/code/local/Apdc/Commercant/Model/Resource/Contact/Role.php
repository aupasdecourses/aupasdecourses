<?php

/**
 * Class Apdc_Commercant_Model_Resource_Contact_Role
 */
class Apdc_Commercant_Model_Resource_Contact_Role extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/contact_role', 'role_id');
    }
}