Validation.add('required-entry', "Merci de compléter ce champ!", function(v) {
        return !Validation.get('IsEmpty').test(v);
});

var newsletterPopup = [];

jQuery(document).ready(function() {

    if (typeof(apdcNewsletterPopup) === 'undefined') {
      apdcNewsletterPopup = new ApdcPopup({
          id: 'newsletter-form'
      });
    }

    jQuery(document).on('click', '#newsletter-popup',function(e) {
        e.preventDefault();
        e.stopPropagation();
        showNewsletterForm(this,'apdc_newsletter_view');
    });

    jQuery(document).on('submit','#newsletter-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        processNewsletterForm(this);
    });

    function showNewsletterForm(elt,handle) {
        apdcNewsletterPopup.showLoading();
        if (typeof(newsletterPopup[handle]) !== 'undefined') {
            apdcNewsletterPopup.updateContent(newsletterPopup[handle]);
            setNewsletterPopupHeight();
        } else {
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
                    newsletterPopup[handle] = response.html;
                    apdcNewsletterPopup.updateContent(response.html);
                } else if (response.status === 'ERROR') {
                    var message = '<ul class="messages"><li class="notice-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
                    apdcNewsletterPopup.updateContent(message);
                }
                setNewsletterPopupHeight();
            })
            .fail(function() {
                console.log('failed');
            });

        }
    }

    function setNewsletterPopupHeight() {
        var popupContainer = jQuery('#' + apdcNewsletterPopup.id + ' .apdc-popup-container');
        var height = popupContainer.find('.apdc-popup-content').children().outerHeight(true);
        var padding = parseFloat(popupContainer.css('padding-top')) + parseFloat(popupContainer.css('padding-bottom'));
        var border = parseFloat(popupContainer.css('border-top')) + parseFloat(popupContainer.css('border-bottom'));
        popupContainer.css('height', (height + padding + border) + 'px');
    }

    function processNewsletterForm(elt) {
        var ajaxUrl = jQuery(elt).attr('action');
        var data = new FormData(jQuery(elt)[0]);
        data.append("isAjax", 1);
        jQuery(elt).children(".form-group").children("input").attr("disabled", true);
        jQuery(elt).children(".form-group").children("select").attr("disabled", true);
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
                apdcNewsletterPopup.updateContent(response.html);
            })
            .fail(function() {
                console.log('failed');
            });
    }

});
