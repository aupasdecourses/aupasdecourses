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
    protected $childPerParent = null;
    protected $parentPerChild = null;

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

    public function generateProductsAvailabilitiesByNeighborhood(Apdc_Neighborhood_Model_Neighborhood $neighborhood)
    {
        $websiteId = $neighborhood->getWebsiteId();
        $productIds = [];
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addWebsiteFilter($websiteId);
        $products->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $products->getSelect()->columns(['entity_id']);
        $productIds = $products->getColumnValues('entity_id');           

        if (!empty($productIds)) {
            $this->generateProductsAvailabilities($productIds);
        }
    }

    public function generateProductsAvailabilitiesByShop(Apdc_Commercant_Model_Shop $shop)
    {
        $productIds = [];
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->joinAttribute(
                'commercant_id',
                'catalog_product/commercant',
                'entity_id'
            );
        $products->getSelect()->where('at_commercant_id.value = ?', $shop->getIdAttributCommercant());
        $products->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $products->getSelect()->columns(['entity_id']);
        $productIds = $products->getColumnValues('entity_id');           

        if (!empty($productIds)) {
            $this->generateProductsAvailabilities($productIds);
        }
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
                    if($childs<>null){
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
                    }

                } else {
                    $key = $product['website_id'] . '_' . $product['commercant_id'];
                    $productId = $product['entity_id'];
                    if (!isset($availabilities[$day][$key])) {
                        if (!isset($errors[$productId . $key])) {
                            $errors[$productId . $key] = $product['entity_id'] . ' / website : ' . $product['website_id'] . ' / commercant ; ' . $product['commercant_id'];
                        }
                        continue;
                    } else if ($availabilities[$day][$key]['status'] == -1) {
                        if (!isset($warnings[$productId . $key])) {
                            $warnings[$product['website_id']] = 'Les produits ne sont plus disponible pour le website : ' . (int) $product['website_id'];
                        }
                        continue;
                    }
                    $available = $availabilities[$day][$key];

                    // Check product availability_days
                    if ($product['availability_days']) {
                        $productDays = explode(',', $product['availability_days']);
                        if (empty($productDays) || !in_array($day, $productDays)) {
                            $available['status'] = 5;
                        }
                    }
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
        if (!empty($warnings)) {
            foreach ($warnings as $warning) {
                Mage::getSingleton('adminhtml/session')->addWarning($warning);
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
        $productCollection->joinAttribute(
            'availability_days',
            'catalog_product/availability_days',
            'entity_id',
            null,
            'left'
        );
        $productCollection->getSelect()->join(
            ['pw' => $this->getResource()->getTableName('catalog/product_website')],
            'pw.product_id = e.entity_id',
            ['pw.website_id']
        );
        if (!empty($this->getProductIds())) {
            $productIds = $this->getProductIds();
            $parentPerChild = $this->getParentPerChild();
            if (!empty($parentPerChild)) {
                foreach ($parentPerChild as $parents) {
                    $productIds = array_merge($productIds, $parents);
                }
                array_unique($productIds);
                $this->setProductIds($productIds);
            }

            $childPerParent = $this->getChildPerParent();
            if (!empty($childPerParent)) {
                foreach ($childPerParent as $childs) {
                    $productIds = array_merge($productIds, $childs);
                }
                array_unique($productIds);
                $this->setProductIds($productIds);
            }

            $productCollection->addFieldToFilter('entity_id', ['in' => $this->getProductIds()]);
        }

        return $productCollection;
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
            $childPerParent = $this->getResource()->getChildPerParent($this);
            foreach ($childPerParent as $relation) {
                if (!isset($this->childPerParent[$relation['parent_id']])) {
                    $this->childPerParent[$relation['parent_id']] = [];
                }
                $this->childPerParent[$relation['parent_id']][] = $relation['child_id'];
            }
        }
        if ($productId && (int)$productId > 0) {
            if (isset($this->childPerParent[(int)$productId])) {
                return $this->childPerParent[(int)$productId];
            }
        }
        return $this->childPerParent;
    }

    /**
     * getParentPerChild 
     * 
     * @return array
     */
    protected function getParentPerChild()
    {
        if (is_null($this->parentPerChild)) {
            $parentPerChild = $this->getResource()->getParentPerChild($this);
            foreach ($parentPerChild as $relation) {
                if (!isset($this->parentPerChild[$relation['child_id']])) {
                    $this->parentPerChild[$relation['child_id']] = [];
                }
                $this->parentPerChild[$relation['child_id']][] = $relation['parent_id'];
            }
        }
        return $this->parentPerChild;
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
                    'an.website_id',
                    'an.is_active'
                ]
            );
            $collection->getSelect()->group('an.entity_id');

            foreach ($collection as $neighborhood) {
                $id = $neighborhood->getWebsiteId();
                $this->neighborhoods[$id] = $neighborhood->getData();
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

            $nbDays = 7 * (int) Mage::getStoreConfig('ddate/info/weeks');
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
        //      -1 => neighborhood is not activated
        //      1 => available,
        //      2 => Service not available
        //      3 => shop not available for delivery
        //      4 => shop in holidays
        //      5 => product not available for delivery
        // ]
        foreach ($this->getAllDays() as $time) {
            $day = date('w', $time);
            $date = date('Y-m-d', $time);
            $status = 1;
            foreach ($neighborhoods as $websiteId => $neighborhood) {
                if ($neighborhood['is_active']) {
                    $serviceStatus = $status;
                    $specialDays = $this->getSpecialDays($websiteId);
                    if (in_array($date, $specialDays)) {
                        $serviceStatus = 2;
                    }
                } else {
                    $serviceStatus = -1;
                }
                foreach ($shops as $commercantId => $shop) {
                    if ($serviceStatus == -1) {
                        $availabilities[$day][$websiteId . '_' . $commercantId] = [
                            'status' => -1
                        ];
                        continue;
                    }
                    $finalStatus = $serviceStatus;
                    if ($serviceStatus == 1) {
                        if (!empty($shop['closing_periods'])) {
                            foreach ($shop['closing_periods'] as $period) {
                                if ($date >= $period['start'] && $date <= $period['end']) {
                                    $finalStatus = 4;
                                    break;
                                }
                            }

                        }

                        if ($finalStatus<>4 && !in_array($day, $shop['delivery_days'])) {
                            $finalStatus = 3;
                        }
                    }
                    $availabilities[$day][$websiteId . '_' . $commercantId] = [
                        'status' => $finalStatus,
                        'neighborhood_id' => $neighborhood['entity_id'],
                        'website_id' => $websiteId,
                        'commercant_id' => $commercantId
                    ];
                }
            }
        }
        return $availabilities;
    }

    /**
     * getSpecialDays 
     * 
     * @param int $websiteId websiteId 
     * 
     * @return array
     */
    protected function getSpecialDays($websiteId)
    {
        $website = Mage::getModel('core/website')->load($websiteId);
        $specialDays = [];
        $list = trim(Mage::getStoreConfig('ddate/info/special_days', $website->getDefaultStore()->getId()));
        if ($list) {
            $list = explode(';', $list);
            foreach ($list as $date) {
                if ($date) {
                    $specialDays[] = Mage::helper('ddate')->validateDate($date);
                }
            }
        }

        return $specialDays;
    }
}
