<?php

class Pmainguet_Custommenu_Block_Navigation extends WP_CustomMenu_Block_Navigation
{

    public function drawMenuItem($children, $level = 1)
    {
        $html = '<div class="itemMenu level' . $level . '">';
        $keyCurrent = $this->getCurrentCategory()->getId();
        foreach ($children as $child) {
            if (is_object($child) && $child->getIsActive()) {
                // --- class for active category ---
                $active = '';
                if ($this->isCategoryActive($child)) {
                    $active = ' actParent';
                    if ($child->getId() == $keyCurrent) $active = ' act';
                }
                // --- format category name ---
                $name = $this->escapeHtml($child->getName());
                if (Mage::getStoreConfig('custom_menu/general/non_breaking_space')) $name = str_replace(' ', '&nbsp;', $name);

                $store_id=Mage::app()->getStore()->getStoreId();
                $cat=Mage::getModel('catalog/category')->setStoreId($store_id)->load($child->getId());
                $isclickable=$cat->getResource()->getAttribute('is_clickable')->getFrontend()->getValue($cat);
                Mage::log(print_r($cat->getResource()->getAttribute('is_clickable')->getFrontend()->getValue($cat),1),null,'custommenu.log');
                if($isclickable=="Non"){
                    $html.= '<span style="display:block;">' . $name . '</span>';
                }else{
                    $html.= '<a class="itemMenuName level' . $level . $active . '" href="' . $this->    getCategoryUrl($child) . '"><span>' . $name . '</span></a>';
                }
                $activeChildren = $this->_getActiveChildren($child, $level);
                if (count($activeChildren) > 0) {
                    $html.= '<div class="itemSubMenu level' . $level . '">';
                    $html.= $this->drawMenuItem($activeChildren, $level + 1);
                    $html.= '</div>';
                }
            }
        }
        $html.= '</div>';
        return $html;
    }
}
