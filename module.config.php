<?php

return array(
	'MozDonations' => function ($sm)
	{
		return new MozDonations();
	},
	'PaymentGatewayCollection' => function ($sm)
	{
		static $obj;

		if (!$obj) {
			$obj = new \MozDonations\Core\PaymentGatewayCollection();
		}

		return $obj;
	},
	'AuthorizeNetCredentials' => array(
		'loginId' => '2j7Md4H8',
		'transactionKey' => '5AU7Ke3u8sD9X7hw'
	),
);