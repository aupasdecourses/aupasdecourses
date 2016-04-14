<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_PaymentDetail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
	
		$helper = Mage::helper('awodev_aworewards');

		$payment_details = '';
		$payment_type = $row->getData('payment_type');
		$details = $row->getData('payment_details');
		if($payment_type=='mage_coupon') $payment_details = $helper->__('Coupon Code').': '.$details;
		elseif($payment_type=='paypal') {
			$a = explode('|',$details);
			$payment_details .= '<div>'.$helper->__('Receiver Transaction ID').': '.@$a[1].'</div>';
			$payment_details .= '<div>'.$helper->__('Sender Transaction ID').': '.@$a[2].'</div>';
		}
		
		$id = $row->getData('id');
		$link = Mage::helper("adminhtml")->getUrl('*/*/details',array('id'=>$id));
		
		$payment_details .= '
				<script language="javascript" type="text/javascript">
				function block_generate_'.$id.'() {
					jQuery(document).ready(function () {
						jQuery.fancybox({
							"width"				: "75%",
							"height"			: "100%",
							"autoScale"     	: false,
							"transitionIn"		: "none",
							"transitionOut"		: "none",
							"type": "iframe",
							"href":"'.$link.'"
						});
					});	
				}
				</script>
				<a href="javascript:block_generate_'.$id.'()">'.$helper->__('View Credits').'</a>
			';



		return $payment_details;
    }
} 