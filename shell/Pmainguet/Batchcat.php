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
                ->addAttributeToFilter('level',array('in'=>array(2,3,4,5)));

            $result=array();
                
            foreach($allCats as $category)
            {
                
                if( $category->getName()!='Aux alentours:'){

                    $parent=$category->getParentCategory();

                    $result[]=array(
                        'overwrite'=>0,
                        'rootcat'=>explode("/",$category->getPath())[1],
                        'parent'=>$parent->getName(),
                        'level'=>$parent->getLevel(),
                        'id'=>$category->getId(),
                        'name'=>$category->getName(),
                        'thumb'=>$category->getThumbnail(),
                        'full'=>$category->getImage(),
                        'is_active'=>$category->getIsActive(),
                        'meta_title'=>html_entity_decode(str_replace('"','',$category->getMetaTitle())),
                        'description'=>html_entity_decode(str_replace('"','',$category->getDescription())),
                        'meta_description'=>html_entity_decode(str_replace('"','',$category->getMetaDescription())),
                        'is_clickable'=>$category->getIsClickable(),
                        'include_in_menu'=>$category->getIncludeInMenu(),
                        'show_in_navigation'=>$category->getShowInNavigation(),
                        'show_age_popup'=>$category->getShowAgePopup(),
                        'display_mode'=>$category->getDisplayMode(),
                        'landing_page'=>$category->getLandingPage(),
                        'menu_bg_color'=>$category->getMenuBgColor(),
                        'menu_text_color'=>$category->getMenuTextColor(),
                        'menu_main_static_block'=>$category->getMenuMainStaticBlock(),
                        'menu_static_block1'=>$category->getMenuStaticBlock1(),
                        'product_count'=>$category->getProductCount(),
                    );
                }
            }

            $myFile = "download.csv";
            $myFileLink = fopen($myFile, 'w+') or die("Can't open file.");
            header('Content-type: application/octet-stream');  
            header('Content-disposition: attachment; filename="download.csv"'); 
            fputcsv($myFileLink, array_keys($result[0]),",",'""');
            foreach($result as $line){
                fputcsv($myFileLink, $line);
            }
            fclose($myFileLink);
        }

    public function setcomcatinfo(){
            
            $myFile = "upload.csv";
            $myFileLink = fopen($myFile, 'r') or die("Can't open file ".$myFile);
            while (!feof($myFileLink) ) {
                $data[] = fgetcsv($myFileLink, 0);
            }
            fclose($myFileLink);
            unset($data[0]);
                
            foreach($data as $line){

                $overwrite=(int) $line[0];

                if($overwrite==1){

                    $rootcat=$line[1];
                    $parent=$line[2];
                    $level=$line[3];

                    $id=$line[4];
                    $name=$line[5];
                    $keys=[
                        'thumbnail'=>$line[6],
                        'image'=>$line[7],
                        'is_active'=>(int) $line[8],
                        'meta_title'=>str_replace('"','',$line[9]),
                        'description'=>str_replace('"','',$line[10]),
                        'meta_description'=>str_replace('"','',$line[11]),
                        'is_clickable'=>(bool) $line[12],
                        'include_in_menu'=>(bool) $line[13],
                        'show_in_navigation'=>$line[14],
                        'show_age_popup'=>$line[15],
                        'display_mode'=>$line[16],
                        'landing_page'=>$line[17],
                        'menu_bg_color'=>$line[18],
                        'menu_text_color'=>$line[19],
                        'menu_main_static_block'=>$line[21],
                        'menu_static_block1'=>$line[22],
                    ];

                    $cat=Mage::getModel('catalog/category')->load($id);

                    foreach($keys as $key =>$value){
                        if($cat->getData($key)<>$value){
                            $cat->setData($key,$value);
                            echo $key." configuré pour ".$rootcat." >.. (".$level.") ".$parent." > ".$name."\r\n";
                        }
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
                $current->setIsActive(true);
                $current->setIsClickable(true);
                $current->setIncludeInMenu(true);
                echo 'Catégorie '.$i."/".$name." cliquable.\n";
            } else {
                $current->setIsActive(true);
                $current->setIsClickable('Non');
                $current->setIncludeInMenu('Non');
                echo 'Catégorie '.$i."/".$name." cliquable.\n";
            }
            $current->save();
        }
        echo "Catégorie ".$id."/".$maincat_name." et ses filles activées!\n\n";

    }

    //Activation d'une catégorie et des catégories filles
    public function deactivatecat($id)
    {
        $maincat_name = Mage::getModel('catalog/category')->load($id)->getName();
        $ids = $this->_getTreeIdsCategories($id);

        echo "//// Désactiver la catégorie ".$id."/".$maincat_name." et ses filles ////\n\n";
        
        foreach ($ids as $i => $name) {
            $current=Mage::getModel('catalog/category')->load($i);
            $current->setIsActive(false);
            echo 'Catégorie '.$i."/".$name." désactivée.\n";
            $current->save();
        }
        echo "Catégorie ".$id."/".$maincat_name." et ses filles désactivées!\n\n";

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

    //Activation de la popup Age d'une catégorie et des catégories filles
    public function deactivateagegate($id)
    {
        $maincat_name = Mage::getModel('catalog/category')->load($id)->getName();
        $ids = $this->_getTreeIdsCategories($id);

        echo "//// Désactiver la popup agegate sur catégorie ".$id."/".$maincat_name." et ses filles ////\n\n";
        
        foreach ($ids as $i => $name) {
            $current=Mage::getModel('catalog/category')->load($i);
                $current->setShowAgePopup(false);
                echo 'Popup activée sur '.$i."/".$name."\n";
            $current->save();
        }
        echo "Popup Agegate désactivée pour catégorie ".$id."/".$maincat_name." et ses filles!\n\n";

    }

    // Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['activatecat','activateagegate','getcomcatinfo','setcomcatinfo','deactivateagegate','deactivatecat'];
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
