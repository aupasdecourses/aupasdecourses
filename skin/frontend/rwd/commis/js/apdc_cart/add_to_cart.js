if (typeof(apdcProductAddedToCart) === "undefined") {
  var apdcProductAddedToCart = {}; // Should be generated in template : apdc_cart/cart/minicart/items.phtml
}
(function($) {
  $(document).ready(function() {
    $(document).on('click', ':submit', function(e) {
      var form = $(this).parents('form');
      if (form.hasClass('apdc-add-to-cart-form')) {
        e.preventDefault();
        e.stopPropagation();
        var productId = parseInt(form.data('product-id'));
        var itemId = 0;

        var $qty = null;
        var qty = 0;

        if (this.value == 'btn-cart-qty-plus') {
          itemId = parseInt($(this).data('item-id'));
          $qty = $(this).parent('.qty-buttons').find('.added-qty');
          qty = parseInt($qty.html(), 10);
          $qty.html(++qty);
          $(document).trigger('minicartAddQty', [itemId, productId]);
        } else if (this.value == 'btn-cart-qty-minus') {
          itemId = parseInt($(this).data('item-id'));
          $qty = $(this).parent('.qty-buttons').find('.added-qty');
          qty = parseInt($qty.html(), 10);
          if (qty > 1) {
            $qty.html(--qty);
            $(document).trigger('minicartRemoveQty', [itemId, productId]);
          } else {
            $('#btn-minicart-remove-' + itemId).click();
          }
        } else if (this.value == 'btn-cart-remove') {
          itemId = parseInt($(this).data('item-id'));
          $('#btn-minicart-remove-' + itemId).click();
        } else {
          var varienForm = new VarienForm(form.attr('id'));
          if (varienForm.validator.validate()) {
            var ajaxUrl = form.data('ajax-action');
            var data = new FormData(form[0]);
            data.append('isAjax', 1);

            var actions = $(form).find('.actions');
            $.ajax({
              url: ajaxUrl,
              data: data,
              processData: false,
              contentType: false,
              type: 'POST',
              beforeSend: function() {
                startLoading(productId);
                $(document).trigger('startUpdateMiniCartContent');
              }
            })
            .done(function(result) {
              if (result.status === 'SUCCESS') {
                if($('.header-minicart').length > 0){
                  $(document).trigger('updateMiniCartContent', [result]);
                }
              }
            })
            .fail(function() {
              console.log('failed');
            })
            .always(function() {
              finishLoading();
            });
          }
        }
        return false;
      }
    });

    $(document).on('change', '.apdc-add-to-cart-form', function(event) {
      var optionKeyTab = [];
      var optionKey = '';
      var formId = $(this).attr('id');
      var productId = parseInt($(this).data('product-id'));
      $(this).find('[name^="super_attribute["]').each(function() {
        var tabOptions = extractOptions(this, 'super_attribute');
        for (var i = 0; i < tabOptions.length; ++i) {
          optionKeyTab.push(tabOptions[i]);
        }
      });
      $(this).find('[name^="options["]').each(function() {
        var tabOptions = extractOptions(this, 'options');
        for (var i = 0; i < tabOptions.length; ++i) {
          optionKeyTab.push(tabOptions[i]);
        }
      });
      $(this).find('[name^="bundle_option["]').each(function() {
        var tabOptions = extractOptions(this, 'bundle_option');
        for (var i = 0; i < tabOptions.length; ++i) {
          optionKeyTab.push(tabOptions[i]);
        }
      });
      if (optionKeyTab.length > 0) {
        optionKey = optionKeyTab.join('_');
      }
      $('.selected-optionKey-' + productId).val(optionKey);
      $(document).trigger('apdcProductFormChanged', [productId]);
    });
  });

  function extractOptions(elt, name) {
    var optionId = 0;
    var optionKeyTab = [];
    var attributeId = parseInt(elt.name.replace(name + '[', '').replace(']', '').replace('[]', ''));

    if (elt.type && (elt.type === 'checkbox' || elt.type === 'radio') && elt.checked) {
      optionId = parseInt(elt.value);
      optionKeyTab.push(attributeId + '-' + optionId);
    } else if (elt.type && elt.type === 'hidden') {
      optionId = parseInt(elt.value);
      optionKeyTab.push(attributeId + '-' + optionId);
    } else if (elt.type && elt.type === 'select-one') {
      optionId = parseInt(elt.value);
      optionKeyTab.push(attributeId + '-' + optionId);
    } else if (elt.type && elt.type === 'select-multiple') {
      var optionsIds = $(elt).val();
      var options = $(elt).find('option');
      options.each(function() {
        if (this.selected) {
          optionKeyTab.push(attributeId + '-' + this.value);
        }
      });
    }

    return optionKeyTab;
  }

  function counterBlink() {
    if (parseInt($('.header-minicart .count').html(), 10) > 0) {
      var blinkInterval = setInterval(function() {
        $('.header-minicart .count').fadeOut(100).fadeIn(100);
      }, 200);
      setTimeout(function() {
        clearInterval(blinkInterval);
      }, 1500);
    }
  }

  function startLoading(productId)
  {
    var actions = $('.product_addtocart_form_' + productId).find('.actions');
    actions.find('.action-loading').show();
  }
  function finishLoading()
  {
    var actions = $('.apdc-add-to-cart-form').find('.actions');
    actions.find('.action-loading').hide();
    counterBlink();
  }

  $(document).on('updateCartStartLoading', function(event, itemId, productId) {
    startLoading(productId);
  });
  $(document).on('minicartLoaded', function(event) {
    finishLoading();
  });

  // used to change the add to cart button. 
  // If the product already added to cart, we must display + and - buttons with the qty already added.
  $(document).on('apdcProductFormChanged', function(event, fromProductId) {

    // init display of add to cart button
    $('.actions .simple-add-to-cart-button').show();
    $('.actions .qty-buttons').hide();
    $('.actions .btn.show-product-popup').hide();
    $('.actions .btn-cart.show-product-popup').show();

    if (Object.keys(apdcProductAddedToCart).length > 0) {
      $('.apdc-add-to-cart-form').each(function() {
        var productId = $(this).data('product-id');
        if (typeof(apdcProductAddedToCart[productId]) !== 'undefined') {
          var productAdded = apdcProductAddedToCart[productId];
          var qty = null;
          var itemId = null;
          if (productAdded.product.type_id === 'bundle') {
              apdcUpdateBundleButtons(productId);
          }
          if (!(productAdded.options instanceof Array) && Object.keys(productAdded.options.length > 0)) {
            var optionKey = $('.selected-optionKey-' + productId).val();
            if (typeof(productAdded.options[optionKey]) !== 'undefined') {
                itemId = productAdded.options[optionKey].itemId;
                qty = productAdded.options[optionKey].qty;
            }
          } else if (typeof(productAdded.qty) !== 'undefined') {
            qty = productAdded.qty;
            itemId = productAdded.itemId;
          }
          if (qty !== null && itemId !== null) {
            apdcUpdateQtyButtons(productId, itemId, qty);
          }
        }
      });
    }
    $(document).trigger('apdcUpdateAddToCartButtons_end', [fromProductId]);
  });
  function apdcUpdateQtyButtons(productId, itemId, qty)
  {
    var productContainer = $('.product_addtocart_form_' + productId);
    productContainer.find('.added-qty').html(qty);
    productContainer.find('.btn-cart-qty-minus').data('item-id', itemId);
    productContainer.find('.btn-cart-qty-plus').data('item-id', itemId);
    productContainer.find('.btn-cart-remove').data('item-id', itemId);
    if (qty <= 0) {
      productContainer.find('.simple-add-to-cart-button').show();
      productContainer.find('.qty-buttons').hide();
    } else {
      productContainer.find('.simple-add-to-cart-button').hide();
      productContainer.find('.qty-buttons').show();
      if (qty === 1) {
        productContainer.find('.btn-cart-qty-minus').hide();
        productContainer.find('.btn-cart-remove').show();
      } else {
        productContainer.find('.btn-cart-qty-minus').show();
        productContainer.find('.btn-cart-remove').hide();
      }
    }
  }

  function apdcUpdateBundleButtons(productId, qty)
  {
    var productContainer = $('.product_addtocart_form_' + productId);
    productContainer.find('.btn.show-product-popup').show();
    productContainer.find('.btn-cart.show-product-popup').hide();
  }

})(jQuery);

