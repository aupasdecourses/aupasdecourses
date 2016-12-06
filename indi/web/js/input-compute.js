function set_diff_color(elem, diff) {
	elem.removeClass('success warning error');
	if (!diff)
		elem.addClass('success');
	else if (diff > 0)
		elem.addClass('warning');
	else
		elem.addClass('error');
}

function update_diff(merchant_id, somme_ticket, somme_diff) {
	$('#' + merchant_id + ' .diff').each(function() {
		var r_diff = /([0-9]*)-diff/;

		var item_id = r_diff.exec($(this).attr('id'))[1];

		somme_ticket += parseFloat($('#' + item_id + '-ticket-input').val());

		var this_diff = parseFloat($(this).html());
		somme_diff += this_diff;

		set_diff_color($(this), this_diff);

		$('#' + merchant_id + '-ticket-total').html(somme_ticket.toFixed(2));
		$('#' + merchant_id + '-diff-total').html(somme_diff.toFixed(2));
		set_diff_color($('#' + merchant_id + '-diff-total'), somme_diff);
	});
}

$(document).ready(function() {
	$('.merchant').each(function() {
		var merchant_id = $(this).attr('id');

		update_diff(merchant_id, 0, 0);

		$('#' + merchant_id + ' .ticket-input').each(function() {
			$(this).on('change', function () {
				var r_ticket = /([0-9]*)-ticket-input/;

				var merchant_id = $(this).closest("table").attr('id'); 
				var product_id = r_ticket.exec($(this).attr('id'))[1];

				var input_value = parseFloat($(this).val());
				var total_command = parseFloat($('#'+product_id+'-total').html());

				var diff_total = (total_command - input_value).toFixed(2);
				diff_total = (total_command - input_value).toFixed(2);

				$('#'+product_id+'-diff').html(diff_total);
				update_diff(merchant_id, 0, 0);
			});
		});
	});
});

