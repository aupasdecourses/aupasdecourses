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

        if($filter=="store"){
            $storeid = Mage::app()->getStore()->getId();
            $shops->addFieldToFilter('stores', array('finset' =>$storeid));
        }

        if($random){
            $shops->getSelect()->order('rand()');
        }

		$nbShops = count($shops);
		
        foreach ($shops as $shop) {
            $shop = $shop->getData();
			if($shop['id_category']) {
				$category = Mage::getModel('catalog/category')->load($shop['id_category'][0]);
				if($category && $category->getParentCategory()) {
                    $type = $category->getParentCategory()->getName();
                    if($filter<>"all" && $filter<>"store" && $filter<>$type){
                        continue;
                    }
					$color = $category->getParentCategory()->getData('menu_bg_color');
				}
                $url=Mage::app()->getStore($shop['stores'][0])->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).Mage::getModel('catalog/category')->load($shop['id_category'][0])->getData('url_path');
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
