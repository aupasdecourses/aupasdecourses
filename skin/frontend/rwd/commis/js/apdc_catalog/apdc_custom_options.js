var opConfig = {
  reloadPrice: function() {
    var productId = jQuery(event.target).parents('.apdc-product-custom-options').data('product-id');
    if (parseInt(productId) > 0) {
      opConfig[productId].reloadPrice();
    }

  }
};
Product.Options = Class.create();
Product.Options.prototype = {
    initialize : function(config, productId, containerId) {
        this.config = config;
        this.productId = productId;

        this.containerId = '';
        if (typeof(containerId) !== 'undefined') {
          this.containerId = containerId;
        }
        this.reloadPrice();
        var self = this;
        document.observe("dom:loaded", function() {
          self.reloadPrice.bind(self);
          var parent = jQuery('.apdc-product-custom-options[data-product-id=' + self.productId + ']');
          parent.find('select').addClass('form-control');
          parent.find('input[type!="radio"][type!="checkbox"]').addClass('form-control');
        });
    },
    reloadPrice : function() {
        var config = this.config;
        var productId = this.productId;
        var containerId = this.containerId;
        var skipIds = [];
        $$('body .product-custom-option').each(function(element){
            var optionId = 0;
            element.name.sub(/[0-9]+/, function(match){
                optionId = parseInt(match[0], 10);
            });
            if (config[optionId]) {
                var configOptions = config[optionId];
                var curConfig = {price: 0};
                if (element.type == 'checkbox' || element.type == 'radio') {
                    if (element.checked) {
                        if (typeof configOptions[element.getValue()] != 'undefined') {
                            curConfig = configOptions[element.getValue()];
                        }
                    }
                } else if(element.hasClassName('datetime-picker') && !skipIds.include(optionId)) {
                    dateSelected = true;
                    $$('.product-custom-option[id^="options_' + optionId + '"]').each(function(dt){
                        if (dt.getValue() === '') {
                            dateSelected = false;
                        }
                    });
                    if (dateSelected) {
                        curConfig = configOptions;
                        skipIds[optionId] = optionId;
                    }
                } else if(element.type == 'select-one' || element.type == 'select-multiple') {
                    if ('options' in element) {
                        $A(element.options).each(function(selectOption){
                            if ('selected' in selectOption && selectOption.selected) {
                                if (typeof(configOptions[selectOption.value]) != 'undefined') {
                                    curConfig = configOptions[selectOption.value];
                                }
                            }
                        });
                    }
                } else {
                    if (element.getValue().strip() !== '') {
                        curConfig = configOptions;
                    }
                }
                if(element.type == 'select-multiple' && ('options' in element)) {
                    $A(element.options).each(function(selectOption) {
                        if (('selected' in selectOption) && typeof(configOptions[selectOption.value]) != 'undefined') {
                            if (selectOption.selected) {
                                curConfig = configOptions[selectOption.value];
                            } else {
                                curConfig = {price: 0};
                            }
                            window['optionsPrice' + containerId.replace(/-/g,'_') + productId].addCustomPrices(optionId + '-' + selectOption.value, curConfig);
                            if (containerId !== '') {
                              window['optionsPrice' + containerId.replace(/-/g,'_') + productId].reloadWithContainer(containerId);
                            } else {
                              window['optionsPrice' + containerId.replace(/-/g,'_') + productId].reload();
                            }
                        }
                    });
                } else {
                    window['optionsPrice' + containerId.replace(/-/g,'_') + productId].addCustomPrices(element.id || optionId, curConfig);
                    if (containerId !== '') {
                      window['optionsPrice' + containerId.replace(/-/g,'_') + productId].reloadWithContainer(containerId);
                    } else {
                      window['optionsPrice' + containerId.replace(/-/g,'_') + productId].reload();
                    }
                }
            }
        });
    }
};

function validateOptionsCallback(elmId, result) {
    var container = $(elmId).up('ul.options-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}
