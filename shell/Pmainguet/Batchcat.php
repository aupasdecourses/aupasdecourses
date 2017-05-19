<?php

/* To start we need to include abscract.php, which is located 
 * in /shell/abstract.php which contains Magento's Mage_Shell_Abstract 
 * class. 
 *
 * Since this .php is in /shell/Namespace/ we
 * need to include ../ in our require statement which means the
 * file we are including is up one directory from the current file location.
 */
require_once '../abstract.php';

class Pmainguet_Batchcat extends Mage_Shell_Abstract
{

    private function _getTreeIdsCategories($id){
            $allCats = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('parent_id',array('eq' => $id)); 

            $result=array();
                
            foreach($allCats as $category)
            {
                $result[$category->getId()]=$category->getName();
                $subcats = $category->getChildren();
                if($subcats != ''){ 
                    $temp=$this->_getTreeIdsCategories($category->getId());
                    foreach($temp as $k => $v){
                        $result[$k]=$v;
                    }
                }
            }
            return $result;
        }

    //Activation d'une catégorie et des catégories filles
    public function activatecat($id)
    {
        $maincat_name = Mage::getModel('catalog/category')->load($id)->getName();
        $ids = $this->_getTreeIdsCategories($id);

        echo "//// Activer la catégorie ".$id."/".$maincat_name." et ses filles ////\n\n";
        
        foreach ($ids as $i => $name) {
            $current=Mage::getModel('catalog/category')->load($i);
            if (strtolower($current->getName())<>"tous les produits") {
                $current->setIsClickable(true);
                $current->setIncludeInMenu(true);
                echo 'Catégorie '.$i."/".$name." cliquable.\n";
            } else {
                $current->setIsClickable('Non');
                $current->setIncludeInMenu('Non');
                echo 'Catégorie '.$i."/".$name." cliquable.\n";
            }
            $current->save();
        }
        echo "Catégorie ".$id."/".$maincat_name." et ses filles activées!\n\n";

    }

    //Activation de la popup Age d'une catégorie et des catégories filles
    public function activateagegate($id)
    {
        $maincat_name = Mage::getModel('catalog/category')->load($id)->getName();
        $ids = $this->_getTreeIdsCategories($id);

        echo "//// Activer la popup agegate sur catégorie ".$id."/".$maincat_name." et ses filles ////\n\n";
        
        foreach ($ids as $i => $name) {
            $current=Mage::getModel('catalog/category')->load($i);
                $current->setShowAgePopup(true);
                echo 'Popup activée sur '.$i."/".$name."\n";
            $current->save();
        }
        echo "Popup Agegate activée pour catégorie ".$id."/".$maincat_name." et ses filles!\n\n";

    }

    // Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['activatecat','activateagegate'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        $id = $this->getArg('id');
        if (in_array($step, $steps) && isset($id)) {
            $this->$step($id);
        } else {
            echo "STEP MUST BE ONE OF THESE:\n";
            foreach ($steps as $s) {
                echo $s.",\n";
            }
        }
    }
}

// Create a new instance of our class and run it.
$shell = new Pmainguet_Batchcat();
$shell->run();