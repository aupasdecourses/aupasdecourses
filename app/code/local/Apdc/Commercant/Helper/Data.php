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

    public function getWeekDays($short=true)
    {
        $weekDays = [];
        if ($short) {
            $labels = $this->getShortDays();
        } else {
            $labels = $this->getDays();
        }
        for ($i=1; $i <= 7; ++$i) {
            $weekDays[$i] = [
                'value' => $i,
                'label' => $labels[$i-1]
            ];
        }
        return $weekDays;
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
        $filter=array();
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
                'src' => Mage::helper('apdc_catalog/category')->getImageUrl($cat),
            ];
        }

        return $filter;
    }
	
	public function getInfoShop($shopId = null)
    {
        $shop_info = [];
        $data = [];
        $categoryShop = null;
		if($shopId == null) {
			$current_cat = Mage::registry('current_category');
			$categoriesParent = $current_cat->getParentCategories();
			foreach($categoriesParent as $categoryParent) {
				if($categoryParent->getLevel() == 3) {
					$categoryShop = $categoryParent;
					break;
				}
			}
            if ($categoryShop) {
                $categoryShop = Mage::getModel('catalog/category')->load($categoryShop->getId());
                $data = Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_category', array('finset' =>$categoryShop->getId()))->getFirstItem()->getData();
            }
		}
		else {
            $collection = Mage::getSingleton('apdc_commercant/shop')
                ->getCollection()
                ->addFieldToFilter('id_attribut_commercant', ['finset' => $shopId]);
            if ($collection->count() > 0) {
                $data = $collection->getFirstItem()->getData();
                if (isset($data['id_category'])) {
			        $categoryShop = Mage::getModel('catalog/category')->load($data['id_category'][0]);
                }
            }
		}
		
        if (!empty($data) && $categoryShop && $categoryShop->getId()) {
            $shop_info["name"]=$data["name"];
            $shop_info["adresse"]=$data["street"]." ".$data["postcode"]." ".$data["city"];
            $shop_info["url_adresse"]="https://www.google.fr/maps/place/".str_replace(" ","+", $shop_info["adresse"]);
            $shop_info["phone"]=$data["phone"];
            $shop_info["website"]=$data["website"];
            $shop_info["closing_periods"]=$data["closing_periods"];
            $shop_info["description"]=$categoryShop->getDescription();
            $shop_info["delivery_days"] = $data["delivery_days"];
            $shop_info["image"] = Mage::helper('apdc_catalog/category')->getImageUrl($categoryShop);
            $shop_info["thumbnail_image"] = Mage::helper('apdc_catalog/category')->getThumbnailImageUrl($categoryShop);
            $shop_info["url"] = $categoryShop->getUrl();
            
            $html = "";
            $days = $this->getShortDays();//["Lun","Mar","Mer","Jeu","Ven","Sam","Dim"];
            foreach($data["timetable"] as $day=>$hours){
                $hours=($hours=="")?"Fermé":$hours;
                $hoursExplode = explode('-', $hours);
                if(count($hoursExplode) > 3) {
                    $hoursExplode1 = $hoursExplode[0].'-'.$hoursExplode[1];
                    $hoursExplode2 = $hoursExplode[2].'-'.$hoursExplode[3];
                    $hours = $hoursExplode1.' / '.$hoursExplode2;
                }
                $html.='<strong>'.$days[$day]."</strong> : ".$hours."</br>";
            }
            $shop_info["timetable"]=$html;
        }

        return $shop_info;
    }

    public function getRandomShopImage($filter="all")
    {
        $shop_info = [];
        $collection = Mage::getModel('apdc_commercant/shop')->getCollection()            
            ->addFieldtoFilter('enabled',1);

        if($filter<>"all"){
            $collection->addFieldToFilter('type', $filter);
        }

        $collection->getSelect()->order('rand()');
        $collection->getSelect()->limit(1);

        if($collection->getFirstItem()->getCategoryImage()<>NULL){
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$collection->getFirstItem()->getCategoryImage();
        }else{
            $i = mt_rand(1, 17); // generate random number size of the array
            $url = "dist/images/header/".$i.".jpg"; // set variable equal to which random filename was chosen
            return Mage::getDesign()->getSkinUrl()."../default/".$url;
        }
    }

}
