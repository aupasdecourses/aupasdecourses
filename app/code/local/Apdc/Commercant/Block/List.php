<?php

class Apdc_Commercant_Block_List extends Mage_Catalog_Block_Product
{
    private function getDateFirstItem($idcommercant)
    {
        $date_firstproduct = Mage::getModel('catalog/product')->getCollection()
                ->addAttributetoSelect(array('commercant', 'created_at'))
                ->addAttributetoFilter('commercant', $idcommercant)
                ->addAttributeToSort('created_at', 'asc')
                ->getFirstItem()->getCreatedAt();

        $creation = new Zend_Date($date_firstproduct, 'YYYY-MM-dd');
        $now = new Zend_Date(now(), 'YYYY-MM-dd');
        $diff = $now->sub($creation)->toValue();
        $days = ceil($diff / 60 / 60 / 24) + 1;

        $new = ($days <= Mage::getStoreConfig('apdc_general/display/commercant_new')) ? true : false;

        return $new;
    }

    private function vipOrder($array,$item){
        if(strpos($item['name'],'Laiterie')!==false){
            array_unshift($array , $item);
        }else{
            $array[] = $item;
        }
        return $array;
    }

    public function getListShops($filter = 'store', $random = false,$limit=0)
    {
        $row1 = array();
        $row2 = array();
        $i = 1;

        $shops = Mage::getModel('apdc_commercant/shop')->getCollection()
            ->addFieldtoFilter('enabled', 1);

        $store = Mage::app()->getStore();
        $storeid = $store->getId();
        $storecode = $store->getCode();
        $storerootid = $store->getRootCategoryId();

        if ($filter == 'store') {
            $shops->addFieldToFilter('stores', array('finset' => $storeid));
        }

        if ($random) {
            $shops->getSelect()->order('rand()');
        }

        if($limit>0){
            $shops->getSelect()->limit($limit);
        }

        $nbShops = count($shops);

        foreach ($shops as $shop) {
            $shop = $shop->getData();
            if ($shop['category_ids']) {
                sort($shop['category_ids']);

                //Récupération de l'id catégorie correspondant au magasin
                if ($filter == 'store') {
                    foreach ($shop['category_ids'] as $id) {
                        $current_cat = $id;
                        $path = Mage::getModel('catalog/category')->load($id)->getPath();
                        $rootcat = explode('/', $path)[1];
                        if (!empty($rootcat) && isset($rootcat[1])) {
                            if ($rootcat == $storerootid) {
                                break;
                            }
                        }
                    }
                } else {
                    $current_cat = $shop['category_ids'][0];
                }

                $category = Mage::getModel('catalog/category')->setStoreId(0)->load($current_cat);

                if ($category && $category->getParentCategory()) {
                    $type = $category->getParentCategory()->getName();
                    if ($filter != 'all' && $filter != 'store' && $filter != $type) {
                        continue;
                    }
                    $color = $category->getParentCategory()->getData('menu_bg_color');
                }

                if ($storecode == 'accueil') {
                    $stores = explode(',', $shop['stores']);
                    $url = Mage::app()->getStore($stores[0])->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$category->getData('url_path');
                } else {
                    $check = $category->getIsActive();
                    if ($check) {
                        $url = Mage::getBaseUrl().$category->getData('url_path');
                    } else {
                        continue;
                    }
                }
            }

            $new = $this->getDateFirstItem($shop['id_attribut_commercant']);

            $sub = [
                'name' => (isset($shop['name'])) ? $shop['name'] : '',
                'postcode' => $shop['postcode'],
                'adresse' => (isset($shop['street'])) ? $shop['street'].' '.$shop['postcode'].' '.$shop['city'] : '',
                'color' => $color,
                'src' => (isset($shop['category_image'])) ? Mage::getBaseUrl('media').$shop['category_image'] : Mage::getBaseUrl('media').'resource/commerçant_dummy.png',
                'url' => $url,
                'type' => $type,
                'new' => $new,
            ];

            if ($i == 1) {
                $row1 = $this->vipOrder($row1,$sub);
            } else {
                $row2 = $this->vipOrder($row2,$sub);
            }
            if ($i == 2) {
                $i = 1;
            } else {
                ++$i;
            }
        }

        return array('row1' => $row1, 'row2' => $row2, 'count' => count($shops));
    }

    /**
     * getInfoShop.
     * 
     * @param int $shopId shopId 
     * 
     * @return array
     */
    public function getInfoShop($shopId = null)
    {
        return Mage::helper('apdc_commercant')->getInfoShop($shopId);
    }

    public function getShopAvailability()
    {
        $shopAvailability = [];
        if (Mage::getSingleton('core/session')->getDdate()) {
            $timestamp = strtotime(Mage::getSingleton('core/session')->getDdate());
            $date = date('Y-m-d', $timestamp);
            $shopInfo = $this->getInfoShop();
            if (isset($shopInfo['availability']) && isset($shopInfo['availability'][$date])) {
                $shopAvailability = $shopInfo['availability'][$date];
            }
        }

        return $shopAvailability;
    }
}
