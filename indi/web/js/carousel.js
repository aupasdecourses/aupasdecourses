$('#ticket_carousel').carousel({
	interval: false,
	wrap: false
});

$('.left').click(function(){
	$('#ticket_carousel').carousel("prev");
});

$('.right').click(function(){
	$('#ticket_carousel').carousel("next");
});
