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

        $deliveryDays = @unserialize($shop->getData('delivery_days'));
        if ($deliveryDays === false) {
            $deliveryDays = [];
        }
        $shop->setData('delivery_days', $deliveryDays);

        $categories = @unserialize($shop->getData('id_category'));
        if ($categories === false) {
            $categories = [];
        }
        $shop->setData('id_category', $categories);

        return parent::_afterLoad($shop);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $shop)
    {
        if (null !== $days = $shop->getData('delivery_days')) {
            $shop->setData('delivery_days', serialize($days));
        }

        if (null !== $categories = $shop->getData('id_category')) {
            $shop->setData('id_category', serialize($categories));
        }

        if (null !== $stores = $shop->getData('stores')) {
            $shop->setData('stores', implode(",",$stores));
        }
    }
}
