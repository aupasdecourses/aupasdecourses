if (typeof(apdcProductQuickViewPopup) === 'undefined') {
  var apdcProductQuickViewPopup = null;
}
(function($) {
  var productShowedInPopup = {};
  $(document).ready(function() {
    if (apdcProductQuickViewPopup === null) {
      apdcProductQuickViewPopup = new ApdcPopup({
        id:'product-quick-view',
        autoHeightPopup: true,
      });
    }
    $('.main').on('click', '.show-product-popup', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var self = this;
      apdcQuickView.checkRequirements(function() {
        showProductQuickView(self);
      });
    }); 

    function showProductQuickView(elt) {
      var form = $(elt).parents('form');
      var productId = parseInt(form.attr('id').replace('product_addtocart_form_', ''));

      apdcProductQuickViewPopup.showLoading();

      if (typeof(productShowedInPopup[productId]) !== 'undefined') {
        apdcProductQuickViewPopup.updateContent(productShowedInPopup[productId]);
      } else {
        var varienForm = new VarienForm(form.attr('id'));
        var data;
        var ajaxUrl = $(elt).data('ajax-product-popup');

        data = new FormData(form[0]);
        data.append('isAjax', 1);
        data.append('productId', productId);

        $.ajax({
          url: ajaxUrl,
          data: data,
          processData: false,
          contentType: false,
          type: 'POST'
          
        })
        .done(function(response) {
          if (response.status === 'SUCCESS') {
            productShowedInPopup[productId] = response.html;
            apdcProductQuickViewPopup.updateContent(response.html);
          } else if (response.status === 'ERROR') {
            var message = '<ul class="messages"><li class="notice-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
            apdcProductQuickViewPopup.updateContent(message);
          }
        })
        .fail(function() {
          console.log('failed');
        });
      }
    }

  });
})(jQuery);
