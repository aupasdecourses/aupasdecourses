<?php

class Pmainguet_Commercants_Block_Fiche extends Mage_Catalog_Block_Product{

	const MODEL='commercants_model/fiche1';
	//A modifier pour récupérer les catégories de Magento
	public $category=['Mon Boucher'=>'Bouchers','Mon Epicier'=>'Epiciers','Mon Boulanger'=>'Boulangers','Mon Primeur'=>'Primeurs'];

	public function gettable(){
	    $fiche = Mage::getModel(self::MODEL)->setOrder('commercant_id', 'desc');
	    return $fiche;
  	}

  	//Return list of all Commercant categories, filtered by store
  	public function getListCommercant(){

  		$storeid=Mage::app()->getStore()->getId();
  		$rootId=Mage::app()->getStore($storeid)->getRootCategoryId();

	    $categories = Mage::getModel('catalog/category')
                         ->getCollection()
                         ->addAttributeToSelect('*')
                         ->addIsActiveFilter()
                         ->addFieldToFilter('path', array('like'=> "1/$rootId/%"))
                         ->addAttributeToFilter('level', array('eq'=>3))
                         //70 est la value_id de l'option du select, correspondant à 'Oui'                        
                         ->addAttributeToFilter('estcom_commercant',70)
                         ->load();
		return $categories;
	}


	public function getListPerCat($category){
	   $categories = Mage::getModel('catalog/category')->load($category)
	   					->getChildrenCategories()
                         ->addAttributeToSelect('*')
                         ->addIsActiveFilter();
		return $categories;
	}  	

  	//En duplicata d'une fonction du controller => voir si possible d'utiliser Helper pour la partager entre les deux
  	public function strtoupperFr($string) {
		$string = strtoupper($string);
		$string = str_replace(
		array('é', 'è', 'ê', 'ë', 'à', 'â', 'î', 'ï', 'ô', 'ù', 'û'),
		array('E', 'E', 'E', 'E', 'A', 'A', 'I', 'I', 'O', 'U', 'U'),
		$string
		);
		return $string;
	}

	public function strtolowerFr($string) {
		$string = strtolower($string);
		$string = str_replace(
		array('é', 'è', 'ê', 'ë', 'à', 'â', 'î', 'ï', 'ô', 'ù', 'û'),
		array('e', 'e', 'e', 'e', 'a', 'a', 'i', 'i', 'o', 'u', 'u'),
		$string
		);
		return $string;
	}

	public function lienCatCommercant($fiche){
		$url=Mage::GetBaseUrl();
		$cat=str_replace(' ', '-', self::strtolowerFr($fiche->getCategory()));
		$subcat=str_replace(' ', '-', self::strtolowerFr($fiche->getAttributeLabel()));
		echo $url.$cat.'/'.$subcat.'.html';
	}

}

?>