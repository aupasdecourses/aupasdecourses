<?php

/**
 * Class Apdc_Commercant_Model_Resource_Shop
 */
class Apdc_Commercant_Model_Resource_Shop extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/shop', 'id_shop');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $shop)
    {
        $timetable = @unserialize($shop->getData('timetable'));
        if ($timetable === false) {
            $timetable = [];
        }
        $shop->setData('timetable', $timetable);

        $periods = @unserialize($shop->getData('closing_periods'));
        if ($periods === false) {
            $periods = [];
        }
        $shop->setData('closing_periods', $periods);

        return parent::_afterLoad($shop);
    }
}
