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
 * Apdc_Partner_Model_Data_Products 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Data_Products extends Apdc_Partner_Model_Data
{
    protected $attributesLabel = null;
    protected $neighborhoods = null;

    /**
     * getList 
     * 
     * @return string
     */
    public function getList()
    {
        return $this->getProductsFile();
    }

    /**
     * generateProductsFile
     * 
     * @return void
     */
    public function generateProductsFile()
    {
        $products = $this->getProductsData();
        $fileName = Mage::getBaseDir() . DS . 'media' . DS . 'products.json';
        if ($file = fopen($fileName, 'w')) {
            if (!fwrite($file, json_encode($products))) {
                throw new Exception('Unable to generate products file');
            }
        }
    }

    /**
     * getProductsFile 
     * 
     * @return string
     */
    protected function getProductsFile()
    {
        $fileName = Mage::getBaseDir() . DS . 'media' . DS . 'products.json';
        if (!file_exists($fileName)) {
            throw new Exception('Products file is not generated');
        }
        return file_get_contents($fileName);
    }

    /**
     * getProductsData 
     * 
     * @return array
     */
    protected function getProductsData()
    {
        $productsData = [];
        try {
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addFieldToFilter('type_id', 'simple')
                ->addFieldToFilter('status', 1)
                ->addAttributeToSelect(['name','commercant','short_description','weight','price','produit_biologique','poids_portion', 'unite_prix', 'image']);
            $commercantAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product','commercant');

            $products->getSelect()->join(
                ['website' => $products->getTable('catalog/product_website')],
                'website.product_id = e.entity_id',
                ['website_ids' => 'GROUP_CONCAT(website.website_id)']
            );

            $products->getSelect()
                -> join( array('at_commercant' => $commercantAttribute->getBackendTable()),
                    'e.entity_id = at_commercant.entity_id',
                    array())
                    ->where('at_commercant.attribute_id = ?', $commercantAttribute->getId());

            $products->getSelect()->join(
                ['shop' => $products->getTable('apdc_shop')],
                'shop.id_attribut_commercant = at_commercant.value',
                ['shop_postcode' => 'postcode']
            );

            $products->getSelect()->group('e.entity_id');



            $products->load();
            //$cpt = 0;
            $attributes = $this->getAttributesLabel();
            $neighborhoods = $this->getNeighborhoods();
            foreach ($products as $prod) {
                $productsData[] = [
                    'entity_id' => $prod->getId(),
                    'sku' => $prod->getSku(),
                    'name' => $prod->getName(),
                    'poids_portion' => $prod->getPoidsPortion(),
                    'unite_prix' => $prod->getUnitePrix(),
                    'price' => $prod->getPrice(),
                    'produit_biologique' => $this->getAttributeValue('produit_biologique', $prod->getProduitBiologique()),
                    'commercant' => $this->getAttributeValue('commercant', $prod->getCommercant()),
                    'image' => ($prod->getImage() ? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $prod->getImage() : ''),
                    'quartier' => $this->getQuartiers(explode(',', $prod->getWebsiteIds())),
                    'postcodes' => $this->getPostcodes(explode(',', $prod->getWebsiteIds())),
                    'shop_postcode' => $prod->getShopPostcode()
                ];
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException($e);
        }
        return $productsData;
    }

    protected function getAttributesLabel()
    {
        if (is_null($this->attributesLabel)) {
            $this->attributesLabel = [];

            $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter(Mage::getResourceModel('catalog/product')->getTypeId())
                ->addFieldToFilter('attribute_code', ['in' => ['produit_biologique', 'commercant']]);

            foreach ($collection as $attribute) {
                $this->attributesLabel[$attribute->getAttributeCode()] = [];
                if ($attribute->getSource()) {
                    $options = $attribute->getSource()->getAllOptions();
                    foreach ($options as $option)
                    {
                        if ($option['value'] && $option['label']) {
                            $this->attributesLabel[$attribute->getAttributeCode()][$option['value']] = $option['label'];
                        }
                    }
                }
            }
        }
        return $this->attributesLabel;
    }

    /**
     * getAttributeValue 
     * 
     * @param string $code code 
     * @param int $optionValue optionValue 
     * 
     * @return string
     */
    protected function getAttributeValue($code, $optionValue)
    {
        $attributesLabel = $this->getAttributesLabel();
        if (isset($attributesLabel[$code]) && isset($attributesLabel[$code][$optionValue])) {
            return $attributesLabel[$code][$optionValue];
        }
        return '';
    }

    /**
     * getNeighborhoods 
     * 
     * @return array
     */
    protected function getNeighborhoods()
    {
        if (is_null($this->neighborhoods)) {
            $this->neighborhoods = [];
            $datas = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();
            $datas->load();
            foreach ($datas as $data) {
                $this->neighborhoods[$data->getWebsiteId()] = $data->getData();
                $this->neighborhoods[$data->getWebsiteId()]['postcodes'] = unserialize($data->getPostcodes());
            }
        }
        return $this->neighborhoods;
    }

    /**
     * getQuartiers
     * 
     * @param array $websiteIds websiteIds 
     * 
     * @return array
     */
    protected function getQuartiers($websiteIds)
    {
        $quartiers = [];
        $neighborhoods = $this->getNeighborhoods();
        foreach ($websiteIds as $websiteId) {
            if (isset($neighborhoods[$websiteId])) {
                $quartiers[] = $neighborhoods[$websiteId]['name'];
            }
        }
        return $quartiers;
    }

    /**
     * getPostcodes 
     * 
     * @param array $websiteIds websiteIds 
     * 
     * @return array
     */
    protected function getPostcodes($websiteIds)
    {
        $postcodes = [];
        $neighborhoods = $this->getNeighborhoods();
        foreach ($websiteIds as $websiteId)
        {
            if (isset($neighborhoods[$websiteId])) {
                $postcodes = array_merge($postcodes, $neighborhoods[$websiteId]['postcodes']);
            }
        }
        return $postcodes;
    }
}
