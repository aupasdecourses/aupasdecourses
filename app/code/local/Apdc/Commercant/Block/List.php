<?php

class Apdc_Commercant_Block_List extends Mage_Catalog_Block_Product
{
    public function getListShops()
    {
        $storeid = Mage::app()->getStore()->getId();
        $rootId = Mage::app()->getStore($storeid)->getRootCategoryId();
        $filter = array();
        $data = array();

        $filter=Mage::helper('apdc_commercant')->getCategoriesInfos($rootId);

        $shops = Mage::getModel('apdc_commercant/shop')->getCollection()
            ->addFieldToFilter('stores', array('finset' =>$storeid))
            ->addFieldtoFilter('enabled',1);

        foreach ($shops as $shop) {
            $shop = $shop->getData();
            foreach($shop['id_category'] as $id){
                if(array_key_exists($id,$filter)){
                    $sub = [
                        'name' => (isset($shop['name'])) ? $shop['name'] : '',
                        'src' => (isset($filter[$id]['src'])) ? Mage::getBaseUrl('media').'catalog/category/'.$filter[$id]['src'] : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
                        'adresse' => (isset($shop['street'])) ? $shop['street'].' '.$shop['postcode'].' '.$shop['city'] : '',
                        'url' => (isset($filter[$id]['url_path'])) ? Mage::getUrl($filter[$id]['url_path']) : '',
                    ];
                    $data[] = $sub;
                }
            }
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
        $shop_info["website"]=$data["website"];
        $shop_info["closing_periods"]=$data["closing_periods"];;
        $shop_info["description"]=$current_cat->getDescription();
        $shop_info["delivery_days"]=Mage::helper('apdc_commercant')->formatDays($data["delivery_days"],true);
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
