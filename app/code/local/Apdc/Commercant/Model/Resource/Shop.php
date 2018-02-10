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

    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $categories = [];
        if ($object->getId()) {
            $categories = $this->lookupCategoryIds($object->getId());
        }
        $object->setData('category_ids', $categories);

        return parent::_afterLoad($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $shop)
    {
        $stores = null;
        if (null !== $shop->getData('category_ids')) {
            $categoryIds = $shop->getData('category_ids');
            $stores = [];
            $S = Mage::helper('apdc_commercant')->getStoresArray();
            $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributetoFilter('entity_id', ['in' => $categoryIds]);
            foreach ($categories as $cat) {
                if ($cat && $cat->getId()) {
                    $storeid = $S[explode('/', $cat->getPath())[1]]['store_id'];
                    if (!in_array($storeid, $stores)) {
                        $stores[] = $storeid;
                    }
                }
            }
        }

        if (null !== $stores) {
            $shop->setData('stores', implode(",", $stores));
        }
    }

    /**
     * Process page data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'shop_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('apdc_commercant/shop_categories'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Assign Shop to categories
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldCategories = $this->lookupCategoryIds($object->getId());
        $newCategories = (array)$object->getCategoryIds();

        $table  = $this->getTable('apdc_commercant/shop_categories');
        $insert = array_diff($newCategories, $oldCategories);
        $delete = array_diff($oldCategories, $newCategories);

        if ($delete) {
            $where = array(
                'shop_id = ?'     => (int) $object->getId(),
                'category_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $categoryId) {
                $data[] = array(
                    'shop_id'  => (int) $object->getId(),
                    'category_id' => (int) $categoryId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        //Mark layout cache as invalidated
        Mage::app()->getCacheInstance()->invalidateType('layout');

        return parent::_afterSave($object);
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupCategoryIds($shopId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('apdc_commercant/shop_categories'), 'category_id')
            ->where('shop_id = ?',(int)$shopId);

        return $adapter->fetchCol($select);
    }

    /**
     * getShopIdByCategoryId 
     * 
     * @param int $categoryId categoryId 
     * 
     * @return int
     */
    public function getShopIdByCategoryId($categoryId)
    {
        $adapter = $this->_getReadAdapter();

        $categorySelect = $adapter->select()
            ->from($this->getTable('catalog/category'))
            ->where('entity_id = ?', (int)$categoryId);
        $category = $adapter->fetchAll($categorySelect);
        $categoryIds = explode('/', $category[0]['path']);


        $select  = $adapter->select()
            ->from($this->getTable('apdc_commercant/shop_categories'), 'shop_id')
            ->where('category_id in(?)', $categoryIds);

        $shopIds = $adapter->fetchCol($select);
        return reset($shopIds);
    }
}
