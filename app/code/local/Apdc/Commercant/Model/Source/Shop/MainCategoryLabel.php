<?php

class Apdc_Commercant_Model_Source_Shop_MainCategoryLabel
{
    protected $options=null;

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addFieldToFilter('level', 2)
                ->addNameToResult();
            $categories->load();
            $options = [];
            foreach ($categories as $cat) {
                if (!isset($optins[$cat->getName()])) {
                    $options[$cat->getName()] = $cat->getName();
                }
            }
            asort($options);
            $this->options = $options;
        }
        return $this->options;
    }
}
