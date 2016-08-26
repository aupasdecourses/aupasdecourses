<?php

/* Used by QuoteItemComment_Block_Adminhtml_Sales_Items_Abstract
Impossible to override through config.xml*/

class  Mage_Adminhtml_Block_Sales_Items_Abstract extends Mage_Adminhtml_Block_Template
{
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