<?php

require_once '../abstract.php';

class Pmainguet_CleanCatInfo extends Mage_Shell_Abstract
{

    private function _getCats($s,$f){
        $storeId = 0;
        Mage::app()->setCurrentStore($storeId);
        $category = Mage::getModel( 'catalog/category' )->getCollection()
            ->addAttributeToSelect(array('name','image','thumbnail','is_clickable','is_active','show_in_navigation','show_age_popup','display_mode','meta_title'))
            ->addAttributeToFilter('level',array('gt'=>$s))
            ->addAttributeToFilter('level',array('lt'=>$f));
        return $category;
    }

    public function setimagecats(){
        $cats=$this->_getCats(2,5);
        foreach ($cats as $cat) {
            Mage::getModel('apdc_referentiel/categoriesbase')->setimagecat($cat);
        }
        echo "setimagecats Done!\n";
    }

    public function setinfocats(){;
        $cats=$this->_getCats(1,6);
        foreach ($cats as $cat) {
            Mage::getModel('apdc_referentiel/categoriesbase')->setinfocat($cat);
        }
        echo "setinfocats Done!\n";
    }

    public function fixCats(){
        $cats=$this->_getCats(1,6);
        foreach ($cats as $cat) {
            $cat->save();
        }
    }

    // Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['setimagecats','setinfocats'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        $id = $this->getArg('id');
        if (in_array($step, $steps) && isset($id)) {
            $this->$step($id);
        }elseif($step==null){
            $this->fixCats();
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