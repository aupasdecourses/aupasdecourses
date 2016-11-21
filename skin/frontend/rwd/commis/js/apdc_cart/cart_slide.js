(function($) {
  $(document).ready(function() {
    $('.header-minicart').on('click', '.mini-commercant-name', function() {
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
    $('.header-minicart').on('click', '.qty-sub', function() {
      var itemId = $(this).data('item-id');
      var inputQty = $('input.cart-item-quantity[data-item-id="' + itemId + '"]');
      var qty = inputQty.val();
      inputQty.val(--qty);
      $('button#qbutton-' + itemId).click();
    });
    $('.header-minicart').on('click', '.qty-add', function() {
      var itemId = $(this).data('item-id');
      var inputQty = $('input.cart-item-quantity[data-item-id="' + itemId + '"]');
      var qty = inputQty.val();
      inputQty.val(++qty);
      $('button#qbutton-' + itemId).click();
    });
  });
})(jQuery);
