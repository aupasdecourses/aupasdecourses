if (typeof(apdcProductQuickViewPopup) === 'undefined') {
  var apdcProductQuickViewPopup = null;
}
(function($) {
  $(document).ready(function() {
    if (apdcProductQuickViewPopup === null) {
      apdcProductQuickViewPopup = new ApdcPopup({
        id:'product-quick-view'
      });
    }
    $('.page-header-container').on('click', '.header-minicart .mini-commercant-name', function() {
      $(this).toggleClass('closed');
      if (cartSlideUpdateAccordionUrl) { // see template /apdc_cart/minicart/items.phtml
        var open = 1;
        var commercant = parseInt($(this).data('commercant'));
        if ($(this).hasClass('closed')) {
          open = 0;
        }
        $.post(
          cartSlideUpdateAccordionUrl,
          {
            isAjax:1,
            commercant: commercant,
            open: open
          }
        ).done(function(response) {
          if (response.status === 'ERROR') {
            console.log(response);
          }
        });
      }
    });
    $('.page-header-container').on('click', '.header-minicart .qty-sub', function() {
      var itemId = $(this).data('item-id');
      var productId = $(this).data('product-id');
      $(document).trigger('minicartRemoveQty', [itemId, productId]);
    });
    $('.page-header-container').on('click', '.header-minicart .qty-add', function() {
      var itemId = $(this).data('item-id');
      var productId = $(this).data('product-id');
      $(document).trigger('minicartAddQty', [itemId, productId]);
    });
    $('.page-header-container').on('click', '.header-minicart .remove', function() {
      var itemId = $(this).data('item-id');
      var productId = $(this).data('product-id');
      $(document).trigger('minicartRemoveItem', [itemId, productId]);
    });
    $('.page-header-container').on('mouseleave', '.header-minicart .details', function() {
      $(this).removeClass('display');
      $(this).parents('.item').find('.item-details').stop().slideUp('fast');
    });
    $('.page-header-container').on('mouseover', '.header-minicart .details', function() {
      $(this).addClass('display');
      $(this).parents('.item').find('.item-details').stop().slideDown('fast');
    });

    var itemShowedInPopup = {};
    $('.page-header-container').on('click', '.show-item-popup', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var self = this;
      apdcQuickView.checkRequirements(function() {
        showItemQuickView(self);
      });
    });

    function showItemQuickView(elt) {
      var itemId = $(elt).data('item-id');

      apdcProductQuickViewPopup.showLoading();

      if (typeof(itemShowedInPopup[itemId]) !== 'undefined') {
        apdcProductQuickViewPopup.updateContent(itemShowedInPopup[itemId]);
      } else {
        var ajaxUrl = $(elt).data('ajax-product-popup');

        data = new FormData();
        data.append('isAjax', 1);
        data.append('itemId', itemId);

        $.ajax({
          url: ajaxUrl,
          data: data,
          processData: false,
          contentType: false,
          type: 'POST'
        })
        .done(function(response) {
          if (response.status === 'SUCCESS') {
            itemShowedInPopup[itemId] = response.html;
            apdcProductQuickViewPopup.updateContent(response.html);
          } else if (response.status === 'ERROR') {
            var message = '<ul class="messages"><li class="error-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
            apdcProductQuickViewPopup.updateContent(message);
          }
        })
        .fail(function() {
          console.log('failed');
        });
      }
    }
  });

  $(document).on('minicartAddQty', function(event, itemId, productId, qty) {
    var inputQty = $('input.cart-item-quantity[data-item-id="' + itemId + '"]');
    if (typeof(qty) === 'undefined') {
      qty = inputQty.val();
      qty++;
    }
    inputQty.val(qty);
    $('button#qbutton-' + itemId).click();
  });
  $(document).on('minicartRemoveQty', function(event, itemId, productId, qty) {
    var inputQty = $('input.cart-item-quantity[data-item-id="' + itemId + '"]');
    if (typeof(qty) === 'undefined') {
      qty = inputQty.val();
      qty--;
    }
    inputQty.val(qty);
    $('button#qbutton-' + itemId).click();
  });

})(jQuery);
