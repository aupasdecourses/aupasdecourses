    function AddressBar(options) {
        this.root = options.root;
        this.zipcodes = options.zipcodes;
        this.zipcode = '';
        this.ajaxUrl = '';
        this.init();
        this.is_in_popup = (jQuery(this.root).parents('.apdc-popup').length > 0);
        this.popup = null;
        if (this.is_in_popup) {
          var popupId = jQuery(this.root).parents('.apdc-popup').attr('id');
          this.popup = window[popupId];
          GoogleApiLandingpage();
        }
    }

    AddressBar.prototype.init = function() {
        this.clearMessage();
        this.setUrl();
        this.setZipcode();
    };

    AddressBar.prototype.showLoading = function() {
      if (this.is_in_popup) {
        this.popup.showLoading();
      } else {
        this.addMessage("<p style='margin-top:15px;'>Veuillez patienter ...</p>");
      }
      $j(this.root + ' button').attr( "disabled", true );
      $j(this.root + ' input').attr( "disabled", true );
      $j(this.root + ' select').attr( "disabled", true );
    };

    AddressBar.prototype.hideLoading = function() {
      if (this.is_in_popup) {
        this.popup.hideLoading();
      } else {
        this.clearMessage();
      }
      $j(this.root + ' button').attr( "disabled", false );
      $j(this.root + ' input').attr( "disabled", false );
      $j(this.root + ' select').attr( "disabled", false );
    };

    AddressBar.prototype.setZipcode = function() {
      this.zipcode = $j(this.root+' input[name="input_zipcode"]').val();
    };

    AddressBar.prototype.addMessage = function(message) {
        $j(this.root+" div[name='messages']").html(message);
    };

    AddressBar.prototype.clearMessage = function() {
        $j(this.root+" div[name='messages']").html("");
    };

    AddressBar.prototype.displayNewsletter = function() {
        $j(this.root+" #newsletter-popup").show();
    };

    AddressBar.prototype.setUrl = function() {
      this.ajaxUrl = $j(this.root+' button').attr('data-ajax-url');
    };

    AddressBar.prototype.checkZipcode = function(zipcodes){
        this.setZipcode();
        var self = this;
        r=$j.map(this.zipcodes, function(obj,i) {
            if(i== self.zipcode){
                return true;
            } 
        });
        return r[0];
    };

    AddressBar.prototype.getData = function(attribute){
        r = this.zipcodes[this.zipcode]

        if(r.length==1){
            return this.zipcodes[this.zipcode][0][attribute];
        }else{
            return this.zipcodes[this.zipcode];
        }
    };

jQuery(document).ready(function() {

    $j('.GoogleAutoCompleteInput').on('keydown',function(e){  
        $e=$j(this);
        if($e.val()!=''){
            $e.siblings('button').hide();
            $e.parents('.address-bar').removeClass('has-value');
        }
    });

    lpAddressBar = new AddressBar({root: '.address-bar',zipcodes: zipcodes});

    $j('.address-bar button[type="submit"]').on('click',function(e){      
        e.preventDefault();
        e.stopPropagation();
        if (lpAddressBar) {
            lpAddressBar.showLoading();
            if(lpAddressBar.checkZipcode()){
                website_ids = lpAddressBar.getData('website_id');
                if (website_ids instanceof Array) {
                  website_ids = website_ids[0].website_id;
                }
                $j.ajax( {
                    url : lpAddressBar.ajaxUrl,
                    dataType : 'json',
                    type : 'post',
                    data : {
                        isAjax:1,
                        medium:'zipcode',
                        zipcode:lpAddressBar.zipcode,
                        website:website_ids,
                        name:lpAddressBar.getData('name'),
                    },
                    success: function(data) {
                      if(data.redirect) {
                        window.location = data.redirectURL;
                      }
                    },
                });
            }else if (lpAddressBar.zipcode==""||lpAddressBar.zipcode.length!=5||isNaN(parseInt(lpAddressBar.zipcode))){
                message="<p style='margin-top:15px;'>Désolé, mais nous n\'avons pas reconnu votre adresse, merci de bien vouloir réessayer.</p>";
                lpAddressBar.hideLoading();
                lpAddressBar.addMessage(message);
                return;
            }else{
                message="<p style='margin-top:15px;'>Pas encore chez vous ? On arrive bientôt !</p>";
                lpAddressBar.hideLoading();
                lpAddressBar.addMessage(message);
                lpAddressBar.displayNewsletter();
                return;
            }
        }

    });
   
    $j('.address-bar select[name="website-list"]').on('change',function(e){ 
        lpAddressBar.showLoading();
        var $elt=$j(this).children('option:selected');
        $j.ajax( {
            url : lpAddressBar.ajaxUrl,
            dataType : 'json',
            type : 'post',
            data : {
                isAjax:1,
                medium:'select',
                website:$elt.attr('value'),
                name:$elt.attr('name'),
            },
            success: function(data) {
              if(data.redirect) {
                window.location = data.redirectURL;
              }
            },
        });
    });    


});
