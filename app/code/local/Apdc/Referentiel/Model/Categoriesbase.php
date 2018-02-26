<?php

class Apdc_Referentiel_Model_Categoriesbase extends Mage_Core_Model_Abstract
{

    private $_limitsmall=1;
    private $_textcolor_l1 = '#ffffff';
    private $_bgcolors_l1 = [
        'Boucher' => '#f3606f',
        'Boulanger' => '#f57320',
        'Caviste' => '#c62753',
        'Primeur' => '#3ab64b',
        'Fromager' => '#faae37',
        'Poissonnier' => '#5496d7',
        'Epicerie' => '#2f4da8',
        'Traiteur' => '#272b32',
        'Bio' => '#00595E',
        'Envies' => '#800040',
    ];
    private $_maincats_l3=['Tous','Tout','Toute'];

    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_referentiel/categoriesbase');
    }

    private function _getImageRef(){
        $imagecat_collection=Mage::getModel('apdc_referentiel/categoriesbase')->getCollection();
        $ics = $imagecat_collection->toArray(array('name', 'url'))['items'];
        $result=[];
        foreach($ics as $i){
            if(!isset($result[$i['name']])){
                $result[$i['name']]=array(0=>$i['url']);
            }else{
                array_push($result[$i['name']], $i['url']);
            }
        }
        return $result;
    }

    private function _getPositionRef(){
        $imagecat_collection=Mage::getModel('apdc_referentiel/categoriesposition')->getCollection();
        $ics = $imagecat_collection->toArray(array('name', 'position'))['items'];
        $result=[];
        foreach($ics as $i){
            if(!isset($result[$i['name']])){
                $result[$i['name']]=array(0=>$i['position']);
            }else{
                array_push($result[$i['name']], $i['position']);
            }
        }
        return $result;
    }

    private function _setData($cat,$att,$value,$save=false){
        if($cat->getData($att)<>$value){
            $cat->setData($att,$value);
            if($save){
                $cat->save();
            }
            return "Fix ".$att." for Level ".$cat->getLevel()." cat: ".$cat->getId()." / ".$cat->getName()." (new value = ".$value.")\n";
        }else{
            return false;
        }
    }

    public function getCats($s=null,$f=null,$store=null){
        $storeId = 0;
        Mage::app()->setCurrentStore($storeId);
        $category = Mage::getModel( 'catalog/category' )->getCollection()
                ->addAttributeToSelect(array('store_id','name','image','thumbnail','is_clickable','is_active','show_in_navigation','show_age_popup','display_mode','meta_title','menu_bg_color','menu_text_color'));
        if($store<>null){
            $rootId = Mage::app()->getStore($store)->getRootCategoryId();
            $category->addFieldToFilter('path', array('like'=> "1/$rootId/%"));
        }
        if($s<>null){
            $category->addAttributeToFilter('level',array('gt'=>$s));
        }
        if($s<>null){
            $category->addAttributeToFilter('level',array('lt'=>$f));
        }
        return $category;
    }
    public function eraseerrorcat($pk){
        $forbidden=['#VALUE!','Tous les produits','Tous Les Produits'];
        //$forbidden=['Cavavin','Les Bonnes Crèmes','Paris Terroirs','Paris Terroirs 5e','Les Papilles Gourmandes','Les Boucheries Francis','Paris Terroirs','La Mère-Mimosa','Paris Terroirs','Scübe'];
        //$forbidden=['#VALUE!','Detox\'','Noël','Menus','Evènements','Spécial été','Spécial Eté','Tous les produits','Tous Les Produits'];
        if(in_array($pk->getName(), $forbidden)){
            $pk->delete();
            echo "Delete Forbidden Cat: ".$pk->getId()." / ".$pk->getName()."\n";
            return false;
        }
    }

    public function setimagecat($pk){
        $ics=$this->_getImageRef();
        $url="";
        $change="";
        $change2="";
        if($pk->getThumbnail()==NULL){
            if(isset($ics[$pk->getName()])){
                $rand=rand(0,sizeof($ics[$pk->getName()])-1);
                $url=$ics[$pk->getName()][$rand];
                $pk->setImage($url);
                $pk->setThumbnail($url);
                $change.="Image changed for cat: ".$pk->getId()." / ".$pk->getName()."\n";
            }elseif($pk->getLevel()==3){
                $shops=Mage::getModel("apdc_commercant/shop")->getCollection();
                $shops->addFieldToFilter("name",$pk->getName());
                $i=$shops->getFirstItem();
                $img=$i->getData("category_image");
                $th=$i->getData("category_thumbnail");
                if($img<>null && $img<>""){
                    $pk->setImage($img);
                    $change.="Image changed for cat: ".$pk->getId()." / ".$pk->getName()."\n";
                }
                if($th<>null && $th<>""){
                    $pk->setThumbnail($th);
                    $change.="Thumbnail changed for cat: ".$pk->getId()." / ".$pk->getName()."\n";
                }
            }else{
                $url=$ics['Default'][0];
                $pk->setImage($url);
                $pk->setThumbnail($url);
                $change.="Warning - no base image for: ".$pk->getId()." / ".$pk->getName().". Use default instead.\n";
            }
        }elseif($pk->getThumbnail()==$ics['Default'][0]){
            if(isset($ics[$pk->getName()])){
                $rand=rand(0,sizeof($ics[$pk->getName()])-1);
                $url=$ics[$pk->getName()][$rand];
                $pk->setImage($url);
                $pk->setThumbnail($url);
                $change.="Image changed for cat: ".$pk->getId()." / ".$pk->getName()."\n";
            }
        }
        if($change<>""){
            $pk->save();
            echo $change;
        }
        echo $change2;
    }

    public function setinfocat($pk){
        $url="";
        $change="";
        if($pk->getLevel()==2){
            echo $this->_setData($pk,'name',trim($pk->getName()),true);
            if($pk->getIsClickable()==1){
                $pk->setIsClickable(0);
                $change.="Fix issue with IsClickable for Level 2 cat: ".$pk->getId()." / ".$pk->getName()."\n";
            }
        }
        if($pk->getLevel()==3){
            if($pk->getParentCategory()->getName()=="Envies"){
               $change.=$this->_setData($pk,'is_clickable',0); 
            }else{
                if(!$pk->hasChildren() && $pk->getIsClickable()==1){
                    $pk->setIsClickable(0);
                    $change.="Fix issue with IsClickable for Level 3 with no children: ".$pk->getId()." / ".$pk->getName()."\n";
                }elseif($pk->hasChildren() && $pk->getIsClickable()==0){
                    $pk->setIsClickable(1);
                    $change.="Fix issue with IsClickable for activated cat: ".$pk->getId()." / ".$pk->getName()."\n";
                }
            }
        }
        if($pk->getLevel()>3 && $pk->getIsActive()==1 && $pk->getIsClickable()==0){
            $pk->setIsClickable(1);
            $change.="Fix issue with IsClickable for activated cat: ".$pk->getId()." / ".$pk->getName()."\n";
        }
        //A désactiver en temps normal
        //     $change.=$this->_setData($pk,'show_in_navigation',1);
        if($pk->getLevel()==2 && $pk->getName()=="Caviste"){
            if($pk->getShowAgePopup()==0){
                $pk->setShowAgePopup(1);
                $change.="Fix issue with ShowAgePopup for cat: ".$pk->getId()." / ".$pk->getName()."\n";
            }
            $subcats = $pk->getChildrenCategories();
            foreach($subcats as $subCat){
                $change.=$this->_setData($subCat,'show_age_popup',0,true);
            }
        }
        $change.=$this->_setData($pk,'display_mode',"PRODUCTS");
        if($pk->getMetaTitle()=="" || $pk->getMetaTitle()==null){
            $parent_name=$pk->getParentCategory()->getName(); 
            $pk->setMetaTitle($parent_name." - ".$pk->getName());
            $change.="Fix issue with MetaTitle for cat: ".$pk->getId()." / ".$pk->getName()."\n";
        }
        if($change<>""){
            $pk->save();
            echo $change;
        }
    }

    public function setsmallcat($pk){
        if($pk->getLevel()<=2){
            return;
        }else{
            $count=$pk->getProductCollection()->count();
            if($count==0 ){
                $pk->delete();
                echo "Delete Cat with 0 products: ".$pk->getId()." / ".$pk->getName()."\n";
                return false;
            }
            if($count<=$this->_limitsmall && $pk->getIsActive()==1){
                $pk->setIsActive(0);
                $pk->save();
                echo "Deactivate Small Cats: ".$pk->getId()." / ".$pk->getName()."\n";
                return false;
            }elseif($count>$this->_limitsmall && $pk->getIsActive()==0 && $pk->getParentCategory()->getIsActive()==1 && $pk->getParentCategory()->getName()<>"Envies"){
                $pk->setIsActive(1);
                $pk->save();
                echo "Activate Cats: ".$pk->getId()." / ".$pk->getName()."\n";
                return false;
            }
        }
    }

    public function disableshop($pk){
        if($pk->getLevel()==3){
            $shopid=Mage::getResourceModel("apdc_commercant/shop")->getShopIdByCategoryId($pk->getId());
            if($shopid<>false){
                $shops=Mage::getModel("apdc_commercant/shop")->getCollection();
                $shops->addFieldToFilter("id_shop",array($shopid));
                $i=$shops->getFirstItem();
                $status=$i->getData("enabled");
                if($status==0 && $pk->getIsActive()==1){
                    $pk->setIsActive(0);
                    $pk->save();
                    echo "Disable Categories of Disabled Shops: ".$pk->getId()." / ".$pk->getName()."\n";
                    return;
                }
                //A réactiver une fois qu'il n'y a plus de catégories fantomes de commerçants
                // elseif($status && $pk->getIsActive()==0){
                //     $pk->setIsActive(1);
                //     $pk->save();
                //     echo "Activate Main Shop Cat for enabled shops: ".$pk->getId()." / ".$pk->getName()."\n";
                //     return;
                // }
            }else{
                echo $this->_setData($pk,'is_active',1,true); 
            }
        }
    }

    public function deactivatesubcat($pk){
        if($pk->getLevel()<=3){
            return;
        }else{
            if($pk->getParentCategory()->getIsActive()==0 && $pk->getIsActive()==1){
                $pk->setIsActive(0);
                $pk->save();
                echo "Deactivate Children Cats of deactivated Parent Cat: ".$pk->getId()." / ".$pk->getName()."\n";
                return false;
            }
        }
    }

    public function sortcat($pk){
        $ics=$this->_getPositionRef();
        //pas vraiment fonctionnel, à tester en mettant en propriété de l'objet appelé en tant que singleton
        $counter_l3=10;
        if($pk->getLevel()<=2){
            return;
        }elseif($pk->getLevel()==3){
            if(in_array(explode(" ",$pk->getName())[0],$this->_maincats_l3)){
                echo $this->_setData($pk,'position',0,true);
                return false;
            }else{
                $result=$this->_setData($pk,'position',$counter_l3,true);
                if($result){
                    $counter+=10;
                    echo $result;
                }
                return false;
            }
        }else{
            if(isset($ics[$pk->getName()])){
                echo $this->_setData($pk,'position',$ics[$pk->getName()][0],true);
            }
        }
    }

    public function fixlevel2($pk){
        $change="";
        if($pk->getLevel()==2){
            if($pk->getIsActive()==0 && $pk->hasChildren()){
                $pk->setIsActive(1);
                $change.="Fix activation for Level 2 cat: ".$pk->getId()." / ".$pk->getName()."\n";
            }
            if(!isset($this->_bgcolors_l1[$pk->getName()])){
                echo "WARNING ".$pk->getName()." (".$pk->getId().") DOESN'T EXIST IN THE TABLE OF LEVEL 2 CATS !!!!";
            }
            $change.=$this->_setData($pk,'menu_bg_color',$this->_bgcolors_l1[$pk->getName()]);
            $change.=$this->_setData($pk,'menu_text_color',$this->_textcolor_l1);
        }
        if($change<>""){
            $pk->save();
            echo $change;
        }
    }

    public function setcorrectchildrennumber(){
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('catalog/category');
        $query_read = "SELECT p.entity_id, p.children_count, COUNT(c.entity_id) AS correct_children_count, COUNT(c.entity_id) - p.children_count AS child_diff FROM catalog_category_entity p LEFT JOIN catalog_category_entity c ON c.path LIKE CONCAT(p.path,'/%') WHERE 1 GROUP BY p.entity_id HAVING correct_children_count != p.children_count";
        $query_write = "UPDATE catalog_category_entity c SET children_count = (SELECT COUNT(*) FROM (SELECT * FROM catalog_category_entity) cc WHERE cc.path LIKE CONCAT(c.path, '/%') AND cc.path NOT LIKE CONCAT(c.path, '/%/%'));";
        $result=$readConnection->fetchAll($query_read);
        if((int) $result[0]['children_count']<0){
            $writeConnection->exec($query_write);
        }
    }

    public function clearcache(){
        Mage::app('admin')->setUseSessionInUrl(false);
        Mage::getConfig()->init();
        $types = Mage::app()->getCacheInstance()->getTypes();
        try {
            echo "Cleaning data cache... \n";
            flush();
            Mage::app()->cleanCache();
            // foreach ($types as $type => $data) {
            //     echo "Removing $type ... ";
            //     echo Mage::app()->getCacheInstance()->clean($data["tags"]) ? "Cache cleared!" : "There is some error!";
            //     echo "\n";
            // }
        } catch (exception $e) {
            die("[ERROR:" . $e->getMessage() . "]");
        }
        try {
            flush();
            Mage::getModel('catalog/product_image')->clearCache();
            // echo Mage::app()->getCacheInstance()->clean() ? "Cache cleared!" : "There is some error!";
            // echo "\n\n";
        } catch (exception $e) {
            die("[ERROR:" . $e->getMessage() . "]");
        }
    }

    public function fixCats($cat){
    	$this->eraseerrorcat($store);
        $this->setimagecat($store);
        $this->setinfocat($store);
        $this->setsmallcat($store); 
        $this->disableshop($store);
        $this->deactivatesubcat($store);
        $this->sortcat($store);
        $this->fixlevel2($store);
        $this->setcorrectchildrennumber();
    }

}