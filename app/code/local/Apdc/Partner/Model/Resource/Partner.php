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
 * Apdc_Partner_Model_Resource_Partner 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Model_Resource_Db_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Resource_Partner extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        $this->_init('apdc_partner/partner', 'entity_id');
    }

    /**
     * loadByParnterKey 
     * 
     * @param string $partnerKey : partner key 
     * 
     * @return false | array
     */
    public function loadByPartnerKey($partnerKey)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
                    ->from($this->getMainTable())
                    ->where('partner_key=:partner_key');

        $binds = [
            'partner_key' => $partnerKey
        ];
        return $adapter->fetchRow($select, $binds);
    }
}
