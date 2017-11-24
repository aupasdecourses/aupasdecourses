<?php

class Apdc_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{    
    //get saved Attribute Value for Amasty Order Attribute module
    public function getSavedAttrValue($attributecode){
        $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
        $orderAttributes = $session->getAmastyOrderAttributes();
        if (isset($orderAttributes[$attributecode])) {
            return $orderAttributes[$attributecode];
        }

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

    /**
     * saveDdate 
     * 
     * @param string $ddate ddate 
     * @param string $dtimeId dtimeId 
     * @param string $ddatei ddatei 
     * 
     * @return void
     */
    public function saveDdate($ddate, $dtimeId, $ddatei)
    {
        $dtime = Mage::getModel('ddate/dtime')->load($dtimeId)->getDtime();

        Mage::getSingleton('core/session')->setDdate($ddate);
        Mage::getSingleton('core/session')->setDtime($dtime);
        Mage::getSingleton('core/session')->setDtimeId($dtimeId);
        Mage::getSingleton('core/session')->setDdatei($ddatei);

        $ddate_array=explode("-",$ddate);
        //$strDate_FR = date('d/m/Y', mktime(0,0,0,$ddate_array[1],$ddate_array[2],$ddate_array[0]));
        $date = Mage::app()->getLocale()->date(mktime(0,0,0,$ddate_array[1],$ddate_array[2],$ddate_array[0]));

        $formatedDate = $date->get(Zend_Date::WEEKDAY_SHORT)." ".$date->get(Zend_Date::DAY)."/".$date->get(Zend_Date::MONTH);
        Mage::getSingleton('core/session')->setHeaderDdate($formatedDate . ' ' . $dtime);
        $_SESSION['ddate'] = $ddate;
        $_SESSION['dtime'] = $dtime;
    }

    public function cleanDdate()
    {
        $session = Mage::getSingleton('core/session');
        $session->unsDdate();
        $session->unsDtime();
        $session->unsDtimeId();
        $session->unsDdatei();
        $session->unsHeaderDdate();
        if (isset($_SESSION['ddate'])) {
            unset($_SESSION['ddate']);
        }
        if (isset($_SESSION['dtime'])) {
            unset($_SESSION['dtime']);
        }
    }

    /**
     * getHeaderDdate 
     * 
     * @return string | false
     */
    public function getHeaderDdate()
    {
        Mage::getSingleton('core/session')->setNeedToSelectDeliveryDays(false);
        if (Mage::getSingleton('core/session')->getDdate()) {
            $date = Mage::getSingleton('core/session')->getDdate();
            $currentDate = date('Y-m-d');
            if ($date < $currentDate) {
                Mage::getSingleton('core/session')->setNeedToSelectDeliveryDays(true);
                return false;
            }
            return Mage::getSingleton('core/session')->getHeaderDdate();
        }
        return false;
    }

    public function getCommercantname($object){
        $name=$object->getProduct()->getAttributeText('commercant');

        if($name==NULL || $name==""){
            return "";
        }else{
            return $name;
        }
    }

    public function getCommercantInCart()
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $commercants=array();

        foreach ($items as $item) {
            $com = $this->getCommercantname($item);
            $id_attribut_commercant = $item->getCommercant();
            if (empty($commercants[$com])) {
                $commercants[$com] = $id_attribut_commercant;
            }
        }
        return $commercants;
    }

    public function getDaysCommercants($number){
        $commercants = $this->getCommercantInCart();
        $data=array();
        foreach ($commercants as $com => $id){
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

    public function SaturdayEnable(){
        $store=Mage::app()->getStore();
        $storeid = $store->getId();
        $data=Mage::helper("ddate")->getDtime($storeid)->load()->toArray(array('sat'))['items'];
        foreach($data as $key => $value){
            if((int)$value['sat']){
                return true;
            }
        }
        return false;
    }
    
    public function AvailUnavail($days)
    {
        $tmp = Mage::helper('apdc_commercant')->getDays();
        $result=array();
        //Unset Lundi et Dimanche
        unset($tmp[0]);
        unset($tmp[6]);
        //Unset Samedi si non actif
        if(!$this->SaturdayEnable()){
            unset($tmp[5]);
        }
        //Unset every missing delivery days
        foreach($days as $day){
            $result[]=$tmp[$day-1];
            unset($tmp[$day-1]);
        }

        return array('unavailability'=>$tmp,'availability'=>$result);
    }

    public function getUnaivalableCommercantInfosPopup()
    {
        $commercants = Mage::helper('apdc_commercant')->getShops("store");
        $datas = [];
        $check = false;
        foreach ($commercants as $name => $id) {
            $shopInfo = Mage::helper('apdc_commercant')->getInfoShopByCommercantId($id);
            $datas[$name] = [
                'delivery_days' => [],
                'is_closed' => null,
                'next_closed_this_week' => null
            ];

            $deliveryDays = $shopInfo['delivery_days'];
            $data = $this->AvailUnavail($deliveryDays);
            $unavailability= $data['unavailability'];
            $deliveryDays = $data['availability'];

            $datas[$name]['delivery_days'] = $deliveryDays;
            $datas[$name]['unavailability'] = $unavailability;
            if (isset($shopInfo['is_closed']) && !empty($shopInfo['is_closed'])) {
                $datas[$name]['is_closed'] = $shopInfo['is_closed'];
                if($shopInfo['is_closed']<>null){
                    $check = true;
                }
            }
            if (isset($shopInfo['next_closed_this_week']) && !empty($shopInfo['next_closed_this_week'])) {
                $datas[$name]['next_closed_this_week'] = $shopInfo['next_closed_this_week'];
                if($shopInfo['next_closed_this_week']<>null){
                    $check = true;
                }
            }
            if($unavailability<>array()){
                $check = true;
            }

        }
        return array("check"=>$check,'data'=>$datas);
    }
}
