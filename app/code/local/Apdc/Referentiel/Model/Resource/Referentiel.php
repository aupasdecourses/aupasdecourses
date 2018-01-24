<?php 

class Apdc_Referentiel_Model_Resource_Referentiel extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        $this->_init('apdc_referentiel/referentiel', 'entity_id');
    }
}
