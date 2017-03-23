<?php

class Apdc_Notation_Model_Resource_Notation_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('apdc_notation/notation');
    }
}