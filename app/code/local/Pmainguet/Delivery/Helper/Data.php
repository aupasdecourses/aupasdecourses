<?php
/**
 * @author Pierre Mainguet
 * @copyright Copyright (c) 2016 Pierre Mainguet - mainguetpierre@gmail.com
 * @package Pmainguet_Delivery
 */
class Pmainguet_Delivery_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function check_amorderattach($orderid){
		  $field=Mage::getModel('amorderattach/order_field');
		  //Check if entity exists in database
		  $check=$field->getCollection()->addFieldToFilter('order_id', $orderid)->getFirstItem()->getId();
		  if($check==NULL){
		     $field->setData('order_id',intval($orderid));
		  }else{
		     $field->load($orderid, 'order_id');
		  }

		  return $field;
	}

	public function json_unicodechar($json){
   		return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", $json);
	}

	function liste_store_id(){
		$allStores = Mage::app()->getStores();
		$return=array();
		foreach ($allStores as $_eachStoreId => $val) 
		{
			array_push($return,Mage::app()->getStore($_eachStoreId)->getId());
		}
		return $return;
	}

	function liste_commercant_id()
	{

		//Get all store ids
		$storeIds=$this->liste_store_id();

	    $return=[];

	    //with active categories
	   	foreach($storeIds as $id){
	    	$categories = Mage::getModel('catalog/category')
			->getCollection()
	    	->addFieldToFilter('is_active', array('eq' => 1))
	        ->addAttributeToSelect('*');
	    	$rootCategoryId = Mage::app()->getStore($id)->getRootCategoryId();
	    	$storename=Mage::app()->getStore($id)->getName();
	    	$categories->addAttributeToFilter('path', array('like' => "1/".$rootCategoryId."/%"));
		    foreach($categories as $cat){
		        if($cat->getData('estcom_commercant')==true){
		            $return[$storename][$cat->getData('att_com_id')]=$cat->getName();
		        }
		    }
		}

	    asort($return);
	    return $return;
	}

	function getgooglecsv(){

		return [
			#Les Fromages des Batignolles
			63=>[
				"key"=>"1TzE4XZu0QeqKYDergYwfMwnvVHKLPXSMvc2fTxRueH0",
				"gid"=>"186615455"
			],
			"RGA17"=>[
				"key"=>"1VCEWDmHeT2AROPdhybS-t1ggYA6UUi2EzHBeUtJOIbs",
				"gid"=>"1539964024"
			],
			#Comptoir des Vignes
			107=>[
				"key"=>"1pYtUc3U59Vq8LAcPgmxjtQw2qOHBnKUyiKTp0z-5twI",
				"gid"=>"1504362974"
			],
			#La Fille du Boulanger
			275=>[
				"key"=>"1YETjLO70-eBtJWpE1k-4Nw1LqjAy_jz29w7Nof32wf4",
				"gid"=>"1218336066"
			],
			#Pascal Bassard Primeur
			272=>[
				"key"=>"1uCsvSg8x9a7DhF-9VTMw35lJ4oEryK-e2Kw6yc7x2jo",
				"gid"=>"2030347927"
			],
			#Marée 17
			72=>[
				"key"=>"1B1eWstCOVbr-ZhAcPwZVwzpdFvKzCBMXOMvd5dmdf4Q",
				"gid"=>"2131873323"
			],
			#Boucherie des Moines
			7=>[
				"key"=>"1bP2WLdxn0JtA13UG3CKC4DBbnkrTHiSpEH0J5BHmfcU",
				"gid"=>"1349743815"
			],
			#Boucherie Dandelion
			480=>[
				"key"=>"1dIal6coAGYc9x2mZ2Qasigg8iedK2Vt0GCko1WPc8G8",
				"gid"=>"1349743815"
			],
			#Terres de Café
			706=>[
				"key"=>"1wALZpT5p8FSE-f99vLquPDMHxG6sVumlPynlp5MGwyo",
				"gid"=>"2030347927"
			],
			"LRE17"=>[
				"key"=>"1E8RiNjExayaTUmZwAO1j2cfeiHwl7dnRBf2TWhWYo8w",
				"gid"=>"2030347927"
			],
			#Chez Saïd
			877=>[
				"key"=>"1jeTsLqQoG1UUfGonhF5LYxP0zcrptExewcYSJJGL4Xo",
				"gid"=>"2030347927"
			],
			
		];
	}

}