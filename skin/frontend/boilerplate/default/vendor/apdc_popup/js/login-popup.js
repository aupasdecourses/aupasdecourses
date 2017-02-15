Validation.add('required-entry', "Merci de compléter ce champ!", function(v) {
        return !Validation.get('IsEmpty').test(v);
});

jQuery(document).ready(function() {

    if (typeof(apdcLoginPopup) === 'undefined') {
      apdcLoginPopup = new ApdcPopup({
          id: 'login-form'
      });
    }

    jQuery(document).on('click', '#account-login',function(e) {
        e.preventDefault();
        e.stopPropagation();
        apdcLoginPopup.showLoading();
        showLoginForm(this,'apdc_login_view');
    });

    jQuery(document).on('submit','#login-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        processLoginForm(this);
    });

    jQuery(document).on('click', '#choose-district',function(e) {
        apdcLoginPopup.close();
    });

    jQuery(document).on('click','#forgot-password', function(e) {
        e.preventDefault();
        e.stopPropagation();
        showLoginForm(this, 'apdc_forgotpassword_view');
    });

    jQuery(document).on('submit','#password-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        processLoginForm(this);
    });

    function showLoginForm(elt,handle) {
        var ajaxUrl = jQuery(elt).data('login-view');
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
                loginContent = response.html;
                apdcLoginPopup.updateContent(response.html);
            } else if (response.status === 'ERROR') {
                var message = '<ul class="messages"><li class="notice-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
                apdcLoginPopup.updateContent(message);
            }
        })
        .fail(function() {
            console.log('failed');
        });

    }

    function processLoginForm(elt) {
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
                if (response.status === 'SUCCESS') {
                    if(typeof response.redirect != 'undefined'){
                      window.location.href = response.redirect;
                    } else {
                      loginContent = response.html;
                      apdcLoginPopup.updateContent(response.html);
                    }
                } else if (response.status === 'ERROR') {
                    loginContent = response.html;
                    apdcLoginPopup.updateContent(response.html);
                } else {
                    console.log('failed');
                }
            })
            .fail(function() {
                console.log('failed');
            });
    }

});
