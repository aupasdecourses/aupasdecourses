<?php

class Apdc_Commercant_Block_Adminhtml_Contact_Renderer_Role extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());
		//$roles=Mage::getModel('apdc_commercant/source_contact_type')->toOptionArray();
        //foreach ( $role as $role){
		//  $attributeArray[$role['value']] = $role['label'];
		//}
		//return $attributeArray[$input];	 
	}
 
}