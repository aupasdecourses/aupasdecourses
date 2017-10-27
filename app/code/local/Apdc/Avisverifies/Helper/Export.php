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

	protected function plaProductsData($order,$pla){ 
		$products = $order->getAllVisibleItems(); //filter out simple products
		$products_arr = array();
		$helper=Mage::helper('apdc_commercant');
		foreach ($products as $product) {
			$productId = $product->getProductId();
			if (!empty($this->plaProducts[$productId])) {
				$products_arr[] = $this->plaProducts[$productId];
			}
			else {
				$full_product = Mage::getModel('catalog/product')->load($productId);
				$collection = Mage::getModel('catalog/product')->getCollection();
				$collection->addAttributeToFilter('entity_id', $productId);
				$collection->addAttributeToSelect('entity_id');

				$shop=$helper->getInfoShopByCommercantId($full_product->getCommercant());

				// get product $data according to format pla
				foreach($pla as $fields){
					// first check if its the product id
					$collection->addAttributeToSelect($fields['static_value']);
				}
				$full_product = $collection->getFirstItem();
				$tmp_product = Mage::getModel('catalog/product')->load($productId);
				$_data = $full_product->getData();
				$product_data = array();
				foreach ($pla as $attribute) {
					// product_id
					if ($attribute['name'] == 'id') {
						//$_val = $full_product->getData($attribute['static_value']);
						$_val = $full_product->getCommercant();
						$product_data['product_id'] = ($_val)? $_val : '';
					}
					// product_description
					if ($attribute['name'] == 'description') {
						//$_val = $full_product->getData($attribute['static_value']);
						$_val = utf8_decode(str_replace(",", " - ", $shop['name']));
						$product_data['product_name'] = ($_val)? $_val : '';
					}
					// product_link
					if ($attribute['name'] == 'link') {
						// get parent URL or NOT
						//$product_data['url'] = $this->getProductUrlOrParentUrl($productId,$full_product->getData($attribute['static_value']),$order->getStoreId(),true);
						$product_data['url'] = $shop['url'];
					}
					// product_image_link
					if ($attribute['name'] == 'image_link') {
						//$product_data['url_image'] = $this->getProductImageOrParentImage($productId,$full_product->getData($attribute['static_value']),true);
						if($shop['category_thumbnail']<>""){
							$product_data['url_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$shop['category_thumbnail'];
						}else{
							$product_data['url_image'] = $shop['thumbnail_image'];
						}
					}
					// product_sku
					if ($attribute['name'] == 'sku') {
						//$_val = $full_product->getData($attribute['static_value']);
						$_val = $shop['code'];
						$product_data['sku'] = ($_val)? $_val : '';
					}
					// product_brand
					if ($attribute['name'] == 'brand') {
						$_val = $full_product->getData($attribute['static_value']);
						
						// If selectbox $_val is number so rather get Text.
						if ( is_numeric( $_val ) ) {
							$_val = $full_product -> getAttributeText( $attribute['static_value'] );
						}
						$product_data['brand'] = ( $_val ) ? $_val : '';
					}
					// product_category
					if ($attribute['name'] == 'category') {
						$_val = $full_product->getData($attribute['static_value']);
						
						// If selectbox $_val is number so rather get Text.
						if ( is_numeric( $_val ) ) {
							$_val = $full_product -> getAttributeText( $attribute['static_value'] );
						}
						$product_data['category'] = ( $_val ) ? $_val : '';
					}
					// product_gtin
					if ($attribute['name'] == 'gtin') {
						$_val = $full_product->getData($attribute['static_value']);
						$product_data['gtin'] = ($_val)? $_val : '';
					}
					// product_mpn
					if ($attribute['name'] == 'mpn') {
						$_val = $full_product->getData($attribute['static_value']);
						$product_data['mpn'] = ($_val)? $_val : '';
					}
					// extra info 1 to 10
					for($i=1;$i<11;$i++){
						$name = 'Extra Info'.$i;
						// product_mpn
						if ($attribute['name'] == $name) {
							$_val = $full_product->getData($attribute['static_value']);
							$product_data['info'.$i] = ($_val)? $_val : '';
						}
					}
				}
				
				// now test if basic value are present , if not the add them
				// product_id
				if (empty($product_data['product_id'])) {
					$product_data['product_id'] = $tmp_product->getId();
				}
				// product_description
				if (empty($product_data['product_name'])) {
					$product_data['product_name'] = utf8_decode(str_replace(",", " - ", $tmp_product->getName()));
				}
				// product_link
				if (empty($product_data['url'])) {
					$product_data['url'] = $this->getProductUrlOrParentUrl($productId,$tmp_product->getUrlInStore(array('_store' => $order->getStoreId())),$order->getStoreId());
				}
				// product_image_link
				if (empty($product_data['url_image'])) {
					try{
						$product_data['url_image'] = $this->getProductImageOrParentImage($productId,$tmp_product->getImageUrl());
						
					}catch(Exception $e) {
						$product_data['url_image'] = '';
					};
				}
				// product_sku
				if (empty($product_data['sku'])) {
					$product_data['sku'] = $full_product->getSku();
				}
				$products_arr[] = $this->plaProducts[$productId] = $product_data;
			}
		}
		return $products_arr;
	}
	
}