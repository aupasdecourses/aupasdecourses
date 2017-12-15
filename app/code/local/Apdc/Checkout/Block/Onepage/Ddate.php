<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Checkout
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Checkout_Block_Onepage_Ddate 
 * 
 * @category Apdc
 * @package  Checkout
 * @uses     MW
 * @uses     MW_Ddate_Block_Onepage_Ddate
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Checkout_Block_Onepage_Ddate extends MW_Ddate_Block_Onepage_Ddate
{
    /**
     * getAvailableDaysAndSlots 
     * 
     * @return array
     */
    public function getAvailableDaysAndSlots($withUnaivalableDays=true)
    {
        $availableDaysAndSlots = [];
        $slots = $this->getSlots();

        $currentTime = Mage::getSingleton('core/date')->timestamp();
        $currentTimeGMT = Mage::getSingleton('core/date')->gmtTimestamp();
        $cpt = 0;
        $day = 0;
        if ($withUnaivalableDays) {
            for ($day = 0; $day < 7; $day++) {
                Mage::log("=> getAvailableDaysAndSlots launch!",null,"ddate.log");
                $strDate = date('Y-m-d', strtotime('+' . $day . ' day', $currentTime));
                $date_FR = Mage::app()->getLocale()->date(strtotime('+' . $day . ' day', $currentTimeGMT));
                $hasSlot = false;
                if (!isset($availableDaysAndSlots[$strDate])) {
                    $availableDaysAndSlots[$strDate] = [
                        'day' => [
                            'day' => $date_FR->get(Zend_Date::WEEKDAY_SHORT),
                            'date' => date(Mage::helper('ddate')->month_date_format(), strtotime('+' . $day . ' day', $currentTime))
                        ],
                        'slots' => [
                        ]
                    ];
                }
                foreach ($slots as $slot) {
                    if($this->isEnabled($slot->getId(), $strDate)){
                        $hasSlot = true;
                        $availableDaysAndSlots[$strDate]['slots'][$slot->getDtimesort()] = [
                            'dtime_id' => $slot->getDtimeId(),
                            'dtime' => $slot->getDtime()
                        ];
                    }
                }
                $availableDaysAndSlots[$strDate]['has_slot'] = $hasSlot;
            }
        } else {
            while($cpt < 7) {
                $strDate = date('Y-m-d', strtotime('+' . $day . ' day', $currentTime));
                $date_FR = Mage::app()->getLocale()->date(strtotime('+' . $day . ' day', $currentTimeGMT));
                $hasSlot = false;
                foreach ($slots as $slot) {
                    if($this->isEnabled($slot->getId(), $strDate)){
                        $hasSlot = true;
                        if (!isset($availableDaysAndSlots[$strDate])) {
                            $availableDaysAndSlots[$strDate] = [
                                'day' => [
                                    'day' => $date_FR->get(Zend_Date::WEEKDAY_SHORT),
                                    'date' => date(Mage::helper('ddate')->month_date_format(), strtotime('+' . $day . ' day', $currentTime))
                                ],
                                'slots' => [
                                ]
                            ];
                        }
                        $availableDaysAndSlots[$strDate]['slots'][$slot->getDtimesort()] = [
                            'dtime_id' => $slot->getDtimeId(),
                            'dtime' => $slot->getDtime()
                        ];
                    }
                }
                if ($hasSlot) {
                    $cpt++;
                }
                $day++;
            }
        }
        return $availableDaysAndSlots;
    }
}
