var Apdc_DdateTimePicker = function(options) {
  var defaults = {
    'saveUrl': '',
    'redirectUrl':'',
    'daysAndSlots' : {},
    'container': '#co-ddate-form .delivery'
  };
  var apdc_ddateTimePicker = {
    options: {},
    currentDate: null,
    currentTime: null,
    init: function(options) {
      this.options = jQuery.extend(defaults, options);
      this.isInPopup = (jQuery(this.options.container).parents('.apdc-popup').length > 0);
      this.initPreselected();
      this.initActions();
    },

    initActions: function() {
      var self = this;
      jQuery(this.options.container).find('.select-days li.available').on('click', function() {
        self.currentDate = jQuery(this).data('date');
        self.currentTime = null;
        self.refreshCurrentDate();
      });
      jQuery(document).on('click', this.options.container + ' .select-time li', function() {
        self.currentTime = jQuery(this).data('time');
        self.refreshCurrentTime();
      });
    },
    initPreselected: function() {
      if (typeof (this.options.currentDate) !== 'undefined' &&
        this.options.currentDate !== ''
      ) {
        this.currentDate = this.options.currentDate;
        if (typeof(this.options.currentTime) !== 'undefined' &&
          this.options.currentTime !== ''
        ) {
          this.currentTime = this.options.currentTime;
        }
        this.refreshCurrentDate();
      }
    },
    refreshCurrentDate: function() {
      jQuery(this.options.container + ' .select-days li.selected').removeClass('selected');
      if (this.currentDate && this.currentDate !== '') {
        if (this.isInPopup && typeof(apdcDeliveryPopup) !== 'undefined') {
          apdcDeliveryPopup.showLoading();
          window.setTimeout(function() {
            apdcDeliveryPopup.hideLoading();
          }, 200);
        }
        jQuery(this.options.container + ' .select-days li[data-date="' + this.currentDate + '"]').addClass('selected');
        var slots = this.options.daysAndSlots[this.currentDate].slots;
        var chooseTime = '';
        for (var slot in slots) {
          var selected = (this.currentDate === this.options.currentDate && slots[slot].dtime === this.options.currentTime ? ' selected' : '');
          chooseTime += '<li class="available' + selected + '" data-time="' + slots[slot].dtime_id + '"><div>' + slots[slot].dtime + '</div></li>';
        }
        //chooseTime += '<li class="availabl" data-time=""><div>15h00-18h00</div></li>';
        jQuery(this.options.container + ' .select-time ul').html(chooseTime);
        jQuery(this.options.container + ' .select-time').removeClass('hide');
        if (this.isInPopup && typeof(apdcDeliveryPopup) !== 'undefined') {
          apdcDeliveryPopup.initPopupHeight();
        }
      }
    },
    refreshCurrentTime: function() {
      jQuery(this.options.container + ' .select-time li.selected').removeClass('selected');
      if (this.currentTime && this.currentTime !== '') {
        jQuery(this.options.container + ' .select-time li[data-time="' + this.currentTime + '"]').addClass('selected');
        this.saveSelectedDateTime();
      }
    },
    saveSelectedDateTime: function() {
      var self = this;

      if (this.isInPopup && typeof(apdcDeliveryPopup) !== 'undefined') {
        apdcDeliveryPopup.showLoading();
      }
      jQuery.ajax( {
        url : self.options.saveUrl,
        type: 'post',
        dataType : 'json',
        data:{
          'date':self.currentDate,
          'dtime':self.currentTime,
          'ddatei':'',
          'url':self.options.redirectUrl
        },
        success : function(data) {
          if (self.isInPopup) {
            window.location.reload();
          }
        }
      });
    }
  };
  apdc_ddateTimePicker.init(options);
  return apdc_ddateTimePicker;
};
