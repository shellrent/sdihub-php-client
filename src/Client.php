<?php

namespace SHL\SdiClient;

use SHL\SdiClient\Exceptions\RequestFailureException;
use SHL\SdiClient\Types\ErrorMessage;

class Client {
	/**
	 * L'endpoint delle api
	 * @var string
	 */
	private $Endpoint;
	
	/**
	 * Il token che autentica la richiesta
	 * @var string
	 */
	private $Token;
	
	/**
	 * Il numero di secondi massimo per l'esecuzione della curl
	 * @var int
	 */
	private $Timeout = 3600;
		
	/**
	 * Il numero di secondi massimo di attesa per il tentativo di connession
	 * @var int
	 */
	private $TryConnectionTimeout = 120;
	

	/**
	 * Costruisce il client con i parametri di connessione
	 * @param string $endpoint
	 * @param string $username
	 * @param string $apiToken
	 */
	public function __construct( $endpoint, $username, $apiToken ) {
		$this->Endpoint = $endpoint;
		$this->Token = $username . '.' . $apiToken;
	}
	
	
	/**
	 * Imposta il numero di secondi massimo per l'esecuzione della curl
	 * @param int $timeout
	 */
	public function setTimeout( int $timeout ) {
		$this->Timeout = $timeout;
	}

	
	/**
	 * Imposta il numero di secondi massimo di attesa per il tentativo di connession
	 * @param int $tryConnectionTimeout
	 */
	public function setTryConnectionTimeout( int $tryConnectionTimeout ) {
		$this->TryConnectionTimeout = $tryConnectionTimeout;
	}

	
	/**
	 * Effettua la richiesta tramite curl
	 * @param string $verb Il verbo http
	 * @param string $request La richiesta
	 * @param string $json I dati da inviare convertiti in json
	 * @return string
	 * @throws RequestFailureException
	 */
	private function curl( string $verb, string $request, string $json = null ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $verb );
		curl_setopt( $ch, CURLOPT_URL, $this->Endpoint . $request );
		
		curl_setopt( $ch, CURLOPT_POST, ! is_null( $json ) );
		if( ! is_null( $json ) ) {
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
		}
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: ' . $this->Token,
			'Content-Type: application/json'
		]);

		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $this->Timeout );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->TryConnectionTimeout );
		
		$result = curl_exec( $ch );
		$curlErrorMessage = curl_error( $ch );
		$curlErrorNumber = curl_errno( $ch );
		$httpStatusCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		
		curl_close($ch);
		
		if( $curlErrorMessage ) {
			throw new RequestFailureException( sprintf( 'Curl "%s%s" error: [%s] %s', $this->Endpoint, $request, $curlErrorNumber, $curlErrorMessage ) );
		}
		
		if ( $httpStatusCode != 200 ) {
			$exception = new RequestFailureException( sprintf( 'Http request "%s%s" error', $this->Endpoint, $request ) );
			$exception->setResponse( new ErrorMessage( $result ) );
			throw $exception;
		}
		
		return $result;
	}
	
	
	/**
	 * Ritorna la lista degli id dei documenti inviati
	 * @return array
	 */
	public function getDocumentSent(): array {
		return json_decode( $this->curl( 'GET', '/document_sent' ), true );
	}
}
