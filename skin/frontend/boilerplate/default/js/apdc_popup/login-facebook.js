(function($) {
  $(document).ready(function() {
    $.ajaxSetup({ cache: true });
    $.getScript('//connect.facebook.net/fr_FR/all.js', function(){
      FB.init({
        appId: apdcFbLoginAppiId,
        version: 'v2.9',
        xfbml:1
      });
      jQuery('#connect_with_facebook').show();

      jQuery(document).on('click', '#connect_with_facebook', function(e) {
        e.preventDefault();
        e.stopPropagation();
        FB.login(function(response) {
          continueLoginFacebook();
        }, {scope: 'public_profile,email,user_birthday'});
      });
    });
  });

  function continueLoginFacebook()
  {
    FB.getLoginStatus(function(response) {
      var data = {};
      data.status = response.status;
      if (response.status === 'connected') {
        data.uid = response.authResponse.userID;
        data.token = {
          access_token: response.authResponse.accessToken,
          expires_in: response.authResponse.expiresIn
        };
        FB.api(
          '/me',
          'GET',
          {"fields":"id,name,first_name,last_name,link,birthday,gender,email,picture.type(large)"},
          function(fields) {
            data.fields = fields;
            ajaxLogin(data);
          }

        );
      }
    });
  }

  function ajaxLogin(data)
  {
    var ajaxUrl = jQuery('#connect_with_facebook').data('ajax-url');
    apdcLoginPopup.showLoading();
    data.isAjax = 1;
    jQuery.ajax({
      url: ajaxUrl,
      data: data,
      type: 'POST'
    })
      .done(function(response) {
        if (typeof (response.status) !== 'undefined' && response.status === 'SUCCESS') {
          if (typeof(response.new_account) !== 'undefined' && response.new_account === 1) {
            // Google Tag Manager event
            tagmanager_event('validationInscription',{});
          }
        }

        if (typeof(response.redirect) !== 'undefined' && response.redirect !== '') {
          window.location.href = response.redirect;
        } else if (typeof(response.html) !== 'undefined' && response.html !== '') {  
          apdcLoginPopup.updateContent(response.html);
          if (response.need_to_choose_neighborhood) {
            jQuery('#account-login').remove();
            jQuery('#choose-my-district').show();
          }
          apdcLoginPopup.initPopupHeight();
        }
      })
      .fail(function() {
        console.log('failed');
      });
  }
})(jQuery);

