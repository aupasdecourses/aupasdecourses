<?php // Our EXPORT CLASS. 
class Apdc_Avisverifies_Helper_Export extends Netreviews_Pla_Helper_Export{   
	
	protected function defaultProductsData($order){
		$products = $order->getAllVisibleItems(); //filter out simple products
		$products_arr = array();

		$helper=Mage::helper('apdc_commercant');

		foreach ($products as $product) {
			$productId = $product->getProductId();
			if (!empty($this->defaultProducts[$productId])) {
				$products_arr[] = $this->defaultProducts[$productId];
			}
			else {
				$full_product = Mage::getModel('catalog/product')->load($productId);

				$shop=$helper->getInfoShopByCommercantId($full_product->getCommercant());

				$product_data = array();
				$product_data['product_name'] = utf8_decode(str_replace(",", " - ", $shop['name']));
				//replace product id with id of attribute commerçant
				$product_data['product_id'] = $full_product->getCommercant();
				$product_data['sku'] = $shop['code'];
				$product_data['url'] = $shop['url'];
				if($shop['category_thumbnail']<>""){
					$product_data['url_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$shop['category_thumbnail'];
				}else{
					$product_data['url_image'] = $shop['thumbnail_image'];
				}

				$products_arr[] = $this->defaultProducts[$productId] = $product_data;
			}
            
		}
		return $products_arr;
	}
	
}