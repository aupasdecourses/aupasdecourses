<?php

/**
 * Class Apdc_Commercant_Model_Resource_Shop
 */
class Apdc_Commercant_Model_Resource_Shop extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_serializableFields = [
        'timetable' => [[], []],
        'closing_periods' => [[], []],
        'delivery_days' => [[], []]
    ];

    protected function _construct()
    {
        $this->_init('apdc_commercant/shop', 'id_shop');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $shop)
    {
        $categories = explode(",",$shop->getData('id_category'));
        if ($categories === false) {
            $categories = [];
        }
        $shop->setData('id_category', $categories);

        return parent::_afterLoad($shop);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $shop)
    {
        $stores = null;
        if (null !== $shop->getData('id_category')) {
            $idCategory = $shop->getData('id_category');
            $stores = [];
            $S = Mage::helper('apdc_commercant')->getStoresArray();
            $categories = Mage::getModel('catalog/category');
            foreach ($idCategory as $key => $id) {
                $cat = $categories->getCollection()
                    ->addAttributetoFilter('entity_id', $id)
                    ->getFirstItem();

                if ($cat && $cat->getId()) {
                    $storeid = $S[explode('/', $cat->getPath())[1]]['store_id'];
                    if (!in_array($storeid, $stores)) {
                        $stores[] = $storeid;
                    }
                } else {
                    unset($idCategory[$key]);
                }
            }
            $shop->setData('id_category', implode(",", $idCategory));
        }

        if (null !== $stores) {
            $shop->setData('stores', implode(",", $stores));
        }
    }
}
