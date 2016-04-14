<?php

class Pmainguet_Blockselection_Block_Selection extends Mage_Catalog_Block_Product{

	function getAllSelections(){
		$collection = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect(array('name','price','small_image','short_description','produit_biologique','origine'))
					->addFieldToFilter('status',1)
					->addFieldToFilter('on_selection',True);
		$collection->getSelect()->orderRand();
		return $collection;
	}

	function getSelectionbyById($category_id){
		$collection = Mage::getModel('catalog/product')->getCollection()
				->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
				->addAttributeToSelect('*')
				->addAttributeToFilter('category_id', $category_id)
				->addFieldToFilter('status',1)
				->addFieldToFilter('on_selection',True);
		$collection->getSelect()->orderRand();
		return $collection;
	}

	function getCustomerLastOrders(){
			$customer_data = Mage::helper('customer')->getCustomer();
			$orders = Mage::getResourceModel('sales/order_collection')
				    ->addFieldToSelect('*')
				    ->addFieldToFilter('customer_id', $customer_data->getId())
				    ->addAttributeToSort('created_at', 'DESC')
				    ->setPageSize(1);
			return $orders;
	}

	function getCustomerLastOrderedItems($orders,$commercant_id){
			$itemarray=[];
			foreach($orders as $order){
				$order_id=$order->getId();
				$order = Mage::getModel("sales/order")->load($order_id);
				$ordered_items = $order->getItemsCollection();
				foreach ($ordered_items as $item){
					$product_id = $item->getProductId();
				    $_product = Mage::getModel('catalog/product')->load($product_id);
				    $cats = $_product->getCategoryIds();
					if(isset($cats) && in_array($commercant_id,$cats)){
						$itemarray=[$product_id];
					}
				}	
			}
			return $itemarray;
	}

	    public function getPriceHtml($product)
		    {
		        $this->setTemplate('blockselection/price.phtml');
		        $this->setProduct($product);
		        return $this->toHtml();
		    }
}

?>