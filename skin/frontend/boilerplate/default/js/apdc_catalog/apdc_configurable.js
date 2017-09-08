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
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (typeof Product == 'undefined') {
    var Product = {};
}

/**************************** CONFIGURABLE PRODUCT **************************/
Product.ApdcConfig = Class.create();
Product.ApdcConfig.prototype = {
    initialize: function(config){
        this.config     = config;
        this.taxConfig  = this.config.taxConfig;
        if (config.containerId) {
            this.settings   = $$('#' + config.containerId + ' ' + '.super-attribute-select_' + this.config.productId);
        } else {
            this.config.containerId = '';
            this.settings   = $$('.super-attribute-select_' + this.config.productId);
        }
        this.state      = new Hash();
        this.priceTemplate = new Template(this.config.template);
        this.prices     = config.prices;

        // Set default values from config
        if (config.defaultValues) {
            this.values = config.defaultValues;
        } else {
            var defaultValues = {};
            for (var attributeId in config.attributes) {
              var attribute = config.attributes[attributeId];
              if (attribute.default_value) {
                defaultValues[attributeId] = attribute.default_value;
              }
            }
            if (Object.keys(defaultValues).length > 0) {
              this.values = defaultValues;
            }
        }

        // Overwrite defaults by url
        var separatorIndex = window.location.href.indexOf('#');
        if (separatorIndex != -1) {
            var paramsStr = window.location.href.substr(separatorIndex+1);
            var urlValues = paramsStr.toQueryParams();
            if (!this.values) {
                this.values = {};
            }
            for (var i in urlValues) {
                this.values[i] = urlValues[i];
            }
        }

        // Overwrite defaults by inputs values if needed
        if (config.inputsInitialized) {
            this.values = {};
            this.settings.each(function(element) {
                if (element.value) {
                    var elementAttributeId = this.getElementAttributeId(element.id);
                    var attributeId = this.getAttributeId(elementAttributeId);
                    this.values[attributeId] = element.value;
                }
            }.bind(this));
        }

        // Put events to check select reloads
        this.settings.each(function(element){
            Event.observe(element, 'change', this.configure.bind(this));
        }.bind(this));

        // fill state
        this.settings.each(function(element){
            var elementAttributeId = this.getElementAttributeId(element.id);
            var attributeId = this.getAttributeId(elementAttributeId);
            if(attributeId && this.config.attributes[attributeId]) {
                element.config = this.config.attributes[attributeId];
                element.attributeId = attributeId;
                this.state[attributeId] = false;
            }
        }.bind(this));

        // Init settings dropdown
        var childSettings = [];
        for(var j=this.settings.length-1;j>=0;j--){
            var prevSetting = this.settings[j-1] ? this.settings[j-1] : false;
            var nextSetting = this.settings[j+1] ? this.settings[j+1] : false;
            if (j === 0){
                this.fillSelect(this.settings[j]);
            } else {
                this.settings[j].disabled = true;
            }
            $(this.settings[j]).childSettings = childSettings.clone();
            $(this.settings[j]).prevSetting   = prevSetting;
            $(this.settings[j]).nextSetting   = nextSetting;
            childSettings.push(this.settings[j]);
        }

        // Set values to inputs
        this.configureForValues();
        document.observe("dom:loaded", this.configureForValues.bind(this));
    },

    configureForValues: function () {
        if (this.values) {
            this.settings.each(function(element){
                this.setElementDefaultValue(element);
                this.configureElement(element);
            }.bind(this));
        }
    },
    setElementDefaultValue: function(element) {
        var attributeId = this.getAttributeId(element.attributeId);
        element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];


        jQuery('#' + this.config.containerId + 'swatch-images-' + attributeId + '-' + this.config.productId + ' .attr-image-container').removeClass('option-selected');
        if (jQuery('#' + this.config.containerId + 'attr-image-container-' + element.value + '-' + this.config.productId).length > 0) {
            jQuery('#' + this.config.containerId + 'attr-image-container-' + element.value + '-' + this.config.productId).addClass('option-selected');
        }
        jQuery('#' + element.id).trigger('change');
    },

    configure: function(event){
        var element = Event.element(event);
        this.configureElement(element);
    },

    configureElement : function(element) {
        this.reloadOptionLabels(element);
        if(element.value){
            this.state[element.config.id] = element.value;
            if(element.nextSetting){
                element.nextSetting.disabled = false;
                this.fillSelect(element.nextSetting);
                this.setElementDefaultValue(element.nextSetting);
                this.resetChildren(element.nextSetting);
            }
        }
        else {
            this.resetChildren(element);
        }
        this.reloadPrice();
    },

    reloadOptionLabels: function(element){
        var selectedPrice;
        if(element.options[element.selectedIndex].config && !this.config.stablePrices){
            selectedPrice = parseFloat(element.options[element.selectedIndex].config.price);
        }
        else{
            selectedPrice = 0;
        }
        for(var i=0;i<element.options.length;i++){
            if(element.options[i].config){
                element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice);
            }
        }
    },

    resetChildren : function(element){
        if(element.childSettings) {
            for(var i=0;i<element.childSettings.length;i++){
                element.childSettings[i].selectedIndex = 0;
                element.childSettings[i].disabled = true;
                if(element.config){
                    this.state[element.config.id] = false;
                }
            }
        }
    },

    fillSelect: function(element){
        var elementAttributeId = this.getElementAttributeId(element.id);
        var attributeId = this.getAttributeId(elementAttributeId);

        var options = this.getAttributeOptions(attributeId);
        this.clearSelect(element);
        element.options[0] = new Option('', '');
        element.options[0].innerHTML = this.config.chooseText;

        var prevConfig = false;
        if(element.prevSetting){
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }

        if(options) {

          if (this.config.attributes[attributeId].use_swatches) {
              $(this.config.containerId + 'attribute' + elementAttributeId).addClassName('no-display');
              if ($(this.config.containerId + 'swatch-images-' + attributeId + '-' + this.config.productId)) {
                  $(this.config.containerId + 'swatch-images-' + attributeId + '-' + this.config.productId).parentNode.removeChild($(this.config.containerId + 'swatch-images-' + attributeId + '-' + this.config.productId));
              }
              contentDiv = new Element(
                'div',
                { 
                  'class': 'settings-swatch-container', 
                  'id': this.config.containerId + 'swatch-images-' + attributeId + '-' + this.config.productId
                }
              );
                  
              $(element.parentNode).insert({
                top: contentDiv
              });
          }
            var index = 1;
            for(var i=0;i<options.length;i++){
                var allowedProducts = [];
                if(prevConfig) {
                    for(var j=0;j<options[i].products.length;j++){
                        if(prevConfig.config.allowedProducts && 
                          prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1
                        ){
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                } else {
                    allowedProducts = options[i].products.clone();
                }

                if(allowedProducts.size()>0){

                    if (this.config.attributes[attributeId].use_swatches) {
                        var imgContainer = new Element('div', { 
                            'class': 'attr-image-container', 
                            'id': this.config.containerId + 'attr-image-container-' + options[i].id + '-' + this.config.productId
                        });
                        
                        contentDiv.insert(imgContainer);
                        
                        var swatch = null;
                        if (options[i].image) {
                            swatch = new Element('img', { 
                                'class': 'attr-image', 
                                'id': this.config.containerId + 'attr-image-' + options[i].id + '-' + this.config.productId,
                                'src': options[i].image,
                                'alt': options[i].label,
                                'title': options[i].label,
                                'height': this.config.swatches_size_list,
                                'width': this.config.swatches_size_list
                            });
                        } else {
                            var style = 'height: '+ this.config.swatches_size_list + 'px;';
                            style += ' min-width:' + this.config.swatches_size_list + 'px;';
                            style += ' line-height:' + this.config.swatches_size_list + 'px';
                            swatch = new Element('div', { 
                                'class': 'attr-text', 
                                'id': this.config.containerId + 'attr-image-' + options[i].id + '-' + this.config.productId,
                                'alt': options[i].label,
                                'title': options[i].label,
                                'style': style
                            });
                          swatch.innerText = options[i].label;
                        }
                        swatch.observe('click', this.setSwatches.bind(this));
                        
                        imgContainer.insert(swatch);
                        
                    }
                    options[i].allowedProducts = allowedProducts;
                    element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                    if (typeof options[i].price != 'undefined') {
                        element.options[index].setAttribute('price', options[i].price);
                    }
                    element.options[index].config = options[i];
                    index++;
                }
            }
        }
        if(this.config.attributes[attributeId].use_swatches) {
            $(element.parentNode).insert({
              bottom: new Element('div', {'class': 'swatches-separator'})
            });
        }
    },

    getOptionLabel: function(option, price){
        price = parseFloat(price);
        var tax = 0;
        var excl = 0;
        var incl = 0;
        if (this.taxConfig.includeTax) {
            tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
            excl = price - tax;
            incl = excl*(1+(this.taxConfig.currentTax/100));
        } else {
            tax = price * (this.taxConfig.currentTax / 100);
            excl = price;
            incl = excl + tax;
        }

        if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
            price = incl;
        } else {
            price = excl;
        }

        var str = option.label;
        if(price){
            if (this.taxConfig.showBothPrices) {
                str+= ' ' + this.formatPrice(excl, true) + ' (' + this.formatPrice(price, true) + ' ' + this.taxConfig.inclTaxTitle + ')';
            } else {
                str+= ' ' + this.formatPrice(price, true);
            }
        }
        return str;
    },

    formatPrice: function(price, showSign){
        var str = '';
        price = parseFloat(price);
        if(showSign){
            if(price<0){
                str+= '-';
                price = -price;
            }
            else{
                str+= '+';
            }
        }

        var roundedPrice = (Math.round(price*100)/100).toString();

        if (this.prices && this.prices[roundedPrice]) {
            str+= this.prices[roundedPrice];
        }
        else {
            str+= this.priceTemplate.evaluate({price:price.toFixed(2)});
        }
        return str;
    },

    clearSelect: function(element){
        for(var i=element.options.length-1;i>=0;i--){
            element.remove(i);
        }
    },

    getAttributeOptions: function(attributeId){
        if(this.config.attributes[attributeId]){
            return this.config.attributes[attributeId].options;
        }
    },

    reloadPrice: function(){
        if (this.config.disablePriceReload) {
            return;
        }
        var price    = 0;
        var oldPrice = 0;
        for(var i=this.settings.length-1;i>=0;i--){
            if (this.settings[i].selectedIndex > -1) {
              var selected = this.settings[i].options[this.settings[i].selectedIndex];
              if(selected.config){
                  price    += parseFloat(selected.config.price);
                  oldPrice += parseFloat(selected.config.oldPrice);
              }
            }
        }

        window['optionsPrice' + this.config.containerId.replace(/-/g,'_') + this.config.productId].changePrice('config', {'price': price, 'oldPrice': oldPrice});
        if (this.config.containerId !== '') {
          window['optionsPrice' + this.config.containerId.replace(/-/g, '_') + this.config.productId].reloadWithContainer(this.config.containerId);
        } else {
          window['optionsPrice' + this.config.containerId.replace(/-/g, '_') + this.config.productId].reload();
        }

        return price;
    },

    reloadOldPrice: function(){
        if (this.config.disablePriceReload) {
            return;
        }
        var price;
        var selected;
        if (this.config.containerId !== '') {
          if ($$('#' + this.config.containerId + ' #old-price-'+this.config.productId).length > 0) {

              price = parseFloat(this.config.oldPrice);
              for(var i=this.settings.length-1;i>=0;i--){
                  selected = this.settings[i].options[this.settings[i].selectedIndex];
                  if(selected.config){
                      price+= parseFloat(selected.config.price);
                  }
              }
              if (price < 0)
                  price = 0;
              price = this.formatPrice(price);

              $$('#' + this.config.containerId + ' #old-price-'+this.config.productId)[0].innerHTML = price;

          }
        } else {
          if ($('old-price-'+this.config.productId)) {

              price = parseFloat(this.config.oldPrice);
              for(var j=this.settings.length-1;j>=0;j--){
                  selected = this.settings[j].options[this.settings[j].selectedIndex];
                  if(selected.config){
                      price+= parseFloat(selected.config.price);
                  }
              }
              if (price < 0)
                  price = 0;
              price = this.formatPrice(price);

              $('old-price-'+this.config.productId).innerHTML = price;
          }
        }
    },

    setSwatches: function(event) {
        var element = Event.element(event);
        attributeId = element.parentNode.parentNode.id.replace(/[a-z-]*/, '');
        var optionId = element.id.replace(/[a-z-]*/, '');

        jQuery('#' + this.config.containerId + 'swatch-images-' + attributeId + ' .attr-image-container').removeClass('option-selected');
        jQuery('#' + this.config.containerId + 'attr-image-container-' + optionId).addClass('option-selected');
        var position = optionId.indexOf('-');
        if ('-1' != position) {
            optionId = optionId.substring(0, position);
        }

        var self = this;
        $$('#' + this.config.containerId + 'attribute' + attributeId).each(function(select){
          select.value = optionId;    
          for (var i=0; i < select.options.length; ++i) {
            var option = select.options[i];
            if (option.value == optionId) {
              option.selected = true;
            } else {
              option.selected = false;
            }
          }
          jQuery('#' + self.config.containerId + 'attribute' + attributeId).trigger('change');
        });
        this.configureElement($(this.config.containerId + 'attribute' + attributeId));
    },

    getAttributeId: function(elementAttributeId) {
        var attributeId = elementAttributeId;
        var position = elementAttributeId.indexOf('-');
        if ('-1' != position) {
            attributeId = elementAttributeId.substring(0, position);
        }
        return attributeId;
    },
    getElementAttributeId: function(elementId) {
      var id = elementId;
      if (this.config.containerId !== '') {
        id = elementId.replace(this.config.containerId, '');
      }
      return id.replace(/[a-z]*/, '');
    }
};

PDPSwatchesData = Class.create();
PDPSwatchesData.prototype = 
{
    initialize : function(additionalData)
    {
        this.additionalData = additionalData;
    },
    
    getGalleryInfo : function(label,url,galleryUrl){
        var liContent = "<li><a href='#' onclick=\"popWin('"+galleryUrl+"', 'gallery', 'width=300,height=300,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes'); return false;\" title='"+label+"'><img src="+url+" width='56' height='56' alt='"+label+"' /></a></li>";

        return liContent;
    },

    hasselectValue : function(selectValue)
    {
        return ('undefined' != typeof(this.additionalData[selectValue]));
    },
    
    getData : function(selectValue, param)
    {
        if (this.hasselectValue(selectValue) && 'undefined' != typeof(this.additionalData[selectValue][param]))
        {
            return this.additionalData[selectValue][param];
        }
        return false;
    }
};
