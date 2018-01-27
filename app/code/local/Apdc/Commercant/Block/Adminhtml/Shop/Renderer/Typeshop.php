<?php

class Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Typeshop extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	protected $_typeshop;

    protected function _construct()
    {
        parent::_construct();

        $this->_typeshop = Mage::getModel('apdc_commercant/typeshop')->getCollection()->toOptionArray();
       	array_unshift($this->_typeshop, ['value' => '', 'label' => '']);

    }

	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());
        
        foreach ( $this->_typeshop as $option){
		  $attributeArray[$option['value']] = $option['label'];
		} 

		if(!is_null($input) && !empty($input)){
			return $attributeArray[$input];
		} else {
			return $input;
		}
	 
	}
 
}