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
 * Apdc_Cart_Adminhtml_ApdcCartController 
 * 
 * @category Apdc
 * @package  Cart
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Cart_Adminhtml_ApdcCartController extends Mage_Adminhtml_Controller_Action
{
    public function updateItemCommentAjaxAction()
    {
        if ($this->getRequest()->getParam('isAjax') == 'true') {
            try {
                $itemId = (int)$this->getRequest()->getParam('item_id');
                if ($itemId > 0) {
                    //$item = Mage::getModel('sales/quote_item')->load($itemId);
                    $item = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getItemById($itemId);
                    if ($item && $item->getId() > 0) {
                        $comment = htmlentities($this->getRequest()->getParam('item_comment'), ENT_QUOTES, 'UTF-8');
                        $item->setItemComment($comment);
                        $item->save();
                        $response['status'] = 'SUCCESS';
                        $response['message'] = $this->__('Your comment has been saved successfully.');
                    } else {
                        $response['status'] = 'ERROR';
                        $response['message'] = $this->__('Unable to find the requested item');
                    }
                } else {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Unable to find the item id');
                }
            } catch (Mage_Core_Exception $e) {
                $msg = "";
                if ($this->_getSession()->getUseNotice(true)) {
                    $msg = $e->getMessage();
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $msg .= $message.'<br/>';
                    }
                }
 
                $response['status'] = 'ERROR';
                $response['message'] = $msg;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot add your comment to the item.');
                Mage::logException($e);
            }
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
    }
}
