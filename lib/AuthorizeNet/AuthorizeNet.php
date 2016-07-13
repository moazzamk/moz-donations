<?php
/**
 * The AuthorizeNet PHP SDK. Include this file in your project.
 *
 * @package AuthorizeNet
 */


if (class_exists('AuthorizeNet')) {
	return;
}

require_once __DIR__ . '/lib/shared/AuthorizeNetRequest.php';
require_once __DIR__ . '/lib/shared/AuthorizeNetTypes.php';
require_once __DIR__ . '/lib/shared/AuthorizeNetXMLResponse.php';
require_once __DIR__ . '/lib/shared/AuthorizeNetResponse.php';
require_once __DIR__ . '/lib/AuthorizeNetAIM.php';
require_once __DIR__ . '/lib/AuthorizeNetARB.php';
require_once __DIR__ . '/lib/AuthorizeNetCIM.php';
require_once __DIR__ . '/lib/AuthorizeNetSIM.php';
require_once __DIR__ . '/lib/AuthorizeNetDPM.php';
require_once __DIR__ . '/lib/AuthorizeNetTD.php';
require_once __DIR__ . '/lib/AuthorizeNetCP.php';

if (class_exists("SoapClient")) {
    require_once __DIR__ . '/lib/AuthorizeNetSOAP.php';
}
/**
 * Exception class for AuthorizeNet PHP SDK.
 *
 * @package AuthorizeNet
 */
class AuthorizeNetException extends Exception
{
}