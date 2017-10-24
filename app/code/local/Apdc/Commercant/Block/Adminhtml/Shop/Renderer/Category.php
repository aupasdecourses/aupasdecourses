<?php

class Apdc_Commercant_Block_Adminhtml_Shop_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
    protected $_commercantCategories;
    protected $_ShopsArray;

    protected function _construct()
    {
        parent::_construct();

        $this->_commercantCategories = Mage::helper('apdc_commercant')->getCategoriesCommercant();
        $this->_ShopsArray = Mage::helper('apdc_commercant')->getStoresArray();

    }

	public function render(Varien_Object $row)
	{
		$input =  $row->getData($this->getColumn()->getIndex());

        foreach ($this->_commercantCategories as $category) {
            $catId = explode('/', $category->getPath())[1];
            $storename = '';
            if (isset($this->_ShopsArray[$catId])) {
                $storename=$this->_ShopsArray[explode('/', $category->getPath())[1]]['name'];
            }
            $parentcat=$category->getParentCategory()->getName();
            $values[$category->getId()] = $category->getName().' - '.$parentcat.' - '.$storename;
        }

        $suppstr="";
        if(count($input)>0)
        {
            $suppstr="<ul>";
			foreach($input as $id){
                if(isset($values[$id])){
                    $suppstr.= "<li>".$values[$id]."</li>";   
                }
			}
			$suppstr   .= "</ul>";
		}

		return $suppstr;
	 
	}
 
}
