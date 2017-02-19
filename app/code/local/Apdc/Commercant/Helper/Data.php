<?php

/**
 * Class Apdc_Commercant_Helper_Data
 */
class Apdc_Commercant_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getDays()
    {
        return ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    }

    public function formatDays($days, $string=false){
    	$labeldays=$this->getDays();
        $rsl=[];
        foreach($days as $day){
            $rsl[]=$labeldays[$day-1];
        }

        if ($string){
           	$r=implode(", ", $rsl);
        }else {
        	$return=$rsl;
        }
        return $r;
    }

    public function getStoresArray($type="rootcatid"){
        $S = [];
        $app = Mage::app();
        $stores = $app->getStores();
        foreach ($stores as $id => $idc) {
            if($type=="storeid"){
                $S[$id]['store_id'] = $id;
                $S[$id]['id'] = $app->getStore($id)->getRootCategoryId();
                $S[$id]['name'] = $app->getStore($id)->getName();
            } else {
                $S[$app->getStore($id)->getRootCategoryId()]['store_id'] = $id;
                $S[$app->getStore($id)->getRootCategoryId()]['id'] = $app->getStore($id)->getRootCategoryId();
                $S[$app->getStore($id)->getRootCategoryId()]['name'] = $app->getStore($id)->getName();
            }
        }
        return $S;
    }

    public function getCategoriesArray(){
        $categories = Mage::getModel('catalog/category')->getCollection();
        $cat_array=[];
        foreach($categories as $cat){
            $cat_array[$cat->getId()]=$cat->getPath();
        }
        return $cat_array;
    }

    public function getCategoriesInfos($rootId){
        $categories = Mage::getModel('catalog/category')
                         ->getCollection()
                         ->addAttributeToSelect(array('image', 'url_path'))
                         ->addIsActiveFilter()
                         ->addFieldToFilter('path', array('like' => "1/$rootId/%"))
                         ->addAttributeToFilter('level', array('eq' => 3))
                         //70 est la value_id de l'option du select, correspondant à 'Oui'                        
                         ->addAttributeToFilter('estcom_commercant', 70)
                         ->load();

        foreach ($categories as $cat) {
            $filter[$cat->getId()] = [
                'store_id' => $cat->getStoreId(),
                'url_path' => $cat->getUrlPath(),
                'src' => $cat->getImage(),
            ];
        }

        return $filter;
    }

}
