<?php

require_once __DIR__ . '/../../IPaymentGateway.php';
require_once __DIR__ . '/../../Gateway/AuthorizeNet.php';
require_once __DIR__ . '/../../Gateway/AuthorizeNetAimECheck.php';

class AuthorizeNetAimECheckTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var AuthorizeNetAimECheck
	 */
	protected $gateway;

	public function setUp()
	{
		$this->gateway = new \MozDonations\Gateway\AuthorizeNetAimECheck();
	}

	public function tearDown()
	{
		$this->gateway = null;
	}

	public function testCharge()
	{
		$data = array(
			'name' =>  'test user',
			'' => 'something',
			'city' => 'qwe',
			'state' => 'NY',
			'card_num' => '4007000000027',
			'exp_date' => '01/15',
			'reason' => 'Zakah',
			'amount' => 0.01,
			'email' => 'moazzamk@gmail.com'
		);

		$rs = $this->gateway->charge($data);

		if ($rs->response_code == 1) {
			return array('success' => 1);
		}

		return array('error' => $rs->response_reason_text);
	}
}
