<?php

class Apdc_Commercant_Model_Resource_Typeshop_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/typeshop');
    }

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

    /**
     * Retreive array of attributes
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray($arrAttributes = array())
    {
        $arr = array();
        foreach ($this->_items as $k => $item) {
            $arr[$k] = $item->toArray($arrAttributes);
        }
        return $arr;
    }

}
