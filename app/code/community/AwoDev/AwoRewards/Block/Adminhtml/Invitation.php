<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Invitation extends AwoDev_AwoRewards_Block_Adminhtml_Widget_Grid_Container {
	public function __construct() {
		// The blockGroup must match the first half of how we call the block, and controller matches the second half
		// ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'awodev_aworewards';
        $this->_controller = 'adminhtml_invitation';
        $this->_headerText = $this->__('Invitations');
         
        parent::__construct();
    }
}