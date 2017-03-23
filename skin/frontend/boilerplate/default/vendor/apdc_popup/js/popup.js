function ApdcPopup(options) {
  this.id = options.id; // eg : product-quick-view
  this.ajaxUrl = apdcPopupAjaxUrl; // see apdc_popup/popup_js.phtml

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
    if (e.keyCode == 27) {
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
  jQuery('#' + this.id + ' .apdc-popup-content').html('');
  jQuery('#' + this.id + ' .apdc-popup-loading').show();
  this.show();
};

ApdcPopup.prototype.hideLoading = function() {
  jQuery('#' + this.id + ' .apdc-popup-loading').hide();
};

//Remove validation advice that prevents form to be submitted
jQuery(document).on('click','input',function(e){
    jQuery('.validation-advice').remove();
});
