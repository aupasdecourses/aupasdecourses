<?php 

class Apdc_Commercant_IndexController extends Mage_Core_Controller_Front_Action
{
	protected function _list($shop=true){
		$result=array();
		$stores=Mage::helper("apdc_commercant")->getStoresArray();		
		if($shop){
			$_shops = Mage::getModel('apdc_commercant/shop')->getCollection()
	                     ->addFieldToSelect('id_shop')
	                     ->addFieldToSelect('name')
	                     ->addFieldToSelect('id_category')
	                     ->addFieldToSelect('stores')
	                     ->addFieldToFilter('enabled',1);

	        foreach($_shops as $_shop){
	        	$temp=array();
	        	foreach($_shop->getCategoryIds() as $id => $cat){
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
		}else{
			$categories = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToFilter("level",3)
				->addAttributeToSelect('is_active',true);
			$temp=array();
			foreach($categories as $cat){
				$info=Mage::getModel("catalog/category")->load($cat->getEntityId());
				$url_path=$info->getUrlPath();
		       	$path=$cat->getPath();
				$rootcat = explode('/', $path)[1];
				if(isset($stores[$rootcat])){
					$temp[] = Mage::app()->getStore($stores[$rootcat]["store_id"])->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$url_path;
				}
			}

			$result[0]=$temp;
		}
		return $result;
	}

	public function jsonlistAction(){
		$result=$this->_list();
		echo json_encode($result);
	}

	public function rawlistshopAction(){
		$result=$this->_list();
		foreach($result as $urls){
			foreach($urls as $url){
				echo $url."</br>";
			}
		}
	}

	public function rawlistcatAction(){
		$result=$this->_list(false);
		foreach($result as $urls){
			foreach($urls as $url){
				echo $url."</br>";
			}
		}
	}
}