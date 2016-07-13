<?php

namespace MozDonations\Core;

class PaymentGatewayCollection implements \IteratorAggregate
{
	protected $data = array();

	public function add(IPaymentGateway $gateway)
	{
		$this->data[$gateway->getName()] = $gateway;
		return $this;
	}

	public function getAllGateways()
	{
		return $this->data;
	}

	/**
	 * Get a payment getway by it's name
	 *
	 * @param  $name  Payment gateway's name
	 *
	 * @return \MozDonations\Core\IPaymentGateway
	 */
	public function get($name)
	{
		return $this->data[$name];
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->data);
	}
}