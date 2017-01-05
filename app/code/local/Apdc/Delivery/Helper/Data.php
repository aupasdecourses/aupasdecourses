<?php
/**
 * @author Pierre Mainguet
 * @copyright Copyright (c) 2016 Pierre Mainguet - mainguetpierre@gmail.com
 * @package Apdc_Delivery
 */
class Apdc_Delivery_Helper_Data extends Mage_Core_Helper_Abstract
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

	public function liste_store_id(){
		$allStores = Mage::app()->getStores();
		$return=array();
		foreach ($allStores as $_eachStoreId => $val) 
		{
			array_push($return,Mage::app()->getStore($_eachStoreId)->getId());
		}
		return $return;
	}

	#Renvoie la liste des id commerçants (les ids de l'attribut produit "commerçant", par store, avec leur nom (utilisé uniquement dans MAGMI)
	public function liste_commercant_id()
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

	#Récupère les informations commerçants dans la catégorie lui correspondant en se basant sur l'id de l'attributs produits "commercant" le concernant, et non pas le numéro de catégorie - uniquement utilisé par getgooglecsv pour MAGMI
	public function info_commercant($attcomid)
	{
	    	$shop=Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_attribut_commercant', $attcomid)->getFirstItem();   
            return $shop;
	}

	public function getgooglecsv($comid){

		$cat=$this->info_commercant($comid);
		return [
			"name"=>$cat->getName(),
			"key"=>$cat->getData('google_key'),
			"gid"=>$cat->getData('google_id')
			];
			
	}

	public function getRefunditemdata($item)
    {
        $refund_items = Mage::getModel('pmainguet_delivery/refund_items');
        $item = $refund_items->load($item->getData('item_id'), 'order_item_id');
        $response = $item->getData();
        return $response;
    }  

    public function getRefundorderdata($order)
    {
        $refund_order = Mage::getModel('pmainguet_delivery/refund_order');
        $orders = $refund_order->getCollection()->addFieldToFilter('order_id', array('in' => $order->getIncrementId()));
        $response = array();
        foreach ($orders as $o) {
                $response[$o->getData('commercant')] = $o->getData();
            }

        return $response;
    }

}