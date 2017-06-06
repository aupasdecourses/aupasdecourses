<?php
/* @author Pierre Mainguet
/**
 * Shopping cart controller
 */

require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'CartController.php';
class Apdc_Cart_CartController extends Mage_Checkout_CartController
{
    /**
     * Minicart delete action.
     */
    public function ajaxDeleteAction()
    {
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
        $id = (int) $this->getRequest()->getParam('id');
        $result = array();
        if ($id) {
            try {
                $item = $this->_getCart()->getQuote()->getItemById($id);
                $productId = $item->getProduct()->getId();
                $this->_getCart()->removeItem($id)->save();

                $result['qty'] = $this->_getCart()->getSummaryQty();
                $result['product_id'] = $productId;

                $this->loadLayout();
                $minicartContent = $this->getLayout()->getBlock('minicart_content');
                $minicartContent->setData('product_id', $productId);
                $result['content'] = $minicartContent->toHtml();

                $result['success'] = 1;
                $result['message'] = $this->__('Item was removed successfully.');
                Mage::dispatchEvent('ajax_cart_remove_item_success', array('id' => $id));
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $this->__('Can not remove the item.');
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Minicart ajax update qty action.
     */
    public function ajaxUpdateAction()
    {
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
        $id = (int) $this->getRequest()->getParam('id');
        $qty = $this->getRequest()->getParam('qty');
        $result = array();
        if ($id) {
            try {
                $cart = $this->_getCart();
                if (isset($qty)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $qty = $filter->filter($qty);
                }

                $quoteItem = $cart->getQuote()->getItemById($id);
                if (!$quoteItem) {
                    Mage::throwException($this->__('Quote item is not found.'));
                }
                if ($qty == 0) {
                    $cart->removeItem($id);
                } else {
                    $quoteItem->setQty($qty)->save();
                }
                $this->_getCart()->save();

                $this->loadLayout();
                $minicartContent = $this->getLayout()->getBlock('minicart_content');
                $minicartContent->setData('product_id', $quoteItem->getProductId());
                $result['content'] = $minicartContent->toHtml();

                $result['qty'] = $this->_getCart()->getSummaryQty();

                if (!$quoteItem->getHasError()) {
                    $result['message'] = $this->__('Item was updated successfully.');
                    $result['success'] = 1;
                } else {
                    $result['error'] = $quoteItem->getMessage();
                    $result['success'] = 0;
                }
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $this->__('Can not save item.');
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Update product configuration for a cart item
     */
    public function ajaxUpdateItemOptionsAction()
    {
        $cart   = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();
        if ($params['isAjax'] == 1) {
            if (!$this->_validateFormKey()) {
                Mage::throwException('Invalid form key');
                return;
            }

            if (!isset($params['options'])) {
                $params['options'] = array();
            }
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $quoteItem = $cart->getQuote()->getItemById($id);
                if (!$quoteItem) {
                    Mage::throwException($this->__('Quote item is not found.'));
                }

                $item = $cart->updateItem($id, new Varien_Object($params));
                if (is_string($item)) {
                    Mage::throwException($item);
                }
                if ($item->getHasError()) {
                    Mage::throwException($item->getMessage());
                }

                $related = $this->getRequest()->getParam('related_product');
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }


                $cart->save();

                $comment = htmlentities($this->getRequest()->getParam('item_comment'), ENT_QUOTES, 'UTF-8');
                $item->setItemComment($comment)
                    ->save();

                $this->_getSession()->setCartWasUpdated(true);

                Mage::dispatchEvent('checkout_cart_update_item_complete',
                    array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
                $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->escapeHtml($item->getProduct()->getName()));
                $result['status'] = 'SUCCESS';
                $result['message'] = $message;
                $result['quote_item_id'] = $quoteItem->getId();
                $result['item_id'] = $item->getId();
                //New Code Here
                $this->loadLayout();
                $minicartContent = $this->getLayout()->getBlock('minicart_content');
                $minicartContent->setData('product_id', $item->getProductId());
                $result['content'] = $minicartContent->toHtml();
                $result['product_id'] = $item->getProductId();
                $result['qty'] = $cart->getSummaryQty();

                Mage::register('referrer_url', $this->_getRefererUrl());
            } catch (Mage_Core_Exception $e) {
                $msg = '';
                if ($this->_getSession()->getUseNotice(true)) {
                    $msg = $e->getMessage();
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $msg .= $message.'<br/>';
                    }
                }

                $result['status'] = 'ERROR';
                $result['message'] = $msg;
            } catch (Exception $e) {
                $result['status'] = 'ERROR';
                $result['message'] = $this->__('Cannot update the item.');
                Mage::logException($e);
            }
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }
    }

    /**
     * ajaxUpdateMiniCartAccordion 
     * 
     * @return void
     */
    public function ajaxUpdateCartAccordionAction()
    {
        $postData = $this->getRequest()->getPost();
        $response = array();
        if((int)$postData['isAjax'] == 1 && (int)$postData['commercant'] > 0){

            $apdcCart = Mage::getSingleton('checkout/session')->getApdcCart();
            if (!$apdcCart) {
                $apdcCart = new Varien_Object();
                $apdcCart->setAccordion(array());
            }
            $accordion = $apdcCart->getAccordion();
            if (isset($postData['open']) && (int)$postData['open'] == 1) {
                $accordion[(int)$postData['commercant']] = 1;
            } else {
                $accordion[(int)$postData['commercant']] = 0;
            }
            $apdcCart->setAccordion($accordion);
            Mage::getSingleton('checkout/session')->setApdcCart($apdcCart);
            $response = array(
                'status' => 'OK',
            );
        } else {
            $response = array(
                'status' => 'ERROR',
                'message' => $this->__('Cannot update cart accordion')
            );
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

}
