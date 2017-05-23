<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Neighborhood_Model_Resource_Neighborhood 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Core_Model_Resource_Db_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Model_Resource_Neighborhood extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_serializableFields = array(
        'postcodes' =>  array('', array())
    );

    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        $this->_init('apdc_neighborhood/neighborhood', 'entity_id');
    }

    /**
     * _beforeSave 
     * 
     * @param Mage_Core_Model_Abstract $object object 
     * 
     * @return void
     */
    protected function _serializeField(Varien_Object $object, $field, $defaultValue=null, $unsetEmpty=false)
    {
        if ($field == 'postcodes') {
            if (!is_array($object->getPostcodes()) && $object->getPostcodes() != '') {
                $postcodes = explode(',', $object->getPostcodes());
                $cleanPostcodes = array();
                foreach ($postcodes as $postcode) {
                    $cleanPostcodes[] = trim($postcode);
                }
                $object->setPostcodes($cleanPostcodes);
            }
        }

        return parent::_serializeField($object, $field, $defaultValue, $unsetEmpty);
    }
}
