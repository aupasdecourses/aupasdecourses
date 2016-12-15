if (typeof(apdcProductAddedToCart) === "undefined") {
  var apdcProductAddedToCart = {}; // Should be generated in template : apdc_cart/cart/minicart/items.phtml
}
(function($) {
  var cartItemCommentMessageInterval = null;
  $(document).ready(function() {

    $('.apdc-cart-item-comment').on('click', '.apdc-cart-item-comment-save', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var form = $(this).parents('form');
        var productId = parseInt(form.data('product-id'));
        var varienForm = new VarienForm(form.attr('id'));
        var ajaxUrl = $(this).parents('.apdc-cart-item-comment').data('ajax-url');
        var data = new FormData(form[0]);
        data.append('isAjax', 1);

        var messages = '';
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
            if($('.header-minicart').length > 0){
              $('.header-minicart').html(response.minicarthead);
              message = '<ul class="messages"><li class="success-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
              $('.apdc-cart-item-comment-' + productId + ' .apdc-cart-item-comment-messages').html(message).slideDown('fast');
              if (cartItemCommentMessageInterval) {
                window.clearInterval(cartItemCommentMessageInterval);
              }
              cartItemCommentMessageInterval = window.setInterval(function() {
                $('.apdc-cart-item-comment-' + productId + ' .apdc-cart-item-comment-messages').slideUp('fast').html('');
              }, 3000);
            }
          } else if (response.status === 'ERROR') {
            message = '<ul class="messages"><li class="error-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
            $('.apdc-cart-item-comment-' + productId + ' .apdc-cart-item-comment-messages').html(message).slideDown('fast');
          }
        })
        .fail(function() {
          console.log('failed');
        })
        .always(function() {
          finishLoading();
        });
    });
  });
  var oldOptionKey = '';
  $(document).on('apdcUpdateAddToCartButtons_end', function(event, fromProductId) {
    $(document).trigger('apdcUpdateCartItemComment', [fromProductId]);
  });
  $(document).on('apdcUpdateCartItemComment', function(event, fromProductId) {
    fromProductId = parseInt(fromProductId);
    var currentProductProcessed = false;
    if (Object.keys(apdcProductAddedToCart).length > 0) {
      for (var productId in apdcProductAddedToCart) {
        if (fromProductId === parseInt(productId)) {
          currentProductProcessed = true;
        }
        var optionKey = $('.selected-optionKey-' + productId).val();
        if (oldOptionKey != optionKey) {
          jQuery('.apdc-cart-item-comment-' + productId).data('item-id', '');
          jQuery('.apdc-cart-item-comment-' + productId + ' .apdc-cart-item-comment-item-id').val('');
          jQuery('.apdc-cart-item-comment-' + productId + ' textarea').val('');
          jQuery('.apdc-cart-item-comment-' + productId).hide();
        }
        var qty = 0;
        var productAdded = apdcProductAddedToCart[productId];
        if ($('.product_addtocart_form_' + productId).length > 0) {
          var itemId = null;
          var comment = null;
          if (!(productAdded.options instanceof Array) && Object.keys(productAdded.options.length > 0)) {
            if (typeof(productAdded.options[optionKey]) !== 'undefined') {
                itemId = productAdded.options[optionKey].itemId;
                comment = productAdded.options[optionKey].comment;
                qty = productAdded.options[optionKey].qty;
            }
          } else if (typeof(productAdded.qty) !== 'undefined') {
            comment = productAdded.comment;
            itemId = productAdded.itemId;
            qty = productAdded.qty;
          }
          if (qty !== null && itemId !== null) {
            if (oldOptionKey != optionKey) {
              jQuery('.apdc-cart-item-comment-' + productId + ' textarea').val(comment);
            }
            jQuery('.apdc-cart-item-comment-' + productId).data('item-id', itemId);
            jQuery('.apdc-cart-item-comment-' + productId + ' .apdc-cart-item-comment-item-id').val(itemId);
            if (parseInt(qty) > 0) {
              jQuery('.apdc-cart-item-comment-' + productId ).removeClass('hide');
              jQuery('.apdc-cart-item-comment-' + productId).show();
            }
          }
          oldOptionKey = optionKey;
        }
        if (qty === 0) {
          jQuery('.apdc-cart-item-comment-' + productId + ' textarea').val('');
          jQuery('.apdc-cart-item-comment-' + productId).hide();
        }
      }
    }
    if (fromProductId > 0 && !currentProductProcessed) {
      jQuery('.apdc-cart-item-comment-' + fromProductId + ' textarea').val('');
      jQuery('.apdc-cart-item-comment-' + fromProductId).hide();
    }
  });

  function startLoading(productId) {
    $('.apdc-cart-item-comment-' + productId + ' .apdc-cart-item-comment-messages').slideUp('fast').html('');
    $('.apdc-cart-item-comment-' + productId + ' .loading').show('fast');
  }
  function finishLoading() {
    $('.apdc-cart-item-comment .loading').hide('fast');
  }
})(jQuery);
