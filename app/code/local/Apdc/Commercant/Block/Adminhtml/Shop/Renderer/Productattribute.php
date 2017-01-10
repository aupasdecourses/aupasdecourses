<?php

class Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Productattribute extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());
		//get all options except empty
		$values = Mage::getSingleton('eav/config')
            ->getAttribute('catalog_product', 'commercant')
            ->getSource()
            ->getAllOptions(false,true);
        foreach ( $values as $option){
		  $attributeArray[$option['value']] = $option['label'];
		} 

		if(!is_null($input) && !empty($input)){
			return $attributeArray[$input];
		} else {
			return $input;
		}
	 
	}
 
}