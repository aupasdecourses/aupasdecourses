<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$tablename=$installer->getTable('apdc_referentiel/categoriesposition');

//Extract data from CSV file
$csv = new Varien_File_Csv;
$data = $csv->getData(dirname(__FILE__) .DS.'categoriesposition/categoriesposition.csv');

$resultNum = $installer->getConnection()->insertArray(
    $tablename,
    array('name','parent','g_parent','g_g_parent','position'),    //column names
    $data
);

Mage::log(__FILE__." added $resultNum records to $tablename",Zend_Log::INFO,"setup.log",true);

$installer->endSetup();