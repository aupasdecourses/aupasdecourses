<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Core
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Core_Block_Adminhtml_Tab_CategoriesTreeView 
 * 
 * @category Apdc
 * @package  Core
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Core_Block_Adminhtml_Tab_CategoriesTreeView
    extends Mage_Adminhtml_Block_Catalog_Category_Tree
{

    protected $_categoryIds = null;
    protected $_selectedNodes = null;
    protected $_categoriesIdsExpanded = null;

    public function __construct() {
        parent::__construct();
        $this->setTemplate('apdc/apdc_core/tab/categories.phtml');
        $this->_withProductCount = false;
    }

    public function getCategoryIds(){
        if (is_null($this->_categoryIds)){
            $this->_categoryIds = [];
        }
        return $this->_categoryIds;
    }

    /**
     * getCategoriesIdsExpanded 
     * 
     * @return array
     */
    protected function getCategoriesIdsExpanded()
    {
        if (is_null($this->_categoriesIdsExpanded)) {
            $this->_categoriesIdsExpanded = [];
            if (!empty($this->getCategoryIds())) {
                $categories = Mage::getModel('catalog/category')->getCollection()
                    ->addFieldToFilter('entity_id', array('in' => $this->getCategoryIds()));
                $categories->getSelect()->reset(Zend_Db_Select::COLUMNS);
                $categories->getSelect()->columns('path');
                $categories->getSelect()->columns('entity_id');
                foreach ($categories as $cat) {
                    $catPath = explode('/', $cat->getPath());
                    if(($key = array_search($cat->getId(), $catPath)) !== false) {
                        unset($catPath[$key]);
                    }

                    $this->_categoriesIdsExpanded = array_merge($this->_categoriesIdsExpanded, $catPath);
                }
            }
        }
        return $this->_categoriesIdsExpanded;
    }
    public function getIdsString(){
        return implode(',', $this->getCategoryIds());
    }
    public function getRootNode(){
        $root = $this->getRoot();
        if ($root && in_array($root->getId(), $this->getCategoryIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    public function getRoot($parentNodeCategory = null, $recursionLevel = 3){
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('category_root');
        if (is_null($root)) {
            $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            $ids = $this->getSelectedCategoryPathIds($rootId);
            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->loadByIds($ids, false, false);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);
            Mage::register('category_root', $root);
        }
        return $root;
    }
    protected function _getNodeJson($node, $level = 1){
        $item = parent::_getNodeJson($node, $level);
        $item['expanded'] = false;
        if ($this->_isParentSelectedCategory($node)) {
            $item['expanded'] = true;
        }
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }
        return $item;
    }
    protected function _isParentSelectedCategory($node){
        $result = false;
        // Contains string with all category IDs of children (not exactly direct) of the node
        $allChildren = $node->getAllChildren();
        if ($allChildren) {
            $selectedCategoryIds = $this->getCategoryIds();
            $allChildrenArr = explode(',', $allChildren);
            for ($i = 0, $cnt = count($selectedCategoryIds); $i < $cnt; $i++) {
                $isSelf = $node->getId() == $selectedCategoryIds[$i];
                if (!$isSelf && in_array($selectedCategoryIds[$i], $allChildrenArr)) {
                    $result = true;
                    break;
                }
            }
        } else {
            $categoriesIdsExpanded = $this->getCategoriesIdsExpanded();
            if (in_array($node->getId(), $categoriesIdsExpanded)) {
                $result = true;
            }
        }
        
        return $result;
    }
    protected function _getSelectedNodes(){
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getCategoryIds() as $categoryId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($categoryId);
                }
            }
        }
        return $this->_selectedNodes;
    }

    public function getCategoryChildrenJson($categoryId){
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $node = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);
        if (!$node || !$node->hasChildren()) {
            return '[]';
        }
        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }
        return Mage::helper('core')->jsonEncode($children);
    }
    public function getLoadTreeUrl($expanded = null){
        return $this->getUrl('*/*/categoriesJson', array('_current' => true));
    }
    public function getSelectedCategoryPathIds($rootId = false){
        $ids = array();
        $categoryIds = $this->getCategoryIds();
        if (empty($categoryIds)) {
            return array();
        }
        $collection = Mage::getResourceModel('catalog/category_collection');
        if ($rootId) {
            $collection->addFieldToFilter('parent_id', $rootId);
        }
        else {
            $collection->addFieldToFilter('entity_id', array('in'=>$categoryIds));
        }

        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }
}
