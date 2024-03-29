/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if(typeof Product=='undefined') {
    var Product = {};
}
var ApdcBundleLoaded = true;

/**************************** BUNDLE PRODUCT **************************/
Product.Bundle.prototype.origInitialize = Product.Bundle.prototype.initialize;
Product.Bundle.prototype.initialize = function(config, productId, containerId) {
  this.productId = productId;
  this.containerId = containerId;
  this.origInitialize(config);
};
Product.Bundle.prototype.reloadPrice = function() {
  var calculatedPrice = 0;
  var dispositionPrice = 0;
  var includeTaxPrice = 0;

  for (var option in this.config.selected) {
      if (this.config.options[option]) {
          for (var i=0; i < this.config.selected[option].length; i++) {
              var prices = this.selectionPrice(option, this.config.selected[option][i]);
              calculatedPrice += Number(prices[0]);
              dispositionPrice += Number(prices[1]);
              includeTaxPrice += Number(prices[2]);
          }
      }
  }

  //Tax is calculated in a different way for the the TOTAL BASED method
  //We round the taxes at the end. Hence we do the same for consistency
  //This variable is set in the bundle.phtml
  if (taxCalcMethod == CACL_TOTAL_BASE) {
      var calculatedPriceFormatted = calculatedPrice.toFixed(10);
      var includeTaxPriceFormatted = includeTaxPrice.toFixed(10);
      var tax = includeTaxPriceFormatted - calculatedPriceFormatted;
      calculatedPrice = includeTaxPrice - Math.round(tax * 100) / 100;
  }

  //make sure that the prices are all rounded to two digits
  //this is needed when tax calculation is based on total for dynamic
  //price bundle product. For fixed price bundle product, the rounding
  //needs to be done after option price is added to base price
  if (this.config.priceType == '0') {
      calculatedPrice = Math.round(calculatedPrice*100)/100;
      dispositionPrice = Math.round(dispositionPrice*100)/100;
      includeTaxPrice = Math.round(includeTaxPrice*100)/100;

  }

  var event = $(document).fire('bundle:reload-price', {
      price: calculatedPrice,
      priceInclTax: includeTaxPrice,
      dispositionPrice: dispositionPrice,
      bundle: this
  });
  if (!event.noReloadPrice) {
      window['optionsPrice' + this.containerId.replace(/-/g,'_') + this.productId].specialTaxPrice = 'true';
      window['optionsPrice' + this.containerId.replace(/-/g,'_') + this.productId].changePrice('bundle', calculatedPrice);
      window['optionsPrice' + this.containerId.replace(/-/g,'_') + this.productId].changePrice('nontaxable', dispositionPrice);
      window['optionsPrice' + this.containerId.replace(/-/g,'_') + this.productId].changePrice('priceInclTax', includeTaxPrice);

      if (this.containerId !== '') {
        window['optionsPrice' + this.containerId.replace(/-/g,'_') + this.productId].reloadWithContainer(this.containerId);
      } else {
        window['optionsPrice' + this.containerId.replace(/-/g,'_') + this.productId].reload();
      }
  }

  return calculatedPrice;
};

Product.Bundle.prototype.selectionPrice = function(optionId, selectionId) {
  if (selectionId === '' || selectionId === 'none') {
      return 0;
  }
  var qty = null;
  var tierPriceInclTax, tierPriceExclTax;
  if (this.config.options[optionId].selections[selectionId].customQty == 1 && !this.config.options[optionId].isMulti) {

    var selector = 'bundle-option-' + optionId + '-qty-input';
    if (this.containerId !== '') {
      selector = this.containerId + ' .' + selector;
    }
    if (jQuery('#' + selector)) {
      qty = jQuery('#' + selector).val();
    } else {
      qty = 1;
    }
  } else {
      qty = this.config.options[optionId].selections[selectionId].qty;
  }
  if (this.config.priceType == '0') {
      price = this.config.options[optionId].selections[selectionId].price;
      tierPrice = this.config.options[optionId].selections[selectionId].tierPrice;

      for (var i=0; i < tierPrice.length; i++) {
          if (Number(tierPrice[i].price_qty) <= qty && Number(tierPrice[i].price) <= price) {
              price = tierPrice[i].price;
              tierPriceInclTax = tierPrice[i].priceInclTax;
              tierPriceExclTax = tierPrice[i].priceExclTax;
          }
      }
  } else {
      selection = this.config.options[optionId].selections[selectionId];
      if (selection.priceType == '0') {
          price = selection.priceValue;
      } else {
          price = (this.config.basePrice*selection.priceValue)/100;
      }
  }
  var disposition = this.config.options[optionId].selections[selectionId].plusDisposition +
      this.config.options[optionId].selections[selectionId].minusDisposition;

  if (this.config.specialPrice) {
      newPrice = (price*this.config.specialPrice)/100;
      price = Math.min(newPrice, price);
  }

  selection = this.config.options[optionId].selections[selectionId];
  if (tierPriceInclTax !== undefined && tierPriceExclTax !== undefined) {
      priceInclTax = tierPriceInclTax;
      price = tierPriceExclTax;
  } else if (selection.priceInclTax !== undefined) {
      priceInclTax = selection.priceInclTax;
      price = selection.priceExclTax !== undefined ? selection.priceExclTax : selection.price;
  } else {
      priceInclTax = price;
  }

  var result = '';
  if (this.config.priceType == '1' || taxCalcMethod == CACL_TOTAL_BASE) {
      result = new Array(price*qty, disposition*qty, priceInclTax*qty);
  } else if (taxCalcMethod == CACL_UNIT_BASE) {
      price = (Math.round(price*100)/100).toString();
      disposition = (Math.round(disposition*100)/100).toString();
      priceInclTax = (Math.round(priceInclTax*100)/100).toString();
      result = new Array(price*qty, disposition*qty, priceInclTax*qty);
  } else { //taxCalcMethod == CACL_ROW_BASE)
      price = (Math.round(price*qty*100)/100).toString();
      disposition = (Math.round(disposition*qty*100)/100).toString();
      priceInclTax = (Math.round(priceInclTax*qty*100)/100).toString();
      result = new Array(price, disposition, priceInclTax);
  }
  return result;
};
Product.Bundle.prototype.showQtyInput = function(optionId, value, canEdit) {
  var selector = 'bundle-option-' + optionId + '-qty-input';
  if (this.containerId !== '') {
    selector = this.containerId + ' .' + selector;
  }
  elem = jQuery('#' + selector);
  elem.val(value);
  elem.prop('disabled', !canEdit);
  if (canEdit) {
      elem.removeClass('qty-disabled');
  } else {
      elem.addClass('qty-disabled');
  }
};
