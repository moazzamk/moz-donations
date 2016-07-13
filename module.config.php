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
		'loginId' => '',
		'transactionKey' => ''
	),
);
