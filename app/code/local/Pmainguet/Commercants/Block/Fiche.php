<?php

class Pmainguet_Commercants_Block_Fiche extends Mage_Catalog_Block_Product{

	const MODEL='commercants_model/fiche1';

	public function gettable(){
	    $fiche = Mage::getModel(self::MODEL)->setOrder('commercant_id', 'desc');
	    return $fiche;
  	}

  	public function getListCommercant(){

  		$commercant=array();
		$helper = Mage::helper('catalog/category');
	 	$categoriesCollection = $helper->getStoreCategories('name', true, false)->addAttributeToFilter('estcom_commercant', 70)->addAttributeToSelect(array('url_path','src'));
	 	foreach($categoriesCollection as $cat){
	 		$result[$cat->getId()]=[
		 		'url_path' => $cat->getUrlPath(),
		 		'src' => $cat->getThumbnail(),
	 		];
	 	}

	 	$shops=Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_category',array('in' => array_keys($result)));

	 	$data=array();

		foreach ($shops as $shop){
			$shop=$shop->getData();
			$sub=[
				'name'=>(isset($shop['name'])) ? $shop['name'] : "",
				'src'=>(isset($result[$shop->getIdCategory()]['src'])) ? Mage::getBaseUrl('media').'catalog/category/'.$result[$shop->getIdCategory()]['src'] : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
				'adresse'=>(isset($shop['street'])) ? $shop['street'].$shop['postcode'].$shop['city'] : "",
				'url'=>(isset($result[$shop->getIdCategory()]['url_path'])) ? Mage::getUrl($result[$shop->getIdCategory()]['url_path']) : "",
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