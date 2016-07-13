<?php

namespace MozDonations\Gateway;

use MozDonations\Core\IPaymentGateway;

class PaypalStandard implements IPaymentGateway
{

	/**
	 * Charge the provided Paypal account
	 *
	 * @param array $data Payment data
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function charge(array $data)
	{
		if (empty($data['md-amount']) || !is_numeric($data['md-amount']) || $data['md-amount'] <= 0) {
			throw new \Exception('Invalid amount specified');
		}

		if (empty($data['md-payment-type']) || $data['md-payment-type'] == 'once') {
			return $this->chargeOnce($data);
		}

		return $this->chargeRecurring($data);
	}

	/**
	 * Setup recurring payment subscription
	 *
	 * @param array  $data  Payment data
	 *
	 * @return array
	 */
	protected function chargeRecurring(array $data)
	{
		$reason = '';
		if (!empty($data['md-reason'])) {
			$reason = 'Donation for ' . $data['md-reason'];
		}

		$totalOccurrences = '';
		$amount = doubleval($data['md-amount']);
		$email = 'info.darululoomny@gmail.com';


		if (!empty($data['md-recurring-duration-length'])) {
			$totalOccurrences = "+ '<input type=\"hidden\" name=\"recur_times\" value=\"{$data['md-recurring-duration-length']}\"/>'";
		}

		$html = <<<HTML
			var tmp = jQuery(document.createElement('div'));

			tmp.css('height', '0px')
				.css('width', '0px')
				.html(
					'<form id="md-paypal-redirect" action="https://www.paypal.com/cgi-bin/webscr" method="post">'
					+ '<input type="hidden" name="cmd" value="_xclick-subscriptions">'
					+ '<input type="hidden" name="item_name" value="Donation for {$data['md-reason']}">'
					+ '<input type="hidden" name="business" value="{$email}">'
					+ '<input type="hidden" name="currency_code" value="USD">'
					+ '<input type="hidden" name="lc" value="US">'

					+ '<input type="hidden" name="a3" value="{$data['md-amount']}">'
					+ '<input type="hidden" name="no_shipping" value="1">'
					+ '<input type="hidden" name="no_note" value="1">'
					+ '<input type="hidden" name="p3" value="1">'
					+ '<input type="hidden" name="t3" value="{$data['md-payment-type']}">'
					$totalOccurrences
					+ '</form>'
				);

			document.body.appendChild(tmp[0]);
			jQuery('#md-paypal-redirect').submit();
HTML;

		return array(
			'execute' => $html
		);
	}

	/**
	 * Make a one time charge
	 *
	 * @param array  $data  Payment data
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function chargeOnce(array $data)
	{
		$reason = '';
		if (!empty($data['md-reason'])) {
			$reason = 'Donation for ' . $data['md-reason'];
		}

		$amount = doubleval($data['md-amount']);
		$email = 'info.darululoomny@gmail.com';


		$html = <<<HTML
			var tmp = jQuery(document.createElement('div'));

			tmp.css('height', '0px')
				.css('width', '0px')
				.html(
					'<form id="md-paypal-redirect" action="https://www.paypal.com/cgi-bin/webscr" method="post">'
					+ '	<input type="hidden" name="cmd" value="_xclick">'
					+ '	<input type="hidden" name="business" value="{$email}">'
					+ '	<input type="hidden" name="item_name" value="$reason">'
					+ '	<input type="hidden" name="currency_code" value="USD">'
					+ '	<input type="hidden" name="amount" value="{$amount}">'
					+ '	<input type="hidden" name="return" value="/?action=md-paypal-success">'
					+ '</form>'
				);

				document.body.appendChild(tmp[0]);
				jQuery('#md-paypal-redirect').submit();
HTML;


		return array(
			'execute' => $html
		);


	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'paypal-standard';
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return 'PayPal';
	}

	public function getFormHtml()
	{
		return <<<HTML
			<div id="moz-donations-paypal">
				Please click on "Donate" to be taken to PayPal's website for payment
			</div>
HTML;
	}
}

function md_paypal_success()
{

}

