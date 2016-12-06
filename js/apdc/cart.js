//@author Pierre Mainguet
//Add comment in cart page
$j(document).ready(function() {
      $j('#total-box').scrollToFixed({marginTop: 15});
    });

    function setLocationAjax(url,id){
        jQuery('#ajax_loader_'+id).show();
        jQuery('#ajax_loader_success_'+id).hide();
        qty=jQuery('#product-cart-actions_'+id).find('input[name="cart['+id+'][qty]"]').val();
        comments=jQuery('#product-cart-actions_'+id).find('textarea[name="cart['+id+'][item_comment]"]').val();
        try {
            jQuery.ajax( {
                url : url,
                type: 'post',
                dataType : 'json',
                data:{
                    'id':id,
                    'qty':qty,
                    'comments':comments,
                },
                success : function(data) {
                    jQuery('#ajax_loader_'+id).fadeOut(500);
                    jQuery('#ajax_loader_success_'+id).delay(500).fadeIn(500).delay(2000).fadeOut(500);
                    
                    //Update miniheader and totals
                    jQuery('.header-minicart').html(data.minicarthead);
                    jQuery('#shopping-cart-totals-table').html(data.totals);

                    //Rebind click event on header-cart
                    var skipContents = $j('.skip-content');
                    var skipLinks = $j('.skip-link');

                    skipLinks.on('click', function (e) {
                        e.preventDefault();

                        var self = $j(this);
                        // Use the data-target-element attribute, if it exists. Fall back to href.
                        var target = self.attr('data-target-element') ? self.attr('data-target-element') : self.attr('href');

                        // Get target element
                        var elem = $j(target);

                        // Check if stub is open
                        var isSkipContentOpen = elem.hasClass('skip-active') ? 1 : 0;

                        // Hide all stubs
                        skipLinks.removeClass('skip-active');
                        skipContents.removeClass('skip-active');

                        // Toggle stubs
                        if (isSkipContentOpen) {
                            self.removeClass('skip-active');
                        } else {
                            self.addClass('skip-active');
                            elem.addClass('skip-active');
                        }
                    });

                    $j('#header-cart').on('click', '.skip-link-close', function(e) {
                        var parent = $j(this).parents('.skip-content');
                        var link = parent.siblings('.skip-link');

                        parent.removeClass('skip-active');
                        link.removeClass('skip-active');

                        e.preventDefault();
                    });
                }
            });
        } catch (e) {
        }
    }