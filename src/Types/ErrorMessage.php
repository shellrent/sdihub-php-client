<?php

namespace SHL\SdiClient\Types;

class ErrorMessage extends GenericType {
	/**
	 * Il codice dell'errore
	 * @var string
	 */
	public $code;
	
	/**
	 * Il messaggio d'errore
	 * @var string
	 */
	public $message;
	
	/**
	 * L'eventuale oggetto di errore
	 * @var array
	 */
	public $data;
}
