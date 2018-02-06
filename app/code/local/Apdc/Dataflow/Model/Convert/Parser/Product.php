<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Dataflow
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Dataflow_Model_Convert_Parser_Product 
 * 
 * @category Apdc
 * @package  Dataflow
 * @uses     Mage
 * @uses     Mage_Catalog_Model_Convert_Parser_Product
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Dataflow_Model_Convert_Parser_Product extends Mage_Catalog_Model_Convert_Parser_Product
{

    /**
     * Unparse (prepare data) loaded products
     *
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function unparse()
    {
        $entityIds = $this->getData();


        $linksRows = $this->_prepareLinks($entityIds);
        $linkIdColPrefix = array(
            Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED   => 're_skus',
            Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL    => 'us_skus',
            Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL => 'cs_skus',
            Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED   => 'gr_skus'
        );

        foreach ($entityIds as $i => $entityId) {
            $product = $this->getProductModel()
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setProductTypeInstance($product);
            /* @var $product Mage_Catalog_Model_Product */

            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => '',
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                                        $product->getAttributeSetId()),
                'type'          => $product->getTypeId(),
                'us_skus' => '',
                'cs_skus' => '',
                're_skus' => '',
                'gr_skus' => ''
            );

            //Stores


            //if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
            $websiteCodes = array();
            $storeCodes = array();
            foreach ($product->getWebsiteIds() as $websiteId) {
                $website=Mage::app()->getWebsite($websiteId);
                $websiteCode = $website->getCode();
                $websiteCodes[$websiteCode] = $websiteCode;

                $storeids = $website->getStoreIds();
                foreach($storeids as $id){
                    $code=Mage::getModel('core/store')->load($id)->getCode();
                    $storeCodes[$code] = $code;
                }
            }
            $row['websites'] = join(',', $websiteCodes);
            $row['store'] = 'admin,'.join(',', $storeCodes);
            //} else {
            //    $row['websites'] = $this->getStore()->getWebsite()->getCode();
            //    if ($this->getVar('url_field')) {
            //        $row['url'] = $product->getProductUrl(false);
            //    }
            //}
            $row['_product_websites'] = $row['websites'];

            foreach ($linkIdColPrefix as $linkId => &$colPrefix) {
                if (!empty($linksRows[$entityId][$linkId])) {
                    $rowLinks = array();
                    foreach ($linksRows[$entityId][$linkId] as $link) {
                        $rowLinks[] = $link['sku'];

                    }
                    if (!empty($rowLinks)) {
                        $row[$colPrefix] = implode(',', $rowLinks);
                    }

                }
            }

            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }
                if ($field == 'tax_class_id'||$field == 'produit_fragile'||$field == 'risque_rupture') {
                    $row[$field] = $value;
                } else if ($field == 'status'){
                    switch($value){
                        case "1":
                            $row[$field]=1;
                            break;
                        case "0":
                            $row[$field]=2;
                            break;
                        case "2":
                            $row[$field]=2;
                            break;
                    }
                    continue;
                }else if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }

                if ($field == 'image') {
                    $row[$field] = basename($value);
                } else {
                    $row[$field] = $value;
                }
            }

            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }


            if($product->getHasOptions()) {
                foreach ($product->getOptions() as $option) {
                    $optionColumn = $option->getDefaultTitle() . ':' . $option->getType() . ':' . (int)$option->getIsRequire();
                    $optionType = $option->getType();

                    $values = $option->getValues();
                    if ($values) {
                        $rowValues = [];

                        foreach ($values as $value) {
                            $rowValues[] = trim($value->getDefaultTitle());
                        }
                        $row[$optionColumn] = implode('|', $rowValues);
                    } else {
                        $row[$optionColumn] = 1;
                    }
                }
            }

            $productMediaGallery = $product->getMediaGallery();
            $product->reset();

            $processedImageList = array();
            foreach ($this->_imageFields as $field) {
                if (isset($row[$field])) {
                    if ($row[$field] == 'no_selection') {
                         $row[$field] = null;
                    } else {
                        $processedImageList[] = $row[$field];
                    }
                }
            }

            $processedImageList = array_unique($processedImageList);

            foreach ($productMediaGallery['images'] as $image) {
                if (in_array($image['file'], $processedImageList)) {
                    continue;
                }

                $row['image']=basename($image['file']);
            }

            $batchModelId = $this->getBatchModel()->getId();
            $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($batchModelId)
                ->setBatchData($row)
                ->setStatus(1)
                ->save();

            // $baseRowData = array(
            //     'store'     => $row['store'],
            //     'website'   => $row['websites'],
            //     'sku'       => $row['sku']
            // );
            unset($row);

            // foreach ($productMediaGallery['images'] as $image) {
            //     if (in_array($image['file'], $processedImageList)) {
            //         continue;
            //     }

            //     $rowMediaGallery = array(
            //         '_media_image'          => basename($image['file']),
            //         '_media_label'          => $image['label'],
            //         '_media_position'       => $image['position'],
            //         '_media_is_disabled'    => $image['disabled']
            //     );
            //     $rowMediaGallery = array_merge($baseRowData, $rowMediaGallery);

            //     $this->getBatchExportModel()
            //         ->setId(null)
            //         ->setBatchId($batchModelId)
            //         ->setBatchData($rowMediaGallery)
            //         ->setStatus(1)
            //         ->save();
            // }
        }

        return $this;
    }

    /**
     * Prepare product links
     *
     * @param  array $productIds
     * @return array
     */
    protected function _prepareLinks(array $productIds)
    {
        if (empty($productIds)) {
            return array();
        }
        $resource = Mage::getSingleton('core/resource');
        $adapter = $resource->getConnection('read');
        $select = $adapter->select()
            ->from(
                array('cpl' => $resource->getTableName('catalog/product_link')),
                array(
                    'cpl.product_id', 'cpe.sku', 'cpl.link_type_id',
                    'position' => 'cplai.value', 'default_qty' => 'cplad.value'
                )
            )
            ->joinLeft(
                array('cpe' => $resource->getTableName('catalog/product')),
                '(cpe.entity_id = cpl.linked_product_id)',
                array()
            )
            ->joinLeft(
                array('cpla' => $resource->getTableName('catalog/product_link_attribute')),
                $adapter->quoteInto(
                    '(cpla.link_type_id = cpl.link_type_id AND cpla.product_link_attribute_code = ?)',
                    'position'
                ),
                array()
            )
            ->joinLeft(
                array('cplaq' => $resource->getTableName('catalog/product_link_attribute')),
                $adapter->quoteInto(
                    '(cplaq.link_type_id = cpl.link_type_id AND cplaq.product_link_attribute_code = ?)',
                    'qty'
                ),
                array()
            )
            ->joinLeft(
                array('cplai' => $resource->getTableName('catalog/product_link_attribute_int')),
                '(cplai.link_id = cpl.link_id AND cplai.product_link_attribute_id = cpla.product_link_attribute_id)',
                array()
            )
            ->joinLeft(
                array('cplad' => $resource->getTableName('catalog/product_link_attribute_decimal')),
                '(cplad.link_id = cpl.link_id AND cplad.product_link_attribute_id = cplaq.product_link_attribute_id)',
                array()
            )
            ->where('cpl.link_type_id IN (?)', array(
                Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED
            ))
            ->where('cpl.product_id IN (?)', $productIds);

        $stmt = $adapter->query($select);
        $linksRows = array();
        while ($linksRow = $stmt->fetch()) {
            $linksRows[$linksRow['product_id']][$linksRow['link_type_id']][] = array(
                'sku'         => $linksRow['sku'],
                'position'    => $linksRow['position'],
                'default_qty' => $linksRow['default_qty']
            );
        }

        return $linksRows;
    }
}
