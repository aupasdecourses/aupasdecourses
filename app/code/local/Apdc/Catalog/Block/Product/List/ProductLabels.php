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
    protected $maxProductLabels = 4;
    protected $months;
    protected $authorizedBio;
    protected $authorizeLabels;
    protected $maxNewDays = 25;

    public function __construct()
    {
        parent::_construct();

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

        $this->authorizedBio = ['Oui', 'AB', 'Bio Européen', 'AB,Bio Européen'];
        $this->authorizeLabels = [
            'AOC' => 'images/labels/AOC.png',
            'DOC' => 'images/labels/AOC.png',
            'DOP' => 'images/labels/AOC.png',
            'AOP' => 'images/labels/AOC.png',
            'IGP' => 'images/labels/AOC.png',
            'Demeter' => 'images/labels/demeter.png',
            'Label Rouge' => 'images/labels/labelrouge.png',
            'Casher' => 'images/labels/casher.png',
            'Kasher' => 'images/labels/casher.png',
            'Halal' => 'images/labels/halal.png',
        ];
        $this->authorizeOrigine = [
                "Afrique du Sud" => "",
                "Algérie" => "",
                "Allemagne" => "images/labels/countries/europe.png",
                "Angleterre" => "images/labels/countries/europe.png",
                "Antilles" => "",
                "Argentine" => "",
                "Atlantique " => "",
                "Atlantique Centre-Est" => "",
                "Atlantique Nord" => "",
                "Atlantique Nord-Est" => "",
                "Australie" => "",
                "Autriche" => "images/labels/countries/europe.png",
                "Belgique" => "images/labels/countries/europe.png",
                "Bénin" => "",
                "Biélorussie" => "",
                "Brésil" => "",
                "Bulgarie" => "images/labels/countries/europe.png",
                "Cameroun" => "",
                "Canada" => "",
                "Chili" => "",
                "Chine" => "",
                "Colombie" => "",
                "Costa Rica" => "",
                "Côte d'Ivoire" => "",
                "Crète" => "images/labels/countries/europe.png",
                "Cuba" => "",
                "Danemark" => "images/labels/countries/europe.png",
                "Ecosse" => "images/labels/countries/europe.png",
                "Egypte" => "",
                "Equateur" => "",
                "Espagne" => "images/labels/countries/europe.png",
                "Ethiopie" => "",
                "Europe" => "images/labels/countries/europe.png",
                "France" => "images/labels/countries/france.png",
                "Ghana" => "",
                "Grande Bretagne" => "images/labels/countries/europe.png",
                "Grèce" => "images/labels/countries/europe.png",
                "Hollande" => "images/labels/countries/europe.png",
                "Honduras" => "",
                "Ile Maurice" => "",
                "Iran" => "",
                "Irlande" => "images/labels/countries/europe.png",
                "Islande " => "images/labels/countries/europe.png",
                "Israël" => "",
                "Italie " => "images/labels/countries/europe.png",
                "Japon" => "",
                "Jordanie" => "",
                "Kenya" => "",
                "La Réunion" => "images/labels/countries/france.png",
                "Lituanie" => "images/labels/countries/europe.png",
                "Madagascar" => "",
                "Manche" => "",
                "Maroc" => "",
                "Martinique" => "images/labels/countries/france.png",
                "Méditerranée" => "",
                "Mer Baltique" => "",
                "Mexique" => "",
                "Norvège" => "images/labels/countries/europe.png",
                "Nouvelle-Zélande" => "",
                "Océan Indien" => "",
                "Ouganda" => "",
                "Pacifique Nord" => "",
                "Pays-Bas" => "images/labels/countries/europe.png",
                "Pérou" => "",
                "Pologne" => "images/labels/countries/europe.png",
                "Portugal" => "images/labels/countries/europe.png",
                "République Dominicaine" => "",
                "République Tchèque" => "images/labels/countries/europe.png",
                "Réunion" => "images/labels/countries/france.png",
                "Russie" => "",
                "Sardaigne" => "images/labels/countries/europe.png",
                "Sénégal" => "",
                "Sicile" => "images/labels/countries/europe.png",
                "Suisse" => "images/labels/countries/europe.png",
                "Togo" => "",
                "Tunisie" => "",
                "Turquie" => "",
                "Uruguay" => "",
                "Venezuela" => "",
                "Vietnam" => "",
        ];
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
                 'icon' => $this->getSkinUrl('images/labels/new.png'),
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

            if (array_key_exists($attributeValue, $this->authorizeOrigine)) {

                $productOrigine = array(
                    'text' => $attributeValue,
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

            if (in_array($attributeValue, $this->authorizedBio)) {
                $labelBio = array(
                     'text' => 'Produit Biologique',
                     'icon' => $this->getSkinUrl('images/labels/bio.png'),
                 );
            }

            if (!empty($labelBio)) {
                $this->allProductLabels[] = $labelBio;
            }
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
            if (array_key_exists($attributeValue, $this->authorizeLabels)) {
                $productLabel = array(
                    'text' => $attributeValue,
                    'icon' => $this->getSkinUrl($this->authorizeLabels[$attributeValue]),
                );
            }

            if (!empty($productLabel)) {
                $this->allProductLabels[] = $productLabel;
            }
        }
    }

    /**
     * populateProductLabel.
     */
    protected function populateSaison()
    {
        if ($saison = $this->getProduct()->getData('saisonnalite')) {
            $timestamp = Mage::getSingleton('core/date')->timestamp();
            $currentmonth_name = date('M', $timestamp);
            $currentmonth = date('m', $timestamp);
            $test = $this->months[$currentmonth];

            if (in_array($test, str_split($saison))) {
                $labelSaison = array(
                     'text' => 'Produit de saison',
                     'icon' => $this->getSkinUrl('images/labels/saison.png'),
                 );
            }

            if (!empty($labelSaison)) {
                $this->allProductLabels[] = $labelSaison;
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
                 'icon' => $this->getSkinUrl('images/labels/rutpure.png'),
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
