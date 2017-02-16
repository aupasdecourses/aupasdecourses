<?php

/**
 * Class Apdc_Commercant_Model_Source_Contact_Type
 */
class Apdc_Commercant_Model_Source_Contact_Type
{
    const TYPE_CEO = 'ceo';
    const TYPE_BILLING = 'billing';
    const TYPE_MANAGER = 'manager';
    const TYPE_EMPLOYEE = 'employee';

    public function toOptionArray()
    {
        return Mage::getModel('apdc_commercant/contact_role')
            ->getCollection()
            ->toOptionArray();
    }
}
