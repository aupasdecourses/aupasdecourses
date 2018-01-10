<?php

/* To start we need to include abscract.php, which is located 
 * in /shell/abstract.php which contains Magento's Mage_Shell_Abstract 
 * class. 
 *
 * Since this .php is in /shell/Namespace/ we
 * need to include ../ in our require statement which means the
 * file we are including is up one directory from the current file location.
 */
require_once '../abstract.php';

class Pmainguet_ShopUrl extends Mage_Shell_Abstract
{
	public function getcatlist(){
		$result=array();
		$_shops = Mage::getModel('apdc_commercant/shop')->getCollection()
                     ->addFieldToSelect('id_shop')
                     ->addFieldToSelect('name')
                     ->addFieldToSelect('id_category')
                     ->addFieldToSelect('stores')
                     ->addFieldToFilter('enabled',1);


        $stores=Mage::helper("apdc_commercant")->getStoresArray();

        foreach($_shops as $_shop){
        	$temp=array();
        	foreach($_shop->getIdCategory() as $id => $cat){
        		$info=Mage::getModel("catalog/category")->load($cat);
        		if($info->getIsActive()){
	        		$url_path=$info->getUrlPath();
	        		$path=$info->getPath();
	        		$rootcat = explode('/', $path)[1];
	                $temp[] = Mage::app()->getStore($stores[$rootcat]["store_id"])->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$url_path;
	            }

        	}
			$result[$_shop->getName()]=$temp;
		}

		var_dump(json_encode($result));
	}

	// Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['geturllist','getcatlist'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        if (in_array($step, $steps)) {
            $this->$step();
        } else {
            echo "STEP MUST BE ONE OF THESE:\n";
            foreach ($steps as $s) {
                echo $s.",\n";
            }
        }
    }
}

// Create a new instance of our class and run it.
$shell = new Pmainguet_ShopUrl();
$shell->run();