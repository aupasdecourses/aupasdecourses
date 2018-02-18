<?php

require_once '../abstract.php';

class Pmainguet_CleanCatInfo extends Mage_Shell_Abstract
{

    private function _getCats($s,$f){
        $storeId = 0;
        Mage::app()->setCurrentStore($storeId);
        $category = Mage::getModel( 'catalog/category' );
        $tree = $category->getTreeModel();

        $tree->load();
        $ids = $tree->getCollection()
            ->addAttributeToFilter('level',array('gt'=>$s))
            ->addAttributeToFilter('level',array('lt'=>$f));
        return $ids->getAllIds();
    }

    public function setimagecats(){
        $ids=$this->_getCats(2,5);
        foreach ($ids as $id) {
            Mage::getModel('apdc_referentiel/categoriesbase')->setimagecat($id);
        }
        echo "setimagecats Done!\n";
    }

    public function setinfocats(){;
        $ids=$this->_getCats(1,6);
        foreach ($ids as $id) {
            Mage::getModel('apdc_referentiel/categoriesbase')->setinfocat($id);
        }
        echo "setinfocats Done!\n";
    }

    public function fixCats(){
        $ids=$this->_getCats(1,6);
        foreach ($ids as $id) {
            $cat=Mage::getModel('catalog/category')->load($id);
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