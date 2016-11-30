<?php

class Pmainguet_Commercants_Block_Fiche extends Mage_Catalog_Block_Product{

	const MODEL='commercants_model/fiche1';

	public function gettable(){
	    $fiche = Mage::getModel(self::MODEL)->setOrder('commercant_id', 'desc');
	    return $fiche;
  	}

  	public function getListCommercant(){
  		$storeid=Mage::app()->getStore()->getId();
  		$rootId=Mage::app()->getStore($storeid)->getRootCategoryId();
  		$data=array();

	    $commercants = Mage::getModel('catalog/category')
                         ->getCollection()
                         ->addAttributeToSelect(array('name','image','adresse_commercant','url_path'))
                         ->addIsActiveFilter()
                         ->addFieldToFilter('path', array('like'=> "1/$rootId/%"))
                         ->addAttributeToFilter('level', array('eq'=>3))
                         //70 est la value_id de l'option du select, correspondant à 'Oui'                        
                         ->addAttributeToFilter('estcom_commercant',70)
                         ->load();

		foreach ($commercants as $commercant){
			$commercant=$commercant->getData();
			$sub=[
				'name'=>(isset($commercant['name'])) ? $commercant['name'] : "",
				'stripped_name'=>(isset($commercant['name'])) ? $this->stripTags($commercant['name'], null, true) : "",
				'image'=>(isset($commercant['image'])) ? $commercant['image'] : "",
				'src'=>(isset($commercant['image'])) ? Mage::getBaseUrl('media').'catalog/category/'.$commercant['image'] : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
				'adresse'=>(isset($commercant['adresse_commercant'])) ? $commercant['adresse_commercant'] : "",
				'url'=>(isset($commercant['url_path'])) ? Mage::getUrl($commercant['url_path']) : "",
			];
			$data[]=$sub;
		}

		return $data;

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
