<?php

class Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Enabled extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());
		if($input=1){
			return $this->__("Oui");
		} else {
			return $this->__("Non");
		}
	 
	}
 
}