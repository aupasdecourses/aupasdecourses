Validation.add('required-entry', "Merci de compléter ce champ!", function(v) {
	return !Validation.get('IsEmpty').test(v);
});

var accountPopup = [];
var deliveryPopup = [];
var neighborhoodPopup = [];
var apdcLoginPopup = null;
var apdcDeliveryPopup = null;
var apdcNeighborhoodPopup = null;

function showLoginForm(elt,handle) {
  apdcLoginPopup.showLoading();
  jQuery('#' + apdcLoginPopup.id).data('currentView', handle);
  jQuery('#' + apdcLoginPopup.id)[0].dataset.currentView =  handle;
  if (typeof(accountPopup[handle]) !== 'undefined') {
    apdcLoginPopup.updateContent(accountPopup[handle]);
  } else {
    var ajaxUrl = jQuery(elt).data('login-view');
    var data = new FormData();
    data.append('isAjax', 1);
    data.append('handle', handle);
    data.append('referer', window.location.href);
    jQuery.ajax({
      url: ajaxUrl,
      data: data,
      processData: false,
      contentType: false,
      type: 'POST'

    })
      .done(function(response) {
        if (response.status === 'SUCCESS') {
          accountPopup[handle] = response.html;
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
}

function processLoginForm(elt) {
  apdcLoginPopup.showLoading();
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
        if(typeof response.redirect !== 'undefined'){
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

function initLoginPopup() {

  if (jQuery('#account-login').length > 0 || jQuery('#choose-my-district')) {
    if (apdcLoginPopup === null) {
      apdcLoginPopup = new ApdcPopup({
        id: 'login-form',
        autoHeightPopup:true,
        getTemplate:true,
        onReady: function() {
          accountPopup.apdc_login_view = jQuery('#' + apdcLoginPopup.id).find('.content').html();
        }
      });
    }
  }

  if (jQuery('#account-login').length > 0) {
    jQuery('#account-login').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (apdcLoginPopup) {
        if (apdcLoginPopup.isLoaded()) {
          apdcLoginPopup.showLoading();
          apdcLoginPopup.updateContent(accountPopup.apdc_login_view);
        } else {
          apdcLoginPopup.showLoading();
        }
      }
    });
    jQuery(document).on('click', '.to-login-form', function(e) {
      e.preventDefault();
      e.stopPropagation();
      showLoginForm(this, 'apdc_login_view');
    });

    jQuery(document).on('submit','#login-form', function(e) {
      e.preventDefault();
      e.stopPropagation();
      processLoginForm(this);
    });
    
    jQuery(document).on('submit','#register-form', function(e) {
      e.preventDefault();
      e.stopPropagation();
      processLoginForm(this);
    });

    jQuery(document).on('click','#forgot-password', function(e) {
      e.preventDefault();
      e.stopPropagation();
      showLoginForm(this, 'apdc_forgotpassword_view');
    });

    jQuery(document).on('click','#connect-with-google', function(e) {
        e.preventDefault();
        e.stopPropagation();
        showLoginForm(this, 'connect_with_google');
    });

    jQuery(document).on('submit','#password-form', function(e) {
      e.preventDefault();
      e.stopPropagation();
      processLoginForm(this);
    });
  }

  if (jQuery('header #header-delivery-link').length > 0) {
    if (apdcDeliveryPopup === null) {
      apdcDeliveryPopup = new ApdcPopup({
        id: 'delivery',
        autoHeightPopup:true,
        getTemplate:true
      });
    }
    jQuery('#header-delivery-link').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (apdcDeliveryPopup) {
        if (apdcDeliveryPopup.isLoaded()) {
          apdcDeliveryPopup.show();
          apdcDeliveryPopup.initPopupHeight();
        } else {
          apdcDeliveryPopup.showLoading();
        }
      }
    });
  }

  if (jQuery('#header-neighborhood-link').length > 0) {
    if (apdcNeighborhoodPopup === null) {
      apdcNeighborhoodPopup = new ApdcPopup({
        autoHeightPopup:true,
        id: 'neighborhood',
        getTemplate: true
      });
    }
    jQuery('#header-neighborhood-link').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (apdcNeighborhoodPopup) {
        if (apdcNeighborhoodPopup.isLoaded()) {
          apdcNeighborhoodPopup.show();
          apdcNeighborhoodPopup.initPopupHeight();
        } else {
          apdcNeighborhoodPopup.showLoading();
        }
      }
    });
  }

	jQuery(document).on('click', '#choose-district',function(e) {
		e.preventDefault();
		e.stopPropagation();
		showLoginForm(this, 'apdc_register_view');
	});

  jQuery(document).on('click', '#choose-my-district',function(e) {
      e.preventDefault();
      e.stopPropagation();
      showLoginForm(this, 'apdc_choose_neighborhood');
  });

}

jQuery(document).ready(function() {
  initLoginPopup();
});
