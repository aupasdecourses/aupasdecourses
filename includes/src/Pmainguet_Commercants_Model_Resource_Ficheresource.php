 <?php
 
 class Pmainguet_Commercants_Model_Resource_Ficheresource extends Mage_Core_Model_Resource_Db_Abstract
 {
     protected function _construct()
    {
        $this->_init('commercants_model/ficheentity', 'commercant_id');
    }
 }
?>