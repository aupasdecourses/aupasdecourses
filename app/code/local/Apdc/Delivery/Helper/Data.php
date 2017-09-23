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

	public function liste_commercant_id($type="attcomid")
	{
	    $return = [];

	    //with Apdc_Commercant module
	    $shops = Mage::getModel('apdc_commercant/shop')->getCollection();
	    $shops->addFieldToFilter("flag_magmi",1);
        $shops->getSelect()->join('catalog_category_entity', 'main_table.id_category=catalog_category_entity.entity_id', array('catalog_category_entity.path'));
        $shops->addFilterToMap('path', 'catalog_category_entity.path');
        foreach ($shops as $shop) {
            $rootCategoryId = explode('/', $shop->getPath())[1];
            $rootCategoryName=Mage::getSingleton('catalog/category')->load($rootCategoryId)->getName();
            if($type=="catid"){
            	$return[$rootCategoryName][$shop->getIdCategory()] = $shop->getName();
            } else {
            	$return[$rootCategoryName][$shop->getIdAttributCommercant()] = $shop->getName();
            }
        }

	    arsort($return);

	    return $return;
	}

	#Récupère les informations commerçants dans la catégorie lui correspondant en se basant sur l'id de l'attributs produits "commercant" le concernant, et non pas le numéro de catégorie - uniquement utilisé par getgooglecsv pour MAGMI
	public function info_commercant($id,$type="attcomid")
	{
	    	if($type=="catid") {
	    		$shop=Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_category', $id)->getFirstItem();   
	    	} else {
	    		$shop=Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_attribut_commercant', $id)->getFirstItem();   
	    	}
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