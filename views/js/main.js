(function ($) {

	$(document).ready(function () {
		$('[placeholder]').placeholder();

		$('#moz-donations-payment-gateways').tabs({
			activate: function (e, ui) {
				// todo: Needs to be verified that it works
				$('#md-gateway').val(
					ui.newPanel.attr('id').replace(/md-tab-/, '')
				);
			}
		});

		$(document).on('change', '#md-payment-type', function (e) {
			var label = 'years',
				selectedValue = $('#md-payment-type').find(':selected').val();

			if (selectedValue != 'once') {
				if (selectedValue == 'M') {
					label = 'months';
				}
				$('#md-recurring-duration-unit').html(label);
				$('#md-recurring-duration-container').removeClass('hidden');
			} else {
				$('#md-recurring-duration-container').addClass('hidden');
			}
		});
	});


}(jQuery));

jQuery('#moz-donations-form').bind('submit', function (e) {

	var jAmount = jQuery.trim(jQuery('#md-amount').val());

	e.preventDefault();
	jQuery('#moz-donations-errors')
		.html('')
		.addClass('hidden');

	if (jAmount == '') {
		jQuery('#moz-donations-errors')
			.removeClass('hidden')
			.html(
				'Please type the amount you want to donate'
			);
		return;
	} else if (isNaN(parseInt(jAmount))) {
		jQuery('#moz-donations-errors')
			.removeClass('hidden')
			.html('Invalid amount specified');

		return;
	}


	jQuery('#moz-donations-form').loading('show');

	jQuery.post(
		ajaxurl,
		jQuery('#moz-donations-form').serialize(),
		function (data) {
			if (data.error) {
				jQuery('#moz-donations-errors')
					.html(data.error)
					.removeClass('hidden');
			}
			else if (data.execute) {
				eval(data.execute);
			} else if (data.success) {
				jQuery('#moz-donations-form').addClass('hidden');
				jQuery('#moz-donations-thank-you').removeClass('hidden');
			}
			jQuery('#moz-donations-form').loading('hide');
		}
	);

	return false;
});