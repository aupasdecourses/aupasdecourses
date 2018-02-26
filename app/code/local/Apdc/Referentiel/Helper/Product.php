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
 * Apdc_Catalog_Helper_Product_Availability 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Helper_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Referentiel_Helper_Product extends Mage_Core_Helper_Abstract
{
	private $_unit_strings;

	public function __construct(){
		$this->unit_strings=['pièce','lot','unité','tête'];
	}

	public function getSaisonnalite(Mage_Catalog_Model_Product $product){
        $code_ref=$product->getData('code_ref_apdc');
        $ref=Mage::getModel('apdc_referentiel/referentiel')->loadByField('code_ref_apdc',$code_ref,array('code_ref_apdc','saisonnalite'));
        if($ref){
            return $ref->getData('saisonnalite');
        }else{
            return false;
        }
    }

    public function checkDescription($string){
    	$check=false;
    	foreach($this->unit_strings as $s){
    		if(strpos($string, $s)!==false){
    			$check=true;
    		}
    	}
    	return $check;
    }

    public function getPoidsUnit(Mage_Catalog_Model_Product $product){
        $code_ref=$product->getData('code_ref_apdc');
        $ref=Mage::getModel('apdc_referentiel/referentiel')->loadByField('code_ref_apdc',$code_ref,array('code_ref_apdc','poids_unit'));
        if($ref){
        	if($product->getData('unite_prix')=="kg" && $this->checkDescription($product->getData('short_description'))== false){
        		$single=floatval($ref->getData('poids_unit'));
        		$portion=floatval($product->getData('poids_portion'));	
        		if($single<>0){
        			$items=round($portion/$single,0);
        			$return="(environ ".$items;
        			if($items>1){
        				return $return.=" pièces)";
        			}else{
        				return $return.=" pièce)";
        			}
        		}
        	}
        	return false;
        }else{
            return false;
        }
    }
}