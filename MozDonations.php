<?php

/**
 * MozDonations class
 * 
 * @package MozDonations
 */

use MozDonations\Core\ServiceManager;

/**
 * Moz donations
 */
class MozDonations
{
	/**
	 * Service manager
	 *
	 * @var ServiceManager
	 */
	protected $serviceManager;

	/**
	 * Constructor
	 */
	public function __construct()
	{

	}

	/**
	 * Process donation form submission
	 *
	 * @param array  $data  Data array
	 */
	public function processDonationsForm(array $data)
	{
		$ret = array(
			'success' => 1
		);

		$sm = $this->getServiceManager();
		$collection = $sm->get('PaymentGatewayCollection');
		$gateway = $collection->get($data['md-gateway']);

		try {
			$rs = $gateway->charge($data);
			$ret = array_merge($ret, $rs);
		} catch (\Exception $e) {
			$ret = array(
				'error' => $e->getMessage()
			);
		}

		wp_send_json($ret);
	}


	/**
	 * Render the donation form
	 * 
	 * @return string
	 */
	public function renderDonationsForm()
	{
		/*
		if (empty($_SERVER['HTTPS'])) {
			$url =  get_site_url(null, null, 'https') 
				. $_SERVER['REQUEST_URI'];


			header('Location: ' . $url . PHP_EOL, true, '302');
			print '<meta http-equiv="refresh" content="0;url=' 
				. $url . '"/>';

		}
		*/

		$tabLabels = array();
		$tabContents = array();
		$collection = $this->serviceManager->get('PaymentGatewayCollection');
		foreach ($collection as $name => $gateway) {
			$tabId = 'md-tab-' . $name;
			$tabLabels[] = '<li><a href="#' . $tabId . '">' . $gateway->getLabel() . '</a></li>';
			$tabContents[] = "<div id=\"$tabId\">{$gateway->getFormHtml()}</div>";
		}

		$url = plugins_url('moz-donations/views/');
		$adminUrl = admin_url('admin-ajax.php');
		$tabLabels = implode(PHP_EOL, $tabLabels);
		$tabContents = implode(PHP_EOL, $tabContents);

		return require __DIR__ . '/views/html/main.phtml';
	}

	/**
	 * Set service manager
	 *
	 * @param \MozDonations\Core\ServiceManager  $sm  ServiceManager
	 *
	 * @return $this
	 */
	public function setServiceManager(ServiceManager  $sm)
	{
		$this->serviceManager = $sm;
		return $this;
	}

	/**
	 * Get serviceManager
	 *
	 * @return \MozDonations\Core\ServiceManager
	 */
	public function getServiceManager()
	{
		return $this->serviceManager;
	}

}

