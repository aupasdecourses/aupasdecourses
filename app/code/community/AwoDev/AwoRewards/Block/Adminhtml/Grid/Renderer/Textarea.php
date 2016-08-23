<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Textarea extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$test = explode('.',$this->getColumn()->getIndex());
		$column = array_pop($test);
		if($row->getData($column)==""){
			return "";
		}
        else{
			return nl2br($row->getData($column));
        }
    }
} 