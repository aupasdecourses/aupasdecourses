<?php

class Apdc_Commercant_Block_List extends Mage_Catalog_Block_Product
{
    public function getListShops()
    {
        //Fonction peut être trop complexe: suffit de filter les catégories de la boutique à afficher, pas besoin de vérifier si le commerçant est activé ou non.
        $storeid = Mage::app()->getStore()->getId();
        $rootId = Mage::app()->getStore($storeid)->getRootCategoryId();
        $filter = array();
        $row1 = array();
		$row2 = array();
		$i = 1;

        $filter = Mage::helper('apdc_commercant')->getCategoriesInfos($rootId);

        $shops = Mage::getModel('apdc_commercant/shop')->getCollection()
            ->addFieldToFilter('stores', array('finset' =>$storeid))
            ->addFieldtoFilter('enabled',1);
		$nbShops = count($shops);
		
        foreach ($shops as $shop) {
            foreach($shop['id_category'] as $id){
                if(array_key_exists($id,$filter)){
					if($shop['id_category']) {
						$category = Mage::getModel('catalog/category')->load($shop['id_category'][0]);
						if($category && $category->getParentCategory()) {
							$color = $category->getParentCategory()->getData('menu_bg_color');
						}
					}
                    $sub = [
                        'name' => (isset($shop['name'])) ? $shop['name'] : '',
                        'src' => (isset($filter[$id]['src'])) ? Mage::getBaseUrl('media').'catalog/category/'.$filter[$id]['src'] : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
                        'adresse' => (isset($shop['street'])) ? $shop['street'].' '.$shop['postcode'].' '.$shop['city'] : '',
                        'url' => (isset($filter[$id]['url_path'])) ? Mage::getUrl($filter[$id]['url_path']) : '',
						'color' => $color
                    ];
					if($i == 1) {
						$row1[] = $sub;
					}
					else {
						$row2[] = $sub;
					}
					if($i == 2) {
						$i = 1;
					}
					else {
						$i ++;
					}
                }
            }
        }
		return array('row1' => $row1, 'row2' => $row2, 'count' => count($shops));
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
        $shop_info["delivery_days"]=Mage::helper('apdc_commercant')->formatDays($data["delivery_days"],true);
        $shop_info["image"]=$categoryShop->getImageURL();

        $html="";
        $days=["Lun","Mar","Mer","Jeu","Ven","Sam","Dim"];
        foreach($data["timetable"] as $day=>$hours){
            $hours=($hours=="")?"Fermé":$hours;
            $html.=$days[$day].": ".$hours."</br>";
        }
        $shop_info["timetable"]=$html;

        return $shop_info;
    }
}
