<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$tablename=$installer->getTable('apdc_referentiel/categoriesbase');

//Extract data from CSV file
$csv = new Varien_File_Csv;
$data = $csv->getData(dirname(__FILE__) .DS.'categoriesbase/categoriesbase.csv');

$resultNum = $installer->getConnection()->insertArray(
    $tablename,
    array('name','url'),    //column names
    $data
);

Mage::log(__FILE__." added $resultNum records to $tablename",Zend_Log::INFO,"setup.log",true);

$origDir = dirname(__FILE__) .DS . 'categoriesbase/images/base_thumbnail';

function copyFiles($dir){
	$root='base_thumbnail';
	$mediaDir = 'catalog/category/'.$root;
    $destDir = Mage::getBaseDir('media') . DS . $mediaDir;
    $ffs = scandir($dir);
    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);
    if (count($ffs) < 1)
        return;
    foreach($ffs as $ff){
        if(is_dir($dir.DS.$ff)){
        	copyFiles($dir.DS.$ff);
        }else{
	        $folder=array_slice(explode("/",$dir),-1)[0];
			if($folder<>$root){
				$copydir=$destDir.DS.$folder;
			}else{
				$copydir=$destDir;
			}
			if (!file_exists($copydir)) {
		        mkdir($copydir, 0777, true);
		    }
	        copy($dir . DS . $ff, $copydir.DS.$ff);
	    }
    }
}

copyFiles($origDir);


$installer->endSetup();