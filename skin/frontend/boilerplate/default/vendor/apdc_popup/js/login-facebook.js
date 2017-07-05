(function($) {
  $(document).ready(function() {
    $.ajaxSetup({ cache: true });
    $.getScript('//connect.facebook.net/fr_FR/all.js', function(){
      FB.init({
        appId: apdcFbLoginAppiId,
        version: 'v2.9',
        xfbml:1
      });     

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
      console.log(response);
      data.status = response.status;
      if (response.status === 'connected') {
        data.uid = response.authResponse.userID;
        data.access_token = response.authResponse.accessToken;
        data.expires_in = response.authResponse.expiresIn;
        FB.api(
          '/me',
          'GET',
          {"fields":"id,name,first_name,last_name,link,birthday,gender,email,picture.type(large)"},
          function(fields) {
            data.fields = fields;
            ajaxLogin(data);
          }

        );

      } else {
      //} else if (response.status === 'not_authorized') {
        ajaxLogin(data);
      }
    });
  }

  function ajaxLogin(data)
  {
    var ajaxUrl = jQuery('#connect_with_facebook').data('ajax-action');
    apdcLoginPopup.showLoading();
    data.isAjax = 1;
    jQuery.ajax({
      url: ajaxUrl,
      data: data,
      type: 'POST'
    })
      .done(function(response) {
        apdcLoginPopup.updateContent(response.html);
      })
      .fail(function() {
        console.log('failed');
      });
  }
})(jQuery);

