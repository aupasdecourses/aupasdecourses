//Troubleshoot issue when hide is used for dropdown menu in header (Aide)

(function() {
    var isBootstrapEvent = false;
    if (window.jQuery) {
        var all = jQuery('*');
        jQuery.each(['hide.bs.dropdown',
            'hide.bs.collapse',
            'hide.bs.modal',
            'hide.bs.tooltip',
            'hide.bs.popover'], function(index, eventName) {
            all.on(eventName, function( event ) {
                isBootstrapEvent = true;
            });
        });
    }
    var originalHide = Element.hide;
    Element.addMethods({
        hide: function(element) {
            if(isBootstrapEvent) {
                isBootstrapEvent = false;
                return element;
            }
            return originalHide(element);
        }
    });
})();

$j(document).ready(function() {
    msieversion();
});

function msieversion() {

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    var edge = ua.indexOf('Edge/');

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))  // detect IE
    {
        $j('body').addClass('ie');
    }
    else if (edge > 0) { // detect Edge
        $j('body').addClass('edge');
    }

    return false;
}
/* Facebook Pixel Code - Commented on May 2nd 2017 cause duplicate*/
// !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
// n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
// n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
// t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
// document,'script','https://connect.facebook.net/en_US/fbevents.js');
// fbq('init', '1709948302620869');
// fbq('track', "PageView");
/* End Facebook Pixel Code */
