<?php

class Apdc_Referentiel_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCats($s, $f, $store)
    {
        $storeId = 0;
        Mage::app()->setCurrentStore($storeId);
        $cats = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect(['store_id', 'name', 'image', 'thumbnail', 'is_clickable', 'is_active', 'show_in_navigation', 'show_age_popup', 'display_mode', 'meta_title', 'menu_bg_color', 'menu_text_color']);
        if ($store <> null) {
            $rootId = Mage::app()->getStore($store)->getRootcatsId();
            $cats->addFieldToFilter('path', ['like' => "1/$rootId/%"]);
        }
        if ($s <> null) {
            $cats->addAttributeToFilter('level', ['gt' => $s]);
        }
        if ($f <> null) {
            $cats->addAttributeToFilter('level', ['lt' => $f]);
        }
        return $cats;
    }
}