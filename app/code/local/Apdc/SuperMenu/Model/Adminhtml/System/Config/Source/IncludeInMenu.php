<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  SuperMenu
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_SuperMenu_Model_Adminhtml_System_Config_Source_IncludeInMenu 
 * 
 * @category Apdc
 * @package  SuperMenu
 * @uses     Mage
 * @uses     Mage_Eav_Model_Entity_Attribute_Source_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_SuperMenu_Model_Adminhtml_System_Config_Source_IncludeInMenu extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     *
     * @var array
     */
    protected $_options = null;

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                '1' => $this->getHelper()->__('Sur tous les devices'),
                '2' => $this->getHelper()->__('Uniquement sur Desktop'),
                '3' => $this->getHelper()->__('Uniquement sur mobile')
            );
        }
        return $this->_options;
    }

    /**
     * toOptionArray
     *
     * @param boolean $withEmpty  : add Empty label to options
     * @param string  $emptyLabel : empty Label
     *
     * @return array
     */
    public function toOptionArray($withEmpty = false, $emptyLabel = null)
    {
        $options = array();

        foreach ($this->getAllOptions() as $value => $label) {
            $options[] = array(
                'label' => $label,
                'value' => $value
            );
        }

        if ($withEmpty) {
            $emptyLabel = ($emptyLabel ? $emptyLabel : $this->getHelper()->__('-- Please Select --'));
            array_unshift($options, array('value'=>'-1', 'label'=> $emptyLabel));
        }

        return $options;
    }

    /**
     * getLabel
     * get Option label from its value
     *
     * @param int $value value
     *
     * @return string
     */
    public function getLabel($value)
    {
        $options = $this->getAllOptions();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return '';
    }

    protected function getHelper()
    {
        return Mage::helper('apdc_supermenu');
    }
}
