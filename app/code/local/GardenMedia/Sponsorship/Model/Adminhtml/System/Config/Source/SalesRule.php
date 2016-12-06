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
 * GardenMedia_Sponsorship_Model_Adminhtml_System_Config_Source_SalesRule 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Model_Adminhtml_System_Config_Source_SalesRule
{
    /**
     *
     * @var array
     */
    protected $_options = null;

    public function getOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $salesrules = Mage::getModel('salesrule/rule')->getCollection()
                ->addFieldToSelect('name')
                ->addFieldToSelect('rule_id');
            foreach ($salesrules as $rule) {
                $this->_options[$rule->getId()] = $rule->getName();
            }
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

        foreach ($this->getOptions() as $value => $label) {
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
        $options = $this->getOptions();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return '';
    }

    protected function getHelper()
    {
        return Mage::helper('gm_sponsorship');
    }
}
