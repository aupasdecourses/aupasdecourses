<?php
class Apdc_Referentiel_Model_Referentiel extends Mage_Core_Model_Abstract
{
	 public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_referentiel/referentiel');
    }

}