if (typeof(apdcProductAddedToCart) === "undefined") {
  var apdcProductAddedToCart = {}; // Should be generated in template : apdc_cart/cart/minicart/items.phtml
}
(function($) {
  $(document).ready(function() {
    $('.main').on('click', ':submit', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var form = $(this).parents('form');
      var productId = parseInt(form.attr('id').replace('product_addtocart_form_', ''));
      var itemId = 0;
      if (this.value == 'btn-cart-qty-plus') {
        itemId = parseInt($(this).data('item-id'));
        $(document).trigger('minicartAddQty', [itemId, productId]);
      } else if (this.value == 'btn-cart-qty-minus') {
        itemId = parseInt($(this).data('item-id'));
        $(document).trigger('minicartRemoveQty', [itemId, productId]);
      } else if (this.value == 'btn-cart-remove') {
        itemId = parseInt($(this).data('item-id'));
        $('#btn-minicart-remove-' + itemId).click();
      } else {
        var varienForm = new VarienForm(form.attr('id'));
        if (varienForm.validator.validate()) {
          var data;
          var ajaxUrl = form.attr('action');

          data = new FormData(form[0]);
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
            }
            
          })
          .done(function(response) {
            response = response.evalJSON();
            if (response.status === 'SUCCESS') {
              if($('.header-minicart')){
                $('.header-minicart').html(response.minicarthead);
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
    });

    $('.main').on('change', '.apdc-add-to-cart-form', function(event) {
      var optionKeyTab = [];
      var optionKey = '';
      var formId = $(this).attr('id');
      var productId = parseInt(formId.replace('product_addtocart_form_', ''));
      $(this).find('select[name^="super_attribute["]').each(function() {
        var attributeId = parseInt(this.name.replace('super_attribute[', '').replace(']', ''));
        var optionId = parseInt(this.value);
        optionKeyTab.push(attributeId + '-' + optionId);
      });
      $(this).find('select[name^="options["]').each(function() {
        var attributeId = parseInt(this.name.replace('options[', '').replace(']', ''));
        var optionId = parseInt(this.value);
        optionKeyTab.push(attributeId + '-' + optionId);
      });
      if (optionKeyTab.length > 0) {
        optionKey = optionKeyTab.join('_');
      }
      $('#selected-optionKey-' + productId).val(optionKey);
      $(document).trigger('apdcUpdateAddToCartButtons');
    });
  });

  function counterBlink() {
    var blinkInterval = setInterval(function() {
      $('.header-minicart .count').fadeOut(100).fadeIn(100);
    }, 200);
    setTimeout(function() {
      clearInterval(blinkInterval);
    }, 1500);
  }

  function startLoading(productId)
  {
    var actions = $('#product_addtocart_form_' + productId).find('.actions');
    actions.find('.action-loading').show();
  }
  function finishLoading()
  {
    var actions = $('.product-info').find('.actions');
    actions.find('.action-loading').hide();
    counterBlink();
  }

  $(document).on('minicartAddQty', function(event, itemId, productId) {
    startLoading(productId);
  });
  $(document).on('minicartRemoveQty', function(event, itemId, productId) {
    startLoading(productId);
  });
  $(document).on('minicartRemoveItem', function(event, itemId, productId) {
    startLoading(productId);
  });
  $(document).on('minicartLoaded', function(event) {
    finishLoading();
  });

  // used to change the add to cart button. 
  // If the product already added to cart, we must display + and - buttons with the qty already added.
  $(document).on('apdcUpdateAddToCartButtons', function() {

    // init display of add to cart button
    $('.product-info .simple-add-to-cart-button').show();
    $('.product-info .qty-buttons').hide();

    if (Object.keys(apdcProductAddedToCart).length > 0) {
      for (var productId in apdcProductAddedToCart) {
        var productAdded = apdcProductAddedToCart[productId];
        if ($('#product_addtocart_form_' + productId)) {
          var qty = null;
          var itemId = null;
          if (!(productAdded.options instanceof Array) && Object.keys(productAdded.options.length > 0)) {
            for (var optionKey in productAdded.options) {
              if ($('#selected-optionKey-' + productId).val() == optionKey) {
                itemId = productAdded.options[optionKey].itemId;
                qty = productAdded.options[optionKey].qty;
                break;
              }
            }
          } else if (typeof(productAdded.qty) !== 'undefined') {
            qty = productAdded.qty;
            itemId = productAdded.itemId;
          }
          if (qty !== null && itemId !== null) {
            apdcUpdateQtyButtons(productId, itemId, qty);
          }
        }
      }
    }
  });
  function apdcUpdateQtyButtons(productId, itemId, qty)
  {
    var productContainer = $('#product_addtocart_form_' + productId);
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

})(jQuery);

