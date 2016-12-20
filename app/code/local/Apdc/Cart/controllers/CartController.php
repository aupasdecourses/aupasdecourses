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
                $result['content'] = $this->getLayout()->getBlock('minicart_content')->toHtml();

                $result['qty'] = $this->_getCart()->getSummaryQty();

                if (!$quoteItem->getHasError()) {
                    $result['message'] = $this->__('Item was updated successfully.');
                } else {
                    $result['notice'] = $quoteItem->getMessage();
                }
                $result['success'] = 1;
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $this->__('Can not save item.');
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
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
