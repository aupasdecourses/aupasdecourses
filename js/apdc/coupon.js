$j.noConflict();
$j(document).ready(function(){
    $j('.addcoupon').on('click',function(){
        $j('.message-coupon').hide();
        $j('#coupon_code').removeClass('validation-failed');
        if($j('#coupon_code').val() == ''){
            $j('#coupon_code').addClass('validation-failed');
            return false;}
        $j('#coupon-please-wait').show();
        $j('button.addcoupon').hide();
        $j.ajax({
            url: siteurl+'ajaxcoupon/index/customcouponPost',
            dataType: 'json',
            data: $j("form#discount-coupon-form").serializeArray(), 
            type:'POST',
            success: function(data) {
                $j('#coupon-please-wait').hide();
                $j('.message-coupon').removeClass('error').removeClass('success');
                if(data.status == 'SUCCESS'){
                    if($j('#discount-coupon-form')){
                        $j('#checkout-review-table').find("tfoot").replaceWith(data.totals);
                        $j('#shipping_method-progress-opcheckout').replaceWith(data.progress);
                        $j('#discount-coupon-form').replaceWith(data.review);
                        $j('.message-coupon').addClass('success');
                        $j('button.addcoupon').show();
                    }
                    if(data.msg){
                        $j('.message-coupon').html(data.msg);
                        $j('.message-coupon').show();
                      }
                }else{
                    if($j('#discount-coupon-form')){
                        $j('#checkout-review-table').find("tfoot").replaceWith(data.totals);
                        $j('#shipping_method-progress-opcheckout').replaceWith(data.progress);
                        $j('#discount-coupon-form').replaceWith(data.review);
                        $j('.message-coupon').addClass('error');
                        $j('button.addcoupon').show();
                    }
                    if(data.msg){
                        $j('.message-coupon').html(data.msg);
                        $j('.message-coupon').show();
                      }
                }
            }
        });
    });

    $j('.cancelcoupon').on('click',function(){
        var data1 = new Object();
        data1.remove = 1;
        $j('.message-coupon').hide();
        if($j('#coupon_code').val() == ''){return false;}
        $j('#coupon-please-wait').show();
        $j('button.cancelcoupon').hide();
        $j.ajax({
            url: siteurl+'ajaxcoupon/index/customcouponPost',
            dataType: 'json',
            data: data1, 
            type:'POST',
            success: function(data) {
                $j('#coupon-please-wait').hide();
                $j('.message-coupon').removeClass('error').removeClass('success');
                if(data.status == 'SUCCESS'){
                    if($j('#discount-coupon-form')){
                        $j('#checkout-review-table').find("tfoot").replaceWith(data.totals);
                        $j('#shipping_method-progress-opcheckout').replaceWith(data.progress);
                        $j('#discount-coupon-form').replaceWith(data.review);
                        $j('.message-coupon').addClass('success');
                        $j('button.cancelcoupon').show();
                    }
                    if(data.msg){
                        $j('.message-coupon').html(data.msg);
                        $j('.message-coupon').show();
                      }
                }else{
                    if($j('#discount-coupon-form')){
                        $j('#checkout-review-table').find("tfoot").replaceWith(data.totals);
                        $j('#shipping_method-progress-opcheckout').replaceWith(data.progress);
                        $j('#discount-coupon-form').replaceWith(data.review);
                        $j('.message-coupon').addClass('error');
                        $j('button.cancelcoupon').show();
                    }
                    if(data.msg){
                        $j('.message-coupon').html(data.msg);
                        $j('.message-coupon').show();
                      }
                
                    }
            }
        });
    });
});