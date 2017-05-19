<?php

namespace Apdc\ApdcBundle\Services;

trait Products
{
    public $_attributeArraysLabels;
    public $_attributeArraysIds;

    /**
     * Return an array Id => Label for the specified attributes.
     *
     * @param array attributeCodes
     * list of attributes codes to process 
     *
     * @return array
     */
    public function getAttributesLabelFromId($attributeCodes)
    {
        $attributeArray = array();

        foreach ($attributeCodes as $code) {
            $attribute = \Mage::getModel('eav/config')->getAttribute('catalog_product', $code);
            foreach ($attribute->getSource()->getAllOptions(true, true) as $option) {
                $attributeArray[$code][$option['value']] = $option['label'];
            }
        }

        return $attributeArray;
    }

    /**
     * Return an array Label => Id for the specified attributes.
     *
     * @param array attributeCodes
     * list of attributes codes to process 
     *
     * @return array
     */
    public function getAttributesIdFromLabel($attributeCodes)
    {
        $attributeArray = array();

        foreach ($attributeCodes as $code) {
            $attribute = \Mage::getModel('eav/config')->getAttribute('catalog_product', $code);
            foreach ($attribute->getSource()->getAllOptions(true, true) as $option) {
                $attributeArray[$code][$option['label']] = $option['value'];
            }
        }

        return $attributeArray;
    }

    /*
    * 	@param int $size
    *   Taille de la page de la collection d'objet (limite)
    * 	@param int $page
    *   Indique le numéro de la page de la collection que l'on souhaite afficher (offset)
    * 	@param string $order_param
    *   Paramètre utilisé pour le tri. "name" par défaut
    * 	@param string $order
    *   Sens du tri. Deux valeurs possibles:  ASC ou DESC
    * 	@param string $commercant
    *   Correspond au nom de l'attribut commercant_value (ce sera mieux d'utiliser l'id commercant utilisé dans les tables)
    * 	@param string $name
    *   Nom du produit 
    *	@return Mage_Collection
    */
    public function getProductsList($size = 20, $page = 1, $order_param = 'name', $order = 'ASC', $commercant = null, $name = null)
    {
        $collection = \Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $collection->addAttributeToSort($order_param, $order);

        if ($commercant != null) {
            // $attributeCode = 'commercant';
            // $alias = 'commercant'.'_table';
            // $attribute = \Mage::getSingleton('eav/config')->getAttribute(\Mage_Catalog_Model_Product::ENTITY, 'commercant');
            // $collection->getSelect()
            // 	->join(
            // 		array($alias => $attribute->getBackendTable()),
            // 		"e.entity_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
            // 		array($attributeCode => 'value')
            // 	)
            // ->join(array('shop_id'=> 'apdc_shop'),$alias.'.value = shop_id.id_attribut_commercant', array('name_commercant'=>"shop_id.name"));
            // $collection->addFilterToMap('name_commercant', 'shop_id.name');
            //$collection->addFieldToFilter('name_commercant', "Boucherie des Moines");

            $attributeArrays = $this->_attributeArraysIds;

            $collection->addFieldToFilter('commercant', $attributeArrays['commercant'][$commercant]);
        }

        if ($name != null) {
            $collection->addFieldToFilter('name', $name);
        }

        $collection->setPageSize($size)
                    ->setCurPage($page);

        return $collection;
    }

    /**
     * Get Product info.
     *
     * @param int $entity_id id of product
     *
     * @return array list of product attributes with value
     */
    public function getProduct($entity_id)
    {
        $product = \Mage::getModel('catalog/product')->load($entity_id)->toArray();

        $product['produit_biologique'] = $this->_attributeArraysLabels['produit_biologique'][$product['produit_biologique']];
        $product['produit_de_saison'] = $this->_attributeArraysLabels['produit_de_saison'][$product['produit_de_saison']];

        return $product;
    }

