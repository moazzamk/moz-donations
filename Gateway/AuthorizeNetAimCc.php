<?php

/**
 * Authorize.Net AIM credit card gateway
 */

namespace MozDonations\Gateway;

/**
 * Class AuthorizeNetAimCc
 *
 * @package MozDonations\Gateway
 */
class AuthorizeNetAimCc extends AuthorizeNet
{
	protected static $requiredFields = array(
		'md-first-name' => 'First Name',
		'md-last-name' => 'Last Name',
		'md-card-num' => 'Card number',
		'md-address' => 'Address',
		'md-city' => 'City',
		'md-state' => 'State',
		'md-exp-month' => 'Credit card expiration month',
		'md-exp-year' => 'Credit card expiration year',
		'md-reason' => 'Department',
		'md-email' => 'Email address',
		'md-zip' => 'ZIP code'
	);

	/**
	 * Charge a credit card through authorize.net
	 *
	 * @param array  $data  Data array. Keys can be:
	 *                      - md-email
	 *                      - md-first-name
	 *                      - md-last-name
	 *                      - md-address
	 *                      - md-city
	 *                      - md-state
	 *                      - md-credit-num
	 *                      - md-exp-year
	 *                      - md-exp-month
	 *                      - md-amount
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function charge(array $data)
	{
		parent::validateFields($data);
		if (empty($data['md-amount']) || !is_numeric($data['md-amount']) || $data['md-amount'] <= 0) {
			throw new \Exception('Invalid amount specified');
		}

		$reason = 'Donation for ' . $data['md-reason'];
		$amount = doubleval($data['md-amount']);
		$email = str_replace('@', '#', $data['email']);
	
		if ($data['md-payment-type'] == 'once') {
			return $this->chargeOnce($data, $amount, $reason, $email);
		}
		return $this->chargeRecurring($data, $amount, $reason, $email);
	}

	/**
	 * Charge a one time payment
	 *
	 * @param array   $data    Data array
	 * @param int     $amount  Amount to charge
	 * @param string  $reason  Order description (reason for donation)
	 * @param string  $email   Email address
	 *
	 * @return array
	 */
	protected function chargeOnce($data, $amount, $reason, $email)
	{
		$sale = $this->getAuthorizeNetTransport();
		$sale->setFields(array(
			'amount' => $amount,
			'description' => $reason,
			'card_num' => $data['md-card-num'],
			'exp_date' => $data['md-exp-month'] . '-'  . $data['md-exp-year'],
			'first_name' => $data['md-first-name'],
			'last_name' => $data['md-last-name'],
			'address' => $data['md-address'],
			'city' => $data['md-city'],
			'state' => $data['md-state'],
			'zip' => $data['md-zip'],
			'email' => $email,
			'country' => 'US'
		));

		return $this->authorizeAndCapture($sale, $data);
	}

	/**
	 * Charge a recurring payment
	 *
	 * @param array   $data    Data array
	 * @param int     $amount  Amount to charge
	 * @param string  $reason  Order description (reason for donation)
	 * @param string  $email   Email address
	 *
	 * @return array
	 */
	protected function chargeRecurring($data, $amount, $reason, $email)
	{
		$subscription = $this->getSubscriptionObject($data, $amount, $reason, $email);

		$subscription->creditCardExpirationDate = $data['md-exp-month'] . '-'  . $data['md-exp-year'];
		$subscription->creditCardCardNumber = $data['md-card-num'];
		$subscription->billToFirstName = $data['md-first-name'];
		$subscription->billToLastName = $data['md-last-name'];
		$subscription->billToAddress = $data['md-address'];
		$subscription->billToCity = $data['md-city'];
		$subscription->billToState = $data['md-state'];
		$subscription->billToZip = $data['md-zip'];
		$subscription->billToCountry = 'US';

		return $this->createSubscription($subscription, $data);
	}

