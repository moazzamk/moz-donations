<?php


namespace MozDonations\Core;
/**
 * Interface IPaymentGateway
 */
interface IPaymentGateway
{

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getLabel();

	/**
	 * @return string
	 */
	public function getFormHtml();

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function charge(array $data);
}