function ApdcPopup(options) {
	this.id = options.id; // eg : product-quick-view
	this.ajaxUrl = '';
  this.is_loaded = false;
  this.is_open = false;
	this.onReady = options.onReady || false;
  this.populateTemplate = (typeof(options.getTemplate) !== 'undefined' && options.getTemplate === true) ? true : false;
  this.autoHeightPopup = (typeof(options.autoHeightPopup) !== 'undefined' && options.autoHeightPopup === true) ? true : false;

	if (!(/-popup$/.test(this.id))) {
		this.id = this.id + '-popup';
	}
	if (typeof(options.ajaxUrl) !== 'undefined') {
		this.ajaxUrl = options.ajaxUrl;
	}
	this.init();
}
ApdcPopup.prototype.init = function() {
  if (this.populateTemplate) {
    this.getTemplate();
  }
  this.cloneDefaultTemplate();
};

ApdcPopup.prototype.cloneDefaultTemplate = function() {
  var self = this;

  var newTemplateHtml = jQuery('<div />').append(jQuery('#_apdc_popup_default_template_id_').clone()).html();
  newTemplateHtml = newTemplateHtml.replace(new RegExp('_apdc_popup_default_template_id_', 'g'), this.id);
  jQuery('body').append(newTemplateHtml);

  window.setTimeout(function() {
    self.initActions();
    if (!self.populateTemplate) {
      if (self.onReady && typeof(self.onReady) === 'function') {
        self.onReady(jQuery('#' + self.id).find('.content'));
      }
      self.is_loaded = true;
    }
  }, 0);
};
ApdcPopup.prototype.getTemplate = function() {
  var self = this;
  if (typeof (apdcPopupAjaxUrl) !== 'undefined') {
	  this.ajaxUrl = apdcPopupAjaxUrl; // see apdc_popup/popup_js.phtml
  }
  if (this.ajaxUrl !== '') {
    jQuery.ajax({
      url:self.ajaxUrl,
      data:{ isAjax:1, id: self.id },
      type:'POST'
    })
    .done(function(response) {
      if (response.status === 'SUCCESS') {
        var newContent = jQuery(response.html).find('.content').html();
        if (jQuery('#' + self.id).length > 0) {
          if (!self.is_open) {
            self.updateContent(newContent);
          }
        } else {
          jQuery('body').append(response.html);
        }
        window.setTimeout(function() {
          if (self.onReady && typeof(self.onReady) === 'function') {
            self.onReady(newContent);
          }
          self.is_loaded = true;
        }, 0);
      }
    })
    .fail(function() {
      console.log('ERROR: get popup template for ' + self.id);
    });
  } else {
      console.log('ERROR: ajax url not set for ' + self.id);
  }
};
ApdcPopup.prototype.isLoaded = function() {
  return this.is_loaded;
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
  this.is_open = true;
};

ApdcPopup.prototype.close = function() {
  jQuery('#' + this.id).fadeOut('fast');
  this.is_open = false;
};

ApdcPopup.prototype.updateContent = function(contentHtml) {
  jQuery('#' + this.id + ' .apdc-popup-content').html(contentHtml);
  if (this.autoHeightPopup) {
    this.initPopupHeight();
  }
  this.hideLoading();
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

ApdcPopup.prototype.isOpen = function() {
  return this.is_open;
};

//Remove validation advice that prevents form to be submitted
jQuery(document).on('click','input',function(e){
    jQuery('.validation-advice').remove();
});
