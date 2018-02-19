<?php

require_once '../abstract.php';

class Pmainguet_CleanCatInfo extends Mage_Shell_Abstract
{
    
    public function eraseerrorcats($store=null){
        $cats= Mage::getSingleton('apdc_referentiel/categoriesbase')->getCats(1,6,$store);
        foreach ($cats as $cat) {
            Mage::getSingleton('apdc_referentiel/categoriesbase')->eraseerrorcat($cat);
        }
        echo "eraseerrorcats Done!\n";
    }

    public function setimagecats($store=null){
        $cats= Mage::getSingleton('apdc_referentiel/categoriesbase')->getCats(2,5,$store);
        foreach ($cats as $cat) {
            Mage::getSingleton('apdc_referentiel/categoriesbase')->setimagecat($cat);
        }
        echo "setimagecats Done!\n";
    }

    public function setinfocats($store=null){;
        $cats= Mage::getSingleton('apdc_referentiel/categoriesbase')->getCats(1,6,$store);
        foreach ($cats as $cat) {
            Mage::getSingleton('apdc_referentiel/categoriesbase')->setinfocat($cat);
        }
        echo "setinfocats Done!\n";
    }

    public function setsmallcats($store=null){
        $cats= Mage::getSingleton('apdc_referentiel/categoriesbase')->getCats(2,6,$store);
        foreach ($cats as $cat) {
            Mage::getSingleton('apdc_referentiel/categoriesbase')->setsmallcat($cat);
        }
        echo "setsmallcats Done!\n";
    }

    public function disableshops($store=null){
        $cats= Mage::getSingleton('apdc_referentiel/categoriesbase')->getCats(2,4,$store);
        foreach ($cats as $cat) {
            Mage::getSingleton('apdc_referentiel/categoriesbase')->disableshop($cat);
        }
        echo "disableshops Done!\n";
    }

    public function deactivatesubcats($store=null){
        $cats= Mage::getSingleton('apdc_referentiel/categoriesbase')->getCats(1,6,$store);
        foreach ($cats as $cat) {
            Mage::getSingleton('apdc_referentiel/categoriesbase')->deactivatesubcat($cat);
        }
        echo "deactivatesubcats Done!\n";
    }

    public function sortcats($store=null){
        $cats= Mage::getSingleton('apdc_referentiel/categoriesbase')->getCats(1,6,$store);
        foreach ($cats as $cat) {
            Mage::getSingleton('apdc_referentiel/categoriesbase')->sortcat($cat);
        }
        echo "sortcats Done!\n";
    }

    public function shellfixCats($store=null){
        $this->eraseerrorcats($store);
        $this->setimagecats($store);
        $this->setinfocats($store);
        $this->setsmallcats($store); 
        $this->disableshops($store);
        $this->deactivatesubcats($store);
        $this->sortcats($store);
    }

    // Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['eraseerrorcats','setimagecats','setinfocats','setsmallcats','disableshops','deactivatesubcats','sortcats'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        $store = $this->getArg('store');
        if (in_array($step, $steps)) {
            if(isset($store)){
                $this->$step($store);    
            }else{
                $this->$step();
            }
        }elseif($step==null){
            if(isset($store)){
                $this->shellfixCats($store);    
            }else{
                $this->shellfixCats();
            }
        }else {
            echo "STEP MUST BE ONE OF THESE:\n";
            foreach ($steps as $s) {
                echo $s.",\n";
            }
        }
    }
}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CleanCatInfo();
$shell->run();