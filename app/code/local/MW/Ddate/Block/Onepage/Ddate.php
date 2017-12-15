<?php

class MW_Ddate_Block_Onepage_Ddate extends Mage_Checkout_Block_Onepage_Abstract
{
    private $_currentTime;
    private $enableDate;

    protected function _construct()
    {
        $this->getCheckout()->setStepData('ddate', array(
            'label' => Mage::helper('ddate')->__('Delivery Information'),
            'is_show' => $this->isShow()
        ));
        parent::_construct();
    }

    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }

    public function getDdate()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getDdate();
    }

    public function getIsCalender()
    {
        // get calender_display attribute in configuration
        return Mage::getStoreConfig('ddate/info/calender_display');
    }

    public function getDeliverySaturday()
    {
        return Mage::getStoreConfig('ddate/info/deliver_saturdays');
    }

    public function getSundaySaturday()
    {
        return Mage::getStoreConfig('ddate/info/deliver_sundays');
    }

    public function getDateMax()
    {
        $a = Mage::getStoreConfig('ddate/info/weeks') * 7 * 86400;
        $max_date_time = $a + intVal(strtotime(date('m/d/Y')));
        $max_date = date("Ymd", $max_date_time);

        return $max_date;
    }

    public function getNumberWeek()
    {
        $numberWeek = Mage::getStoreConfig("ddate/info/weeks") != '' ? Mage::getStoreConfig("ddate/info/weeks") : 4;
        
        return $numberWeek;
    }

    public function getSlots()
    {
        return Mage::getResourceModel('ddate/ddate')->getDtime();
    }
    
    /**
     * check available date
     * @param int $slotId: dtime's id
     * @param date_type $date (example: 2011/11/2)
     * @return boolean 
     */
    public function isEnabled($slotId, $date,$debug=false)
    {
        if($debug){Mage::log("current slot id = ".$slotId." for ".$date,null,'ddate.log');}
        $ddateTime = strtotime($date);/*** date at 0 hour:00 min example:2014-05-08 00:00:00 **/
        $special_date = Mage::helper('ddate')->getSpecialDay();
        $delay = (Mage::getStoreConfig('ddate/info/delay') ? (int)Mage::getStoreConfig('ddate/info/delay') : 0);
        $slots = $this->getSlots();
        $numberWeek = $this->getNumberWeek();
        $ddates = Mage::getSingleton('ddate/ddate')->getNumberOrderFromNow();
        
        //check available slot based on day of week
        $method = 'get' . date('D', strtotime($date));
        if ($slots[$slotId]->{$method}() == "0") {
            if($debug){Mage::log("Pas dispo ce jour (semaine)",null,'ddate.log');}
            return false;
        }
        
        //check available slot based on configuration of weekend (Satuday and Sunday)
        if (method_exists(Mage::helper('ddate'), $method)) {
            if (Mage::helper('ddate')->{$method}() == "0") {
                if($debug){Mage::log("Pas dispo ce jour (weekend)",null,'ddate.log');}
                return false;
            }
        }
        
        //check available slot based on configuration of special days
        if (($slots[$slotId]->getSpecialday() == "1") && isset($special_date[$date])) {
            if($debug){Mage::log("Pas dispo ce jour (special day)",null,'ddate.log');}
            return false;
        }
        
        //check available slot based on specified slot's special days
        $specifiedSpecial = $slots[$slotId]->getSpecialDays();
        if (isset($specifiedSpecial[$date])) {
            if($debug){Mage::log("Pas dispo ce jour (special day 2)",null,'ddate.log');}
            return false;
        }

        //check available slot based on holidays & Day off
        $holiday = Mage::getModel('ddate/ddate')->getCollection()
            ->addFieldToFilter('ddate', array('eq' => $date))
            ->addFieldToFilter('holiday', array('eq' => '1'));

        if ($holiday->count() == 1 && Mage::helper('ddate')->getDayoff()) {
            Mage::log("Holiday ou Dayoff",null,'ddate.log');
            return false;
        } else if(isset($ddates[$slotId][$date])) {
            if (intval($ddates[$slotId][$date]->getOrdered()) >= intval($slots[$slotId]->getMaximumBooking())) {
                if($debug){Mage::log("Too many bookings",null,'ddate.log');}
                return false;
            }
        }

        //Vérifie délai par rapport au premier créneau si option configuré
        $delayTime = strtotime('+'.$delay.' hours', $this->getCurrentTime());    
        if(Mage::getStoreConfig('ddate/info/disable_base_firststlot')){
            if(!isset($this->enableDate[$date])){
                //Avant getSingleton mais pose problème pour les différentes validations des dates lorsque créneaux d'horaires différents sur différents jours
                $startTime = Mage::getModel('ddate/dtime')->getStartTimeOfFirstSlot($ddateTime,true);
                if($debug){Mage::log("DDATETIME ".$ddateTime,null,'ddate.log');}
                if($debug){Mage::log("STARTTIME ".$startTime,null,'ddate.log');}
                if($debug){Mage::log("DELAYTIME ".$delayTime,null,'ddate.log');}
                if ($delayTime > strtotime('+'.$startTime.' hours', $ddateTime)) {
                    $this->enableDate[$date] = false;
                    if($debug){Mage::log("delai avant créneau trop faible",null,'ddate.log');}
                } else {
                    $this->enableDate[$date] = true;
                }
            }
            if (!$this->enableDate[$date]) {
                return false;
            }
        }

        //check available slot based on slot's time interval
        $hightBoundTime = strtotime('+ '.$slots[$slotId]->getHighBoundHour().' hours'.$slots[$slotId]->getAdditionMin(), $ddateTime);
        if ($hightBoundTime < $delayTime) {
            if($debug){Mage::log("Slots returned by MWDdate",null,'ddate.log');}
            if($debug){Mage::log($slots,null,'ddate.log');}
            if($debug){Mage::log("High Bound Time",null,'ddate.log');}
            if($debug){Mage::log($hightBoundTime,null,'ddate.log');}
            if($debug){Mage::log("Delay Time".$slotId,null,'ddate.log');}
            if($debug){Mage::log($delayTime,null,'ddate.log');}
            if($debug){Mage::log("Delay ".$slotId,null,'ddate.log');}
            if($debug){Mage::log($delay,null,'ddate.log');}
            if($debug){Mage::log("High Bound Time > Delay Time",null,'ddate.log');}
            return false;
        }
        
        return true;
    }

    public function getCurrentTime()
    {
        if (empty($this->_currentTime)) {
            $this->_currentTime = Mage::getSingleton('core/date')->timestamp();
        }

        return $this->_currentTime;
    }
}
