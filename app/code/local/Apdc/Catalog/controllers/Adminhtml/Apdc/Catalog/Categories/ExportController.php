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
 * Apdc_Catalog_Adminhtml_Apdc_Catalog_Categories_ExportController 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Adminhtml_Apdc_Catalog_Categories_ExportController extends Mage_Adminhtml_Controller_Action
{
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
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Infos'), Mage::helper('adminhtml')->__('Export Infos'));

        return $this;
    }
    /**
     * indexAction 
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Export infos catégories'));
        $this->_getSession()->addNotice($this->getHelper()->__('Si aucune catégorie n\'est sélectionnée alors toutes les catégories seront exportées.'));
        $this->_getSession()->addNotice($this->getHelper()->__('Si une catégorie est sélectionnée alors cette catégorie et toutes ses sous-catégories seront exportées.'));
        $this->initActions();

        $this->renderLayout();
    }

    public function downloadAction()
    {
        $idCategories = $this->getRequest()->getPost('id_category', null);
        try {
            if ($filepath = $this->generateCsv()) {
                return $this->_prepareDownloadResponse(
                    'categories-' . date('Y-m-d_H') . '.csv',
                    [
                        'type' => 'filename',
                        'value' => $filepath,
                        'rm' => true
                    ]
                );
            }
            $this->_getSession()->addError($this->getHelper()->__('Aucune catégorie ne correspond à votre sélection'));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->setFormData(['id_category' => $idCategories]);
            $this->_getSession()->addError($this->getHelper()->__('Impossible de mettre à jour les catégories (veuillez vérifier les logs)'));
        }
        return $this->_redirect('*/*/');
    }

    /**
     * generateCsv 
     * 
     * @return void
     */
    protected function generateCsv()
    {
        $idCategories = $this->getRequest()->getPost('id_category', null);
        if ($idCategories) {
            $allCats = $this->getAllChildrens();
        } else {
            $allCats = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*');
        }
        $allCats->addAttributeToFilter('level', ['in' => [2,3,4]]);

        if ($allCats->count() > 0) {
            $result = [];
            foreach ($allCats as $category) {
                if ($category->getName() != 'Aux alentours:') {
                    $parent = $category->getParentCategory();
                    $result[] = [
                        'overwrite'=>0,
                        'rootcat'=>explode("/",$category->getPath())[1],
                        'parent'=>$parent->getName(),
                        'level'=>$parent->getLevel(),
                        'id'=>$category->getId(),
                        'name'=>$category->getName(),
                        'thumb'=>$category->getThumbnail(),
                        'full'=>$category->getImage(),
                        'estcom_commercant'=>$category->getEstcomCommercant(),
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
                        'menu_template'=>$category->getMenuTemplate(),
                        'menu_main_static_block'=>$category->getMenuMainStaticBlock(),
                        'menu_static_block1'=>$category->getMenuStaticBlock1(),
                        'product_count'=>$category->getProductCount(),
                    ];
                }
            }

            $baseDir = Mage::getBaseDir('var') . DS . 'export';
            if (!file_exists($baseDir)) {
                mkdir($baseDir, 0755);
            }

            $filepath = $baseDir . DS . 'download.csv';
            $myFileLink = fopen($filepath, 'w+');
            fputcsv($myFileLink, array_keys($result[0]), ',', '"');
            foreach($result as $line){
                fputcsv($myFileLink, $line);
            }
            fclose($myFileLink);
            return $filepath;
        }
        return false;
    }

    /**
     * getAllChildrens 
     * 
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function getAllChildrens()
    {
        $idCategories = $this->getRequest()->getPost('id_category', null);
        if ($idCategories) {
            $idCategories = explode(',', trim($idCategories));
        }
        $selectedCats = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['in' => $idCategories]);

        $searchPath = [];
        foreach ($selectedCats as $cat) {
            $searchPath[$cat->getId()] = '^' . $cat->getPath();
        }
        $allCats = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*');
        $allCats->getSelect()->where('path REGEXP ?', implode('|', $searchPath));

        return $allCats;
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
