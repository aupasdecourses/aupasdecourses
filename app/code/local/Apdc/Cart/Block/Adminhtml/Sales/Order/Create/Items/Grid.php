<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Cart
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Cart_Block_Adminhtml_Sales_Order_Create_Items_Grid 
 * 
 * @category Apdc
 * @package  Cart
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Cart_Block_Adminhtml_Sales_Order_Create_Items_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid
{
    /**
     * getItemCommentButtonHtml 
     * 
     * @param Mage_Sales_Model_Quote_Item $item item 
     * 
     * @return string
     */
    public function getItemCommentButtonHtml(Mage_Sales_Model_Quote_Item $item) 
    {
        if ($item->getItemComment()) {
            $options = array('label' => Mage::helper('sales')->__('Edit comment'));
        } else {
            $options = array('label' => Mage::helper('sales')->__('Add comment'));
        }
        $options['onclick'] = sprintf('apdcManageItemComment(%s, \'%s\')', $item->getId(), htmlentities($item->getItemComment(), ENT_QUOTES, 'utf-8'));

        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData($options)
            ->toHtml();
    }

    /**
     * getItemCommentAjaxUrl 
     * 
     * @param Mage_Sales_Model_Quote_Item $item item 
     * 
     * @return string
     */
    public function getItemCommentAjaxUrl(Mage_Sales_Model_Quote_Item $item)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/apdcCart/updateItemComment', array('item_id', $item->getId()));
    }

    /**
     * getItemCommentPopupContentAjaxUrl 
     * 
     * @param Mage_Sales_Model_Quote_Item $item item 
     * 
     * @return string
     */
    public function getItemCommentPopupContentAjaxUrl(Mage_Sales_Model_Quote_Item $item)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/apdcCart/getItemCommentPopupContentAjax', array('item_id', $item->getId()));
    }
}