	public function getName()
	{
		return 'credit-card';
	}

	public function getLabel()
	{
		return 'Credit card';
	}

	public function getFormHtml()
	{
		return <<<HTML
			<div id="moz-donations-credit-card" class="moz-donations-credit-card clear-both overflow-auto">
				<div class="width-50 float-left">
					<div class="margin-bottom-small">
						<div>First name:</div>
						<input type="text" name="md-first-name" />
					</div>
					<div class="margin-bottom-small">
						<div>Last name:</div>
						<input type="text" name="md-last-name" />
					</div>
					<div class="margin-bottom-small">
						<div>Address:</div>
						<input type="text" name="md-address" />
					</div>
					<div class="margin-bottom-small">
						<div>City:</div>
						<input type="text" name="md-city" />
					</div>
					<div class="margin-bottom-small">
						<div>State:</div>
						<select name="md-state">
							<option value=""></option>
							<option value="AA">AA</option>
							<option value="AE">AE</option>
							<option value="AK">AK</option>
							<option value="AL">AL</option>
							<option value="AP">AP</option>
							<option value="AR">AR</option>
							<option value="AS">AS</option>
							<option value="AZ">AZ</option>
							<option value="CA">CA</option>
							<option value="CO">CO</option>
							<option value="CT">CT</option>
							<option value="DC">DC</option>
							<option value="DE">DE</option>
							<option value="FL">FL</option>
							<option value="FM">FM</option>
							<option value="GA">GA</option>
							<option value="GU">GU</option>
							<option value="HI">HI</option>
							<option value="IA">IA</option>
							<option value="ID">ID</option>
							<option value="IL">IL</option>
							<option value="IN">IN</option>
							<option value="KS">KS</option>
							<option value="KY">KY</option>
							<option value="LA">LA</option>
							<option value="MA">MA</option>
							<option value="MD">MD</option>
							<option value="ME">ME</option>
							<option value="MH">MH</option>
							<option value="MI">MI</option>
							<option value="MN">MN</option>
							<option value="MO">MO</option>
							<option value="MP">MP</option>
							<option value="MS">MS</option>
							<option value="MT">MT</option>
							<option value="NC">NC</option>
							<option value="ND">ND</option>
							<option value="NE">NE</option>
							<option value="NH">NH</option>
							<option value="NJ">NJ</option>
							<option value="NM">NM</option>
							<option value="NV">NV</option>
							<option value="NY">NY</option>
							<option value="OH">OH</option>
							<option value="OK">OK</option>
							<option value="OR">OR</option>
							<option value="PA">PA</option>
							<option value="PR">PR</option>
							<option value="PW">PW</option>
							<option value="RI">RI</option>
							<option value="SC">SC</option>
							<option value="SD">SD</option>
							<option value="TN">TN</option>
							<option value="TX">TX</option>
							<option value="UT">UT</option>
							<option value="VA">VA</option>
							<option value="VI">VI</option>
							<option value="VT">VT</option>
							<option value="WA">WA</option>
							<option value="WI">WI</option>
							<option value="WV">WV</option>
							<option value="WY">WY</option>
						</select>
					</div>
					<div class="margin-bottom-small"> 
						<div>ZIP code:</div>
						<input type="text" name="md-zip" />
					</div>
				</div>
				<div class="width-50 float-left">
					<div class="margin-bottom-small">
						<div>Credit card number</div>
						<input type="text" name="md-card-num" />
					</div>
					<div class="margin-bottom-small">
						<div>Credit card expiration:</div>
						<span  class="float-left">
							<input type="text" name="md-exp-month" size="3" placeholder="MM" style="width: 40px" />
						</span>
						<span  class="float-left"> / </span>
						<span class="float-left">
							<input type="text" name="md-exp-year" size="3" placeholder="YY" style="width: 40px; float: left"/>
						</span>
					</div>
				</div>
			</div>
HTML;
	}
}
