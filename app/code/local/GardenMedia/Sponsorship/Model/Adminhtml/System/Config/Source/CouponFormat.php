<?php

class GardenMedia_Sponsorship_Model_Adminhtml_System_Config_Source_CouponFormat
{
    /**
     *
     * @var array
     */
    protected $_options = null;

    public function getOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = Mage::helper('salesrule/coupon')->getFormatsList();
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
