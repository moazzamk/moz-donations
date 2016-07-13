<?php

/**
 * Moz Donations settings
 *
 * @package MozDonations
 * @author Moazzam Khan <moazzam@moazzam-khan.com>
 */

/**
 * Class MdOptions
 */
class MdOptions
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		if (is_admin()) {
			add_action('admin_menu', array($this, 'addPluginPage'));
			add_action('admin_init', array($this, 'pageInit'));
		}
	}

	/**
	 * Add the plugin page
	 *
	 * This page appears under "Settings"
	 */
	public function addPluginPage()
	{
		add_options_page(
			'Moz Donations Admin',
			'Moz Donations',
			'manage_options',
			'moz-donations-options',
			array($this, 'createAdminPage')
		);
	}

	public function createIdField()
	{
		$mdCategories = get_option('md-categories');

		print <<<HTML
			<input type="text" id="md-categories" name="md-categories" value="$mdCategories" />
HTML;
	}

	public function printSectionInfo()
	{
		print 'Enter your setting below:';
	}

	public function createAdminPage()
	{
		$icon = screen_icon();
		print <<<HTML
			<div class="wrap">
				$icon
				<h2>Moz Donation Settings</h2>
				<form method="post" action="options.php">
HTML;
						settings_fields('moz-donations-options-group');
						do_settings_sections('moz-donations-options-section');
		submit_button();

		print <<<HTML
				</form>
			</div>
HTML;

	}

	/**
	 * Initialize options page
	 */
	public function pageInit()
	{
		register_setting(
			'moz-donations-options-group',
			'md_category'//,
			//array($this, 'checkID')
		);

		register_setting(
			'moz-donations-options-group',
			'md_reasons'//,
		//array($this, 'checkID')
		);



		add_settings_section(
			'moz-donations-options-section',
			'Setting',
			array($this, 'printSectionInfo'),
			'moz-donations-options'
		);


		add_settings_field(
			'md_reasons',
			'Reasons for donation',
			array($this, 'createIdField'),
			'moz-donations-options'//,
			//'moz-donations-options-section'
		);

		add_settings_field(
			'md_category',
			'Reasons for donation',
			array($this, 'createIdField'),
			'moz-donations-options',
			'moz-donations-options-section'
		);
	}

	/**
	 * Check for existing ID
	 *
	 * @param array  $input  Input array
	 *
	 * @return int|string
	 */
	public function checkID($input)
	{
		return '';

		if (is_numeric($input['md_categoy'])) {
			$mid = $input['md_categoy'];
			if (get_option('md_category') === false) {
				add_option('md_category', $mid);
			} else {
				update_option('md_category', $mid);
			}
		} else {
			$mid = '';
		}

		return $mid;
	}

}

$options = new MdOptions();