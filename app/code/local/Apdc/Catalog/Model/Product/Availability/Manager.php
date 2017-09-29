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
 * Apdc_Catalog_Model_Product_Availability_Manager 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Product_Availability_Manager extends Mage_Core_Model_Abstract
{
    protected $productIds = [];
    protected $shops = null;
    protected $neighborhoods = null;
    protected $allDays = null;
    protected $productRelations = null;
    protected $childPerParent = [];

    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_catalog/product_availability_manager');
    }

    /**
     * setProductIds 
     * 
     * @param array $productIds productIds 
     * 
     * @return Apdc_Catalog_Model_Product_Availability_Manager
     */
    public function setProductIds($productIds=[])
    {
        $this->productIds = $productIds;
        return $this;
    }

    /**
     * getProductIds 
     * 
     * @return array
     */
    public function getProductIds()
    {
        return $this->productIds;
    }

    /**
     * generateProductsAvailabilities 
     * 
     * @param array[int] $productIds : productIds 
     * 
     * @return void
     */
    public function generateProductsAvailabilities($productIds=[])
    {
        $this->setProductIds($productIds);
        $this->removeProductsAvailabilites();
        $this->addProductAvailabilities();
    }

    /**
     * removeProductsAvailabilites 
     * 
     * @return void
     */
    protected function removeProductsAvailabilites()
    {
        try {
            $this->getResource()->removeProductsAvailabilites($this);
        } catch(Exception $e) {
            Mage::logException($e);
            Mage::throwException(__('Unable to clean products availabilities. Please check the logs for more details.'));
        }
    }

    /**
     * addProductAvailabilities 
     * 
     * @return void
     */
    protected function addProductAvailabilities()
    {
        $availabilities = $this->getAvailabilities();
        $datas = [];
        $errors = [];
        $productDatas = $this->getProductDatas();
        foreach ($this->getAllDays() as $time) {
            $day = date('w', $time);
            $date = date('Y-m-d', $time);

            foreach ($productDatas as $product) {
                if (in_array($product['type_id'], ['bundle', 'grouped'])) {
                    $childs = $this->getChildPerParent($product['entity_id']);
                    foreach ($childs as $childId) {
                        $childKey = $childId . '_' . $product['website_id'];
                        if (isset($productDatas[$childKey])) {
                            $childData = $productDatas[$childKey];
                            $key = $childData['website_id'] . '_' . $childData['commercant_id'];
                            $available = $availabilities[$day][$key];
                            if ($available['status'] > 1) {
                                break;
                            }
                        }
                    }

                } else {
                    $key = $product['website_id'] . '_' . $product['commercant_id'];
                    $productId = $product['entity_id'];
                    if (!isset($availabilities[$day][$key])) {
                        if (!isset($errors[$productId . $key])) {
                            $errors[$productId . $key] = $product['entity_id'] . ' / website : ' . $product['website_id'] . ' / commercant ; ' . $product['commercant_id'];
                        }
                        continue;
                    }
                    $available = $availabilities[$day][$key];
                }
                $datas[] = [
                    'product_id' => $product['entity_id'],
                    'website_id' => $product['website_id'],
                    'delivery_date' => $date,
                    'neighborhood_id' => $available['neighborhood_id'],
                    'status' => $available['status']
                ];
                if (count($datas) >= 500) {
                    $this->getResource()->insertData($datas);
                    $datas = [];
                }
            }
        }
        if (!empty($datas)) {
            $this->getResource()->insertData($datas);
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $error = 'Erreur lors de la regénération des disponibilités produits avec le produit : ' . $error;
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
        }
    }

    /**
     * getProductDatas 
     * 
     * @return array
     */
    protected function getProductDatas()
    {
        $productDatas = [];
        $datas = $this->getResource()->getProductDatas($this);
        foreach ($datas as $product) {
            $key = $product['entity_id'] . '_' . $product['website_id'];
            $productDatas[$key] = $product;
        }
        unset($datas);
        return $productDatas;
    }

    /**
     * getProductCollection 
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('status', 1);
        $productCollection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $productCollection->getSelect()->columns(['entity_id', 'type_id']);
        $productCollection->joinAttribute(
            'commercant_id',
            'catalog_product/commercant',
            'entity_id'
        );
        $productCollection->getSelect()->join(
            ['pw' => $this->getResource()->getTableName('catalog/product_website')],
            'pw.product_id = e.entity_id',
            ['pw.website_id']
        );
        if (!empty($this->getProductIds())) {
            $productIds = $this->getProductIds();
            $productRelations = $this->getProductRelations();
            if (!empty($productRelations)) {
                foreach ($productRelations as $product) {
                    $productIds[] = $product['child_id'];
                }
                $this->setProductIds($productIds);
            }
            $productCollection->addFieldToFilter('entity_id', ['in' => $this->getProductIds()]);
        }

        return $productCollection;
    }

    /**
     * getProductRelations
     * 
     * @return array
     */
    protected function getProductRelations()
    {
        if (is_null($this->productRelations)) {
            $this->productRelations = $this->getResource()->getProductRelations($this);
            $this->childPerParent = [];
            foreach ($this->productRelations as $relation) {
                if (!isset($this->childPerParent[$relation['parent_id']])) {
                    $this->childPerParent[$relation['parent_id']] = [];
                }
                $this->childPerParent[$relation['parent_id']][] = $relation['child_id'];
            }
        }
        return $this->productRelations;
    }

    /**
     * getChildPerParent 
     * 
     * @param int|null $productId productId 
     * 
     * @return array
     */
    protected function getChildPerParent($productId=null)
    {
        if (is_null($this->childPerParent)) {
            $this->getProductRelations();
        }
        if ($productId && (int)$productId > 0) {
            if (isset($this->childPerParent[(int)$productId])) {
                return $this->childPerParent[(int)$productId];
            }
        }
        return $this->childPerParent;
    }

    /**
     * getShopsAvailability 
     * 
     * @return array
     */
    protected function getShopsAvailability()
    {
        if (is_null($this->shops)) {
            $this->shops = [];
            $collection = $this->getProductCollection();
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
            $collection->getSelect()->join(
                ['shop' => $this->getResource()->getTableName('apdc_commercant/shop')],
                'shop.id_attribut_commercant = at_commercant_id.value',
                [
                    'shop.id_shop',
                    'shop.id_attribut_commercant',
                    'shop.id_commercant',
                    'shop.enabled',
                    'shop.timetable',
                    'shop.delivery_days',
                    'shop.closing_periods'
                ]
            );
            $collection->getSelect()->group('shop.id_shop');

            foreach ($collection as $shop) {
                $id = $shop->getIdAttributCommercant();
                $this->shops[$id] = $shop->getData();
                $this->shops[$id]['timetable'] = unserialize($shop->getTimetable());
                $this->shops[$id]['delivery_days'] = unserialize($shop->getDeliveryDays());
                $this->shops[$id]['closing_periods'] = unserialize($shop->getClosingPeriods());
                if (isset($this->shops[$id]['stock_item'])) {
                    unset($this->shops[$id]['stock_item']);
                }
            }
        }
        return $this->shops;
    }

    /**
     * getNeighborhoodsAvailability 
     * 
     * @return array
     */
    protected function getNeighborhoodsAvailability()
    {
        if (is_null($this->neighborhoods)) {
            $this->neighborhoods = [];

            $collection = $this->getProductCollection();
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
            $collection->getSelect()->join(
                ['an' => $this->getResource()->getTableName('apdc_neighborhood/neighborhood')],
                'an.website_id = pw.website_id',
                [
                    'an.entity_id',
                    'an.opening_days',
                    'an.website_id'
                ]
            );
            $collection->getSelect()->group('an.entity_id');

            foreach ($collection as $neighborhood) {
                $id = $neighborhood->getWebsiteId();
                $this->neighborhoods[$id] = $neighborhood->getData();
                $this->neighborhoods[$id]['opening_days'] = unserialize($neighborhood->getOpeningDays());
                if (isset($this->neighborhoods[$id]['stock_item'])) {
                    unset($this->neighborhoods[$id]['stock_item']);
                }
            }
        }
        return $this->neighborhoods;
    }

    /**
     * getAllDays 
     * 
     * @return array
     */
    public function getAllDays()
    {
        if (is_null($this->allDays)) {
            $date = new DateTime();
            $this->allDays = [
                $date->getTimestamp()
            ];

            // @TODO
            //$nbDays = Mage::getStoreConfig('');
            $nbDays = 7;
            for ($cpt=1; $cpt < $nbDays; ++$cpt)
            {
                $date->add(new DateInterval('P1D'));
                $this->allDays[] = $date->getTimestamp();
            }
        }
        return $this->allDays;
    }

    /**
     * getAvailabilities 
     * 
     * @return array
     */
    protected function getAvailabilities()
    {
        $availabilities = [];
        $neighborhoods = $this->getNeighborhoodsAvailability();
        $shops = $this->getShopsAvailability();

        // [
        //      1 => available,
        //      2 => neighborhood not available for delivery
        //      3 => shop not available for delivery
        //      4 => shop in holidays
        // ]
        foreach ($this->getAllDays() as $time) {
            $day = date('w', $time);
            $status = 1;
            foreach ($neighborhoods as $websiteId => $neighborhood) {
                $status = 1;
                if (!in_array($day, $neighborhood['opening_days'])) {
                    $status = 2;
                }
                foreach ($shops as $commercantId => $shop) {
                    if ($status == 1) {
                        if (!in_array($day, $shop['delivery_days'])) {
                            $status = 3;
                        }
                        // @TODO : check holidays
                    }
                    $availabilities[$day][$websiteId . '_' . $commercantId] = [
                        'status' => $status,
                        'neighborhood_id' => $neighborhood['entity_id'],
                        'website_id' => $websiteId,
                        'commercant_id' => $commercantId
                    ];

                }
            }
        }

        return $availabilities;
    }
}
