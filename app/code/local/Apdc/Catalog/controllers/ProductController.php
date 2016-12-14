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

require_once Mage::getModuleDir('controllers', 'Mage_Catalog') . DS . 'ProductController.php';

/**
 * Apdc_Catalog_IndexController 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Catalog_ProductController
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_ProductController extends Mage_Catalog_ProductController
{
    /**
     * ajaxQuickViewAction 
     * 
     * @return string
     */
    public function ajaxQuickViewAction()
    {
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $response = array();
            try {

                $categoryId = (int) $this->getRequest()->getParam('category', false);
                $productId  = (int) $this->getRequest()->getParam('id');
                $specifyOptions = $this->getRequest()->getParam('options', false);

                // Prepare helper and params
                $viewHelper = Mage::helper('apdc_catalog/product_view');

                $params = new Varien_Object();
                $params->setCategoryId($categoryId);
                $params->setSpecifyOptions($specifyOptions);

                // Render page
                $viewHelper->prepareAndRender($productId, $this, $params);
                $response['status'] = 'SUCCESS';
                $response['html'] = $this->getLayout()->getOutput();
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
                $response['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            if (!empty($response)) {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            }
            return;
        }
    }
}
