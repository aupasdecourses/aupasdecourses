jQuery(document).ready(function() {
    apdcLoginPopup = new ApdcPopup({
        id: 'login-form'
    });

    jQuery('#account-login').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        apdcLoginPopup.showLoading();
        showLoginForm(this,'apdc_login_view');
    });

    jQuery('#login-form').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        processLoginForm(this);
    });

    jQuery('#forgot-password').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        showLoginForm(this, 'apdc_forgotpassword_view');
    });

    jQuery('#password-form').on('submit', function(e) {
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
