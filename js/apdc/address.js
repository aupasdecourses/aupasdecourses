jQuery(document).ready(function() {
    function AddressBar(options) {
        this.root = options.root;
        this.zipcodes = options.zipcodes;
        this.zipcode = '';
        this.ajaxUrl = '';
        this.init();
    }

    AddressBar.prototype.init = function() {
        this.clearMessage();
        this.setUrl();
        this.setZipcode();
    };

    AddressBar.prototype.showLoading = function() {
      this.addMessage("Veuillez patentiez ...");
      $j(this.root + ' button').attr( "disabled", true );
    };

    AddressBar.prototype.hideLoading = function() {
      this.clearMessage();
      $j(this.root + ' button').attr( "disabled", false );
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

    //Popup in case several choices for quartier

    var disambiguationPopup = [];

    if (typeof(apdcDisambiguationPopup) === 'undefined') {
      apdcDisambiguationPopup = new ApdcPopup({
          id: 'disambiguation-neighborhood'
      });
    }

    function updateNeighborhood(zipcodes){
        $j('#' + apdcDisambiguationPopup.id + ' .apdc-popup-content li.neighborhood_list').each(function(i,elt){
            var $elt = $j(elt);
            zipcodes.each(function(e,i){
                if(parseInt($elt.attr('id'))==e.website_id){
                    $elt.show();
                }
            });
        }); 
    };

    function showDisambiguationForm(elt,handle,zipcodes) {
        apdcDisambiguationPopup.showLoading();
        var ajaxUrl = $j(elt).data('disambiguation-view');
        var data = new FormData();
        data.append('isAjax', 1);
        data.append('handle', handle);
        $j.ajax({
            url: ajaxUrl,
            data: data,
            processData: false,
            contentType: false,
            type: 'POST'

        })
        .done(function(response) {
            if (response.status === 'SUCCESS') {
                disambiguationPopup[handle] = response.html;
                apdcDisambiguationPopup.updateContent(response.html);
                updateNeighborhood(zipcodes);

            } else if (response.status === 'ERROR') {
                var message = '<ul class="messages"><li class="notice-msg"><ul><li><span>' + response.message + '</span></li></ul></li></ul>';
                apdcDisambiguationPopup.updateContent(message);
            }
            apdcDisambiguationPopup.initPopupHeight();
        })
        .fail(function() {
            console.log('failed');
        });
    }

    $j('#GoogleAutoCompleteInput').on('keydown',function(e){  
        $e=$j(this);
        if($e.val()!=''){
            $e.siblings('button').hide();
        }
    });

    lpAddressBar = new AddressBar({root: '#address-bar',zipcodes: zipcodes});

    $j('#address-bar button[type="submit"]').on('click',function(e){      
        e.preventDefault();
        e.stopPropagation();
        if (lpAddressBar) {
            lpAddressBar.showLoading();
            if(lpAddressBar.checkZipcode()){
                website_ids = lpAddressBar.getData('website_id');
                if(!(website_ids instanceof Array)){
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
                }else{
                    showDisambiguationForm(this,'apdc_disambiguation_neighborhood',website_ids);
                    lpAddressBar.hideLoading();
                }
            }else if (lpAddressBar.zipcode==""||lpAddressBar.zipcode.length!=5||isNaN(parseInt(lpAddressBar.zipcode))){
                message="<p>Désolé, mais nous n\'avons pas reconnu votre adresse, merci de bien vouloir réessayer.</p>";
                lpAddressBar.hideLoading();
                lpAddressBar.addMessage(message);
                return;
            }else{
                message="<p>Désolé, aucun quartier ne correspond à votre recherche.</p>";
                lpAddressBar.hideLoading();
                lpAddressBar.addMessage(message);
                lpAddressBar.displayNewsletter();
                return;
            }
        }

    });
   
    $j('#address-bar select[name="website-list"]').on('change',function(e){ 
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