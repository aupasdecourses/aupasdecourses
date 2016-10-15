/* =================================
===  CAROUSEL FOR COMMENTS           ====
=================================== */

$j(document).ready(function() {$j("#carousel-how").owlCarousel({autoPlay: 6000,itemsCustom : [[0, 1],[768, 3]],stopOnHover:true});});
//$j(document).ready(function() {$j("#carousel-quartier").owlCarousel({autoPlay: 6000,itemsCustom : [[0, 1],[768, 2]],stopOnHover:true});});
$j(document).ready(function() {$j("#carousel-price").owlCarousel({autoPlay: 6000,itemsCustom : [[0, 1],[768, 3]],stopOnHover:true});});
$j(document).ready(function() {$j("#carousel-comments").owlCarousel({autoPlay: 6000,itemsCustom : [[0, 1],[768, 3]],stopOnHover:true});});

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

/* =================================
===  SCROLL IN-LINE               ====
=================================== */

var amountScrolled = 300;


$j(window).scroll(function() {
    if ( $j(window).scrollTop() > amountScrolled ) {
        $j('a.back-to-top').fadeIn('slow');
    } else {
        $j('a.back-to-top').fadeOut('slow');
    }
});

$j('a.back-to-top').click(function() {
    $j('body, html').animate({
        scrollTop: 0
    }, 700);
    return false;
});

$j("#scroll-button").click(function() {
    $j('html,body').animate({
        scrollTop: $j("#concept").offset().top},
        'slow');
});
