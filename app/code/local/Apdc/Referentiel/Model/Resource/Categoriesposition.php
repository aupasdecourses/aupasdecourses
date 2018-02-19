<?php 

class Apdc_Referentiel_Model_Resource_Categoriesposition extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        $this->_init('apdc_referentiel/categoriesposition', 'entity_id');
    }
}
