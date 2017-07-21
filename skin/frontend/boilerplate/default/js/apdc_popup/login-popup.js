Validation.add('required-entry', "Merci de compléter ce champ!", function(v) {
	return !Validation.get('IsEmpty').test(v);
});

var accountPopup = [];
var deliveryPopup = [];
var neighborhoodPopup = [];

function initLoginPopup() {

	jQuery(document).on('click', '#account-login, .to-login-form',function(e) {
		e.preventDefault();
		e.stopPropagation();
		apdcLoginPopup.showLoading();
		showLoginForm(this,'apdc_login_view');
	});
	
	jQuery(document).on('click', '#header-delivery-link',function(e) {
		e.preventDefault();
		e.stopPropagation();
		apdcDeliveryPopup.showLoading();
		showDelivery(this,'apdc_headerdelivery_view');
	});
	
	jQuery(document).on('click', '#header-neighborhood-link',function(e) {
		e.preventDefault();
		e.stopPropagation();
		apdcNeighborhoodPopup.showLoading();
		showNeighborhood(this,'apdc_neighborhood_view');
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

	jQuery(document).on('click', '#choose-district',function(e) {
		e.preventDefault();
		e.stopPropagation();
		showLoginForm(this, 'apdc_register_view');
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
	
	function showNeighborhood(elt,handle) {
		apdcNeighborhoodPopup.showLoading();
		jQuery('#' + apdcNeighborhoodPopup.id).data('currentView', handle);
		jQuery('#' + apdcNeighborhoodPopup.id)[0].dataset.currentView =  handle;
		if (typeof(neighborhoodPopup[handle]) !== 'undefined') {
		  apdcNeighborhoodPopup.updateContent(neighborhoodPopup[handle]);
		  setPopupHeight(apdcNeighborhoodPopup);
		} else {
			//var ajaxUrl = jQuery(elt).data('login-view');
			var ajaxUrl = jQuery(elt).data('url');
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
					neighborhoodPopup[handle] = response.html;
					apdcNeighborhoodPopup.updateContent(response.html);
				} else if (response.status === 'ERROR') {
					var message = '<ul class="messages"><li class="notice-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
					apdcNeighborhoodPopup.updateContent(message);
				}
			  setPopupHeight(apdcNeighborhoodPopup);
			})
			.fail(function() {
				console.log('failed');
			});
		}
	}
	
	function showDelivery(elt,handle) {
		apdcDeliveryPopup.showLoading();
		jQuery('#' + apdcDeliveryPopup.id).data('currentView', handle);
		jQuery('#' + apdcDeliveryPopup.id)[0].dataset.currentView =  handle;
		if (typeof(deliveryPopup[handle]) !== 'undefined') {
		  apdcDeliveryPopup.updateContent(deliveryPopup[handle]);
		  setPopupHeight(apdcDeliveryPopup);
		} else {
			//var ajaxUrl = jQuery(elt).data('login-view');
			var ajaxUrl = jQuery(elt).data('url');
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
					deliveryPopup[handle] = response.html;
					apdcDeliveryPopup.updateContent(response.html);
				} else if (response.status === 'ERROR') {
					var message = '<ul class="messages"><li class="notice-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
					apdcDeliveryPopup.updateContent(message);
				}
			  setPopupHeight(apdcDeliveryPopup);
			})
			.fail(function() {
				console.log('failed');
			});
		}
	}

	function showLoginForm(elt,handle) {
		apdcLoginPopup.showLoading();
		jQuery('#' + apdcLoginPopup.id).data('currentView', handle);
		jQuery('#' + apdcLoginPopup.id)[0].dataset.currentView =  handle;
		if (typeof(accountPopup[handle]) !== 'undefined') {
		  apdcLoginPopup.updateContent(accountPopup[handle]);
		  setPopupHeight(apdcLoginPopup);
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
			  setPopupHeight(apdcLoginPopup);
			})
			.fail(function() {
				console.log('failed');
			});
		}
	}

	function setPopupHeight(apdcPopup) {
		var popupContainer = jQuery('#' + apdcPopup.id + ' .apdc-popup-container');
		var height = popupContainer.find('.apdc-popup-content').children().outerHeight(true);
		var padding = parseFloat(popupContainer.css('padding-top')) + parseFloat(popupContainer.css('padding-bottom'));
		var border = parseFloat(popupContainer.css('border-top')) + parseFloat(popupContainer.css('border-bottom'));
		popupContainer.css('height', (height + padding + border) + 'px');
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

}

jQuery(document).ready(function() {
	if (typeof(apdcLoginPopup) === 'undefined') {
		apdcLoginPopup = new ApdcPopup({
			id: 'login-form',
			onReady: initLoginPopup
		});
	}
	if (typeof(apdcDeliveryPopup) === 'undefined') {
		apdcDeliveryPopup = new ApdcPopup({
			id: 'delivery'
		});
	}
	if (typeof(apdcNeighborhoodPopup) === 'undefined') {
		apdcNeighborhoodPopup = new ApdcPopup({
			id: 'neighborhood'
		});
	}
});
