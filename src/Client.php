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
	 * @param \JsonSerializable $json I dati da inviare convertiti in json
	 * @return string
	 * @throws RequestFailureException
	 */
	private function curl( string $verb, string $request, \JsonSerializable $json = null ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $verb );
		curl_setopt( $ch, CURLOPT_URL, $this->Endpoint . $request );
		
		
		$dataString = json_encode( $json );
		curl_setopt( $ch, CURLOPT_POST, ! is_null( $json ) );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $dataString );
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: ' . $this->Token,
		]);

		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $this->Timeout );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->TryConnectionTimeout );
		
		$result = curl_exec( $ch );
		$curlErrorMessage = curl_error( $ch );
		$curlErrorNumber = curl_errno( $ch );
		$httpStatusCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		
		curl_close($ch);
		
		if( $curlErrorNumber ) {
			throw new RequestFailureException( sprintf( 'Curl "%s%s" was wrong: [%s] %s', $this->Endpoint, $request, $curlErrorNumber, $curlErrorMessage ) );
		}
		
		if ( $httpStatusCode != 200 ) {
			$exception = new RequestFailureException( sprintf( 'Http request "%s%s" was wrong', $this->Endpoint, $request ) );
			$exception->setResponse( new ErrorMessage( $result ) );
			throw $exception;
		}
		
		return $result;
	}
	
	
	/**
	 * Ritorna la lista degli id dei documenti inviati
	 * @param int $userId
	 * @return array
	 */
	public function getDocumentSentList( $userId = null ) {
		$route = '/document_sent';
		if( !is_null( $userId ) ) {
			$route .= '/'.$userId;
		}
		
		return json_decode( 
			$this->curl( 'GET', $route )
		, true );
	}
	
	
	/**
	 * Ritorna i dettagli di un documento inviato
	 * @param int $id
	 * @return \SHL\SdiClient\Types\DocumentSent
	 */
	public function getDocumentSent( $id ) {
		return new Types\DocumentSent( 
			$this->curl( 'GET', '/document_sent/details/' . $id ) 
		);
	}
	
	
	/**
	 * Crea un nuovo documento da inviare allo sdi
	 * @param \SHL\SdiClient\Types\DocumentInfo $document
	 * @return \SHL\SdiClient\Types\DocumentSent
	 */
	public function sendDocument( Types\DocumentInfo $document ) {
		return new Types\DocumentSent(
			$this->curl( 'POST', '/document_sent/create', $document )
		);
	}
	
	
	/**
	 * Ritorna la lista di tutte le notifiche di un documento
	 * @param int $documentId
	 * @return array
	 */
	public function getDocumentSentNotificationList( $documentId ) {
		return json_decode( 
			$this->curl( 'GET', '/document_sent_notification/' . $documentId )
		, true );
	}
	
	
	/**
	 * Ritorna i dettagli di una notifica di un documento
	 * @param int $id
	 * @return \SHL\SdiClient\Types\DocumentSentNotification
	 */
	public function getDocumentSentNotification( $id ) {
		return new Types\DocumentSentNotification( 
			$this->curl( 'GET', '/document_sent_notification/details/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna l'allegato di una notifica
	 * @param int $id
	 * @return \SHL\SdiClient\Types\File
	 */
	public function getDocumentSentNotificationFile( $id ) {
		return new Types\File( 
			$this->curl( 'GET', '/document_sent_notification/attachment/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna la lista di tutti documenti ricevuti
	 * @param int $userId
	 * @return array
	 */
	public function getDocumentReceivedList( $userId = null ) {
		$route = '/document_received';
		if( !is_null( $userId ) ) {
			$route .= '/'.$userId;
		}
		
		return json_decode( 
			$this->curl( 'GET', $route )
		, true );
	}
	
	
	/**
	 * Ritorna i dettagli di un documento ricevuto
	 * @param int $id
	 * @return \SHL\SdiClient\Types\DocumentReceived
	 */
	public function getDocumentReceived( $id ) {
		return new Types\DocumentReceived( 
			$this->curl( 'GET', '/document_received/details/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna il file del documento ricevuto
	 * @param int $id
	 * @return \SHL\SdiClient\Types\File
	 */
	public function getDocumentReceivedFile( $id ) {
		return new Types\File( 
			$this->curl( 'GET', '/document_received/attachment/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna il metafile del documento ricevuto
	 * @param int $id
	 * @return \SHL\SdiClient\Types\File
	 */
	public function getDocumentReceivedMetafile( $id ) {
		return new Types\File( 
			$this->curl( 'GET', '/document_received/metafile/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna la lista di tutte le notifiche di un documento ricevuto
	 * @param int $documentId
	 * @return array
	 */
	public function getDocumentReceivedNotificationList( $documentId ) {
		return json_decode( 
			$this->curl( 'GET', '/document_received_notification/' . $documentId )
		, true );
	}
	
	
	/**
	 * Ritorna i dettagli di una notifica di un documento ricevuto
	 * @param int $id
	 * @return \SHL\SdiClient\Types\DocumentReceivedNotification
	 */
	public function getDocumentReceivedNotification( $id ) {
		return new Types\DocumentReceivedNotification( 
			$this->curl( 'GET', '/document_received_notification/details/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna l'allegato di una notifica di un documento ricevuto
	 * @param int $id
	 * @return \SHL\SdiClient\Types\File
	 */
	public function getDocumentReceivedNotificationFile( $id ) {
		return new Types\File( 
			$this->curl( 'GET', '/document_received_notification/attachment/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna la lista di tutte le notifiche di esito inviate per un documento
	 * @param int $documentId
	 * @return array
	 */
	public function getOutcomeSentList( $documentId ) {
		return json_decode( 
			$this->curl( 'GET', '/outcome_sent/' . $documentId )
		, true );
	}
	
	
	/**
	 * Ritorna i dettagli di una notifica di esito inviata
	 * @param int $id
	 * @return \SHL\SdiClient\Types\OutcomeSent
	 */
	public function getOutcomeSent( $id ) {
		return new Types\OutcomeSent( 
			$this->curl( 'GET', '/outcome_sent/details/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna l'allegato di una notifica di esito inviata
	 * @param int $id
	 * @return \SHL\SdiClient\Types\File
	 */
	public function getOutcomeSentFile( $id ) {
		return new Types\File( 
			$this->curl( 'GET', '/outcome_sent/attachment/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna il file che spiega l'errore di una notifica di esito inviata
	 * @param int $id
	 * @return \SHL\SdiClient\Types\File
	 */
	public function getOutcomeSentErrorFile( $id ) {
		$file = $this->curl( 'GET', '/outcome_sent/error_file/' . $id );
		if( empty( $file ) ) {
			return null;
		}
		
		return new Types\File( $file );
	}
	
	
	/**
	 * Invia una nuova notifica di esito
	 * @param int $documentId L'id del documento ricevuto per il quale mandare la notifica
	 * @param \SHL\SdiClient\Types\OutcomeInfo $outcome
	 * @return \SHL\SdiClient\Types\OutcomeSent
	 */
	public function sendOutcome( $documentId, Types\OutcomeInfo $outcome ) {
		return new Types\OutcomeSent(
			$this->curl( 'POST', '/outcome_sent/create/'. $documentId, $outcome )
		);
	}
}
