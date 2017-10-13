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
 * Apdc_Cart_Block_Cart_Sidebar 
 * 
 * @category Apdc
 * @package  Cart
 * @uses     Mage
 * @uses     Mage_Checkout_Block_Cart_Sidebar
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Cart_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{
    /**
     * getItemsByCommercant 
     * 
     * @return array
     */
    public function getItemsByCommercant()
    {
        if (!$this->getSummaryCount()) {
            return array();
        }
        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId()); // used to get commercant name

        if ($this->getCustomQuote()) {
            $itemsCollection = $this->getCustomQuote()->getItemsCollection();
        } else {
            $itemsCollection = $this->getQuote()->getItemsCollection();
        }
        $itemsCollection->getSelect()->order('main_table.commercant DESC');
        $itemsCollection->load();

        $parentCommercantId = array();
        $commercants = array();
        $items = array();
        foreach ($itemsCollection as $item) {
			$color = '#3ab64b';
            if ($item->getParentItemId() && $item->getCommercant()) {
                if (!isset($parentCommercantId[$item->getParentItemId()])) {
                    $parentCommercantId[$item->getParentItemId()] = $item->getCommercant();
                }
                continue;
            }
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $commercant = $item->getCommercant();
                if (!$commercant) {
                    if ($item->getProduct()->getTypeId() == 'bundle') {
                        if (!isset($commercants['bundle'])) {
                            $commercants['bundle']['name'] = $this->__('Paniers & Plateaux');
                            $commercants['bundle']['items'] = array();
                            $commercants['bundle']['color'] = $color;
                        }
                        $commercants['bundle']['items'][] = $item;
                    } else if (isset($parentCommercantId[$item->getItemId()])) {
                        $commercant = $parentCommercantId[$item->getItemId()];
                    }
                    continue;
                }
                if (!isset($commercants[$commercant])) {
                    $name = $product->setCommercant($commercant);
					$shop = Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('id_attribut_commercant', $commercant)->getFirstItem();
					if($shop) {
						$category = Mage::getModel('catalog/category')->load($shop['id_category'][0]);
						if($category && $category->getParentCategory()) {
							$color = $category->getParentCategory()->getData('menu_bg_color');
						}
					}
                    $commercants[$commercant]['name'] = $product->getAttributeText('commercant');
                    $commercants[$commercant]['items'] = array();
					$commercants[$commercant]['color'] = $color;
                }
                $commercants[$item->getCommercant()]['items'][] = $item;
            }
        }
        return $commercants;
    }

    public function getApdcCartAccordion()
    {
        $apdcCart = $this->getCheckout()->getApdcCart();
        if ($apdcCart) {
            $accordion = $apdcCart->getAccordion();
            if ($accordion) {
                return $accordion;
            }
        }
        return array();
    }

    /**
     * getApdcProductAddedToCartFromCommercantItems 
     * 
     * @param array $commercantItems commercantItems 
     * 
     * @return array
     */
    public function getApdcProductAddedToCartFromCommercantItems($commercantItems)
    {
        $productAdded = array();
        foreach ($commercantItems as $commercant) {
            if (isset($commercant['items'])) {
                foreach ($commercant['items'] as $item) {
                    if (!isset($productAdded[$item->getProductId()])) {
                        $productAdded[$item->getProductId()] = array(
                            'product' => $item->getProduct()->getData(),
                            'options' => array(),
                            'itemId' => $item->getId()
                        );
                    }

                    $buyRequest = $item->getBuyRequest();
                    $options = array();
                    if ($buyRequest->getSuperAttribute()) {
                        foreach ($buyRequest->getSuperAttribute() as $id => $values) {
                            if (is_array($values)) {
                                foreach ($values as $value) {
                                    $options[] = $id . '-' . $value;
                                }
                            } else {
                                $options[] = $id . '-' . $values;
                            }
                        }
                    }
                    if ($buyRequest->getOptions()) {
                        foreach ($buyRequest->getOptions() as $id => $values) {
                            if (is_array($values)) {
                                foreach ($values as $value) {
                                    $options[] = $id . '-' . $value;
                                }
                            } else {
                                $options[] = $id . '-' . $values;
                            }
                        }
                    }
                    if ($buyRequest->getBundleOption()) {
                        foreach ($buyRequest->getBundleOption() as $id => $values) {
                            if (is_array($values)) {
                                foreach ($values as $value) {
                                    $options[] = $id . '-' . $value;
                                }
                            } else {
                                $options[] = $id . '-' . $values;
                            }
                        }
                    }
                    if (!empty($options)) {
                        $productAdded[$item->getProductId()]['options'][implode('_', $options)] = array(
                            'qty' => $buyRequest->getQty(),
                            'comment' => html_entity_decode($item->getItemComment(), ENT_QUOTES, 'UTF-8'),
                            'itemId' => $item->getId()
                        );
                    } else {
                        $productAdded[$item->getProductId()]['qty'] = $item->getQty();
                        $productAdded[$item->getProductId()]['comment'] = html_entity_decode($item->getItemComment(), ENT_QUOTES, 'UTF-8');
                    }
                }
            }
        }
        return $productAdded;
    }

    /**
     * getMessages 
     * 
     * @return void
     */
    public function getMessages() 
    {
        // Compose array of messages to add
        $messages = array();
        foreach (Mage::getSingleton('checkout/cart')->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        Mage::getSingleton('checkout/cart')->getCheckoutSession()->addUniqueMessages($messages);

        return Mage::getSingleton('checkout/cart')->getCheckoutSession()->getMessages(true);
    }

    /**
     * Retrieve messages block
     *
     * @return Mage_Core_Block_Messages
     */
    public function getMessagesBlock()
    {
        return $this->getLayout()->createBlock('core/messages', 'minicart_messages');
    }

    /**
     * getQuote 
     * 
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated()) {
            $this->_quote = Mage::getSingleton('checkout/cart')->getQuote();
        }
        return parent::getQuote();
    }
}
