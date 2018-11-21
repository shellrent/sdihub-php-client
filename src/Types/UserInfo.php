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
	public $vat_number = null;
	
	/**
	 * @var string
	 */
	public $country_code = null;
	
	/**
	 * @var string
	 */
	public $recipient_code = null;
	
	/**
	 * @var string
	 */
	public $document_format = null;
}
