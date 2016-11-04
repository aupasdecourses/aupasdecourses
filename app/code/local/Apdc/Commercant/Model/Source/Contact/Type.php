<?php

/**
 * Class Apdc_Commercant_Model_Source_Contact_Type
 */
class Apdc_Commercant_Model_Source_Contact_Type
{
    const TYPE_CEO = 1;
    const TYPE_BILLING = 2;
    const TYPE_MANAGER = 3;
    const TYPE_EMPLOYEE = 4;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::TYPE_CEO,
                'label' => 'Gérant',
            ],
            [
                'value' => self::TYPE_BILLING,
                'label' => 'Contact facturation',
            ],
            [
                'value' => self::TYPE_MANAGER,
                'label' => 'Responsable magasin',
            ],
            [
                'value' => self::TYPE_EMPLOYEE,
                'label' => 'Employé magasin',
            ],
        ];
    }
}
