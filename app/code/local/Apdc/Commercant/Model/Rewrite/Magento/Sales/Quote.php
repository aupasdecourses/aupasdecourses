<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Commercant_Model_Rewrite_Magento_Sales_Quote 
 * 
 * @category Apdc
 * @package  Commercant
 * @uses     Mage
 * @uses     Mage_Sales_Model_Quote
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Commercant_Model_Rewrite_Magento_Sales_Quote extends Mage_Sales_Model_Quote
{
    public function validateMinimumAmount($multishipping = false)
    {
        // Check if minimum order by commercant is reached
        if (parent::validateMinimumAmount($multishipping)) {
            $sidebar = Mage::app()->getLayout()->getBlock('minicart_content');
            if (!$sidebar) {
                $sidebar = Mage::app()->getLayout()->createBlock('apdccart/cart_sidebar');
            }
            if ($sidebar) {
                $commercantItems = $sidebar->getItemsByCommercant();
                foreach ($commercantItems as $id => $commercant) {
                    if ($commercant['minimum_order'] &&
                        $commercant['minimum_order'] > 0 &&
                        $commercant['items_subtotal'] < $commercant['minimum_order']
                    ) {
                        return false;
                    }
                }
            }

        }
        return true;
    }
}
