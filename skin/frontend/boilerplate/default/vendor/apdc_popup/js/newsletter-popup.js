Validation.add('required-entry', "Merci de compléter ce champ!", function(v) {
        return !Validation.get('IsEmpty').test(v);
});

jQuery(document).ready(function() {

    if (typeof(apdcNewsletterPopup) === 'undefined') {
      apdcNewsletterPopup = new ApdcPopup({
          id: 'newsletter-form'
      });
    }

    jQuery(document).on('click', '#newsletter-popup',function(e) {
        e.preventDefault();
        e.stopPropagation();
        apdcNewsletterPopup.showLoading();
        showNewsletterForm(this,'apdc_newsletter_view');
    });

    jQuery(document).on('submit','#newsletter-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        processNewsletterForm(this);
    });

    function showNewsletterForm(elt,handle) {
        var ajaxUrl = jQuery(elt).data('newsletter-view');
        var data = new FormData();
        data.append('isAjax', 1);
        data.append('handle', handle);
        jQuery.ajax({
            url: ajaxUrl,
            data: data,
            processData: false,
            contentType: false,
            type: 'POST'

        })
        .done(function(response) {
            if (response.status === 'SUCCESS') {
                apdcNewsletterPopup.updateContent(response.html);
            } else if (response.status === 'ERROR') {
                var message = '<ul class="messages"><li class="notice-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
                apdcNewsletterPopup.updateContent(message);
            }
        })
        .fail(function() {
            console.log('failed');
        });

    }

    function processNewsletterForm(elt) {
        var ajaxUrl = jQuery(elt).attr('action');
        var data = new FormData(jQuery(elt)[0]);
        data.append("isAjax", 1);
        jQuery(elt).children("input").attr("disabled", true);
        jQuery(elt).children("button").attr("disabled", true).removeClass("button-green");
        jQuery.ajax({
                url: ajaxUrl,
                data: data,
                processData: false,
                contentType: false,
                type: 'POST'

            })
            .done(function(response) {
                response = JSON.parse(response);
                console.log(response);
                console.log(response.html);
                apdcNewsletterPopup.updateContent(response.html);
            })
            .fail(function() {
                console.log('failed');
            });
    }

});
