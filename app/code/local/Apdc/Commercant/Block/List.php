<?php
class Apdc_Commercant_Block_List extends Mage_Catalog_Block_Product
{
    public function getListShops()
    {
        //Fonction peut être trop complexe: suffit de filter les catégories de la boutique à afficher, pas besoin de vérifier si le commerçant est activé ou non.
        $storeid = Mage::app()->getStore()->getId();
        $rootId = Mage::app()->getStore($storeid)->getRootCategoryId();
        $filter = array();
        $data = array();
        $filter=Mage::helper('apdc_commercant')->getCategoriesInfos($rootId);
        $shops = Mage::getModel('apdc_commercant/shop')->getCollection()
            ->addFieldToFilter('stores', array('finset' =>$storeid))
            ->addFieldtoFilter('enabled',1);
        $code_count=array();
        foreach ($shops as $shop) {
            $shop = $shop->getData();
            foreach($shop['id_category'] as $id){
                if(array_key_exists($id,$filter)){
                    $shop["postcode"];
                    $sub = [
                        'name' => (isset($shop['name'])) ? $shop['name'] : '',
                        'src' => (isset($filter[$id]['src'])) ? Mage::getBaseUrl('media').'catalog/category/'.$filter[$id]['src'] : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
                        'postcode' => $shop['postcode'],
                        'adresse' => (isset($shop['street'])) ? $shop['street'].' '.$shop['postcode'].' '.$shop['city'] : '',
                        'url' => (isset($filter[$id]['url_path'])) ? Mage::getUrl($filter[$id]['url_path']) : '',
                    ];
                    $data[$shop['postcode']][] = $sub;
                    if(isset($code_count[$shop['postcode']])){
                        $code_count[$shop['postcode']]+=1;
                    }else{
                        $code_count[$shop['postcode']]=1;
                    }
                }
            }
        }
        arsort($code_count);
        
        $result=array();
        foreach($code_count as $zip => $freq){
            $result[$zip]=$data[$zip];
        }
        return $result;
    }

    public function getInfoShop()
    {
        $shop_info = array();
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

        $shop_info["name"]=$data["name"];
        $shop_info["adresse"]=$data["street"]." ".$data["postcode"]." ".$data["city"];
        $shop_info["url_adresse"]="https://www.google.fr/maps/place/".str_replace(" ","+", $shop_info["adresse"]);
        $shop_info["phone"]=$data["phone"];
        $shop_info["website"]=$data["website"];
        $shop_info["closing_periods"]=$data["closing_periods"];
        $shop_info["description"]=$categoryShop->getDescription();
        //$shop_info["delivery_days"]=Mage::helper('apdc_commercant')->formatDays($data["delivery_days"],true);
        $shop_info["image"]=$categoryShop->getImageURL();
		$shop_info["thumbnail_image"] = Mage::getBaseUrl('media').'catalog/category/'.$categoryShop->getThumbnail();
		
        $html = "";
		$delivery_daysAll = array();
        $days = ["Lun","Mar","Mer","Jeu","Ven","Sam","Dim"];
		$delivery_days = Mage::helper('apdc_commercant')->formatDays($data["delivery_days"], false, true);
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
