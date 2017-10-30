<?php

/**
 * Class Apdc_Commercant_Model_Resource_Shop_Collection
 */
class Apdc_Commercant_Model_Resource_Shop_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/shop');
        $this->_map['fields']['id_shop'] = 'main_table.id_shop';
        $this->_map['fields']['category']   = 'category_table.category_id';
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $item->afterLoad();
            $this->getResource()->unserializeFields($item);
        }
        return $this;
    }

    /**
     * Retreive array of attributes
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray($arrAttributes = array())
    {
        $arr = array();
        foreach ($this->_items as $k => $item) {
            $arr[$k] = $item->toArray($arrAttributes);
        }
        return $arr;
    }

    /**
     * Add filter by category
     *
     * @param int|Mage_Catalog_Model_Category $category
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    public function addCategoryFilter($category)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof Mage_Catalog_Model_Category) {
                $category = array($cateogry->getId());
            }

            if (!is_array($category)) {
                $category = array($category);
            }

            $this->addFilter('category', array('in' => $category), 'public');
        }
        return $this;
    }

    /**
     * Join category relation table if there is category filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('category')) {
            $this->getSelect()->join(
                array('category_table' => $this->getTable('apdc_commercant/shop_categories')),
                'main_table.id_shop = category_table.shop_id',
                array()
            )->group('main_table.id_shop');

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }
}
