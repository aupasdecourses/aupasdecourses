<?php

class Apdc_Commercant_Block_List extends Mage_Catalog_Block_Product
{
    public function getListShops($filter="store",$random=false)
    {

        $row1 = array();
		$row2 = array();
		$i = 1;

        $shops = Mage::getModel('apdc_commercant/shop')->getCollection()            
            ->addFieldtoFilter('enabled',1);

        $store=Mage::app()->getStore();
        $storeid = $store->getId();
        $storecode = $store->getCode();
        $storerootid=$store->getRootCategoryId();

        if($filter=="store"){
            $shops->addFieldToFilter('stores', array('finset' =>$storeid));
        }

        if($random){
            $shops->getSelect()->order('rand()');
        }

		$nbShops = count($shops);
		
        foreach ($shops as $shop) {
            $shop = $shop->getData();
			if($shop['id_category']) {

                //Récupération de l'id catégorie correspondant au magasin
                foreach($shop['id_category'] as $id){
                    $current_cat=$id;
                    $path=Mage::getModel('catalog/category')->load($id)->getPath();
                    $rootcat=explode('/',$path)[1];
                    if($rootcat==$storerootid){
                       continue;
                    }
                }

				$category = Mage::getModel('catalog/category')->setStoreId(0)->load($current_cat);

				if($category && $category->getParentCategory()) {
                    $type = $category->getParentCategory()->getName();
                    if($filter<>"all" && $filter<>"store" && $filter<>$type){
                        continue;
                    }
					$color = $category->getParentCategory()->getData('menu_bg_color');
				}

                if($storecode=='accueil'){
                    $url=Mage::app()->getStore($shop['stores'][0])->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$category->getData('url_path');
                }else{
                    $check=$category->getIsActive();
                    if($check){
                        $url=Mage::getBaseUrl().$category->getData('url_path');
                    }else{
                        continue;
                    }
                }
			}

            $sub = [
                'name' => (isset($shop['name'])) ? $shop['name'] : '',
                'postcode' => $shop['postcode'],
                'adresse' => (isset($shop['street'])) ? $shop['street'].' '.$shop['postcode'].' '.$shop['city'] : '',
				'color' => $color,
                'src' => (isset($shop['category_image'])) ? Mage::getBaseUrl('media').$shop['category_image'] : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
                'url' => $url,
                'type' => $type,
            ];

			if($i == 1) {
				$row1[] = $sub;
			} else {
				$row2[] = $sub;
			}
			if($i == 2) {
				$i = 1;
			} else {
				$i ++;
			}
        }
		return array('row1' => $row1, 'row2' => $row2, 'count' => count($shops));
    }

    /**
     * getInfoShop 
     * 
     * @param int $shopId shopId 
     * 
     * @return array
     */
    public function getInfoShop($shopId=null)
    {
        return Mage::helper('apdc_commercant')->getInfoShop($shopId);
    }
}
