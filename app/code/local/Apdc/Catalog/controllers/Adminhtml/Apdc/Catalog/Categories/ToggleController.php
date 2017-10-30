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
 * Apdc_Catalog_Adminhtml_Apdc_Catalog_Categories_ToggleController 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Adminhtml_Apdc_Catalog_Categories_ToggleController extends Mage_Adminhtml_Controller_Action
{
    protected $toggleAttributes = null;

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
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Toggle'), Mage::helper('adminhtml')->__('Toggle'));

        return $this;
    }
    /**
     * indexAction 
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Toggle enable/disable catégories'));
        $this->_getSession()->addNotice($this->getHelper()->__('Vous devez sélectionner au moins une catégorie. Toutes les sous-catégories seront également affectées par le changement.'));
        $this->initActions();

        $this->renderLayout();
    }

    public function toggleAction()
    {
        $disable = (boolean)$this->getRequest()->getParam('disable', false);
        $idCategories = $this->getRequest()->getPost('category_ids', null);
        try {
            if ($idCategories) {
                if ($disable) {
                    $this->disableCategories();
                } else {
                    $this->enableCategories();
                }
                $process = Mage::getModel('index/process')->load(5); // Reindex Category Flat Data
                $process->reindexAll();
            } else {
                $this->_getSession()->addError($this->getHelper()->__('Veuillez sélectionner au moins une catégorie.'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->setFormData(['category_ids' => $idCategories]);
            $this->_getSession()->addError($this->getHelper()->__('Impossible de mettre à jour les catégories (veuillez vérifier les logs)'));
        }
        return $this->_redirect('*/*/');
    }

    /**
     * disableCategories 
     * 
     * @return void
     */
    protected function disableCategories()
    {
        $cats = $this->getAllChildrens();
        $this->disableCats($cats->getAllIds());
        $this->_getSession()->addSuccess($this->getHelper()->__('%s catégories ont été désactivées', $cats->count()));
    }

    /**
     * enableCategories 
     * 
     * @return void
     */
    protected function enableCategories()
    {
        $cats = $this->getAllChildrens();
        $normalCats = [];
        $allProductsCats = [];
        foreach ($cats as $cat) {
            $cat->setIsActive(1);
            if (strtolower($cat->getName()) != 'tous les produits') {
                $normalCats[] = $cat->getId();
            } else {
                $allProductsCats[] = $cat->getId();
            }
        }
        $this->enableNormalCats($normalCats);
        $this->enableAllProductCats($allProductsCats);
        $this->_getSession()->addSuccess($this->getHelper()->__('%s catégories ont été activées', $cats->count()));
    }

    /**
     * getAllChildrens 
     * 
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function getAllChildrens()
    {
        $idCategories = $this->getRequest()->getPost('category_ids', null);
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
            ->addAttributeToSelect('name');
        $allCats->getSelect()->where('path REGEXP ?', implode('|', $searchPath));
        $allCats->load();

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

    /**
     * enableNormalCats 
     * 
     * @param array $catIds catIds 
     * 
     * @return void
     */
    protected function enableNormalCats($catIds)
    {
        $toggleAttributes = $this->getToggleAttributes();
        $connection = $this->getConnection();
        foreach ($toggleAttributes as $attribute) {
            $where = [
                'attribute_id = ?' => $attribute->getId(),
                'entity_id IN(?)' => $catIds
            ];
            $connection->update($attribute->getBackend()->getTable(), ['value' => 1], $where);
        }
    }

    /**
     * enableAllProductCats 
     * 
     * @param array $catIds catIds 
     * 
     * @return void
     */
    protected function enableAllProductCats($catIds)
    {
        $toggleAttributes = $this->getToggleAttributes();
        $connection = $this->getConnection();
        foreach ($toggleAttributes as $attributeCode => $attribute) {
            $where = [
                'attribute_id = ?' => $attribute->getId(),
                'entity_id IN(?)' => $catIds
            ];
            $value = ($attributeCode == 'is_active' ? 1 : 0);
            $connection->update($attribute->getBackend()->getTable(), ['value' => $value], $where);
        }
    }

    /**
     * disableCats 
     * 
     * @param array $catIds catIds 
     * 
     * @return void
     */
    protected function disableCats($catIds)
    {
        $toggleAttributes = $this->getToggleAttributes();
        $attribute = $toggleAttributes['is_active'];
        $connection = $this->getConnection();
        $where = [
            'attribute_id = ?' => $attribute->getId(),
            'entity_id IN(?)' => $catIds
        ];
        $connection->update($attribute->getBackend()->getTable(), ['value' => 0], $where);
    }

    /**
     * getToggleAttributes 
     * 
     * @return array[Mage_Eav_Model_Attribute]
     */
    protected function getToggleAttributes()
    {
        if (is_null($this->toggleAttributes)) {
            $this->toggleAttributes = [];
            $attributesCode = [
                'is_active',
                'is_clickable',
                'include_in_menu'
            ];
            $attributes = Mage::getModel('eav/entity_attribute')->getCollection()
                ->addFieldToFilter('entity_type_id', 3)
                ->addFieldToFilter('attribute_code', array('in' => $attributesCode));


            foreach ($attributes as $attribute) {
                $this->toggleAttributes[$attribute->getAttributeCode()] = $attribute;
            }
        }
        return $this->toggleAttributes;
    }

    protected function getResource()
    {
        return Mage::getSingleton('core/resource');
    }

    protected function getConnection()
    {
        return $this->getResource()->getConnection('core_write');
    }
}
