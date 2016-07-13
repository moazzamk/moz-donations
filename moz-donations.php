<?php
/*
Plugin Name: Moz Donations
Plugin URI:  http://something.com/
Description: A donation module
Author: Moazzam Khan
Author URI: http://moazzam-khan.com
License: GPL?
*/


require_once __DIR__ . '/MozDonations.php';
//require_once __DIR__ . '/MdOptions.php';
/**
 * Moz donations plugin
 *
 * @author Moazzam Khan <moazzam@moazzam-khan.com>
 */

/**
 * Initialize plugin
 */
function md_init()
{
	md_register_short_codes();
}

/**
 * Process donation form submissions
 *
 * @return string
 */
function md_process_donate_form()
{
	require_once __DIR__ . '/bootstrap.php';

	$serviceManager = MozDonations\Core\ServiceManager::getInstance();
	$mozDonations = $serviceManager->get('MozDonations');
	$mozDonations->setServiceManager($serviceManager);

	$ret = $mozDonations->processDonationsForm($_REQUEST);
	add_action('md-payment-success', 'md_donation_success', 10, 1);

	return $ret;
}

/**
 * Payment success hook
 *
 * Runs when a payment has been made successfully using credit card or e-check
 *
 * @param array  $data  Data array
 */
function md_donation_success($data)
{
	$gatewayLabels = array(
		'credit-card' => 'a credit card',
		'echeck' => 'an e-Check'
	);


	$subject = 'Jazakallahu khair for your donation';
	$body = <<<TEXT
Hi,

Your donation of \${$data['md-amount']} for "{$data['md-reason']}" using {$gatewayLabels[$data['md-gateway']]} has been received. 

Best Regards,
Organization staff
TEXT;

	mail(
		$data['md-email'],
		$subject,
		$body,
		'From: Some organization <noreply@someorganization.org>'
	);

}

/**
 * Print the donation form
 *
 * @return mixed
 */
function md_donate_form()
{
	require_once __DIR__ . '/bootstrap.php';

	$serviceManager = \MozDonations\core\ServiceManager::getInstance();
	$mozDonations = $serviceManager->get('MozDonations');
	$mozDonations->setServiceManager($serviceManager);

	return $mozDonations->renderDonationsForm();
}

/**
 * Register shortcode handlers with wordpress
 */
function md_register_short_codes()
{
	add_shortcode('moz-donations-form', 'md_donate_form');
}

function md_enqueue_scripts()
{
	return;
	wp_enqueue_script(
		'custom-script',
		plugins_url('moz-donations/jquery.placeholder.js'),
		array( 'jquery' )
	);
}
/************************************************
* Bootstrap code
************************************************/

add_action('init', 'md_init');
add_action( 'wp_enqueue_scripts', 'md_enqueue_scripts' );
add_action('wp_ajax_moz-donations-form-submit', 'md_process_donate_form');
add_action('wp_ajax_nopriv_moz-donations-form-submit', 'md_process_donate_form');



