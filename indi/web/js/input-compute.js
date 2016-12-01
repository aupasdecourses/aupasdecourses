$(document).ready(function() {
	var r_ticket = /([0-9]*)\[ticket\]/;
	var somme_ticket;
	var somme_diff;

	$('.merchant').each(function(){
		var id = $(this).attr('id');

		somme_ticket = 0;
		somme_diff = 0;
		$('#'.concat(id).concat(' .ticket')).each(function(){
			var item_id = r_ticket.exec($(this).attr('id'))[1];
			somme_ticket += $(name).html().parseFloat();
			somme_diff += $(this).attr('value');
		});
	})
//	var r_ticket = /([0-9]*)\[ticket\]/;
//
//	$('.ticket').each(function(){
//		var item_id = r_ticket.exec($(this).attr('id'))[1];
//
//		var name = '#'.concat(item_id.concat('-total'));
//
//		var total = $(name).html();
//		var ticket = $(this).attr('value');
//
//		var diff = parseFloat(total) - parseFloat(ticket);
//		$('#'.concat(item_id.concat('-diff'))).text(diff.toFixed(2));
//	});
});
