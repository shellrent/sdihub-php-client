<?php

namespace SHL\SdiClient\Types;

class ErrorMessage extends GenericType {
	/**
	 * Il codice dell'errore
	 * @var string
	 */
	public $Code;
	
	/**
	 * Il messaggio d'errore
	 * @var string
	 */
	public $Message;
	
	/**
	 * L'eventuale oggetto di errore
	 * @var array
	 */
	public $Data;
}
