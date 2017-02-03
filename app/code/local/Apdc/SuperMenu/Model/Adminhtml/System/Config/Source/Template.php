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
 * Apdc_SuperMenu_Model_Adminhtml_System_Config_Source_Template 
 * 
 * @category Apdc
 * @package  SuperMenu
 * @uses     Mage
 * @uses     Mage_Eav_Model_Entity_Attribute_Source_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_SuperMenu_Model_Adminhtml_System_Config_Source_Template extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
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
                '' => $this->getHelper()->__('-- Do not use template --'),
                'template1' => '5 colonnes sur une ligne avec bloc principal, 3 colonnes catégorie, 1 colonne bloc statique',
                'template2' => '5 colonnes sur 1 ligne avec bloc principal, 4 colonnes catégorie',
                'template3' => '5 colonnes et deux lignes, 1ere ligne = bloc principal +  4 colonnes catégorie, 2e ligne = 5 colonnes produits'
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
