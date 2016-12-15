<?php
/*
* @author Pierre Mainguet
*/

require_once 'Mage/Checkout/controllers/CartController.php';

class Apdc_Cart_IndexController extends Mage_Checkout_CartController{

    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {
            if (!$this->_validateFormKey()) {
                Mage::throwException('Invalid form key');
                return;
            }
            $response = array();
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }
 
                $product = $this->_initProduct();
                $related = $this->getRequest()->getParam('related_product');
                /**
                 * Check product availability
                 */
                if (!$product) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Unable to find Product ID');
                }
 
                $cart->addProduct($product, $params);
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }
 
                $cart->save();

                $this->_getSession()->setCartWasUpdated(true);

                /**
                 * @todo remove wishlist observer processAddToCart
                 */
                Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
 
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $response['status'] = 'SUCCESS';
                    $response['message'] = $message;
                    //New Code Here
                    $this->loadLayout();
                    $minicart_head = $this->getLayout()->getBlock('minicart_head')->toHtml();
                    Mage::register('referrer_url', $this->_getRefererUrl());
                    $response['minicarthead'] = $minicart_head;
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
                $response['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        } else {
            return parent::addAction();
        }
    }

    public function addCommentAjaxAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {
            if (!$this->_validateFormKey()) {
                Mage::throwException('Invalid form key');
                return;
            }
            $itemId = (int) $this->getRequest()->getParam('item_id');
            $response = array();
            try {
                $comment = filter_var($this->getRequest()->getParam('item_comment'), FILTER_SANITIZE_SPECIAL_CHARS);
                $item = $this->_getCart()->getQuote()->getItemById($itemId);
                $item->setItemComment($comment)
                    ->save();
                $productId = $item->getProduct()->getId();
                $this->loadLayout();
                $minicartContent = $this->getLayout()->getBlock('minicart_content');
                $minicartContent->setData('product_id', $productId);
                $response['minicarthead'] = $this->getLayout()->getBlock('minicart_head')->toHtml();

                $response['status'] = 'SUCCESS';
                $response['message'] = $this->__('Your comment has been saved successfully.');

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
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }
    }
    
    public function optionsAction(){
        $productId = $this->getRequest()->getParam('product_id');
        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId(false);
        $params->setSpecifyOptions(false);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

    //Adapted from app/code/local/Mage/Checkout/Model/Cart.php
    public function updateAction(){
        $cartData = $this->getRequest()->getParams();
            try {
                $filter = new Zend_Filter_LocalizedToNormalized(array('locale' => Mage::app()->getLocale()->getLocaleCode()));
                //Normalize and sanitize cartData to fit Magento format for updateItems function
                $data=array();
                $data[$cartData['id']]['qty'] = $filter->filter(trim($cartData['qty']));
                $data[$cartData['id']]['item_comment'] = filter_var($cartData['comments'], FILTER_SANITIZE_SPECIAL_CHARS);
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }
                $data = $cart->suggestItemsQty($data);
                //function in Pmainguet/QuoteItemComment/Model/Cart.php
                $cart->updateItems($data)->save();
                $this->_getSession()->setCartWasUpdated(true);
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s a été mis à jour.', Mage::helper('core')->escapeHtml('Le produit'));
                    $response['status'] = 'SUCCESS';
                    $response['message'] = $message;
                    //Update minicart and totals
                    $this->loadLayout();
                    $minicart_head = $this->getLayout()->getBlock('minicart_head')->toHtml();
                    $totals = $this->getLayout()->getBlock('checkout.cart.totals')->toHtml();
                    Mage::register('referrer_url', $this->_getRefererUrl());
                    $response['minicarthead'] = $minicart_head;
                    $response['totals'] = $totals;
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
                $response['message'] = $this->__('Cannot update shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
    }

}
