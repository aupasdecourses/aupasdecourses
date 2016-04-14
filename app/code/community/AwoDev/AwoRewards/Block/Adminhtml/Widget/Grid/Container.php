<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Widget_Grid_Container extends Mage_Adminhtml_Block_Widget_Grid_Container {

	public function __construct() {
		parent::__construct();
		// $this->setTemplate('widget/grid/container.phtml');
		$this->setTemplate('awodev/aworewards/widget/grid/container.phtml');
	}
}
