<?php

/**
 * Class Apdc_Commercant_Model_BankInfo
 */
class Apdc_Commercant_Model_BankInfo extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('apdc_commercant/bankInfo');
    }

    public function getUploadDir($entity)
    {
        return Mage::getBaseDir('media') . DS . $this->getMediaSubpath($entity);
    }

    public function getMediaSubpath($entity)
    {
        return 'commercant'. DS .$entity;
    }
}
