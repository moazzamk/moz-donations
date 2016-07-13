<?php

require_once __DIR__ . '/Core/ServiceManager.php';
require_once __DIR__ . '/Core/PaymentGatewayCollection.php';

use MozDonations\Core\PaymentGatewayCollection,
	MozDonations\Core\ServiceManager as ServiceManager;

$serviceManager = ServiceManager::getInstance();
$serviceManager->addConfig(
	require __DIR__ . '/module.config.php'
);

require_once __DIR__ . '/Core/IPaymentGateway.php';
require_once __DIR__ . '/Gateway/PaypalStandard.php';
require_once __DIR__ . '/Gateway/AuthorizeNet.php';
require_once __DIR__ . '/Gateway/AuthorizeNetAimCc.php';
require_once __DIR__ . '/Gateway/AuthorizeNetAimECheck.php';

$gatewayCollection = $serviceManager->get('PaymentGatewayCollection');
$gatewayCollection->add(new \MozDonations\Gateway\PaypalStandard())
				->add(new \MozDonations\Gateway\AuthorizeNetAimECheck())
				->add(new \MozDonations\Gateway\AuthorizeNetAimCc());


//add_action('md-paypal-success', 'md_paypal_success', 10, 0);


