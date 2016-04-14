<?php

class Pmainguet_QuoteItemComment_Block_Adminhtml_Sales_Order_View_Items extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
	public function getItemComment($item) {
		var_dump('salut');
		$itemId = $item->getId();

		$write = Mage::getSingleton('core/resource')->getConnection('core_write');

    	$query = "SELECT q.* FROM `sales_flat_order_item` o
	    LEFT JOIN `sales_flat_quote_item` q on o.quote_item_id = q.item_id
	    WHERE o.item_id =".$itemId;

		$res = $write->query($query);

		while ($row = $res->fetch() ) {
			if(key_exists('item_comment',$row)) {
				echo nl2br($row['item_comment']);
			}
		}
	}    

}