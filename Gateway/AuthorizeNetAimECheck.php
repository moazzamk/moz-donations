<?php

namespace MozDonations\Gateway;

/**
 * Class AuthorizeNetAimECheck
 *
 * @package MozDonations\Gateway
 */
class AuthorizeNetAimECheck extends AuthorizeNet
{
	protected static $requiredFields = array(
		'md-routing-number' => 'Routing number',
		'md-account-number' => 'Account number',
		'md-account-type' => 'Account type',
		'md-bank-name' => 'Bank name',
		'md-user-name' => 'Your name'
	);

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'echeck';
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return 'E-Check';
	}

	/**
	 * @return string
	 */
	public function getFormHtml()
	{
		// ($bank_aba_code, $bank_acct_num, $bank_acct_type, $bank_name, $bank_acct_name, $echeck_type = 'WEB')
		return <<<HTML
			<div class="moz-donations-echeck-container">
				<input type="hidden" name="md-transaction-type" value="echeck"/>
				<div class="margin-bottom-small">
					<div>Your name:</div>
					<input type="text" name="md-user-name" />
				</div>
				<div class="margin-bottom-small">
					<div>Bank's name</div>
					<input type="text" name="md-bank-name" />
				</div>
				<div class="margin-bottom-small">
					<div>Bank's routing number (ABA code):</div>
					<input type="text" name="md-routing-number" />
				</div>
				<div class="margin-bottom-small">
					<div>Account number:</div>
					<input type="text" name="md-account-number" />
				</div>
				<div class="margin-bottom-small">
					<div>Account type:</div>
					<select name="md-account-type">
						<option></option>
						<option value="checking">Checking</option>
						<option value="savings">Saving</option>
					</select>
				</div>
			</div>
HTML;

	}

	/**
	 * Charge the bank account
	 *
	 * @param array  $data  Data array
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function charge(array $data)
	{
		parent::validateFields($data);

		$reason = '';
		if (!empty($data['md-reason'])) {
			$reason = 'Donation for ' . $data['md-reason'];
		}

		$amount = doubleval($data['md-amount']);
		$email = str_replace('@', '#', $data['md-email']);

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
		$sale->amount = $amount;
		$sale->description = $reason;

		$sale->setECheck(
			$data['md-routing-number'],
			$data['md-account-number'],
			$data['md-account-type'],
			$data['md-bank-name'],
			$data['md-user-name'],
			'WEB'
		);

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
	 * @throws \Exception
	 * @return array
	 */
	protected function chargeRecurring($data, $amount, $reason, $email)
	{
		$name = explode(' ', $data['md-user-name']);
		if (count($name) < 2) {
			throw new \Exception('Please enter your first and last name');
		}

		$subscription = $this->getSubscriptionObject($data, $amount, $reason, $email);
		$subscription->bankAccountRoutingNumber = $data['md-routing-number'];
		$subscription->bankAccountAccountNumber = $data['md-account-number'];
		$subscription->bankAccountAccountType = $data['md-account-type'];
		$subscription->bankAccountNameOnAccount = $data['md-user-name'];
		$subscription->bankAccountBankName = $data['md-bank-name'];
		$subscription->bankAccountEcheckType = 'WEB';
		$subscription->customerEmail = $data['md-email'];
		$subscription->billToFirstName = $name[0];
		$subscription->billToLastName = $name[1];

		return $this->createSubscription($subscription, $data);
	}
}
