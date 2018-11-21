<?php

namespace SHL\SdiClient\Types;

class UserInfo extends GenericType {
	
	/**
	 * @var string
	 */
	public $fiscal_code;
	
	/**
	 * @var string
	 */
	public $vat_number;
	
	/**
	 * @var string
	 */
	public $country_code;
	
	/**
	 * @var string
	 */
	public $recipient_code;
	
	/**
	 * @var string
	 */
	public $document_format;
}
