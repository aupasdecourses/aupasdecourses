<?php
class Vsourz_Agegate_Model_Source_Page
{

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('cms/page_collection')
                ->load()->toOptionIdArray();
        }
		$this->_options[] = array('value' => 'catalog', 'label' => 'Catalog');
		$this->_options[] = array('value' => 'checkout', 'label' => 'Checkout');
		$this->_options[] = array('value' => 'customer', 'label' => 'Customer');
		return $this->_options;
    }

}
