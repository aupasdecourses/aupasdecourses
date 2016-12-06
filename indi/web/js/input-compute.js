function set_diff_color(elem, diff) {
	$(this).removeClass('success warning error');
	if (!diff)
		elem.addClass('success');
	else if (diff > 0)
		elem.addClass('warning');
	else
		elem.addClass('error');
}

function init_diff(merchant_id) {
	$('#' + merchant_id + ' .diff').each(function() {
		var r_diff = /([0-9]*)-diff/;
		var somme_ticket = 0;
		var somme_diff = 0;

		var item_id = r_diff.exec($(this).attr('id'))[1];
		somme_ticket += parseFloat($('#' + item_id + '-ticket').attr('value'));

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
		init_diff($(this).attr('id'));

		$('#' + merchant_id + ' .ticket-input').each(function() {
			$(this).on('change', function () {
				// update {{product_id}}-ticket-total
				// update {{product_id}}-diff
			});
		});

		$('#' + merchant_id + ' .diff').each(function() {
			$(this).on('change', function () {
				// update {{merchant_id}}-diff-total
			});
		});
	});
});

//	$('.merchant').each(function() {
//		var merchant_id = $(this).attr('id');
//
//		function init_diff(merchant_id) {
//			$('#' + merchant_id + ' .diff').each(function() {
//				var r_diff = /([0-9]*)-diff/;
//				var somme_ticket = 0;
//				var somme_diff = 0;
//
//				var item_id = r_diff.exec($(this).attr('id'))[1];
//				somme_ticket += parseFloat($('#' + item_id + '-ticket').attr('value'));
//
//				var this_diff = parseFloat($(this).html());
//				somme_diff += this_diff;
//
//				set_diff_color($(this), this_diff);
//
//				$('#' + merchant_id + '-ticket-total').html(somme_ticket.toFixed(2));
//				$('#' + merchant_id + '-diff-total').html(somme_diff.toFixed(2));
//				set_diff_color($('#' + merchant_id + '-diff-total'), somme_diff);
//			});
//		}
//
//		function update_diff() {
//
//		}
//
//		$('#' + merchant_id + ' .ticket-input').each(function() {
//			$(this).on('change', function () {
//				var r_ticket = /([0-9]*)-ticket/;
//				var merchant_id = $(this).closest("table").attr('id');
//				var item_id = r_ticket.exec($(this).attr('id'))[1];
//				var total_0 = parseFloat($('#' + merchant_id + '-total').html()).toFixed(2);
//				var total_1 = parseFloat($(this).attr('value')).toFixed(2);
//
//				var somme_ticket = 0;
//				var somme_diff = 0;
//
//				init_diff(merchant_id);
//
//				$('#' + merchant_id + '-ticket-total').html(somme_ticket.toFixed(2));
//				$('#' + merchant_id + '-diff-total').html((total_0 - total_1).toFixed(2));
//				set_diff_color($('#' + merchant_id + '-diff'), somme_diff);
//			});
//		});
//
//		init_diff(merchant_id);
//	});

