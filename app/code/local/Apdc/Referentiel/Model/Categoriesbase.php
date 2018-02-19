<?php

class Apdc_Referentiel_Model_Categoriesbase extends Mage_Core_Model_Abstract
{

    private $_limitsmall;

    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_referentiel/categoriesbase');
        $this->_limitsmall=1;
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

    public function getCats($s=null,$f=null,$store=null){
        $storeId = 0;
        Mage::app()->setCurrentStore($storeId);
        $category = Mage::getModel( 'catalog/category' )->getCollection()
                ->addAttributeToSelect(array('store_id','name','image','thumbnail','is_clickable','is_active','show_in_navigation','show_age_popup','display_mode','meta_title'));
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
        //$forbidden=['#VALUE!'];
        $forbidden=['#VALUE!','Detox\'','Noël','Menus','Evènements','Spécial été','Spécial Eté','Tous les produits','Tous Les Produits'];
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
        if($pk->getLevel()==2 && $pk->getIsClickable()==1){
            $pk->setIsClickable(0);
            $change.="Fix issue with IsClickable for Level 2 cat: ".$pk->getId()." / ".$pk->getName()."\n";
        }
        if($pk->getLevel()>2 && $pk->getIsActive()==1 && $pk->getIsClickable()==0){
            $pk->setIsClickable(1);
            $change.="Fix issue with IsClickable for activated cat: ".$pk->getId()." / ".$pk->getName()."\n";
        }
        //A désactiver en temps normal
        // if($pk->getShowInNavigation()!=1){
        //     $pk->setShowInNavigation(1);
        //     $change.="Fix issue with ShowInNavigation for activated cat: ".$pk->getId()." / ".$pk->getName()."\n";
        // }
        if($pk->getLevel()==2 && $pk->getName()=="Caviste"){
            if($pk->getShowAgePopup()==0){
                $pk->setShowAgePopup(1);
                $change.="Fix issue with ShowAgePopup for cat: ".$pk->getId()." / ".$pk->getName()."\n";
            }
            $subcats = $pk->getChildrenCategories();
            foreach($subcats as $subCat){
                if($subCat->setShowAgePopup()==0){
                  $subCat->setShowAgePopup(0);
                  $subCat->save();
                  $change.="Fix issue with ShowAgePopup for cat: ".$subCat->getId()." / ".$subCat->getName()."\n";
                }
            }
        }
        if($pk->getDisplayMode()<>"PRODUCTS"){
            $pk->setDisplayMode("PRODUCTS");
            $change.="Fix issue with DisplayMode for cat: ".$pk->getId()." / ".$pk->getName()."\n";
        }
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
            }elseif($count>$this->_limitsmall && $pk->getIsActive()==0 && $pk->getParentCategory()->getIsActive()==1){
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
        if($pk->getLevel()<=3){
            return;
        }else{
            if(isset($ics[$pk->getName()])){
                if($pk->getPosition()<>$ics[$pk->getName()][0]){
                    $pk->setPosition($ics[$pk->getName()][0]);
                    $pk->save();
                    echo "Order Cats: ".$pk->getId()." / ".$pk->getName()."\n";
                    return false;
                }
            }
        }
    }

    public function fixCats($cat){
        //$this->eraseerrorcat($cat);
    	$this->setimagecat($cat);
    	$this->setinfocat($cat);
        $this->setsmallcat($cat);
        $this->disableshop($cat);
        $this->deactivatesubcat($cat);
        //$this->sortcat($cat);
    }

}