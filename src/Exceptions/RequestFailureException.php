<?php

namespace SHL\SdiClient\Exceptions;

use SHL\SdiClient\Types\ErrorMessage;

class RequestFailureException extends Exception {
	
	/**
	 * Il messaggio di errore json
	 * @var ErrorMessage
	 */
	private $Response = null;
	
	/**
	 * Ritorna il messaggio di errore
	 * @return ErrorMessage
	 */
	public function getResponse() {
		return $this->Response;
	}
	
	
	/**
	 * Imposta il messaggio di errore
	 * @param ErrorMessage $Response
	 * @return RequestFailureException
	 */
	public function setResponse($Response) {
		$this->Response = $Response;
		return $this;
	}
}
