<?php

/**
 * This file is part of the GardenMedia Mission Project.
 * 
 * @category Apdc
 *
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 *
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Block_Product_List_ProductLabels.
 * 
 * @category Apdc
 *
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 *
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 *
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Product_List_ProductLabels extends Mage_Core_Block_Template
{
    protected $allProductLabels = array();
    protected $maxProductLabels = 3;
    protected $months;
    protected $authorizedBio;
    protected $authorizeLabels;
    protected $maxNewDays;

    public function __construct()
    {
        parent::_construct();

        $this->maxNewDays= Mage::getStoreConfig('apdc_general/display/product_new');

        $this->months = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9,
            10 => 'O',
            11 => 'N',
            12 => 'D',
        );

        $this->authorizedBio = ['oui','Oui', 'AB', 'Bio Européen', 'AB,Bio Européen'];
        $this->authorizeLabels = Mage::helper('apdc_catalog/product_labels')->getAuthorizeLabels();
        $this->authorizeOrigine = Mage::helper('apdc_catalog/product_labels')->getAuthorizeOrigine();
    }

    
    /**
     * getAllProductLabels.
     * 
     * @return array
     */
    public function getAllProductLabels()
    {
        $product = $this->getProduct();
        $this->allProductLabels = array();
        if ($product && $product->getId() > 0) {
            $this->populateProductNew();
            $this->populateRupture();
            $this->populateBioLabel();
            $this->populateSaison();
            $this->populateOrigineLabel();
            $this->populateProductLabel();
        }

        return $this->allProductLabels;
    }

    /**
     * populateProductNew.
     */
    protected function populateProductNew()
    {
        
        $creation=new Zend_Date($this->getProduct()->getData('created_at'),'YYYY-MM-dd');
        $now=new Zend_Date(now(),'YYYY-MM-dd');
        $diff = $now->sub($creation)->toValue();
        $days = ceil($diff/60/60/24) +1;

        if ($days <= $this->maxNewDays) {
            $labelNew = array(
                 'text' => 'Nouveau Produit',
                 'icon' => $this->getSkinUrl(Mage::helper('apdc_catalog/product_labels')->getLabelNew()),
             );
        }

        if (!empty($labelNew)) {
            $this->allProductLabels[] = $labelNew;
        }
    }

    /**
     * populateOrigineLabel.
     */
    protected function populateOrigineLabel()
    {
        if ($this->getProduct()->getData('origine')) {
            $attributeValue = $this->getAttributeValue('origine');

            if (!is_array($attributeValue)&&$attributeValue <> false && array_key_exists($attributeValue, $this->authorizeOrigine)&&$this->authorizeOrigine[$attributeValue]<>"") {
                $productOrigine = array(
                    'text' => "Origine: ".$attributeValue,
                    'icon' => $this->getSkinUrl($this->authorizeOrigine[$attributeValue]),
                );
            }

            if (!empty($productOrigine)) {
                $this->allProductLabels[] = $productOrigine;
            }
        }
    }

    /**
     * populateBioLabel.
     */
    protected function populateBioLabel()
    {
        if ($this->getProduct()->getData('produit_biologique')) {
            $labelBio = array();
            $attributeValue = $this->getAttributeValue('produit_biologique');

            if ($attributeValue <> false && in_array($attributeValue, $this->authorizedBio)) {
                $labelBio = array(
                     'text' => 'Produit Biologique',
                     'icon' =>  $this->getSkinUrl(Mage::helper('apdc_catalog/product_labels')->getLabelBio()),
                 );
            }

            if (!empty($labelBio)) {
                $this->allProductLabels[] = $labelBio;
            }
        }
    }

    protected function buildProductLabel($attributeValue){
        if ($attributeValue <> false && array_key_exists($attributeValue, $this->authorizeLabels)) {
            $productLabel = array(
                'text' => $attributeValue,
                'icon' => $this->getSkinUrl($this->authorizeLabels[$attributeValue]),
            );
        }
        if (!empty($productLabel)) {
            $this->allProductLabels[] = $productLabel;
        }
    }

    /**
     * populateProductLabel.
     */
    protected function populateProductLabel()
    {
        if ($this->getProduct()->getData('labels_produits')) {
            $productLabel = array();
            $attributeValue = $this->getAttributeValue('labels_produits');
            if(!is_array($attributeValue)){
                $this->buildProductLabel($attributeValue);
            }else{
                foreach($attributeValue as $att){
                    $this->buildProductLabel($att);
                }
            }
        }
    }

    /**
     * populateProductLabel.
     */
    protected function populateSaison()
    {
        Mage::helper('apdc_commercant')->setLocaleFR();
        $saison=Mage::helper('apdc_referentiel/product')->getSaisonnalite($this->getProduct());
        if($saison){
            if (preg_match("/^[123456789OND]+$/", $saison)) {
                $timestamp = Mage::getSingleton('core/date')->timestamp();
                $currentmonth_name = date('M', $timestamp);
                $currentmonth = date('n', $timestamp);
                $test = $this->months[$currentmonth];

                if (in_array($test, str_split($saison))) {
                    $labelSaison = array(
                         'text' => 'Produit de saison',
                         'icon' => $this->getSkinUrl(Mage::helper('apdc_catalog/product_labels')->getLabelSaison()),
                     );
                }

                if (!empty($labelSaison)) {
                    $this->allProductLabels[] = $labelSaison;
                }
            }
        }
    }

    /**
     * populateProductLabel.
     */
    protected function populateRupture()
    {
        if ($risque_rupture = $this->getProduct()->getData('risque_rupture')) {
            $labelRupture = array(
                 'text' => 'Risque de rupture de stock',
                 'icon' => $this->getSkinUrl(Mage::helper('apdc_catalog/product_labels')->getLabelRupture()),
             );
        }

        if (!empty($labelRupture)) {
            $this->allProductLabels[] = $labelRupture;
        }
    }

    /**
     * getMaxProductLabels.
     * 
     * @return int
     */
    public function getMaxProductLabels()
    {
        return $this->maxProductLabels;
    }

    /**
     * setMaxProductLabels.
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
     * getAttributeValue.
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
