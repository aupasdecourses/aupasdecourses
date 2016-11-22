<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * GardenMedia_Sponsorship_Model_Resource_Sponsor 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Core_Model_Resource_Db_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Model_Resource_Sponsor extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Primery key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement    = false;

    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        $this->_init('gm_sponsorship/sponsor', 'sponsor_id');
    }

	protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $currentTime = Varien_Date::now();
            $object->setCreatedAt($currentTime);
        }

        return $this;
    }

    /**
     * exists 
     * 
     * @param string $code code 
     * 
     * @return boolean
     */
    public function exists($code)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), 'sponsor_code');
        $select->where('sponsor_code = :code');

        if ($read->fetchOne($select, array('code' => $code)) === false) {
            return false;
        }
        return true;
    }
}
