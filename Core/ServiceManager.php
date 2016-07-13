<?php

namespace MozDonations\Core;

class ServiceManager
{
	protected static $obj;
	protected $config = array();

	protected function __construct()
	{

	}

	public static function getInstance()
	{
		if (!self::$obj) {
			self::$obj = new ServiceManager();
		}

		return self::$obj;
	}

	public function get($name)
	{
		if (isset($this->config[$name])) {
			return $this->config[$name]($this);
		}
		throw new Exception('Service ' . $name . ' not found');
	}

	public function addConfig(array $config)
	{
		$this->config = array_merge($this->config, $config);
		return $this;
	}
}

