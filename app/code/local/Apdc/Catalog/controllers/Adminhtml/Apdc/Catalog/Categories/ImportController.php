<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Adminhtml_Apdc_Catalog_Categories_ImportController 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Adminhtml_Apdc_Catalog_Categories_ImportController extends Mage_Adminhtml_Controller_Action
{
    protected $count=0;

    /**
     * initActions 
     * 
     * @return void
     */
    protected function initActions()
    {
        $this->loadLayout()->_setActiveMenu('catalog/categories');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Categories'), Mage::helper('adminhtml')->__('Categories'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Infos'), Mage::helper('adminhtml')->__('Import Infos'));

        return $this;
    }
    /**
     * indexAction 
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Import infos catégories'));
        $this->initActions();

        $this->renderLayout();
    }

    public function importAction()
    {
        try {
            if (isset($_FILES['import_file']['name']) && $_FILES['import_file']['name'] != '') {

                $uploader = new Varien_File_Uploader('import_file');
                $uploader->setAllowedExtensions(array('csv'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $path = Mage::getBaseDir('var') . DS .'import'. DS . 'cats' . DS;
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }
                $uploader->save($path, $_FILES['import_file']['name']);
                $newFilename = $uploader->getUploadedFileName();
                $this->setcomcatinfo($newFilename);
                if ($this->count > 0) {
                    $this->_getSession()->addSuccess($this->getHelper()->__('%s catégories ont été mise à jour', $this->count));
                } else {
                    $this->_getSession()->addWarning($this->getHelper()->__('Aucune mies à jour détecté : aucune catégorie n\'a été mise à jour'));
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->getHelper()->__('Une erreur ne nous a pas permis d\'effectuer la mise à jour. Veuillez vérifier les logs.'));
        }
        return $this->_redirect('*/*/');
    }

    public function setcomcatinfo($filename)
    {
        $filepath = Mage::getBaseDir('var') . DS .'import'. DS . 'cats' . DS . $filename;
        $myFileLink = fopen($filepath, 'r');
        while (!feof($myFileLink) ) {
            $data[] = fgetcsv($myFileLink, 0);
        }
        fclose($myFileLink);
        unset($data[0]);

        foreach ($data as $line) {

            $overwrite = (int) $line[0];
            if ($overwrite == 1) {

                $rootcat = $line[1];
                $parent = $line[2];
                $level = $line[3];

                $id = $line[4];
                $name = $line[5];
                $keys = [
                    'thumbnail' => $line[6],
                    'image' => $line[7],
                    'is_active' => (int) $line[8],
                    'meta_title' => str_replace('"','',$line[9]),
                    'description' => str_replace('"','',$line[10]),
                    'meta_description' => str_replace('"','',$line[11]),
                    'is_clickable' => (bool) $line[12],
                    'include_in_menu' => (bool) $line[13],
                    'show_in_navigation' => $line[14],
                    'show_age_popup' => $line[15],
                    'display_mode' => $line[16],
                    'landing_page' => $line[17],
                    'menu_bg_color' => $line[18],
                    'menu_text_color' => $line[19],
                    'menu_main_static_block' => $line[21],
                    'menu_static_block1' => $line[22],
                ];

                $cat = Mage::getModel('catalog/category')->load($id);

                foreach ($keys as $key => $value) {
                    if ($cat->getData($key) != $value) {
                        $cat->setData($key, $value);
                    }
                }
                if ($cat->hasDataChanges()) {
                    $cat->save();
                    $this->count++;
                }
            }
        }
    }

    /**
     * getHelper 
     * 
     * @return Apdc_Catalog_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('apdc_catalog');
    }
}
