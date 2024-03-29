<?php

/**
 * Class Apdc_Commercant_Helper_Data
 */
class Apdc_Commercant_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var array
     */
    protected $infoShops = [];
    protected $shopIdByCommercantId = [];

    public function getDays()
    {
        return ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    }
	
	public function getShortDays()
    {
        return ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    }

    public function setLocaleFR(){
        $locale = Mage::getStoreConfig('general/locale/code');
        if($locale=='fr_FR'){
          return setLocale(LC_TIME,$locale.'.utf8');
        }
    }

    public function getWeekDays($short=true)
    {
        $weekDays = [];
        if ($short) {
            $labels = $this->getShortDays();
        } else {
            $labels = $this->getDays();
        }
        for ($i=1; $i <= 7; ++$i) {
            $weekDays[$i] = [
                'value' => $i,
                'label' => $labels[$i-1]
            ];
        }
        return $weekDays;
    }

    public function formatDays($days, $string = false, $short = false){
    	$labeldays = $this->getDays();
		if($short == true) {
			$labeldays = $this->getShortDays();
		}
        $rsl=[];
        foreach($days as $day){
            $rsl[]=$labeldays[$day-1];
        }
 
        if ($string){
           	$r = implode(", ", $rsl);
        }else {
        	$r = $rsl;
        }
        return $r;
    }

    public function getStoresArray($type="rootcatid"){
        $S = [];
        $app = Mage::app();
        $stores = $app->getStores();
        foreach ($stores as $id => $idc) {
            if($type=="storeid"){
                $S[$id]['store_id'] = $id;
                $S[$id]['id'] = $app->getStore($id)->getRootCategoryId();
                $S[$id]['name'] = $app->getStore($id)->getName();
                $S[$id]['key'] = $app->getStore($id)->getCode();
            } else {
                $S[$app->getStore($id)->getRootCategoryId()]['store_id'] = $id;
                $S[$app->getStore($id)->getRootCategoryId()]['id'] = $app->getStore($id)->getRootCategoryId();
                $S[$app->getStore($id)->getRootCategoryId()]['name'] = $app->getStore($id)->getName();
                $S[$app->getStore($id)->getRootCategoryId()]['code'] = $app->getStore($id)->getCode();
            }
        }
        return $S;
    }

    public function getCategoriesArray(){
        $categories = Mage::getModel('catalog/category')->getCollection();
        $cat_array=[];
        foreach($categories as $cat){
            $cat_array[$cat->getId()]=$cat->getPath();
        }
        return $cat_array;
    }

    //Voir si on ne peut pas refactoriser les fonctions utilisant getCategoriesArray avec cette fonction
    public function getCategoriesCommercant(){
        $cats = Mage::getModel('apdc_commercant/shop')->getCollection()->load();
        $result = array();
        foreach($cats as $cat){
            $result = array_merge($result, $cat->getCategoryIds());
        }

        $cats = Mage::getModel('catalog/category')
            ->getCollection()
            ->setOrder('name')
            ->addAttributeToSelect('name')
            ->addFieldToFilter('entity_id', array('in' => $result));

        return $cats;
    }
	
    public function getInfoShopByCommercantId($productCommercantId)
    {
        if (!isset($this->shopIdByCommercantId[$productCommercantId])) {
            $shopId = $this->getShopIdFromProductCommecantId($productCommercantId);
            $this->shopIdByCommercantId[$productCommercantId] = $shopId;
        }
        if (is_null($this->shopIdByCommercantId[$productCommercantId])) {
            return [];
        }
        return $this->getInfoShop($this->shopIdByCommercantId[$productCommercantId]);
    }

	public function getInfoShop($shopId = null)
    {
        $shop = null;
		if($shopId == null) {
            $currentCat = Mage::registry('current_category');
            $shop = Mage::getModel('apdc_commercant/shop')->loadByCategoryId($currentCat->getId());
            $shopId = ($shop && $shop->getId() ? $shop->getId() : null);
		}

        if (is_null($shopId)) {
            return [];
        }

        if (!isset($this->infoShops[$shopId])) {
            $this->infoShops[$shopId] = [];
            if (!($shop && $shop->getId())) {
                $shop = Mage::getModel('apdc_commercant/shop')->load($shopId);
            }

            if ($shop && $shop->getId()) {
                $this->infoShops[$shopId] = $this->populateShopInfo($shop);
            }
        }

        if (Mage::getSingleton('core/session')->getDdate()) {
            $date = Mage::getSingleton('core/session')->getDdate();
            if (!empty($this->infoShops[$shopId]) && !isset($this->infoShops[$shopId]['availability'][$date])) {
                $this->infoShops[$shopId]['availability'][$date] = $this->populateShopAvailability($this->infoShops[$shopId]);
            }
        }
		
        return $this->infoShops[$shopId];
    }

    protected function populateShopInfo($shop)
    {
        $shopInfo = $shop->getData();
        $shopInfo['model'] = $shop;
        $shopInfo['availability'] = [];
        $shopInfo['adresse'] = $shop->getStreet() . ' ' . $shop->getPostcode() . ' ' . $shop->getCity();
        $shopInfo['url_adresse'] = 'https://www.google.fr/maps/place/' . str_replace(' ', '+', $shopInfo['adresse']);
        $categoryShop = $shop->getShopMainCategory();
        if ($categoryShop && $categoryShop->getId()) {
            $shopInfo['description'] = $categoryShop->getDescription();
            $shopInfo['image'] = Mage::helper('apdc_catalog/category')->getImageUrl($categoryShop);
            $shopInfo['thumbnail_image'] = Mage::helper('apdc_catalog/category')->getThumbnailImageUrl($categoryShop);
            $stores=$this->getStoresArray();
            $rootId=explode("/",$categoryShop->getPath())[1];
            $shopInfo['url'] = Mage::getUrl($categoryShop->getUrlPath(), array('_store'=>$stores[$rootId]['code']));
        }

        $html = '';
        $days = $this->getShortDays();//["Lun","Mar","Mer","Jeu","Ven","Sam","Dim"];
        foreach ($shop->getTimetable() as $day => $hours) {
            $hours = ($hours == '' ? 'Fermé' : $hours);
            $hoursExplode = explode('-', $hours);
            if (count($hoursExplode) > 3) {
                $hoursExplode1 = $hoursExplode[0].'-'.$hoursExplode[1];
                $hoursExplode2 = $hoursExplode[2].'-'.$hoursExplode[3];
                $hours = $hoursExplode1.' / '.$hoursExplode2;
            }
            $html .= '<strong>'.$days[$day]."</strong> : ".$hours."</br>";
        }
        $shopInfo['timetable'] = $html;


        if (!empty($shopInfo['closing_periods'])) {
            $today = new DateTime();
            $nextWeek = new DateTime();
            $nextWeek->add(new DateInterval('P7D'));
            $this->setLocaleFR();
            foreach ($shopInfo['closing_periods'] as $period) {
                $start = new DateTime($period['start']);
                $end = new DateTime($period['end']);
                if ($end <= $today) {
                    continue;
                }
                if ($today >= $start && $today <= $end) {
                    $shopInfo['is_closed'] = [
                        'message' => $this->__('La boutique du commerçant est fermée jusqu\'au %s', '<strong>' . ucwords(strftime('%e %B', $end->getTimestamp())) . '</strong>')
                    ];
                }
                $diff = $today->diff($start);
                if ($diff->format('%a') < $this->getClosingMessageDelay()) {
                    $shopInfo['next_closed'] = [
                        'message' => sprintf(
                            $this->__('La boutique du commerçant sera fermée du %s au %s'),
                            '<strong>' . ucwords(strftime('%e %B', $start->getTimestamp())) . '</strong>',
                            '<strong>' . ucwords(strftime('%e %B', $end->getTimestamp())) . '</strong>'
                        )
                    ];
                }
                if ($diff->format('%a') <= 7) {
                    $shopInfo['next_closed_this_week'] = [
                        'message' => sprintf(
                            $this->__('La boutique du commerçant sera fermée du %s au %s'),
                            '<strong>' . ucwords(strftime('%e %B', $start->getTimestamp())) . '</strong>',
                            '<strong>' . ucwords(strftime('%e %B', $end->getTimestamp())) . '</strong>'
                        )
                    ];
                }
            }
        }

        return $shopInfo;
    }

    protected function populateShopAvailability($shopInfo)
    {
        $availability = [];
        $timestamp = strtotime(Mage::getSingleton('core/session')->getDdate());
        $date = date('Y-m-d', $timestamp);
        $day = date('w', $timestamp);

        $status = 1;
        $messageInfo = '';
        if (!in_array($day, $shopInfo['delivery_days'])) {
            $status = 3;
        }
        if (!empty($shopInfo['closing_periods'])) {
            foreach ($shopInfo['closing_periods'] as $period) {
                if ($date >= $period['start'] && $date <= $period['end']) {
                    $start = $period['start'];
                    $end = $period['end'];
                    $status = 4;
                    break;
                }
            }
            if (isset($start) && isset($end)) {
                $this->setLocaleFR();
                if ($start == $end) {
                    $start = new DateTime($start);
                    $messageInfo = $this->__('Fermé le %s', ucwords(strftime('%e %B', $start->getTimestamp())));
                } else {
                    $start = new DateTime($start);
                    $end = new DateTime($end);
                    $messageInfo = sprintf(
                        $this->__('Fermé du %s au %s'),
                        ucwords(strftime('%e %B', $start->getTimestamp())),
                        ucwords(strftime('%e %B', $end->getTimestamp()))
                    );
                }
            }
        }
        $availability = [
            'status' => $status,
            'message' => Mage::getSingleton('apdc_catalog/source_product_availability')->getOptionLabel($status),
            'message_info' => $messageInfo
        ];
        return $availability;
    }

    public function getRandomShopImage($filter="all")
    {
        $collection = Mage::getModel('apdc_commercant/shop')->getCollection()            
            ->addFieldtoFilter('enabled',1);

        if($filter<>"all"){
            $collection->addFieldToFilter('type', $filter);
        }

        $collection->getSelect()->order('rand()');
        $collection->getSelect()->limit(1);

        if($collection->getFirstItem()->getCategoryImage()<>NULL){
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$collection->getFirstItem()->getCategoryImage();
        }else{
            $i = mt_rand(1, 17); // generate random number size of the array
            $url = "dist/images/header/".$i.".jpg"; // set variable equal to which random filename was chosen
            return Mage::getDesign()->getSkinUrl()."../default/".$url;
        }
    }

    /**
     * getClosingMessageDelay 
     * 
     * @return string
     */
    public function getClosingMessageDelay()
    {
        return Mage::getStoreConfig('apdc_general/availability/closing_message_delay');
    }

    /**
     * getShopIdFromProductCommecantId
     * 
     * @param int $productCommercantId productCommercantId 
     * 
     * @return int | null
     */
    public function getShopIdFromProductCommecantId($productCommercantId)
    {
        $resource = Mage::getSingleton('core/resource');
	    $readConnection = $resource->getConnection('core_read');
        $sql = 'SELECT id_shop FROM ' . $resource->getTableName('apdc_shop') . ' WHERE id_attribut_commercant = ' . (int) $productCommercantId;
        $result = $readConnection->fetchCol($sql);
        if (!empty($result)) {
            return $result[0];
        }
        return null;
    }

    public function getShops($filter="store",$random=false)
    {
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

        $commercants=array();

        foreach ($shops as $shop) {
            if (empty($commercants[$shop->getName()])) {
                $commercants[$shop->getName()] = $shop->getIdAttributCommercant();
            }
        }
        return $commercants;
    }
}
