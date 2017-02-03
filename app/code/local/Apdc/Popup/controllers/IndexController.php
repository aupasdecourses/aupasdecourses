<?php

class Apdc_Popup_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * templateAjaxAction 
     * 
     * @return void
     */
    public function templateAjaxAction()
    {
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $id = (isset($params['id']) ? $params['id'] : null);
            $name = ($id ? $id . '_apdc_popup' : 'apdc_popup');
            $response = array();
            try {

                $response['status'] = 'SUCCESS';
                $block = Mage::app()->getLayout()->createBlock(
                    'apdc_popup/popup',
                    $name,
                    array('template' => 'apdc_popup/popup.phtml')
                );
                $block->setData('id', $id);
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
