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
	
	public function getShortDays()
    {
        return ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    }

    public function formatDays($days, $string = false, $short = false){
    	$labeldays = $this->getDays();
		if($short == true) {
			$labeldays = $this->getShortDays();
		}
        $rsl=[];
        foreach($days as $day){
            $rsl[]=$labeldays[$day-1];
        }
 
        if ($string){
           	$r = implode(", ", $rsl);
        }else {
        	$r = $rsl;
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
	
	public function getInfoShop($shopId = null)
    {
        $shop_info = array();
		if($shopId == null) {
			$current_cat = Mage::registry('current_category');
			$categoriesParent = $current_cat->getParentCategories();
			foreach($categoriesParent as $categoryParent) {
				if($categoryParent->getLevel() == 3) {
					$categoryShop = $categoryParent;
					break;
				}
			}
			$categoryShop = Mage::getModel('catalog/category')->load($categoryShop->getId());
			$data = Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_category', array('finset' =>$categoryShop->getId()))->getFirstItem()->getData();
		}
		else {
			$data = Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_attribut_commercant', array('finset' => $shopId))->getFirstItem()->getData();
			$categoryShop = Mage::getModel('catalog/category')->load($data['id_category'][0]);
		}
		
        $shop_info["name"]=$data["name"];
        $shop_info["adresse"]=$data["street"]." ".$data["postcode"]." ".$data["city"];
        $shop_info["url_adresse"]="https://www.google.fr/maps/place/".str_replace(" ","+", $shop_info["adresse"]);
        $shop_info["phone"]=$data["phone"];
        $shop_info["website"]=$data["website"];
        $shop_info["closing_periods"]=$data["closing_periods"];
        $shop_info["description"]=$categoryShop->getDescription();
        $shop_info["delivery_days"]=$this->formatDays($data["delivery_days"],true);
        $shop_info["image"]=$categoryShop->getImageURL();
		$shop_info["thumbnail_image"] = Mage::getBaseUrl('media').'catalog/category/'.$categoryShop->getThumbnail();
		
        $html = "";
		$delivery_daysAll = array();
        $days = $this->getShortDays();//["Lun","Mar","Mer","Jeu","Ven","Sam","Dim"];
		$delivery_days = $this->formatDays($data["delivery_days"], false, true);
		foreach($days as $day) {
			if(in_array($day,$delivery_days)) {
				$delivery_daysAll[$day] = 0;
			}
			else {
				$delivery_daysAll[$day] = 1;
			}
		}
		$shop_info["delivery_days"] = $delivery_daysAll;
		
        foreach($data["timetable"] as $day=>$hours){
            $hours=($hours=="")?"Fermé":$hours;
			$hoursExplode = explode('-', $hours);
			if(count($hoursExplode) > 2) {
				$hoursExplode1 = $hoursExplode[0].'-'.$hoursExplode[1];
				$hoursExplode2 = $hoursExplode[2].'-'.$hoursExplode[3];
				$hours = $hoursExplode1.' / '.$hoursExplode2;
			}
            $html.='<strong>'.$days[$day]."</strong> : ".$hours."</br>";
        }
        $shop_info["timetable"]=$html;

        return $shop_info;
    }

}
