<?php

/**
 * Class Apdc_Commercant_Model_Shop
 */
class Apdc_Commercant_Model_Shop extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('apdc_commercant/shop');
    }

    /**
     * Return an array of day schedules
     * Each day is either false (shop is closed) or an array of two day times "open" and "close"
     * A day time is an array with two entries, hours and minutes
     *
     * @return array
     */
    public function getShopTimetable()
    {
        $timetable = [];
        foreach ($this->getData('timetable') as $daySchedule) {
            if (!preg_match('/(\d+):(\d+)-(\d+):(\d+)/', $daySchedule, $matches)) {
                $timetable[] = false;
            } else {
                $timetable[] = [
                    'open' => [$matches[1], $matches[2]],
                    'close' => [$matches[3], $matches[4]],
                ];
            }
        }

        return $timetable;
    }
}
