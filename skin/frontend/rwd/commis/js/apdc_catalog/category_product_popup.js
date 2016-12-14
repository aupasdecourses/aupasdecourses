(function($) {
  var productShowedInPopup = {};
  $(document).ready(function() {
    var productQuickViewPopup = new ApdcPopup({
      id:'product-quick-view'
    });
    $('.main').on('click', '.show-product-popup', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var form = $(this).parents('form');
      var productId = parseInt(form.attr('id').replace('product_addtocart_form_', ''));

      productQuickViewPopup.showLoading();

      if (typeof(productShowedInPopup[productId]) !== 'undefined') {
        productQuickViewPopup.updateContent(productShowedInPopup[productId]);
      } else {
        var varienForm = new VarienForm(form.attr('id'));
        var data;
        var ajaxUrl = $(this).data('ajax-product-popup');

        data = new FormData(form[0]);
        data.append('isAjax', 1);
        data.append('id', productId);


        var actions = $(form).find('.actions');

        $.ajax({
          url: ajaxUrl,
          data: data,
          processData: false,
          contentType: false,
          type: 'POST',
          beforeSend: function() {
            //startLoading(productId);
          }
          
        })
        .done(function(response) {
          if (response.status === 'SUCCESS') {
            productShowedInPopup[productId] = response.html;
            productQuickViewPopup.updateContent(response.html);
          }
        })
        .fail(function() {
          console.log('failed');
        })
        .always(function() {
          //finishLoading();
        });
      }
    });

  });
})(jQuery);
