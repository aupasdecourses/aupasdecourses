function set_diff_color(elem, diff) {
	$(this).removeClass('success warning error');
	if (!diff)
		elem.addClass('success');
	else if (diff > 0)
		elem.addClass('warning');
	else
		elem.addClass('error');
}

$(document).ready(function() {
	var r_diff = /([0-9]*)-diff/;
	var r_ticket = /([0-9]*)-ticket/;

	$('.merchant').each(function() {
		var merchant_id = $(this).attr('id');
		var somme_ticket = 0;
		var somme_diff = 0;

		function update_diff(merchant_id) {
			$('#' + merchant_id + ' .diff').each(function() {
				var item_id = r_diff.exec($(this).attr('id'))[1];
				somme_ticket += parseFloat($('#' + item_id + '-ticket').attr('value'));

				var this_diff = parseFloat($(this).html());
				somme_diff += this_diff;

				set_diff_color($(this), this_diff);
			});
		}

		function update_diff() {

		}

		$('#' + merchant_id + ' .ticket-input').each(function() {
			$(this).change(function () {
				var item_id = r_ticket.exec($(this).attr('id'))[1];
				var total_0 = parseFloat($('#' + merchant_id + '-total').html()).toFixed(2);
				var total_1 = parseFloat($(this).attr('value')).toFixed(2);

				somme_ticket = 0;
				somme_diff = 0;

				update_diff(merchant_id);

				$('#' + merchant_id + '-ticket-total').html(somme_ticket.toFixed(2));
				$('#' + merchant_id + '-diff-total').html((total_0 - total_1).toFixed(2));

				set_diff_color($('#' + merchant_id + '-diff'), somme_diff);
			});
		});

		update_diff(merchant_id);

		$('#' + merchant_id + '-ticket-total').html(somme_ticket.toFixed(2));
		$('#' + merchant_id + '-diff').html(somme_diff.toFixed(2));
		set_diff_color($('#' + merchant_id + '-diff'), somme_diff);
	});
});

