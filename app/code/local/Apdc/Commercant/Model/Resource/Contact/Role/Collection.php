<?php

/**
 * Class Apdc_Commercant_Model_Resource_Contact_Role_Collection
 */
class Apdc_Commercant_Model_Resource_Contact_Role_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/contact_role');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this as $item) {
            $data['value'] = $item->getId();
            $data['label'] = $item->getLabel();
            $result[] = $data;
        }
        return $result;
    }
}
