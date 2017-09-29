<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Model_Resource_Product_Availability_Manager 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Model_Resource_Db_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Resource_Product_Availability_Manager extends Mage_Core_Model_Resource
{
    protected function _getReadAdapter()
    {
        return $this->getConnection('core_read');
    }

    protected function _getWriteAdapter()
    {
        return $this->getConnection('core_write');
    }

    /**
     * removeProductsAvailabilites 
     * 
     * @param Apdc_Catalog_Model_Product_Availability_Manager $manager manager 
     * 
     * @return void
     */
    public function removeProductsAvailabilites(Apdc_Catalog_Model_Product_Availability_Manager $manager)
    {
        $tableName = $this->getTableName('apdc_catalog/product_availability');
        $adapter = $this->_getWriteAdapter();
        try {
            $productIds = $manager->getProductIds();
            if (!empty($productIds)) {
                $adapter->beginTransaction();
                $query = sprintf(
                    'DELETE FROM %s WHERE %s',
                    $adapter->quoteIdentifier($tableName),
                    $adapter->quoteInto('product_id IN(?)', $productIds)
                );
                $adapter->query($query);
                $adapter->commit();
            }  else {
                $adapter->truncateTable($tableName);
            }
        } catch (Exception $e) {
            $adapter->rollback();
            Mage::logException($e);
            Mage::throwException($e);
        }
    }

    /**
     * getProductDatas 
     * 
     * @param Apdc_Catalog_Model_Product_Availability_Manager $manager manager 
     * 
     * @return array
     */
    public function getProductDatas(Apdc_Catalog_Model_Product_Availability_Manager $manager)
    {
        $collection = $manager->getProductCollection();
        return $this->_getReadAdapter()->fetchAll($collection->getSelect());
    }

    /**
     * insertData 
     * 
     * @param array $datas datas 
     * 
     * @return void
     */
    public function insertData($datas)
    {
        try {
            $tableName = $this->getTableName('apdc_catalog/product_availability');
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();

            $adapter->insertOnDuplicate($tableName, $datas, ['status']);

            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollback();
            Mage::logException($e);
            Mage::throwException($e);
        }

    }

    /**
     * getProductRelations
     * 
     * @param Apdc_Catalog_Model_Product_Availability_Manager $manager manager 
     * 
     * @return array
     */
    public function getProductRelations(Apdc_Catalog_Model_Product_Availability_Manager $manager)
    {
        $productIds = $manager->getProductIds();
        if (!empty($productIds)) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()->from($this->getTableName('catalog/product_relation'))
                ->where('parent_id in(?)', implode(',', $productIds));
            return $adapter->fetchAll($select);
        }
        return [];
    }
}
