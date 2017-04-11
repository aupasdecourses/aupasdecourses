function set_diff_color(elem, diff) {
	elem.removeClass('success warning error');
	if (diff!=0)
		elem.addClass('bold');
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

function update_diff_commercant(merchant_id, somme_ticket_commercant, somme_diff_commercant) {
	$('#' + merchant_id + ' .diff-commercant').each(function() {
		var r_diff = /([0-9]*)-diff-commercant/;

		var item_id = r_diff.exec($(this).attr('id'))[1];

		somme_ticket_commercant += parseFloat($('#' + item_id + '-ticket-input-commercant').val());

		var this_diff = parseFloat($(this).html());
		somme_diff_commercant += this_diff;

		set_diff_color($(this), this_diff);

		$('#' + merchant_id + '-ticket-total-commercant').html(somme_ticket_commercant.toFixed(2));

		$('#' + merchant_id + '-diff-total-commercant').html(somme_diff_commercant.toFixed(2));
		set_diff_color($('#' + merchant_id + '-diff-total-commercant'), somme_diff_commercant);
	});
}


$(document).ready(function() {
	$('.merchant').each(function() {
		var merchant_id = $(this).attr('id');

		update_diff(merchant_id, 0, 0);
		update_diff_commercant(merchant_id, 0, 0);

		/* pour chaques tickets (chaques zones) */
		$('#' + merchant_id + ' .ticket-input').each(function() {
			$(this).on('change', function () {
				var r_ticket = /([0-9]*)-ticket-input/;

				var merchant_id = $(this).closest("table").attr('id'); 
				var product_id = r_ticket.exec($(this).attr('id'))[1];

				/* pour chaque ligne*/
				/* ici commence */
				$('#' + product_id + '-ticket-input').each(function() {

					var ticket_input = $('#' + product_id + '-ticket-input').val();
					$('#' + product_id + '-ticket-input-commercant').val(ticket_input);
				});
				/* ici s'arrete la maj input commercant quand on ecrit dans input client */

				var input_value = parseFloat($(this).val());
				var total_command = parseFloat($('#'+product_id+'-total').html());

				var diff_total = (total_command - input_value).toFixed(2);
				diff_total = (total_command - input_value).toFixed(2);

				$('#'+product_id+'-diff').html(diff_total);
				update_diff(merchant_id, 0, 0);
			});
		});

		/* Maj de la colonne total commercant automatiquement via les =/= inputs */
		$('#' + merchant_id + ' .ticket-input-commercant').each(function() {
			$(this).on('change', function () {
				var r_ticket_commercant = /([0-9]*)-ticket-input-commercant/;
				
				var merchant_id = $(this).closest("table").attr('id'); 
				var product_id_commercant = r_ticket_commercant.exec($(this).attr('id'))[1];

				var input_value = parseFloat($(this).val());
				var total_command = parseFloat($('#'+product_id_commercant+'-total').html());

				var diff_total_commercant = (total_command - input_value).toFixed(2);
				diff_total_commercant = (total_command - input_value).toFixed(2);

				$('#'+product_id_commercant+'-diff-commercant').html(diff_total_commercant);
				update_diff_commercant(merchant_id, 0, 0);
			});
		});
	});
});

