if (typeof(apdcProductAddedToCart) === "undefined") {
  var apdcProductAddedToCart = {}; // Should be generated in template : apdc_cart/cart/minicart/items.phtml
}
(function($) {
  $(document).ready(function() {
    $(document).on('click', ':submit', function(e) {
      var $clickedButton = $(this);
      var form = $clickedButton.parents('form');
      $('.advice-must-select-options').hide();
      if (form.hasClass('apdc-add-to-cart-form')) {
        e.preventDefault();
        e.stopPropagation();
        var productId = parseInt(form.data('product-id'));
        var itemId = 0;
        var isConfigureMode = (form.find('input[name="update_product_options"]').length > 0);

        var $qty = null;
        var qty = 0;

        if ($clickedButton.val() === 'btn-cart-qty-plus') {
          itemId = parseInt($clickedButton.data('item-id'));
          $qty = $clickedButton.parent('.qty-buttons').find('.added-qty');
          qty = parseInt($qty.html(), 10);
          $qty.html(++qty);
          updateRemoveAndMinusBtn(form, qty);
          if (!isConfigureMode) {
            $(document).trigger('minicartAddQty', [itemId, productId, qty]);
          } else {
            checkNeedUpdate(form);
          }
        } else if ($clickedButton.val() === 'btn-cart-qty-minus') {
          itemId = parseInt($clickedButton.data('item-id'));
          $qty = $clickedButton.parent('.qty-buttons').find('.added-qty');
          qty = parseInt($qty.html(), 10);
          if (qty > 1) {
            $qty.html(--qty);
            updateRemoveAndMinusBtn(form, qty);
            if (!isConfigureMode) {
              $(document).trigger('minicartRemoveQty', [itemId, productId, qty]);
            } else {
              checkNeedUpdate(form);
            }
          } else {
            $('#btn-minicart-remove-' + itemId).click();
          }
        } else if ($clickedButton.val() === 'btn-cart-remove') {
          itemId = parseInt($clickedButton.data('item-id'));
          $('#btn-minicart-remove-' + itemId).click();
          if (isConfigureMode && typeof(apdcProductQuickViewPopup) !== 'undefined') {
            apdcProductQuickViewPopup.close();
          }
        } else {
          var varienForm = new VarienForm(form.attr('id'));
          var addRelatedProducts = ($clickedButton.val() === 'btn-add-related-products' ? true : false);
          varienForm.form = form[0];
          varienForm.validator = new Validation(form[0]);
          varienForm.validator.options.focusOnError = false;
          if (!addRelatedProducts && !varienForm.validator.validate()) {
            form.find('.advice-must-select-options').show();
            return false;
          } else if (addRelatedProducts && 
            (form.find('.related-products-field').length === 0 || form.find('.related-products-field').val() === '')
          ) {
            return false;
          }
          var ajaxUrl = '';
          if (addRelatedProducts) {
            ajaxUrl = form.find('.add-related-products-to-cart').data('ajax-action');
          } else {
            ajaxUrl = form.data('ajax-action');
          }
          var data = new FormData(form[0]);
          data.append('isAjax', 1);

          if (isConfigureMode) {
            data.append('qty', parseInt(form.find('.added-qty').html(), 10));
          }
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
              $clickedButton.addClass('action-success');
              window.setTimeout(function() {
                $clickedButton.removeClass('action-success');
              }, 2000);

              // If item had an error (eg: item with required options from an old cart), it will probably be delete and a new item will be created.
              // So we need to replace old item_id references 
              if (result.item_id && result.quote_item_id && result.item_id !== result.quote_item_id) {
                form.find('[data-item-id="' + result.quote_item_id + '"]').each(function() {
                  $(this).data('item-id', parseInt(result.item_id, 10));
                });
                form.find('[name="item_id"][value="' + result.quote_item_id + '"]').each(function() {
                  $(this).val(parseInt(result.item_id, 10));
                });
                var formAction = form.data('ajax-action');
                var regexp = new RegExp('/id/' + result.quote_item_id + '/', 'g');
                formAction = formAction.replace(regexp, '/id/' + result.item_id + '/');
                form.data('ajax-action', formAction);
              }

              if($('.header-minicart').length > 0){
                $(document).trigger('updateMiniCartContent', [result]);
                var currentValues = getCurrentFormValues(form);
                form.find('input[name="update_product_options_initial_values"]').val(currentValues);
                checkNeedUpdate(form);
              }
            }
          })
          .fail(function() {
            console.log('failed');
          })
          .always(function() {
            finishLoading(productId);
          });
          return false;
        }
      }
    });

    $(document).on('keyup', '.apdc-cart-item-comment textarea[name="item_comment"]', function() {
      var form = $(this).parents('form');
      checkNeedUpdate(form);
    });

    $(document).on('change', '.apdc-add-to-cart-form', function(event, init) {
      $('.advice-must-select-options').hide();
      var updateProductOptions = $(this).find('input[name="update_product_options"]');
      var optionKeyTab = [];
      var optionKey = '';
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

      if (typeof(init) !== 'undefined' && init === true) {
          if (updateProductOptions.length > 0) {
            var inputInitialValues = $(this).find('input[name="update_product_options_initial_values"]');
            var initialValue = optionKey + '-' + inputInitialValues.val();
            inputInitialValues.val(initialValue);
          }
      }
      if (typeof(init) === 'undefined' || init !== true) {
        if (updateProductOptions.length > 0) {
          checkNeedUpdate($(this));
          return;
        }
      }
      $(document).trigger('apdcProductFormChanged', [productId]);
    });
  });

  function getCurrentFormValues(form) {
    var productId = parseInt(form.data('product-id'));
    var optionKey = $('.selected-optionKey-' + productId).val();
    var qty = parseInt(form.find('.added-qty').html(), 10);
    var comment = form.find('textarea[name="item_comment"]').val();

    return optionKey + '-' + qty + '-' + comment;
  }

  function checkNeedUpdate(form)
  {
    var currentFormValues = getCurrentFormValues(form);
    var initialFormValues = form.find('input[name="update_product_options_initial_values"]').val();
    if (currentFormValues !== initialFormValues) {
      form.find('.action.update-product-options').removeClass('disabled');
      form.find('.action.update-product-options button').prop('disabled', false);
    } else {
      form.find('.action.update-product-options').addClass('disabled');
      form.find('.action.update-product-options button').prop('disabled', true);
    }
  }

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

  var blinkInterval = null;
  function counterBlink() {
    if (parseInt($('.header-minicart .count').html(), 10) > 0) {
      $('.header-minicart .count').stop();
      if (blinkInterval !== null) {
        window.clearInterval(blinkInterval);
      }
      blinkInterval = setInterval(function() {
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
  function finishLoading(productId)
  {
    var actions = null;
    if (typeof(productId) !== 'undefined') {
      actions = $('.product_addtocart_form_' + productId).find('.actions');
    } else {
      actions = $('.apdc-add-to-cart-form').find('.actions');
    }
    actions.find('.action-loading').hide();
    counterBlink();
  }

  $(document).on('updateCartStartLoading', function(event, itemId, productId) {
    startLoading(productId);
  });
  $(document).on('minicartLoaded', function(event, productId) {
    finishLoading(productId);
  });

  // used to change the add to cart button. 
  // If the product already added to cart, we must display + and - buttons with the qty already added.
  $(document).on('apdcProductFormChanged', function(event, fromProductId) {
    var actions = $('.product_addtocart_form_' + fromProductId).find('.actions');

    // init display of add to cart button
    actions.find('.simple-add-to-cart-button').show();
    actions.find('.qty-buttons').hide();
    actions.find('.btn.show-product-popup').hide();
    actions.find('.btn-cart.show-product-popup').show();

    if (Object.keys(apdcProductAddedToCart).length > 0) {
        if (typeof(apdcProductAddedToCart[fromProductId]) !== 'undefined') {
          var productAdded = apdcProductAddedToCart[fromProductId];
          var qty = null;
          var itemId = null;
          if (productAdded.product.type_id === 'bundle') {
              apdcUpdateBundleButtons(fromProductId);
          }
          if (!(productAdded.options instanceof Array) && Object.keys(productAdded.options).length > 0) {
            var optionKey = $('.selected-optionKey-' + fromProductId).val();
            if (typeof(productAdded.options[optionKey]) !== 'undefined') {
                itemId = productAdded.options[optionKey].itemId;
                qty = productAdded.options[optionKey].qty;
            }
          } else if (typeof(productAdded.qty) !== 'undefined') {
            qty = productAdded.qty;
            itemId = productAdded.itemId;
          }
          if (qty !== null && itemId !== null) {
            apdcUpdateQtyButtons(fromProductId, itemId, qty);
          }
        }
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
      updateRemoveAndMinusBtn(productContainer, qty);
    }
  }

  function updateRemoveAndMinusBtn(productContainer, qty)
  {
      if (qty === 1) {
        productContainer.find('.btn-cart-qty-minus').hide();
        productContainer.find('.btn-cart-remove').show();
      } else {
        productContainer.find('.btn-cart-qty-minus').show();
        productContainer.find('.btn-cart-remove').hide();
      }
  }

  function apdcUpdateBundleButtons(productId, qty)
  {
    var productContainer = $('.product_addtocart_form_' + productId);
    productContainer.find('.btn.show-product-popup').show();
    productContainer.find('.btn-cart.show-product-popup').hide();
  }

})(jQuery);

