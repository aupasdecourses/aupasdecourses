<?php

require_once 'MW/Ddate/controllers/Adminhtml/Ddate/DdateController.php';
class Apdc_Sales_Adminhtml_Ddate_DdateController extends MW_Ddate_Adminhtml_Ddate_DdateController
{
	 /**
     * check available date
     * @param int $slotId: dtime's id
     * @param date_type $date (example: 2011/11/2)
     * @return boolean 
     */
    public function isEnabled($slotId, $date)
    {
        $special_date = Mage::helper('ddate')->getSpecialDay();
        $slot = Mage::getModel('ddate/dtime')->load($slotId);
        
        if ($slot->getHoliday() == 1 && Mage::helper('ddate')->getDayoff()) {
        	$this->ajaxerror = Mage::helper('ddate')->__('This is Holiday or Dayoff');
            return $this->ajaxerror;
        }

		/* check ordered items */
		$ordered=Mage::helper('ddate')->ordered_counting($date,$slotId);	
		if($ordered && $ordered >= $slot->getMaximumBooking()) {
			$this->ajaxerror = Mage::helper('ddate')->__('This slot time has been full booking');	
			return $this->ajaxerror;
		}

        //check available slot based on day of week
        $method = 'get' . date('D', strtotime($date));
        if ($slot->{$method}() == "0") {
        	$this->ajaxerror = Mage::helper('ddate')->__('This time slot is not available in ').date('l', strtotime($date));
            return $this->ajaxerror;
        }

        //check available slot based on configuration of weekend (Satuday and Sunday)
        if (method_exists(Mage::helper('ddate'), $method)) {
            if (Mage::helper('ddate')->{$method}() == "0") {
                $this->ajaxerror = Mage::helper('ddate')->__('This time slot is not available in ').date('l', strtotime($date));
                return $this->ajaxerror;
            }
        }

        //check available slot based on configuration of special days
        if (($slot->getSpecialday() == "1") && isset($special_date[$date])) {
            $this->ajaxerror = Mage::helper('ddate')->__('This date is special day');
            return $this->ajaxerror;
        }

        //check available slot based on specified slot's special days
        $specifiedSpecial = $slot->getSpecialDays();
        if (isset($specifiedSpecial[$date])) {
            $this->ajaxerror = Mage::helper('ddate')->__('This time slot is not available in this day');
            return $this->ajaxerror;
        }

        return 'ok';
    }
}
