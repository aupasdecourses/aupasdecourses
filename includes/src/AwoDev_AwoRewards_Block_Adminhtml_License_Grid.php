<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_Block_Adminhtml_License_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		
		// Set some defaults for our grid
		$this->setDefaultSort('id');
		$this->setId('awodev_aworewards_license_grid');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}
	
}
