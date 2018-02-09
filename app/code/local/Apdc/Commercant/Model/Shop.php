<?php

/**
 * Class Apdc_Commercant_Model_Shop
 */
class Apdc_Commercant_Model_Shop extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'apdc_commercant_shop';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'shop';

    protected $shopUrl = null;
    protected $shopTypeCategory = null;
    protected $shopMainCategory = null;
    protected $allProductLinks = null;

    public function _construct()
    {
        $this->_init('apdc_commercant/shop');
    }

    /**
     * Return an array of day schedules
     * Each day is either false (shop is closed) or an array of two day times "open" and "close"
     * A day time is an array with two entries, hours and minutes
     *
     * @return array
     */
    public function getShopTimetable()
    {
        $timetable = [];
        foreach ($this->getData('timetable') as $daySchedule) {
            if (!preg_match('/(\d+):(\d+)-(\d+):(\d+)/', $daySchedule, $matches)) {
                $timetable[] = false;
            } else {
                $timetable[] = [
                    'open' => [$matches[1], $matches[2]],
                    'close' => [$matches[3], $matches[4]],
                ];
            }
        }

        return $timetable;
    }

    /**
     * loadByCategoryId 
     * 
     * @param int $categoryId categoryId 
     * 
     * @return Apdc_Commercant_Model_Shop
     */
    public function loadByCategoryId($categoryId)
    {
        $shopId = $this->getResource()->getShopIdByCategoryId($categoryId);
        return $this->load($shopId);
    }

    /**
     * getMainUrl 
     * 
     * @return string
     */
    public function getShopUrl()
    {
        if (is_null($this->shopUrl)) {
            $this->shopUrl = '';
            if ($shopMainCategory = $this->getShopMainCategory()) {
                $this->shopUrl = $shopMainCategory->getUrl();
            }
        }
        return $this->shopUrl;
    }

    /**
     * getAllProductsLink 
     * 
     * @return string
     */
    public function getAllProductsLink()
    {
        if (is_null($this->allProductLinks)) {
            $this->allProductLinks = '';
            
            if ($shopMainCategory = $this->getShopMainCategory()) {
                $children = $shopMainCategory->getChildrenCategories();
                foreach ($children as $childCat) {
                    if ($childCat->getName() == 'Tous les produits') {
                        $this->allProductLinks = $childCat->getUrl();
                        break;
                    }
                }
            }
        }
        return $this->allProductLinks;
    }

    /**
     * getShopTypeCategory
     * 
     * @return Mage_Catalog_Model_Category | false
     */
    public function getShopTypeCategory()
    {
        if (is_null($this->shopTypeCategory)) {
            $this->shopTypeCategory = false;
            $parent = Mage::app()->getStore()->getRootCategoryId();
            $categories = Mage::getModel('catalog/category')->getCategories($parent, 2, true, true, false);
            $categories->addFieldToFilter('level', 2);
            $categories->addFieldToFilter('name', $this->getShopType());
            if ($categories->count()) {
                $this->shopTypeCategory = $categories->getFirstItem();
            }
        }
        return $this->shopTypeCategory;
    }

    /**
     * getShopMainCategory 
     * 
     * @return Mage_Catalog_Model_Category | false
     */
    public function getShopMainCategory()
    {
        if (is_null($this->shopMainCategory)) {
            $this->shopMainCategory = false;
            
            if ($shopTypeCategory = $this->getShopTypeCategory()) {
                $path = $shopTypeCategory->getPath();
                $subPath = [];
                foreach ($this->getCategoryIds() as $catId) {
                    $subPath[] = $path . '/' . $catId;
                }
                $categories = Mage::getModel('catalog/category')->getCategories($shopTypeCategory->getId(), 3, true, true, false);
                $categories->addFieldToFilter('level', 3);
                $categories->addFieldToFilter('path', ['in' => $subPath]);

                if ($categories->count()) {
                    $this->shopMainCategory = Mage::getModel('catalog/category')->load($categories->getFirstItem()->getId());
                }
            }
        }
        return $this->shopMainCategory;
    }

    /**
     * getRootCategoryId 
     * 
     * @return int
     */
    public function getRootCategoryId()
    {
        return $this->getShopMainCategory()->getId();
    }

}
