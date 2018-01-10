<?php 

class Apdc_Commercant_IndexController extends Mage_Core_Controller_Front_Action
{
	public function listshopsAction(){
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

		echo json_encode($result);
	}
}