<?php

class Apdc_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{    
    //get saved Attribute Value for Amasty Order Attribute module
    function getSavedAttrValue($attributecode){
        //Get value if customer has already set it up
        if (Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $orderCollection = Mage::getModel('sales/order')->getCollection();
            $orderCollection->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getId());
            // 1.3 compatibility
            $alias = Mage::helper('ambase')->isVersionLessThan(1,4) ? 'e' : 'main_table';
            $orderCollection->getSelect()->join(
                    array('custom_attributes' => Mage::getModel('amorderattr/attribute')->getResource()->getTable('amorderattr/order_attribute')),
                    "$alias.entity_id = custom_attributes.order_id",
                    array($attributecode)
               );
            $orderCollection->getSelect()->order('custom_attributes.order_id DESC');
            $orderCollection->getSelect()->limit(1);

            $attributeValue="";
            if ($orderCollection->getSize() > 0)
            {
                foreach ($orderCollection as $lastOrder)
                {
                    $attributeValue = $lastOrder->getData($attributecode);
                }
            }
            return $attributeValue;
        }
    }

    /* Translation of Ddate */
    public function getFrdaytext($daytext)
    {
        $array=[
        "Mon"=>"Lun",
        "Tue"=>"Mar",
        "Wed"=>"Mer",
        "Thu"=>"Jeu",
        "Fri"=>"Ven",
        "Sat"=>"Sam",
        "Sun"=>"Dim",
        ];

        return $array[$daytext];
    }

    public function getCommercantname($object){
        $name=$object->getProduct()->getAttributeText('commercant');

        if($name==NULL || $name==""){
            return "";
        }else{
            return $name;
        }
    }

    public function getDaysCommercants($number){
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $commercants=array();

        foreach ($items as $item) {
            $com=$this->getCommercantname($item);
            $id_attribut_commercant=$item->getCommercant();
            if(empty($commercants[$com])){
                $commercants[$com]=$id_attribut_commercant;
            }
        }
        $data=array();
        foreach ($commercants as $com =>$id){
            $shop=Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_attribut_commercant', $id)->getFirstItem();
            $delivery_days=$shop->getDeliveryDays();
            if(count($delivery_days)<4 && $delivery_days<>NULL){
                if($number){
                    $data[$com]=$delivery_days;
                }else{
                    $tmp=Mage::helper('apdc_commercant')->getDays();
                    foreach($delivery_days as $key=>$day){
                        $delivery_days[$key]=$tmp[$day-1];
                    }
                    $data[$com]=$delivery_days;
                }
            }
        }
        return $data;
    }

    public function getSpottyCom($number=false){
        $data=$this->getDaysCommercants($number);
        return $data;
    }

}
