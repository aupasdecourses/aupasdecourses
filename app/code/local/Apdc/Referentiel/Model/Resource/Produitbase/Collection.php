<?php

class Apdc_Referentiel_Model_Resource_Produitbase_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_referentiel/produit_base');
    }
}
