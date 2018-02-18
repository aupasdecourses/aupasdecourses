<?php
class Apdc_Referentiel_Model_Categoriesbase extends Mage_Core_Model_Abstract
{
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
                $change2.="Warning - no base image for: ".$pk->getId()." / ".$pk->getName().". Use default instead.\n";
            }
        }elseif($pk->getThumbnail()==$ics['Default'][0]){
            if(isset($ics[$pk->getName()])){
                $rand=rand(0,sizeof($ics[$pk->getName()])-1);
                $url=$ics[$pk->getName()][$rand];
                $pk->setImage($url);
                $pk->setThumbnail($url);
                $change.="Image changed for cat: ".$pk->getId()." / ".$pk->getName()."\n";
            }else{
                $change2.="Warning - no base image for: ".$pk->getId()." / ".$pk->getName().". Keep default instead.\n";
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
        if($pk->getShowInNavigation()!=1){
            $pk->setShowInNavigation(1);
            $change.="Fix issue with ShowInNavigation for activated cat: ".$pk->getId()." / ".$pk->getName()."\n";
        }
        //set show_age_popup
        if($pk->getLevel()==2 && $pk->getName()=="Caviste"){
            if($pk->getShowAgePopup()==0){
                $pk->setShowAgePopup(1);
                $change.="Fix issue with ShowAgePopup for cat: ".$pk->getId()." / ".$pk->getName()."\n";
            }
            $subcats = $pk->getChildrenCategories();
            foreach($subcats as $subCatid){
                $catsub = Mage::getModel('catalog/category')->load($subCatid);
                if($catsub->setShowAgePopup()==0){
                  $catsub->setShowAgePopup(1);
                  $catsub->save();
                  $change.="Fix issue with ShowAgePopup for cat: ".$catsub->getId()." / ".$catsub->getName()."\n";
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

    public function fixCats($cat){
    	$this->setimagecat($cat);
    	$this->setinfocat($cat);
    }

}