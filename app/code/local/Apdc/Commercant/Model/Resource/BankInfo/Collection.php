<?php

/**
 * Class Apdc_Commercant_Model_Resource_BankInfo_Collection
 */
class Apdc_Commercant_Model_Resource_BankInfo_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/bankInfo');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        foreach ($this as $item) {
            $data['value'] = $item->getId();
            $data['label'] = $item->getData('account_iban') . ' - ' . $item->getData('owner_name');
            $res[] = $data;
        }
        return $res;
    }
}
