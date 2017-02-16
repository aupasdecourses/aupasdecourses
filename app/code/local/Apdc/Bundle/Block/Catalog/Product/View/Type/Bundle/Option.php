<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Bundle option renderer
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Apdc_Bundle_Block_Catalog_Product_View_Type_Bundle_Option extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option
{
    
    /**
     * Returns the formatted string for the quantity chosen for the given selection
     *
     * @param Mage_Catalog_Model_Proudct $_selection
     * @param bool                       $includeContainer
     * @return string
     */
    public function getSelectionQtyTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
        $this->setFormatProduct($_selection);

        $product = Mage::getSingleton('catalog/product')->load($_selection->getId());
        $unit_price=$product->getUnitePrix();
        $weight=$product->getWeight();

        if(isset($unit_price)){
            switch($unit_price){
                case "pièce":
                    $priceTitle = $_selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($_selection->getName());
                    if($_selection->getShortDescription()<>""){
                        $priceTitle = $_selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($_selection->getName()).' <span class="small">'.$this->escapeHtml($_selection->getShortDescription()).'</span>';
                    }
                    break;
                case "kg":         
                    $priceTitle = strval($_selection->getSelectionQty() *floatval($weight)*1000);
                    $priceTitle.= 'g de ' . $this->escapeHtml($_selection->getName());
                    break;
                default:
                    $priceTitle = $_selection->getSelectionQty() * 1 . ' portion de ' . $this->escapeHtml($_selection->getName());
                    if($_selection->getShortDescription()<>""){
                        $priceTitle = $_selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($_selection->getName()).' <span class="small">'.$this->escapeHtml($_selection->getShortDescription()).'</span>';
                    }
                    break;
            }
        }else{
            $priceTitle = $_selection->getSelectionQty() * 1 . ' portion de ' . $this->escapeHtml($_selection->getName());
            if($_selection->getShortDescription()<>""){
                    $priceTitle = $_selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($_selection->getName()).' <span class="small">'.$this->escapeHtml($_selection->getShortDescription()).'</span>';
               } 
       }

        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
                    . $this->formatPriceString($price, $includeContainer)
                    . ($includeContainer ? '</span>' : '');

        return $priceTitle;
    }

    /**
     * Get price for selection product
     *
     * @param Mage_Catalog_Model_Product $_selection
     * @return int|float
     */
    public function getSelectionPrice($_selection)
    {
        $price = 0;
        $store = $this->getProduct()->getStore();
        if ($_selection) {
            $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
            if (is_numeric($price)) {
                $price = $this->helper('core')->currencyByStore($price, $store, false);
            }
        }
        return is_numeric($price) ? $price : 0;
    }

    /**
     * Get title price for selection product
     *
     * @param Mage_Catalog_Model_Product $_selection
     * @param bool $includeContainer
     * @return string
     */
    public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        $this->setFormatProduct($_selection);
        $priceTitle = $this->escapeHtml($_selection->getName());
        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');
        return $priceTitle;
    }

    /**
     * Format price string
     *
     * @param float $price
     * @param bool $includeContainer
     * @return string
     */
    public function formatPriceString($price, $includeContainer = true)
    {
        $taxHelper  = Mage::helper('tax');
        $coreHelper = $this->helper('core');
        $currentProduct = $this->getProduct();
        if ($currentProduct->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
                && $this->getFormatProduct()
        ) {
            $product = $this->getFormatProduct();
        } else {
            $product = $currentProduct;
        }

        $priceTax    = $taxHelper->getPrice($product, $price);
        $priceIncTax = $taxHelper->getPrice($product, $price, true);

        $formated = $coreHelper->currencyByStore($priceTax, $product->getStore(), true, $includeContainer);
        if ($taxHelper->displayBothPrices() && $priceTax != $priceIncTax) {
            $formated .=
                    ' (+' .
                    $coreHelper->currencyByStore($priceIncTax, $product->getStore(), true, $includeContainer) .
                    ' ' . $this->__('Incl. Tax') . ')';
        }

        return $formated;
    }
}