    /**
     * Update product in Magento tables.
     *
     * @param int   $entity_id if of product
     * @param array $data      list of product attributes to update
     *                         "name" string
     *                         "produit_biologique" string "Oui" or "Non"
     *                         "reference_interne_magasin" string
     *                         "poids_portion" string
     *                         "unite_prix" string kg / piece / ...
     *                         "prix_public" float
     *                         "status" boolean
     *                         "commercant" int (à ne pas intégrer pour le moment)
     *                         "on_selection" boolean
     *                         "produit_de_saison" string "Oui" or "Non"
     *                         "short_description" string
     *                         "nbre_portion" int
     *                         "tax_class_id" int 5, 9 or 10
     *
     * @return bool Whether the update was performed successfully or not
     */
    public function updateProduct($entity_id, $data)
    {
        \Mage::app()->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
        $product = \Mage::getModel('catalog/product')->load($entity_id);

        $attributeArrays = $this->_attributeArraysIds;

        try {

            //Convert specific attribute codes value to EAV id
            foreach (self::ATTRIBUTE_CODES as $code) {
                if ($code != 'commercant') {
                    $data[$code] = $this->_attributeArraysIds[$key][$data[$code]];
                }
            }

            //calculate price
            if (strtolower($data['unite_prix']) == 'kg') {
                $data['price'] = $data['poids_portion'] * $data['prix_public'] * $data['nbre_portion'];
            } else {
                $data['price'] = $data['prix_public'] * $data['nbre_portion'];
            }
            $data['prix_kilo_site'] = $data['prix_public'].'€/'.$data['unite_prix'];

            //meta_title
            $data['meta_title'] = $data['name'].' - Au Pas De Courses';
            $data['meta_description'] = $data['name'].' - Au Pas De Courses - '.$data['short_description'];
            $data['image_label'] = $data['small_image_label'] = $data['thumbnail_label'] = $data['name'];

            //poids
            $data['weight'] = $data['poids_portion'] * $data['nbre_portion'];
            foreach ($data as $key => $value) {
                if ($key != 'commercant') {
                    $product->setData($key, $value);
                }
            }

            $product->setUpdatedAt(strtotime('now'));

            $product->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Create product in Magento tables.
     *
     * @param array $data      list of product attributes to create
     *                         "name" string
     *                         "produit_biologique" string "Oui" or "Non"
     *                         "reference_interne_magasin" string
     *                         "poids_portion" string
     *                         "unite_prix" string kg / piece / ...
     *                         "prix_public" float
     *                         "status" boolean
     *                         "commercant" int (à ne pas intégrer pour le moment)
     *                         "on_selection" boolean
     *                         "produit_de_saison" string "Oui" or "Non"
     *                         "short_description" string
     *                         "nbre_portion" int
     *                         "tax_class_id" int 5, 9 or 10
     *
     * @return bool Whether the update was performed successfully or not
     */
    public function createProduct($data)
    {
        \Mage::app()->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
        $product = \Mage::getModel('catalog/product');

        try {

        //find websiteId
        $data_commercant = $this->getMerchants($data['commercant']);
            $websiteIds = array();
            $datas = array();
            foreach ($data_commercant as $store_id => $data) {
                array_push($websiteIds, $store_id);
                array_push($datas, $data);
            }
        $data['website_ids']=$websiteIds;
        $data['attribute_set_id']=4;
        $data['type_id']='simple';
        $data['created_at']=strtotime('now');
        $data['visibility']=\Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH;

        //Calculate SKU
        $data['sku'] = $datas[0]['code'].'-'.$data['name'].'-'.(1000 + round(100 * rand(), 0));

        //Convert specific attribute codes value to EAV id
        foreach (self::ATTRIBUTE_CODES as $code) {
            if ($code != 'commercant') {
                $data[$code] = $this->_attributeArraysIds[$key][$data[$code]];
            }
        }

        //calculate price
        if (strtolower($data['unite_prix']) == 'kg') {
            $data['price'] = $data['poids_portion'] * $data['prix_public'] * $data['nbre_portion'];
        } else {
            $data['price'] = $data['prix_public'] * $data['nbre_portion'];
        }
        $data['prix_kilo_site'] = $data['prix_public'].'€/'.$data['unite_prix'];

        //meta_title
        $data['meta_title'] = $data['name'].' - Au Pas De Courses';
        $data['meta_description'] = $data['name'].' - Au Pas De Courses - '.$data['short_description'];
        $data['image_label'] = $data['small_image_label'] = $data['thumbnail_label'] = $data['name'];

        //poids
        $data['weight'] = $data['poids_portion'] * $data['nbre_portion'];
        foreach ($data as $key => $value) {
            if ($key != 'commercant') {
                $product->setData($key, $value);
            }
        }

        foreach ($data as $key => $value) {
            if ($key != 'commercant') {
                $product->setData($key, $value);
            }
        }

        $product->save();
        
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }
    }
}
