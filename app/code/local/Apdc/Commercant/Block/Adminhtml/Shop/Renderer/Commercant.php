<?php

class Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Commercant extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());
		//get all options except empty
		$commercants = Mage::getModel('apdc_commercant/commercant')->getCollection()->toOptionArray();
        array_unshift($commercants, ['value' => '', 'label' => '']);
        foreach ( $commercants as $option){
		  $attributeArray[$option['value']] = $option['label'];
		} 

		if(!is_null($input) && !empty($input)){
			return $attributeArray[$input];
		} else {
			return $input;
		}
	 
	}
 
}