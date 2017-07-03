<?php

class Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());

		$commercantCategories = Mage::getModel('catalog/category')
            ->getCollection()
            ->setOrder('name')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('estcom_commercant', 70);
        $S = Mage::helper('apdc_commercant')->getStoresArray();
        foreach ($commercantCategories as $category) {
        	$storename=$S[explode('/', $category->getPath())[1]]['name'];
            $parentcat=$category->getParentCategory()->getName();
            $values[$category->getId()] = $category->getName().' - '.$parentcat.' - '.$storename;
        }

        $suppstr="";
        if(count($input)>0)
        {
            $suppstr="<ul>";
			foreach($input as $id){
				 $suppstr.= "<li>".$values[$id]."</li>";
			}
			$suppstr   .= "</ul>";
		}

		return $suppstr;
	 
	}
 
}