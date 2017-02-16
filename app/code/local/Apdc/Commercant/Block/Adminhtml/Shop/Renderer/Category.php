<?php

class Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());
		//get all options except empty
		$commercantCategories = Mage::getModel('catalog/category')
            ->getCollection()
            ->setOrder('name')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('estcom_commercant', 70);
        foreach ($commercantCategories as $category) {
            $values[$category->getId()] = $category->getName();
        }

		if(!is_null($input) && !empty($input)){
			return $values[$input];
		} else {
			return $input;
		}
	 
	}
 
}