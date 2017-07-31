<?php
/*
* @author Pierre Mainguet
*/

require_once 'Mage/Checkout/controllers/CartController.php';

class Apdc_Cart_IndexController extends Mage_Checkout_CartController{

    public function addAction()
    {
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {
            if (!$this->_validateFormKey()) {
                Mage::throwException('Invalid form key');
                return;
            }
            $result = array();
            $cart   = $this->_getCart();
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }
 
                $product = $this->_initProduct();
                /**
                 * Check product availability
                 */
                if (!$product) {
                    $result['status'] = 'ERROR';
                    $result['message'] = $this->__('Unable to find Product ID');
                }
 
                $cart->addProduct($product, $params);
 
                $cart->save();

                // Reload quote to clean and fetch new error messages
                $quote = Mage::getModel('sales/quote')->load($this->_getCart()->getQuote()->getId());
                $quote->getItemsCollection()->load();
                $this->_getCart()->setQuote($quote);

                $this->_getSession()->setCartWasUpdated(true);

                /**
                 * @todo remove wishlist observer processAddToCart
                 */
                Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
 
                $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                $result['status'] = 'SUCCESS';
                $result['message'] = $message;

                $this->loadLayout();
                $minicartContent = $this->getLayout()->getBlock('minicart_content');
                $minicartContent->setData('product_id', $product->getId());
                $result['content'] = $minicartContent->toHtml();
                $result['product_id'] = $product->getId();
                $result['qty'] = $cart->getSummaryQty();

                Mage::register('referrer_url', $this->_getRefererUrl());

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
 
                $result['status'] = 'ERROR';
                $result['message'] = $msg;
            } catch (Exception $e) {
                $result['status'] = 'ERROR';
                $result['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        } else {
            return parent::addAction();
        }
    }

    public function addRelatedToProductsAction()
    {
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {
            if (!$this->_validateFormKey()) {
                Mage::throwException('Invalid form key');
                return;
            }
            $result = array();
            $cart   = $this->_getCart();
            try {
 
                $related = $this->getRequest()->getParam('related_product');
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }
 
                $cart->save();

                // Reload quote to clean and fetch new error messages
                $quote = Mage::getModel('sales/quote')->load($this->_getCart()->getQuote()->getId());
                $quote->getItemsCollection()->load();
                $this->_getCart()->setQuote($quote);

                $this->_getSession()->setCartWasUpdated(true);
 
                $message = $this->__('Les suggestions ont bien été ajoutées à votre panier');
                $result['status'] = 'SUCCESS';
                $result['message'] = $message;
                //New Code Here
                $this->loadLayout();
                $minicartContent = $this->getLayout()->getBlock('minicart_content');
                $result['content'] = $minicartContent->toHtml();
                $result['qty'] = $cart->getSummaryQty();

                Mage::register('referrer_url', $this->_getRefererUrl());

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
 
                $result['status'] = 'ERROR';
                $result['message'] = $msg;
            } catch (Exception $e) {
                $result['status'] = 'ERROR';
                $result['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        } else {
            return parent::addAction();
        }
    }

    public function addCommentAjaxAction()
    {
        $params = $this->getRequest()->getPost();
        if ($params['isAjax'] == 1) {
            if (!$this->_validateFormKey()) {
                Mage::throwException('Invalid form key');
                return;
            }
            $itemId = (int) $this->getRequest()->getParam('item_id');
            $cart   = $this->_getCart();
            $result = array();
            try {
                $comment = htmlentities($this->getRequest()->getParam('item_comment'), ENT_QUOTES, 'UTF-8');
                $item = $this->_getCart()->getQuote()->getItemById($itemId);
                $item->setItemComment($comment)
                    ->save();
                $productId = $item->getProduct()->getId();

                // Reload quote to clean and fetch new error messages
                $quote = Mage::getModel('sales/quote')->load($this->_getCart()->getQuote()->getId());
                $quote->getItemsCollection()->load();
                $this->_getCart()->setQuote($quote);

                $result['status'] = 'SUCCESS';
                $result['message'] = $this->__('Your comment has been saved successfully.');

                $this->loadLayout();
                $minicartContent = $this->getLayout()->getBlock('minicart_content');
                $minicartContent->setData('product_id', $productId);
                $result['content'] = $minicartContent->toHtml();
                $result['product_id'] = $productId;
                $result['qty'] = $this->_getCart()->getSummaryQty();

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
 
                $result['status'] = 'ERROR';
                $result['message'] = $msg;
            } catch (Exception $e) {
                $result['status'] = 'ERROR';
                $result['message'] = $this->__('Cannot add your comment to the item.');
                Mage::logException($e);
            }
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
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
}
