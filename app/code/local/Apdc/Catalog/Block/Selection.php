<?php

class Apdc_Catalog_Block_Selection extends Mage_Catalog_Block_Product
{
    public function formatSelections($collection){
        $result=array();

        foreach($collection as $entity){
            $data=array();
            $entity_data=$entity->getData();

            $data['stripped_name'] = (isset($entity_data['name'])) ? $this->stripTags($entity_data['name'], null, true) :'' ;
            $data['name'] = (isset($entity_data['name'])) ? $entity_data['name'] :'' ;

            if(isset($entity_data['produit_biologique'])){
                $label_bio=['AB','Bio Européen','AB,Bio Européen'];
                $test_bio=Mage::getResourceSingleton('catalog/product')->getAttribute('produit_biologique')->getSource()->getOptionText($entity_data['produit_biologique']);
                if(in_array($test_bio,$label_bio)){
                    $data['html_bio']='<span class="produit-biologique-selection"><img src="'.$this->getSkinUrl("images/logo_ab_petit.png").'" alt="Bio"/></span>';
                }else{
                    $data['html_bio']='';
                }
            }else{
                $data['html_bio']='';
            }

            $data['url_path']=(isset($entity_data['url_path'])) ? Mage::getUrl($entity_data['url_path']) : '';
            $src= $this->helper('catalog/image')->init($entity, 'small_image')->resize(250, 250);
            $data['image_src']=(isset($src)) ? $this->htmlEscape($src) : '';
            $label=$this->getImageLabel($entity, 'small_image');
            $data['image_label']=(isset($label)) ? $this->htmlEscape($label) : '';
            $data['short_description']=(isset($entity_data['short_description'])) ? $entity_data['short_description'] : '';

            $result[]=$data;
        }

        return $result;
    }

    public function getAllSelections()
    {
        //filter by store
        $storeid = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeid)
                    ->addAttributeToSelect(array('name', 'price', 'small_image', 'short_description', 'produit_biologique', 'origine','url_path'))
                    ->addFieldToFilter('status', 1);
                    //->addFieldToFilter('on_selection',True);
        $collection->getSelect()->orderRand();
        $collection->setPageSize(10);

        return $this->formatSelections($collection);

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
        $collection->setPageSize(10);
        
        return $this->formatSelections($collection);
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
