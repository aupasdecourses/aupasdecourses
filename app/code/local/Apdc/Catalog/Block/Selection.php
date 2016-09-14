<?php

class Apdc_Catalog_Block_Selection extends Mage_Catalog_Block_Product
{
    public function getAllSelections()
    {
        //filter by store
        $storeid = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeid)
                    ->addAttributeToSelect(array('name', 'price', 'small_image', 'short_description', 'produit_biologique', 'origine'))
                    ->addFieldToFilter('status', 1);
                    //->addFieldToFilter('on_selection',True);
        $collection->getSelect()->orderRand();

        return $collection;
    }

    public function getSelectionbyById($category_id)
    {
        $storeid = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeid)
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('category_id', $category_id)
                ->addFieldToFilter('status', 1);
                //->addFieldToFilter('on_selection',True);
        $collection->getSelect()->orderRand();

        return $collection;
    }

    public function getCustomerLastOrders()
    {
        $customer_data = Mage::helper('customer')->getCustomer();
        $orders = Mage::getResourceModel('sales/order_collection')
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('customer_id', $customer_data->getId())
                    ->addAttributeToSort('created_at', 'DESC')
                    ->setPageSize(1);

        return $orders;
    }

    public function getCustomerLastOrderedItems($orders, $commercant_id)
    {
        $itemarray = [];
        foreach ($orders as $order) {
            $order_id = $order->getId();
            $order = Mage::getModel('sales/order')->load($order_id);
            $ordered_items = $order->getItemsCollection();
            foreach ($ordered_items as $item) {
                $product_id = $item->getProductId();
                $_product = Mage::getModel('catalog/product')->load($product_id);
                $cats = $_product->getCategoryIds();
                if (isset($cats) && in_array($commercant_id, $cats)) {
                    $itemarray = [$product_id];
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
