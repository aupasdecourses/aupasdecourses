<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Partner_Model_Partner_Abstract 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
abstract class Apdc_Partner_Model_Partner_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $product;

    protected $allWebsiteIds = null;

    protected $_shop = null;
    protected $_commercantId = -1;
    protected $_commercantSkuPrefix = null;

    /**
     * _initProductData 
     * 
     * @return void
     */
    abstract protected function _initProductData();

    /**
     * getProduct
     * 
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->getData()) {
            Mage::throwException('You must set data before getting the product');
        }
        $this->checkSku();
        $this->product = Mage::getModel('catalog/product');
        $this->product->setData($this->getData());
        $this->product->setStoreId(0);

        $this->setCommonProductData();
        $this->_initProductData();
        $this->importImages();

        return $this->product;
    }

    /**
     * setCommonProductData
     * 
     * @return void
     */
    protected function setCommonProductData()
    {
        if ($this->product->getDescription()) {
            $description = $this->product->getDescription();
        } else if ($this->product->getShortDescription()) {
            $description =  $this->product->getShortDescription();
        } else {
            $description = $this->product->getName();
        }
        $this->product->setDescription(nl2br($description));
        $this->product->setShortDescription(strip_tags(nl2br($description)));
        if (!$this->product->getWeight()) {
            $this->product->setWeight(1);
        }
        $this->product->setAttributeSetId(4);
        $this->product->setTypeId('simple');
        $this->product->setTaxClassId(5);
        $this->product->setStatus(1);
        $this->product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        //$this->product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        $this->product->setAvailabilityDays([1,2,3,4,5,6,7]);
        $this->product->setCanOrderDays([1,2,3,4,5,6,7]);
        $this->product->setWebsiteIds($this->getShopWebsiteIds());
        if ($this->_commercantId == -1) {
            Mage::throwException('You have to define your commercant ID in your Partner class');
        }
        $this->product->setCommercant($this->_commercantId);
        $this->product->setPrixPublic($this->product->getPrice());
        if (!$this->product->getUnitePrix()) {
            $this->product->setUnitePrix('pièce');
        }
        $this->product->setPrixKiloSite($this->product->getPrice() . '€/' . $this->product->getUnitePrix());
        $this->product->setMargeArriere($this->_margeArriere);
        $this->product->setStockData([
            'use_config_manage_stock' => 1,
            'manage_stock' => 0,
            'is_in_stock' => 1
        ]);
    }

    /**
     * importImages 
     * 
     * @return void
     */
    protected function importImages()
    {
        if ($this->getData('images')) {
            $cpt = 1;
            foreach ($this->getData('images') as $image) {
                $imagePath = $this->downloadImage($image);
                $this->product->addImageToMediaGallery($imagePath, ['image', 'thumbnail', 'small_image'], true, false);
                if ($cpt == 1) {
                    foreach ($this->product->getMediaGalleryImages() as $img) {
                        $this->product->setImage($img->getFile())
                            ->setSmallImage($img->getFile())
                            ->setThumbnail($img->getFile());
                    }
                }
                $cpt++;
            }
        }
    }

    /**
     * downloadImage
     * 
     * @param string $imageUrl imageUrl 
     * 
     * @return string
     */
    protected function downloadImage($imageUrl)
    {
        $downloadDir = Mage::getBaseDir('media') . DS . 'apdc' . DS . 'apdc_partner';
        if (!file_exists($downloadDir)) {
            mkdir($downloadDir, 0775, true);
        }
        $filename = basename($imageUrl);
        $filePath = $downloadDir . $filename;
        if(!file_exists($filePath)){
            try {
                file_put_contents($filePath,file_get_contents($imageUrl));
            } catch (Exception $e) {
                Mage::throwException($e);
            }
        }
        return $filePath;
    } 

    /**
     * checkSku
     * 
     * @return void
     */
    public function checkSku()
    {
        $shop = $this->getShop();
        $sku = $this->getSku();
        if (!preg_match('/^' . $shop->getCode() . '/', $sku)) {
            $this->setSku($shop->getCode() . '-' . $sku);
        }
    }

    /**
     * getShop
     * Try to determine which shop to use for the product
     * 
     * @return Apdc_Commercant_Model_Shop
     */
    public function getShop()
    {
        if (is_null($this->_shop)) {
            $postcode = $this->getPostcode();
            $externalIds = $this->getExternalIds();
            $collection = Mage::getModel('apdc_commercant/shop')->getCollection()
                ->addFieldToFilter('postcode', $postcode)
                ->addFieldToFilter('external_id', ['in' => $externalIds]);

            if (!$collection->count()) {
                Mage::throwException('Unable to find associated shop');
            }
            $this->_shop = $collection->getFirstItem();
        }
        return $this->_shop;
    }

    /**
     * getExternalIds
     * 
     * @return array
     */
    public function getExternalIds()
    {
        $ids = null;
        if ($this->hasData('external_id')) {
            $ids = $this->getData('external_id');
        } else if ($this->hasData('external_ids')) {
            $ids = $this->getData('external_ids');
        }
        if (is_null($ids)) {
            Mage::throwException('Unable to determine shop external id');
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        return $ids;
    }

    /**
     * getShopWebsiteIds
     * 
     * @return array
     */
    protected function getShopWebsiteIds()
    {
        if (is_null($this->allWebsiteIds)) {
            $shop = $this->getShop();
            $shopStoreIds = explode(',', $shop->getStores());
            $this->allWebsiteIds = [];
            $websites = Mage::app()->getWebsites(false);
            foreach ($websites as $website) {
                if (in_array($website->getDefaultStore()->getId(), $shopStoreIds)) {
                    $this->allWebsiteIds[] = $website->getId();
                }
            }
        }
        return $this->allWebsiteIds;
    }

    /**
     * getPostcode
     * 
     * @return string
     */
    public function getPostcode()
    {
        if (!$this->hasPostcode()) {
            Mage::throwException('You must set the postcode before');
        }
        return $this->getData('postcode');
    }
        
}
