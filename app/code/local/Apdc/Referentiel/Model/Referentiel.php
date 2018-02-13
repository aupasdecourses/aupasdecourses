<?php
class Apdc_Referentiel_Model_Referentiel extends Mage_Core_Model_Abstract
{
	 public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_referentiel/referentiel');
    }

    public function loadByField($field, $value, $additionalField = '*')
    {
        $collection = $this->getResourceCollection()
            ->addFieldToSelect($additionalField)
            ->addFieldToFilter($field, $value);
        foreach ($collection as $object) {
            return $object;
        }
        return false;
    }

}