$(document).ready(function() {
	function set_diff_color(elem, diff) {
		$(this).removeClass('success warning error');
		if (!diff)
			elem.addClass('success');
		else if (diff > 0)
			elem.addClass('warning');
		else
			elem.addClass('error');
	}

	var r_ticket = /([0-9]*)-diff/;
	$('.merchant').each(function(){
		var merchant_id = $(this).attr('id');
		var somme_ticket = 0;
		var somme_diff = 0;

		$('#' + merchant_id + ' .diff').each(function(){
			var item_id = r_ticket.exec($(this).attr('id'))[1];
			somme_ticket += parseFloat($('#' + item_id + '-ticket').attr('value'));

			var this_diff = parseFloat($(this).html());
			somme_diff += this_diff;

			set_diff_color($(this), this_diff);
		});
		$('#' + merchant_id + '-ticket-total').html(somme_ticket.toFixed(2));
		$('#' + merchant_id + '-diff').html(somme_diff.toFixed(2));
		set_diff_color($('#' + merchant_id + '-diff'), somme_diff);
	});
	$('.class').on('change', function(){
		;
	});
});

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
