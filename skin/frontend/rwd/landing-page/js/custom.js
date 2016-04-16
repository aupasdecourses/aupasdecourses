

/* =================================
   LOADER                     
=================================== */
// makes sure the whole site is loaded
$j(window).load(function() {
        // will first fade out the loading animation
    $j(".status").fadeOut();
        // will fade out the whole DIV that covers the website.
	$j(".preloader").delay(1000).fadeOut("slow");
})

/* =================================
===  RESPONSIVE VIDEO           ====
=================================== */

// $j(".video-container").fitVids();

/* =================================
===  CAROUSEL FOR COMMENTS           ====
=================================== */

$j(document).ready(function() {
  $j("#carousel-comments").owlCarousel({
        autoPlay: 6000,
        itemsCustom : [
        [0, 1],
        [768, 2]
        ],
        stopOnHover:true
  });
});

/* =================================
===  MAILCHIMP                 ====
=================================== */

$j('#sub-form-mailchimp').ajaxChimp({
    callback: mailchimpCallback,
    url: "https://aupasdecourses.us10.list-manage.com/subscribe/post?u=813feff892b5d2dac949b8ad4&amp;id=dbcdbd8580" //Replace this with your own mailchimp post URL. Don't remove the "". Just paste the url inside "".  
});

function mailchimpCallback(resp) {
     if (resp.result === 'success') {
        $j('.subscription-success').html('<i class="icon_check_alt2"></i> ' + resp.msg).fadeIn(1000);
        $j('.subscription-error').fadeOut(500);
        
    } else if(resp.result === 'error') {
        $j('.subscription-error').html('<i class="icon_close_alt2"></i> ' + resp.msg).fadeIn(1000);
    }  
}

/* =================================
===  STICKY NAV                 ====
=================================== */

$j(document).ready(function() {
  $j('.main-navigation').onePageNav({
    scrollThreshold: 0.2, // Adjust if Navigation highlights too early or too late
    filter: ':not(.external)',
    changeHash: true
  });
  
});


/* COLLAPSE NAVIGATION ON MOBILE AFTER CLICKING ON LINK - ADDED ON V1.5*/

if (matchMedia('(max-width: 480px)').matches) {
    $j('.main-navigation a').on('click', function () {
        $j(".navbar-toggle").click();
    });
}


/* NAVIGATION VISIBLE ON SCROLL */

$j(document).ready(function () {
    mainNav();
});

$j(window).scroll(function () {
    mainNav();
});

if (matchMedia('(min-width: 992px), (max-width: 767px)').matches) {
  function mainNav() {
        var top = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
        if (top > 40) $j('.sticky-navigation').stop().animate({"top": '0'});

        else $j('.sticky-navigation').stop().animate({"top": '-60'});
    }
}

if (matchMedia('(min-width: 768px) and (max-width: 991px)').matches) {
  function mainNav() {
        var top = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
        if (top > 40) $j('.sticky-navigation').stop().animate({"top": '0'});

        else $j('.sticky-navigation').stop().animate({"top": '-120'});
    }
}



/* =================================
===  DOWNLOAD BUTTON CLICK SCROLL ==
=================================== */
$j(function( $ ){
			$j('#download-button').localScroll({
				duration:1000
			});
		});


/* =================================
===  FULL SCREEN HEADER         ====
=================================== */
function alturaMaxima() {
  var altura = $j(window).height();
  $j(".full-screen").css('min-height',altura); 
  
}

$j(document).ready(function() {
  alturaMaxima();
  $j(window).bind('resize', alturaMaxima);
});


/* =================================
===  SMOOTH SCROLL             ====
=================================== */
var scrollAnimationTime = 1200,
    scrollAnimation = 'easeInOutExpo';
$j('a.scrollto').bind('click.smoothscroll', function (event) {
    event.preventDefault();
    var target = this.hash;
    $j('html, body').stop().animate({
        'scrollTop': $j(target).offset().top
    }, scrollAnimationTime, scrollAnimation, function () {
        window.location.hash = target;
    });
});


/* =================================
===  WOW ANIMATION             ====
=================================== */
wow = new WOW(
  {
    mobile: false
  });
wow.init();


/* =================================
===  OWL CROUSEL               ====
=================================== */
$j(document).ready(function () {

    $j("#feedbacks").owlCarousel({

        navigation: false, // Show next and prev buttons
        slideSpeed: 800,
        paginationSpeed: 400,
        autoPlay: 5000,
        singleItem: true
    });

    var owl = $j("#screenshots");

    owl.owlCarousel({
        items: 4, //10 items above 1000px browser width
        itemsDesktop: [1000, 4], //5 items between 1000px and 901px
        itemsDesktopSmall: [900, 2], // betweem 900px and 601px
        itemsTablet: [600, 1], //2 items between 600 and 0
        itemsMobile: false // itemsMobile disabled - inherit from itemsTablet option
    });


});


/* =================================
===  Nivo Lightbox              ====
=================================== */
$j(document).ready(function () {

    $j('#screenshots a').nivoLightbox({
        effect: 'fadeScale',
    });

});


/* =================================
===  SUBSCRIPTION FORM          ====
=================================== */
$j("#subscribe").submit(function (e) {
    e.preventDefault();
    var email = $j("#subscriber-email").val();
    var dataString = 'email=' + email;

    function isValidEmail(emailAddress) {
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailAddress);
    };

    if (isValidEmail(email)) {
        $j.ajax({
            type: "POST",
            url: "subscribe/subscribe.php",
            data: dataString,
            success: function () {
                $j('.subscription-success').fadeIn(1000);
                $j('.subscription-error').fadeOut(500);
                $j('.hide-after').fadeOut(500);
            }
        });
    } else {
        $j('.subscription-error').fadeIn(1000);
    }

    return false;
});



/* =================================
===  CONTACT FORM          ====
=================================== */
$j("#contact-form-1").submit(function (e) {
    e.preventDefault();
    var name = $j("#name").val();
    var email = $j("#email").val();
    var subject = $j("#subject").val();
    var message = $j("#message").val();
    var dataString = 'name=' + name + '&email=' + email + '&subject=' + subject + '&message=' + message;

    function isValidEmail(emailAddress) {
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailAddress);
    };

    if (isValidEmail(email) && (message.length > 1) && (name.length > 1)) {
        $j.ajax({
            type: "POST",
            url: "/accueil/ajaxlandingpage/index/sendmail",
            data: dataString,
            success: function () {
                $j('.success').fadeIn(1000);
                $j('.error').fadeOut(500);
            }
        });
    } else {
        $j('.error').fadeIn(1000);
        $j('.success').fadeOut(500);
    }

    return false;
});

/* =================================
===  EXPAND COLLAPSE            ====
=================================== */
$j('.expand-form').simpleexpand({
    'defaultTarget': '.expanded-contact-form'
});



/* =================================
===  STELLAR                    ====
=================================== */
$j(window).stellar({ 
horizontalScrolling: false 
});


/* =================================
===  Bootstrap Internet Explorer 10 in Windows 8 and Windows Phone 8 FIX
=================================== */
if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
  var msViewportStyle = document.createElement('style')
  msViewportStyle.appendChild(
    document.createTextNode(
      '@-ms-viewport{width:auto!important}'
    )
  )
  document.querySelector('head').appendChild(msViewportStyle)
}