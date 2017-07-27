<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  SuperMenu
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_SuperMenu_Block_Page_Html_Topmenu_Renderer 
 * 
 * @category Apdc
 * @package  SuperMenu
 * @uses     Mage
 * @uses     Mage_Page_Block_Html_Topmenu_Renderer
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_SuperMenu_Block_Page_Html_Topmenu_Renderer extends Apdc_SuperMenu_Block_Page_Html_Topmenu
{
    protected $_templateFile;
    protected $allowedTemplates = null;

    /**
     * Renders block html
     * @return string
     * @throws Exception
     */
    protected function _toHtml()
    {
        $this->_addCacheTags();
        $menuTree = $this->getMenuTree();
        $childrenWrapClass = $this->getChildrenWrapClass();
        $this->initRenderer($menuTree, $childrenWrapClass);
        return $this->render($menuTree, $childrenWrapClass);
    }

    /**
     * Add cache tags
     *
     * @return void
     */
    protected function _addCacheTags()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            $this->addCacheTag($parentBlock->getCacheTags());
        }
    }

    /**
     * initRenderer 
     * 
     * @param Varien_Data_Tree_Node $menuTree          : menuTree 
     * @param string                $childrenWrapClass : childrenWrapClass 
     * 
     * @return void
     */
    protected function initRenderer(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        if (!$this->getTemplate() || is_null($menuTree) || is_null($childrenWrapClass)) {
            throw new Exception("Top-menu renderer isn't fully configured.");
        }
        $menuTemplate = $menuTree->getMenuTemplate();
        $includeFilePath = null;
        $templateName = null;
        if ($menuTree->getMenuTemplate() && in_array($menuTree->getMenuTemplate(), $this->getAllowedTemplates())) {
            $templateName = $menuTree->getMenuTemplate();
        } else if($menuTree->getMenuTemplate() && $menuTree->getLevel() == 1) {
            $allowedTemplates = $this->getAllowedTemplates();
            $templateName = $allowedTemplates[0];
        }

        if (!is_null($templateName)) {
            $package = Mage::getSingleton('core/design_package');
            $templatePath = $package->getTemplateFilename('apdc_supermenu/topmenu/' . $templateName . '.phtml');
            if (file_exists($templatePath)) {
                $includeFilePath = $templatePath;
            }
        }
        if (is_null($includeFilePath)) {
            $includeFilePath = realpath(Mage::getBaseDir('design') . DS . $this->getTemplateFile());
        }
        if (strpos($includeFilePath, realpath(Mage::getBaseDir('design'))) === 0 || $this->_getAllowSymlinks()) {
            $this->_templateFile = $includeFilePath;
        } else {
            throw new Exception('Not valid template file:' . $this->_templateFile);
        }

    }

    /**
     * Fetches template. If template has return statement, than its value is used and direct output otherwise.
     * @param Varien_Data_Tree_Node $menuTree
     * @param $childrenWrapClass
     * @return string
     */
    public function render(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $this->initRenderer($menuTree, $childrenWrapClass);
        ob_start();
        $this->setCurrentMenuTree($menuTree);
        $html = include $this->_templateFile;
        $directOutput = ob_get_clean();

        if (is_string($html)) {
            return $html;
        } else {
            return $directOutput;
        }
    }
    /**
     * initLinkStyle 
     * 
     * @param Varien_Data_Tree_Node $child child 
     * 
     * @return void
     */
    public function initLinkStyle(Varien_Data_Tree_Node &$child)
    {
        $child->setLinkStyle('');
        $style = array();
        $bgColor = $child->getMenuBgColor();
        $textColor = $child->getMenuTextColor();
        if ($bgColor) {
            $style[] = 'background-color:' . $bgColor;
            $style[] = 'border-color:' . $bgColor;
        }
        if ($textColor) {
            $style[] = 'color:' . $textColor;
        }

        if (!empty($style)) {
            $child->setLinkStyle(implode('; ', $style));
        }
    }

    /**
     * Returns array of menu item's classes
     *
     * @param Varien_Data_Tree_Node $item
     * @return array
     */
    protected function _getMenuItemClasses(Varien_Data_Tree_Node $item)
    {
        $classes = array();

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }

        if ($item->getLevel() == 0) {
            if ($item->hasChildren()) {
                $classes[] = 'dropdown';
            }
        }
        if ($item->getLevel() == 1) {
            $classes[] = 'row';
            if ($item->hasChildren()) {
                $classes[] = 'dropdown dropdown-submenu menu-commercant';
            }
        }

        $classes[] = $this->getShowInNavigationClasses($item);

        return $classes;
    }

    /**
     * getItemPositionClassPrefix 
     * 
     * @param string $parentPositionClass : parentPositionClass 
     * 
     * @return string
     */
    public function getItemPositionClassPrefix($parentPositionClass)
    {
        return ($parentPositionClass ? $parentPositionClass . '-' : 'nav-');
    }

    /**
     * initChild 
     * 
     * @param Varien_Data_Tree_Node $child    : child 
     * @param Varien_Data_Tree_Node $menuTree : current menu tree 
     * 
     * @return Varien_Data_Tree_Node
     */
    public function initChild(Varien_Data_Tree_Node &$child, Varien_Data_Tree_Node $menuTree)
    {
        $parentLevel = $menuTree->getLevel();
        $childLevel = (is_null($parentLevel) ? 0 : $parentLevel + 1);
        $child->setLevel($childLevel);
        $child->setIsFirst($menuTree->getCounter() == 1);
        $child->setIsLast($menuTree->getCounter() == $menuTree->getChildren()->count());
        $child->setPositionClass($this->getItemPositionClassPrefix($menuTree->getPositionClass()) . $menuTree->getCounter());

        $this->initLinkClasses($child);
        $this->initSubMenuClasses($child);
        $this->initLinkStyle($child);

        return $child;
    }

    /**
     * initLinkClasses 
     * 
     * @param Varien_Data_Tree_Node $child child 
     * 
     * @return void
     */
    public function initLinkClasses(Varien_Data_Tree_Node &$child)
    {
        $child->setLinkClasses('');
        $linkClasses = array(
            'level' . $child->getLevel(),
        );
        if ($child->getLevel() <= 1) {
            $linkClasses[] = 'dropdown-toggle';
            if ($child->getLevel() == 1) {
                $linkClasses[] = 'column-commercant';
            }
        }
        if ($child->hasChildren()) {
            $linkClasses[] = 'has-children';
        }
        $child->setLinkClasses(implode(' ', $linkClasses));
    }

    /**
     * initSubMenuClasses 
     * 
     * @param Varien_Data_Tree_Node $child child 
     * 
     * @return void
     */
    public function initSubMenuClasses(Varien_Data_Tree_Node &$child)
    {
        $child->setSubMenuClasses('');
        $subMenuClasses = array();
        if ($child->hasChildren()) {
            $subMenuClasses = array(
                'level' . $child->getLevel()
            );
            if ($child->getLevel() == 0) {
                $subMenuClasses[] = 'dropdown-menu';
            } else if ($child->getLevel() == 1) {
                $subMenuClasses[] = 'dropdown-menu-template template-content';
            }
        }
        $child->setSubMenuClasses(implode(' ', $subMenuClasses));
    }

    /**
     * getStaticBlockHtml 
     * 
     * @param string $blockId blockId 
     * 
     * @return string
     */
    public function getStaticBlockHtml($blockId)
    {
        return $this->getLayout()->createBlock('cms/block')->setBlockId($blockId)->toHtml(); 
    }

    public function getResizedImage($image, $width, $height = null, $quality = 100) {
        if (!$image) {
            return false;
        }
        $baseDirMedia = Mage::getBaseDir('media') . DS . 'catalog' . DS;
     
        if (preg_match('/^wysiwyg\//', $image) || preg_match('/^catalog\/category\//', $image)) {
            $imageUrl = Mage::getBaseDir('media') . DS . $image;
        } else {
            $imageUrl = $baseDirMedia . 'category' . DS . $image;
        }
        if (!is_file($imageUrl)) {
            return false;
        }
     
        $imageResized =  $baseDirMedia . 'product' . DS . 'cache' . DS . 'cat_resized' . DS . $image;
        if (!file_exists($imageResized) && file_exists($imageUrl) || file_exists($imageUrl) && filemtime($imageUrl) > filemtime($imageResized)) {
            if (!file_exists($baseDirMedia . 'product' .DS . 'cache' . DS . 'cat_resized')) {
                mkdir($baseDirMedia . 'product' .DS . 'cache' . DS . 'cat_resized');
            }
            $imageObj = new Varien_Image($imageUrl);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->quality($quality);
            $imageObj->resize($width, $height);
            $imageObj->save($imageResized);
        }
     
        if(file_exists($imageResized)){
            return Mage::getBaseUrl('media' ) . 'catalog/product/cache/cat_resized/' . $image;
        } else if (preg_match('/^wysiwyg\//', $image) || preg_match('/^catalog\/category\//', $image)) {
            return Mage::getBaseUrl('media') . $image;
        } else {
            return Mage::getBaseUrl('media').'catalog/category/' . $image;
        }
    }


    /**
     * getTemplateColumnClasses 
     * 
     * @param Mage_Catalog_Model_Category $item            : item 
     * @param int                         $cpt             : column number
     * @param boolean                     $mainStaticBlock : Is there any static block before
     * 
     * @return array
     */
    public function getTemplateColumnClasses($item, $cpt, $mainStaticBlock)
    {
        $classes = array();
        $classes[] = $this->getShowInNavigationClasses($item);
        if (!$mainStaticBlock) {
            $cpt--;
        }
        if ($cpt % 5 == 0) {
            $classes[] = 'template-5-cols';
        }
        if ($cpt % 4 == 0) {
            $classes[] = 'template-4-cols';
        }
        if ($cpt % 3 == 0) {
            $classes[] = 'template-3-cols';
        }

        return implode(' ', $classes);
    }

    /**
     * getShowInNavigationClasses 
     * 
     * @param Mage_Catalog_Model_Category $item item 
     * 
     * @return string
     */
    public function getShowInNavigationClasses($item)
    {
        $classes = '';
        if ($item->getShowInNavigation() == 2) {
            $classes = 'hidden-xs';
        } else if ($item->getShowInNavigation() == 3) {
            $classes = 'visible-xs';
        }
        return $classes;
    }

    /**
     * getAllowedTemplates 
     * 
     * @return array
     */
    protected function getAllowedTemplates()
    {
        if (is_null($this->allowedTemplates)) {
            $this->allowedTemplates = array_keys(
                Mage::getModel('apdc_supermenu/adminhtml_system_config_source_template')
                ->getAllOptions()
            );
            array_shift($this->allowedTemplates);
        }
        return $this->allowedTemplates;
    }
}
