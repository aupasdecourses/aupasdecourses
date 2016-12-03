<?php

class Apdc_Commercant_Block_List extends Mage_Catalog_Block_Product
{
    public function getListShops()
    {
        $storeid = Mage::app()->getStore()->getId();
        $rootId = Mage::app()->getStore($storeid)->getRootCategoryId();
        $filter = array();
        $data = array();

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
                'url_path' => $cat->getUrlPath(),
                'src' => $cat->getImage(),
            ];
        }

        $shops = Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_category', array('in' => array_keys($filter)));

        foreach ($shops as $shop) {
            $shop = $shop->getData();
            $sub = [
                'name' => (isset($shop['name'])) ? $shop['name'] : '',
                'src' => (isset($filter[$shop['id_category']]['src'])) ? Mage::getBaseUrl('media').'catalog/category/'.$filter[$shop['id_category']]['src'] : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
                'adresse' => (isset($shop['street'])) ? $shop['street'].' '.$shop['postcode'].' '.$shop['city'] : '',
                'url' => (isset($filter[$shop['id_category']]['url_path'])) ? Mage::getUrl($filter[$shop['id_category']]['url_path']) : '',
            ];
            $data[] = $sub;
        }

        return $data;
    }

    public function getInfoShop()
    {
        $shop_info=array();
        $current_cat=Mage::registry('current_category');
        $data = Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_category', $current_cat->getId())->getFirstItem()->getData();
     
        $shop_info["name"]=$data["name"];
        $shop_info["adresse"]=$data["street"]." ".$data["postcode"]." ".$data["city"];
        $shop_info["url_adresse"]="https://www.google.fr/maps/place/".str_replace(" ","+", $shop_info["adresse"]);
        $shop_info["phone"]=$data["phone"];
        $shop_info["website"]=$data["website"];;
        $shop_info["closing_periods"]=$data["closing_periods"];;
        $shop_info["description"]=$current_cat->getDescription();
        $shop_info["delivery_days"]="Du Mardi au Vendredi";
        $shop_info["image"]=$current_cat->getImageURL();

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
