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

class Pmainguet_Filebatch extends Mage_Shell_Abstract
{

    public function listcatimage(){
        $cats=Mage::getModel('catalog/category')
                    ->getCollection()
                    ->addAttributeToSelect('*');
        foreach($cats as $cat){
            if($cat->getImage()){
                echo $cat->getImage()." ";
            }
        }
    }

    public function listcatthumb(){
        $cats=Mage::getModel('catalog/category')
                    ->getCollection()
                    ->addAttributeToSelect('*');
        foreach($cats as $cat){
            if($cat->getThumbnail()){
                echo $cat->getThumbnail()." ";
            }
        }
    }

    // Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['listcatimage','listcatthumb'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        if (in_array($step, $steps)) {
            $this->$step();
        } else {
            echo "STEP MUST BE ONE OF THESE:\n";
            foreach ($steps as $s) {
                echo $s.",\n";
            }
        }
    }
}

$shell = new Pmainguet_Filebatch();
$shell->run();