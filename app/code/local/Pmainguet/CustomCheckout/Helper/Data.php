<?php

class Pmainguet_CustomCheckout_Helper_Data extends Mage_Core_Helper_Abstract
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
            if ($orderCollection->getSize() > 0)
            {
                foreach ($orderCollection as $lastOrder)
                {
                    $attributeValue = $lastOrder->getData($attributecode);
                }
            }else{
                $attributeValue='';
            }
        }
        return $attributeValue;
    }
  
   public function getCommercantname($object){

    //Old version with category ids
    // $category_ids=$object->getProduct()->getCategoryIds();
    // $category = Mage::getModel('catalog/category')->load($category_ids[count($category_ids)-3]);
    // if ($category->getIsActive()) {
    //     $name=$category->getName();
    //     $url=$category->getUrl();
    //     return [$name,$url];
    // }

        //New version
        $name=$object->getProduct()->getAttributeText('commercant');

        if($name==NULL || $name==""){
            return "";
        }else{
            return $name;
        }
    }

    public function getCommercantcat($com_name){
        $categories = Mage::getModel('catalog/category')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->addIsActiveFilter();

        foreach($categories as $cat){
            $cat_attr=$cat->getAttributes();
            $test=$cat_attr['att_com_id']->getFrontend()->getValue($cat);
            if($test==$com_name){
                return $cat_attr['delivery_days']->getFrontend()->getValue($cat);
            }
        }

        return false;
    }

    public function getDaysCommercants(){
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems(); // Get all items in the cart
        $commercants=array();
        $helper=Mage::helper('customcheckout');

        //go through each item to build commerçants array
        foreach ($items as $item) {
                  //print out custom attribute value
            $com=$helper->getCommercantname($item);
            if(!in_array($com,$commercants)){
                array_push($commercants,$com);
            }
        }
        $data=array();
        foreach ($commercants as $com){
            if($com){
                $days=$helper->getCommercantcat($com);
                $days = str_replace(' ', '', $days);
                $days=explode(",", $days);
                $data[$com]=$days;
            }
        }

        return $data;

    }

    public function convertdaysinnb(array $days){
        $convert=[
            'Dimanche'=>0,
            'Lundi'=>1,
            'Mardi'=>2,
            'Mercredi'=>3,
            'Jeudi'=>4,
            'Vendredi'=>5,
            'Samedi'=>6,
        ];

        $temp=array();
        foreach($days as $day){
            if($day<>''){
                $temp[]=$convert[$day];
            }
        }

        return($temp);
    }

    public function getSpottyCom($number=false){
        $data=$this->getDaysCommercants();
        $temp=array();
        if($number){
            foreach($data as $com => $days){
                if(count($days)<>4){
                    $temp[$com]=$this->convertdaysinnb($days);
                }
            }
        }else{
            foreach($data as $com => $days){
                if(count($days)<>4){
                    $temp[$com]=$days;
                }
            }
        }

        return $temp;
    }

}
