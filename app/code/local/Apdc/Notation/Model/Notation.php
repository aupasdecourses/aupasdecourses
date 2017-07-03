<?php
class Apdc_Notation_Model_Notation extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('apdc_notation/notation');
     }
}