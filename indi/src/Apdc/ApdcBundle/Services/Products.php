<?php

namespace Apdc\ApdcBundle\Services;

trait Products
{

	public $_attributeArray ;

	public function getAttributesArray($attrcode){
		$attribute = \Mage::getModel('eav/config')->getAttribute('catalog_product', $attrcode);
			$attributeArray=array();
			foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
			  $attributeArray[$option['label']] = $option['value'];
			}
		return $attributeArray;
	}

	/*
	* 	@param int $size
	*   Taille de la page de la collection d'objet (limite)
	* 	@param int $page
	*   Indique le numéro de la page de la collection que l'on souhaite afficher (offset)
	* 	@param string $order_param
	*   Paramètre utilisé pour le tri. "name" par défaut
	* 	@param string $order
	*   Sens du tri. Deux valeurs possibles:  ASC ou DESC
	* 	@param string $commercant
	*   Correspond au nom de l'attribut commercant_value (ce sera mieux d'utiliser l'id commercant utilisé dans les tables)
	* 	@param string $name
	*   Nom du produit 
	*	@return Mage_Collection
	*/
	public function getProductsList($size=20,$page=1,$order_param="name",$order='ASC',$commercant=NULL,$name=NULL){
		$collection=\Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
		$collection ->addAttributeToSort($order_param, $order);

		if($commercant<>NULL){


			// $attributeCode = 'commercant';
			// $alias = 'commercant'.'_table';
			// $attribute = \Mage::getSingleton('eav/config')->getAttribute(\Mage_Catalog_Model_Product::ENTITY, 'commercant');
			// $collection->getSelect()
			// 	->join(
			// 		array($alias => $attribute->getBackendTable()),
			// 		"e.entity_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
			// 		array($attributeCode => 'value')
			// 	)
			// ->join(array('shop_id'=> 'apdc_shop'),$alias.'.value = shop_id.id_attribut_commercant', array('name_commercant'=>"shop_id.name"));
			// $collection->addFilterToMap('name_commercant', 'shop_id.name');
			//$collection->addFieldToFilter('name_commercant', "Boucherie des Moines");

			$attributeArray=$this->_attributeArray;

			$collection->addFieldToFilter('commercant', $attributeArray[$commercant]);
		}

		if($name<>NULL){
			$collection->addFieldToFilter('name', $name);
		}

		$collection ->setPageSize($size)
            		->setCurPage($page);

        return $collection;
	}
}