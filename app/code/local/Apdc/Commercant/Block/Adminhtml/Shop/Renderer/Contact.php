<?php

class Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Contact extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	protected $_availableManagers;

    protected function _construct()
    {
        parent::_construct();

        $this->_availableManagers = Mage::getModel('apdc_commercant/contact')
            ->getCollection()
            ->addRoleFilter(Apdc_Commercant_Model_Source_Contact_Type::TYPE_MANAGER)
            ->toOptionArray();
        array_unshift($this->_availableManagers, ['value' => '', 'label' => '']);

    }

	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());
		
        foreach ( $this->_availableManagers as $option){
		  $attributeArray[$option['value']] = $option['label'];
		} 

		if(!is_null($input) && !empty($input) && isset($attributeArray[$input])){
			return $attributeArray[$input];
		} else {
			return $input;
		}
	 
	}
 
}
