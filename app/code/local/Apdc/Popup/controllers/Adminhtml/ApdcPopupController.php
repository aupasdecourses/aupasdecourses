<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Popup
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Popup_Adminhtml_ApdcPopupController 
 * 
 * @category Apdc
 * @package  Popup
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Popup_Adminhtml_ApdcPopupController extends Mage_Adminhtml_Controller_Action
{
    /**
     * templateAjaxAction 
     * 
     * @return void
     */
    public function templateAjaxAction()
    {
        if ($this->getRequest()->getParam('isAjax') == 'true') {
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $id = $this->getRequest()->getParam('id', null);
            $name = ($id ? $id . '_apdc_popup' : 'apdc_popup');
            $response = array();
            try {

                $this->loadLayout();
                $block = Mage::app()->getLayout()->createBlock(
                    'apdc_popup/adminhtml_popup',
                    $name,
                    array('template' => 'apdc/apdc_popup/popup.phtml')
                );
                $block->setData('id', $id);

                $popupContent = $this->getLayout()->getBlock($name . '_child');
                if ($popupContent) {
                    $block->setChild($name . '_child', $popupContent);
                }

                $response['status'] = 'SUCCESS';
                $response['html'] = $block->toHtml();
            } catch (Mage_Core_Exception $e) {
                $msg = "";
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $msg .= $message.'<br/>';
                }
 
                $response['status'] = 'ERROR';
                $response['message'] = $msg;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot get apdc popup template.');
                Mage::logException($e);
            }
            if (!empty($response)) {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            }
            return;
        }
    }
}
