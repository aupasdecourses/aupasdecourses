<?php
//Remove misplaced attribute 
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->removeAttribute('customer','commercant_id');