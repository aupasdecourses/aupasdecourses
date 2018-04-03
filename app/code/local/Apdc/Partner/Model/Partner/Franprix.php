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
 * Apdc_Partner_Model_Partner_Franprix 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Apdc
 * @uses     Apdc_Partner_Model_Partner_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Partner_Franprix extends Apdc_Partner_Model_Partner_Abstract
{
    protected $_commercantId = 2080;
    protected $_commercantSkuPrefix = 'FRANPRIX';
    protected $_margeArriere = '0.20';

    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = [
            'category_picto' => 'category_picto',
            'desc_nutri' => 'desc_nutri',
            'price'=> 'price',
            'desc_preparation'=> 'desc_preparation',
            'cf_departement'=> 'cf_departement',
            'old_price'=> 'old_price',
            'desc_conservation'=> 'desc_conservation',
            'imgs'=> 'images',
            'measure'=> 'unite_prix',
            'cf_rayon'=> 'cf_rayon',
            'unit'=> 'unit',
            'maturity'=> 'maturity',
            'desc_ingredient'=> 'desc_ingredient',
            'fids'=> 'fids',
            'desc_avertissement'=> 'desc_avertissement',
            'ean'=> 'sku',
            'max_q'=> 'maw_q',
            'menus'=> 'menus',
            'description_html'=> 'description',
            'prix_cond'=> 'prix_cond',
            'title'=> 'name',
            'is_alcohol'=> 'is_alcohol',
            'brand'=> 'brand',
            'category'=> 'category',
            'desc'=> 'desc',
            'cf_code_ub'=> 'cf_code_ub',
            'desc_deno_legale'=> 'desc_demo_legale',
            'cond'=> 'cond',
            'cf_code_famille'=> 'cf_code_famille',
            'desc_origine'=> 'desc_origine',
            'desc_allergene'=> 'desc_allergene',
            'stocks' => 'external_ids',
            'cf_code_sous_famille'=> 'cf_code_sous_famille'
        ];
    }

    protected function _initProductData()
    {
        if ($this->getIsAlcohol()) {
            $this->product->setTaxClassId(10);
            $this->product->setShowAgePopup(1);
        } else {
            $this->product->setTaxClassId(5);
        }
    }
}
