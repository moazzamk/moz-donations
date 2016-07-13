<?php

namespace MozDonations\Gateway;

use MozDonations\Core\IPaymentGateway;

require_once __DIR__ . '/../lib/AuthorizeNet/AuthorizeNet.php';
require_once __DIR__ . '/../lib/AuthorizeNet/lib/AuthorizeNetARB.php';

abstract class AuthorizeNet implements IPaymentGateway
{
	private static $gatewayTransport;
	private static $gatewaySubscriptionTransport;
	private static $config = array(
		'loginId' => '2j7Md4H8',
		'transactionKey' => '5AU7Ke3u8sD9X7hw'
	);
/*
	private static $config = [
		'loginId' => '8F2pvQY8D',
		'transactionKey' => '2447rzDX2j9dgGJ9'
	]
*/


	/**
	 * Get a subscription client for authorizeNet
	 * @return \AuthorizeNetARB
	 */
	public function getAuthorizeNetSubscriptionTransport()
	{
		if (!self::$gatewaySubscriptionTransport) {
			self::$gatewaySubscriptionTransport = new \AuthorizeNetARB(
				self::$config['loginId'],
				self::$config['transactionKey']
			);
			self::$gatewaySubscriptionTransport->setSandbox(false);
		}

		return self::$gatewaySubscriptionTransport;
	}

	/**
	 * @return AuthorizeNetAIM
	 */
	public function getAuthorizeNetTransport()
	{
		if (!self::$gatewayTransport) {
			self::$gatewayTransport = new \AuthorizeNetAIM(
				self::$config['loginId'],
				self::$config['transactionKey']
			);
			self::$gatewayTransport->setSandbox(false);
		}

		return self::$gatewayTransport;
	}

	/**
	 * Get a subscription object with common properties populated
	 *
	 * @param array   $data    Data array
	 * @param double  $amount  Amount to be billed
	 * @param string  $reason  Reason/Description of the order
	 * @param string  $email   Customer's email address
	 *
	 * @return \AuthorizeNet_Subscription
	 */
	protected function getSubscriptionObject($data, $amount, $reason, $email)
	{
		$totalOccurrences = 9999;
		$subscription = new \AuthorizeNet_Subscription();

		if (!empty($data['md-recurring-duration-length'])) {
			$totalOccurrences = $data['md-recurring-duration-length'];
		}

		$subscription->orderDescription = $reason;
		$subscription->totalOccurrences = $totalOccurrences;
		$subscription->customerEmail = $email;
		$subscription->amount = $amount;
		$subscription->startDate = date('Y-m-d');

		if ($data['md-payment-type'] == 'Y') {
			$subscription->intervalUnit = 'days';
			$subscription->intervalLength = '365';
		} else {
			$subscription->intervalUnit = 'months';
			$subscription->intervalLength = 1;
		}

		return $subscription;
	}

	/**
	 * Create a subscription (make request to auth net)
	 *
	 * @param \AuthorizeNet_Subscription  $subscription  Subscription data
	 * @param array                       $data          Data array
	 *
	 * @return array
	 */
	protected function createSubscription($subscription, $data)
	{
		$sale = $this->getAuthorizeNetSubscriptionTransport();
		$rs = $sale->createSubscription($subscription);
		if ($rs->isOk()) {
			do_action('md-payment-success', $data);
			return array('success' => 1);
		}

		return array(
			'success' => 0,
			'error' => $rs->getErrorMessage()
		);

	}

	/**
	 * Authorize and capture a one time payment
	 *
	 * @param \AuthorizeNetAIM  $sale  Request object
	 * @param array             $data  Data array (to be used on success)
	 *
	 * @return array
	 */
	protected function authorizeAndCapture(\AuthorizeNetAIM $sale, array $data)
	{
		$rs = $sale->authorizeAndCapture();
		if ($rs->response_code == \AuthorizeNetResponse::APPROVED) {
			do_action('md-payment-success', $data);
			return array('success' => 1);
		}

		return array(
			'success' => 0,
			'error' => $rs->response_reason_text
		);
	}


	/**
	 * Validate user data
	 *
	 * Make sure required fields are not empty and data submitted is in the right format
	 *
	 * @param array  $data  User submitted data
	 *
	 * @throws \Exception
	 */
	protected static function validateFields($data)
	{
		// Make sure all required fields are present
		foreach (static::$requiredFields as $field=>$label) {
			if (empty($data[$field])) {
				throw new \Exception($label . ' cannot be empty');
			}
		}

		// validate email address
		if (!filter_var($data['md-email'], FILTER_VALIDATE_EMAIL)) {
			throw new \Exception('Email address is invalid');
		}

		// Validate amount
		if (!is_numeric($data['md-amount']) || $data['md-amount'] <= 0) {
			throw new \Exception('Invalid amount specified ');
		}
	}
}
