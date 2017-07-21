/* Facebook Pixel Code - Commented on May 2nd 2017 cause duplicate*/
// !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
// n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
// n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
// t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
// document,'script','https://connect.facebook.net/en_US/fbevents.js');
// fbq('init', '1709948302620869');
// fbq('track', "PageView");
/* End Facebook Pixel Code */

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

//Color for custom top menu

var color_menu={
    "Primeur":"#3ab64b",
    "Boucher":"#f14556",
    "Fromager":"#f8a564",
    "Poissonnier":"#5496d7",
    "Caviste":"#e83632",
    "Boulanger":"#f3a71c",
    "Epicerie Fine":"#2f4da8",
    "Epicier":"#2f4da8",
    "Café & Thé":"#f36520",
    "Traiteur":"#f36520"
};

$j(document).ready(function() {
    for (var k in color_menu){
        $j("#custommenu .parentMenu span:contains('"+k+"')").parent().css("background",color_menu[k]);
    }

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

$j(document).ready(function(){
    if( $j(window).innerWidth() < 767 ){
        $j('.page-header-container .skip-links .skip-container-header-search .search-button').attr('disabled', true).addClass('disabled-search');
		$j('#search_mini_form input').hide();
        $j('.fa-search').click(function(){
			if($j('#search_mini_form input').css('display') == 'none') {
				$j('#search_mini_form input').show();
				$j('#search_mini_form').addClass('show-input');
			}
			else {
				$j('#search_mini_form input').hide();
				$j('#search_mini_form').removeClass('show-input');
			}
        })
    }
});
