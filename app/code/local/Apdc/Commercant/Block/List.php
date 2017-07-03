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
                        'src' => (isset($filter[$id]['src'])) ? Mage::helper('apdc_catalog/category')->getImage($filter[$id]['src']) : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
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
        $shop_info=array();
        $current_cat=Mage::registry('current_category');
        $data = Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_category', array('finset' =>$current_cat->getId()))->getFirstItem()->getData();

        $shop_info["name"]=$data["name"];
        $shop_info["adresse"]=$data["street"]." ".$data["postcode"]." ".$data["city"];
        $shop_info["url_adresse"]="https://www.google.fr/maps/place/".str_replace(" ","+", $shop_info["adresse"]);
        $shop_info["phone"]=$data["phone"];
        $shop_info["website"]=$data["website"];
        $shop_info["closing_periods"]=$data["closing_periods"];;
        $shop_info["description"]=$current_cat->getDescription();
        $shop_info["delivery_days"]=Mage::helper('apdc_commercant')->formatDays($data["delivery_days"],true);
        $shop_info["image"] = Mage::helper('apdc_catalog/category')->getImageUrl($current_cat);

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
