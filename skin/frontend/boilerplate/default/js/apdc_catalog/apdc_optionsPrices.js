/**************************** PRICE RELOADER ********************************/
Product.OptionsPrice.prototype.reloadWithContainer = function(containerId) {
  var price;
  var formattedPrice;
  var optionPrices = this.getOptionPrices();
  var nonTaxable = optionPrices[1];
  var optionOldPrice = optionPrices[2];
  var priceInclTax = optionPrices[3];
  optionPrices = optionPrices[0];
  this.containerId = containerId;

  $H(this.containers).each(function(pair) {
    var _productPrice;
    var _plusDisposition;
    var _minusDisposition;
    var _priceInclTax;
    if ($$('#' + this.containerId + ' #' + pair.value).length > 0) {
        if (pair.value == 'old-price-'+this.productId && this.productOldPrice != this.productPrice) {
            _productPrice = this.productOldPrice;
            _plusDisposition = this.oldPlusDisposition;
            _minusDisposition = this.oldMinusDisposition;
        } else {
            _productPrice = this.productPrice;
            _plusDisposition = this.plusDisposition;
            _minusDisposition = this.minusDisposition;
        }
        _priceInclTax = priceInclTax;

        if (pair.value == 'old-price-'+this.productId && optionOldPrice !== undefined) {
            price = optionOldPrice+parseFloat(_productPrice);
        } else if (this.specialTaxPrice == 'true' && this.priceInclTax !== undefined && this.priceExclTax !== undefined) {
            price = optionPrices+parseFloat(this.priceExclTax);
            _priceInclTax += this.priceInclTax;
        } else {
            price = optionPrices+parseFloat(_productPrice);
            _priceInclTax += parseFloat(_productPrice) * (100 + this.currentTax) / 100;
        }

        var excl = null;
        var incl = null;
        var tax = null;

        if (this.specialTaxPrice == 'true') {
            excl = price;
            incl = _priceInclTax;
        } else if (this.includeTax == 'true') {
            // tax = tax included into product price by admin
            tax = price / (100 + this.defaultTax) * this.defaultTax;
            excl = price - tax;
            incl = excl*(1+(this.currentTax/100));
        } else {
            tax = price * (this.currentTax / 100);
            excl = price;
            incl = excl + tax;
        }

        var subPrice = 0;
        var subPriceincludeTax = 0;
        Object.values(this.customPrices).each(function(el){
            if (el.excludeTax && el.includeTax) {
                subPrice += parseFloat(el.excludeTax);
                subPriceincludeTax += parseFloat(el.includeTax);
            } else {
                subPrice += parseFloat(el.price);
                subPriceincludeTax += parseFloat(el.price);
            }
        });
        excl += subPrice;
        incl += subPriceincludeTax;

        if (typeof this.exclDisposition == 'undefined') {
            excl += parseFloat(_plusDisposition);
        }

        incl += parseFloat(_plusDisposition) + parseFloat(this.plusDispositionTax);
        excl -= parseFloat(_minusDisposition);
        incl -= parseFloat(_minusDisposition);

        //adding nontaxlable part of options
        excl += parseFloat(nonTaxable);
        incl += parseFloat(nonTaxable);

        if (pair.value == 'price-including-tax-'+this.productId) {
            price = incl;
        } else if (pair.value == 'price-excluding-tax-'+this.productId) {
            price = excl;
        } else if (pair.value == 'old-price-'+this.productId) {
            if (this.showIncludeTax || this.showBothPrices) {
                price = incl;
            } else {
                price = excl;
            }
        } else {
            if (this.showIncludeTax) {
                price = incl;
            } else {
                price = excl;
            }
        }

        if (price < 0) price = 0;

        if (price > 0 || this.displayZeroPrice) {
            formattedPrice = this.formatPrice(price);
        } else {
            formattedPrice = '';
        }

        if ($$('#' + this.containerId + ' #' + pair.value)[0].select('.price')[0]) {
            $$('#' + this.containerId + ' #' + pair.value)[0].select('.price')[0].innerHTML = formattedPrice;
            if ($$('#' + this.containerId + ' #' + pair.value+this.duplicateIdSuffix).length > 0 && $$('#' + this.containerId + ' #' + pair.value+this.duplicateIdSuffix)[0].select('.price')[0]) {
                $$('#' + this.containerId + ' #' + pair.value+this.duplicateIdSuffix)[0].select('.price')[0].innerHTML = formattedPrice;
            }
        } else {
            $$('#' + this.containerId + ' #' + pair.value)[0].innerHTML = formattedPrice;
            if ($$('#' + this.containerId + ' #' + pair.value+this.duplicateIdSuffix).length > 0) {
                $$('#' + this.containerId + ' #' + pair.value+this.duplicateIdSuffix)[0].innerHTML = formattedPrice;
            }
        }
    }
  }.bind(this));

  if (typeof(skipTierPricePercentUpdate) === "undefined" && typeof(this.tierPrices) !== "undefined") {
    for (var i = 0; i < this.tierPrices.length; i++) {
      $$('.benefit').each(function(el) {
        var parsePrice = function(html) {
          var format = this.priceFormat;
          var decimalSymbol = format.decimalSymbol === undefined ? "," : format.decimalSymbol;
          var regexStr = '[^0-9-' + decimalSymbol + ']';
          //remove all characters except number and decimal symbol
          html = html.replace(new RegExp(regexStr, 'g'), '');
          html = html.replace(decimalSymbol, '.');
          return parseFloat(html);
        }.bind(this);

        var updateTierPriceInfo = function(priceEl, tierPriceDiff, tierPriceEl, benefitEl) {
          if (typeof(tierPriceEl) === "undefined") {
            //tierPrice is not shown, e.g., MAP, no need to update the tier price info
            return;
          }
          var price = parsePrice(priceEl.innerHTML);
          var tierPrice = price + tierPriceDiff;

          tierPriceEl.innerHTML = this.formatPrice(tierPrice);

          var $percent = Selector.findChildElements(benefitEl, ['.percent.tier-' + i]);
          $percent.each(function(el) {
            el.innerHTML = Math.ceil(100 - ((100 / price) * tierPrice));
          });
        }.bind(this);

        var tierPriceElArray = $$('.tier-price.tier-' + i + ' .price');
        var tierPriceInclTaxDiff = null;
        var tierPriceInclTaxEl = null;
        var tierPriceExclTaxDiff = null;
        var tierPriceExclTaxEl = null;
        var containerExclTax = null;
        if (this.showBothPrices) {
          containerExclTax = $(this.containers[3]);
          tierPriceExclTaxDiff = this.tierPrices[i];
          tierPriceExclTaxEl = tierPriceElArray[0];
          updateTierPriceInfo(containerExclTax, tierPriceExclTaxDiff, tierPriceExclTaxEl, el);
          containerInclTax = $(this.containers[2]);
          tierPriceInclTaxDiff = this.tierPricesInclTax[i];
          tierPriceInclTaxEl = tierPriceElArray[1];
          updateTierPriceInfo(containerInclTax, tierPriceInclTaxDiff, tierPriceInclTaxEl, el);
        } else if (this.showIncludeTax) {
          container = $(this.containers[0]);
          tierPriceInclTaxDiff = this.tierPricesInclTax[i];
          tierPriceInclTaxEl = tierPriceElArray[0];
          updateTierPriceInfo(container, tierPriceInclTaxDiff, tierPriceInclTaxEl, el);
        } else {
          container = $(this.containers[0]);
          tierPriceExclTaxDiff = this.tierPrices[i];
          tierPriceExclTaxEl = tierPriceElArray[0];
          updateTierPriceInfo(container, tierPriceExclTaxDiff, tierPriceExclTaxEl, el);
        }
      }, this);
    }
  }

};
