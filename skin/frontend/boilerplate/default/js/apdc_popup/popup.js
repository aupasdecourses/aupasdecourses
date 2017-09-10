function ApdcPopup(options) {
	this.id = options.id; // eg : product-quick-view
	this.ajaxUrl = apdcPopupAjaxUrl; // see apdc_popup/popup_js.phtml
	this.onReady = options.onReady || false;

	if (!(/-popup$/.test(this.id))) {
		this.id = this.id + '-popup';
	}
	if (typeof(options.ajaxUrl) !== 'undefined') {
		this.ajaxUrl = options.ajaxUrl;
	}
	this.init();
}
ApdcPopup.prototype.init = function() {
  this.getTemplate();
};

ApdcPopup.prototype.getTemplate = function() {
  var self = this;
  jQuery.ajax({
    url:self.ajaxUrl,
    data:{ isAjax:1, id: self.id },
    type:'POST'
  })
  .done(function(response) {
    if (response.status === 'SUCCESS') {
      jQuery('body').append(response.html);
      window.setTimeout(function() {
        self.initActions();
        if (self.onReady && typeof(self.onReady) === 'function') {
          self.onReady();
        }
      }, 0);
    }
  })
  .fail(function() {
    console.log('ERROR: get popup template for ' + self.id);
  });
};
ApdcPopup.prototype.initActions = function() {
  var self = this;
  jQuery(document).on('click', '#' + self.id + ' .apdc-popup-overlay', function() {
    self.close();
  });
  jQuery(document).on('click', '#' + self.id + ' .apdc-popup-close', function() {
    self.close();
  });
  jQuery(document).keyup(function(e) {
    if (e.keyCode === 27) {
      self.close();
    }
  });
};

ApdcPopup.prototype.show = function() {
  jQuery('#' + this.id).fadeIn('fast');
};

ApdcPopup.prototype.close = function() {
  jQuery('#' + this.id).fadeOut('fast');
};

ApdcPopup.prototype.updateContent = function(contentHtml) {
  this.hideLoading();
  jQuery('#' + this.id + ' .apdc-popup-content').html(contentHtml);
};

ApdcPopup.prototype.showLoading = function() {
  jQuery('#' + this.id + ' .apdc-popup-loading').show();
  this.show();
};

ApdcPopup.prototype.hideLoading = function() {
  jQuery('#' + this.id + ' .apdc-popup-loading').hide();
};

ApdcPopup.prototype.initPopupHeight = function() {
  var popupContainer = jQuery('#' + this.id + ' .apdc-popup-container');
  var height = popupContainer.find('.apdc-popup-content').children().outerHeight(true);
  var offset = 10;
  var paddingTop = 0;
  if (popupContainer.css('padding-top') !== '' && !isNaN(parseFloat(popupContainer.css('padding-top')))) {
    paddingTop = parseFloat(popupContainer.css('padding-top'));
  }
  var paddingBottom = 0;
  if (popupContainer.css('padding-bottom') !== '' && !isNaN(parseFloat(popupContainer.css('padding-bottom')))) {
    paddingBottom = parseFloat(popupContainer.css('padding-bottom'));
  }

  var borderTop = 0;
  if (popupContainer.css('border-top') !== '' && !isNaN(parseFloat(popupContainer.css('border-top')))) {
    borderTop = parseFloat(popupContainer.css('border-top'));
  }
  var borderBottom = 0;
  if (popupContainer.css('border-bottom') !== '' && !isNaN(parseFloat(popupContainer.css('border-bottom')))) {
    borderBottom = parseFloat(popupContainer.css('border-bottom'));
  }
  popupContainer.css('height', (offset + height + paddingTop + paddingBottom + borderTop + borderBottom) + 'px');
};

//Remove validation advice that prevents form to be submitted
jQuery(document).on('click','input',function(e){
    jQuery('.validation-advice').remove();
});
