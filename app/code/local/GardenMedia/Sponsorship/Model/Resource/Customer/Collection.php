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
 * GardenMedia_Sponsorship_Model_Resource_Customer_Collection 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Customer_Model_Resource_Customer_Collection
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Model_Resource_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    protected $isFromSponsorship = false;

    /**
     * setIsFromSponsorship 
     * 
     * @param boolean $isFromSponsorship isFromSponsorship 
     * 
     * @return GardenMedia_Sponsorship_Model_Resource_Customer_Collection
     */
    public function setIsFromSponsorship($isFromSponsorship)
    {
        $this->isFromSponsorship = $isFromSponsorship;
        return $this;
    }

    /**
     * getIsFromSponsorship 
     * 
     * @return boolean
     */
    public function getIsFromSponsorship()
    {
        return $this->isFromSponsorship;
    }

    public function getSelectCountSql()
    {
        if ($this->getIsFromSponsorship()) {
            $this->_renderFilters();
            $countSelect = clone $this->getSelect();
            $countSelect->reset(Zend_Db_Select::ORDER);
            $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
            $countSelect->reset(Zend_Db_Select::COLUMNS);

            // Count doesn't work with group by columns keep the group by 
            if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
                $countSelect->reset(Zend_Db_Select::GROUP);
                $countSelect->distinct(true);
                $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
                $countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
            } else {
                $countSelect->columns('COUNT(*)');
            }
        } else {
            $countSelect = parent::getSelectCountSql();
        }
		return $countSelect;
    }
}
