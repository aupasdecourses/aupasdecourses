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
 * Apdc_Catalog_Block_Product_List_ProductLabels 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Product_List_ProductLabels extends Mage_Core_Block_Template
{
    protected $allProductLabels = array();
    protected $maxProductLabels = 3;

    /**
     * getAllProductLabels 
     * 
     * @return array
     */
    public function getAllProductLabels()
    {
        $product = $this->getProduct();
        $this->allProductLabels = array();
        if ($product && $product->getId() > 0) {
            $this->populateOrigineLabel();
            $this->populateBioLabel();
            $this->populateProductLabel();
        }
        return $this->allProductLabels;
    }

    /**
     * populateOrigineLabel 
     * 
     * @return void
     */
    protected function populateOrigineLabel()
    {
        if ($this->getProduct()->getData('origine')) {
            $origineLabel = array();
            $attributeValue = $this->getAttributeValue('origine');
            if(is_array($attributeValue)){
                $attributeValue=implode(" ",$attributeValue);
            }
            if (strpos($attributeValue, 'France')) {
                $origineLabel = array(
                    'text' => $attributeValue,
                    'icon' => $this->getSkinUrl('images/france_flag_icon.png')
                );
            }
            if (!empty($origineLabel)) {
                $this->allProductLabels[] = $origineLabel;
            }
        }
    }

    /**
     * populateBioLabel 
     * 
     * @return void
     */
    protected function populateBioLabel()
    {
        if ($this->getProduct()->getData('produit_biologique')) {
            $labelBio = array();
            $authorizedBio = ['Oui','AB','Bio Européen','AB,Bio Européen'];
            $attributeValue = $this->getAttributeValue('produit_biologique');

             if(in_array($attributeValue, $authorizedBio)) {
                 $labelBio = array(
                     'text' => $attributeValue,
                     'icon' => $this->getSkinUrl('images/logo_ab_petit.png')
                 );
            }

            if (!empty($labelBio)) {
                $this->allProductLabels[] = $labelBio;
            }
        }
    }

    /**
     * populateProductLabel 
     * 
     * @return void
     */
    protected function populateProductLabel()
    {
        if($this->getProduct()->getData('labels_produits')) {
            $productLabel = array();

            $attributeValues = $this->getAttributeValue('labels_produits');
            if (is_array($attributeValues)) {
                $productLabel = array(
                    'text' => $attributeValues[0]
                );
            } else {
                $productLabel = array(
                    'text' => $attributeValues
                );
            }

            if (!empty($productLabel)) {
                $this->allProductLabels[] = $productLabel;
            }
        }
    }

    /**
     * getMaxProductLabels 
     * 
     * @return int
     */
    public function getMaxProductLabels()
    {
        return $this->maxProductLabels;
    }

    /**
     * setMaxProductLabels 
     * 
     * @param int $max : max 
     * 
     * @return Apdc_Catalog_Block_Product_List_ProductLabels
     */
    public function setMaxProductLabels($max)
    {
        $this->maxProductLabels = (int) $max;
        return $this;
    }

    /**
     * getAttributeValue 
     * 
     * @param string $attributeCode : attributeCode 
     * 
     * @return string
     */
    protected function getAttributeValue($attributeCode)
    {
        return Mage::getResourceSingleton('catalog/product')
            ->getAttribute($attributeCode)
            ->getSource()
            ->getOptionText($this->getProduct()->getData($attributeCode));
    }
}
