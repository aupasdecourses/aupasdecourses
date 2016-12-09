(function($) {
  $(document).ready(function() {
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
  });

  $(document).on('minicartAddQty', function(event, itemId, productId) {
    var inputQty = $('input.cart-item-quantity[data-item-id="' + itemId + '"]');
    var qty = inputQty.val();
    inputQty.val(++qty);
    $('button#qbutton-' + itemId).click();
  });
  $(document).on('minicartRemoveQty', function(event, itemId, productId) {
    var inputQty = $('input.cart-item-quantity[data-item-id="' + itemId + '"]');
    var qty = inputQty.val();
    inputQty.val(--qty);
    $('button#qbutton-' + itemId).click();
  });

})(jQuery);
