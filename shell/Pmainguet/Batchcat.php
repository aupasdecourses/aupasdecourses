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

    public function getcomcatinfo(){
            $allCats = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('level',array('in'=>array(3,4)));

            $result=array();
                
            foreach($allCats as $category)
            {
                
                if( $category->getName()!='Aux alentours:'){

                    $parent=$category->getParentCategory();

                    $result[]=array(
                        'id'=>$category->getId(),
                        'name'=>$category->getName(),
                        'thumb'=>$category->getThumbnail(),
                        'full'=>$category->getImage(),
                        'parent'=>$parent->getName(),
                        'parent_type'=>$parent->getLevel(),
                        'rootcat'=>explode("/",$category->getPath())[1],
                        'overwrite'=>0,
                    );
                }
            }

            $myFile = "download.csv";
            $myFileLink = fopen($myFile, 'w+') or die("Can't open file.");
            header('Content-type: application/octet-stream');  
            header('Content-disposition: attachment; filename="download.csv"'); 
            foreach($result as $line){
                fputcsv($myFileLink, $line);
            }
            fclose($myFileLink);
        }

    public function setcomcatinfo(){
            
            $myFile = "upload.csv";
            $myFileLink = fopen($myFile, 'r') or die("Can't open file ".$myFile);
            while (!feof($myFileLink) ) {
                $data[] = fgetcsv($myFileLink, 1024);
            }
            fclose($myFileLink);
                
            foreach($data as $line){
                $id=$line[0];
                $name=$line[1];
                $thumb=$line[2];
                $full=$line[3];
                $overwrite=(int) $line[7];

                if($thumb!='' OR $full!=''){
                    $cat=Mage::getModel('catalog/category')->load($id);
                    if($thumb!='' AND ($cat->getThumbnail()=='' OR $overwrite==1)){
                        $cat->setThumbnail($thumb);
                        echo "Thumbnail set for ".$name."\r\n";
                    }
                    if($full!='' AND ($cat->getImage()=='' OR $overwrite==1)){
                        $cat->setImage($full);
                        echo "Image set for ".$name."\r\n";
                    }
                    $cat->save();
                }
            }
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
        $steps = ['activatecat','activateagegate','getcomcatinfo','setcomcatinfo'];
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