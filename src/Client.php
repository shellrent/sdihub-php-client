<?php

namespace SHL\SdiClient;

use JsonSerializable;
use SHL\SdiClient\Exceptions\RequestFailureException;
use SHL\SdiClient\Types\DocumentInfo;
use SHL\SdiClient\Types\DocumentReceived;
use SHL\SdiClient\Types\DocumentSent;
use SHL\SdiClient\Types\DocumentSentNotification;
use SHL\SdiClient\Types\ErrorMessage;
use SHL\SdiClient\Types\File;
use SHL\SdiClient\Types\OutcomeSent;
use SHL\SdiClient\Types\User;
use SHL\SdiClient\Types\UserInfo;

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
	 * @var bool 
	 */
	private $VerifyCertificate = true;
	

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
		return $this;
	}

	
	/**
	 * Imposta il numero di secondi massimo di attesa per il tentativo di connession
	 * @param int $tryConnectionTimeout
	 */
	public function setTryConnectionTimeout( int $tryConnectionTimeout ) {
		$this->TryConnectionTimeout = $tryConnectionTimeout;
		return $this;
	}
	
	
	public function disableCertificateCheck() {
		$this->VerifyCertificate = false;
		return $this;
	}
	
	
	/**
	 * Effettua la richiesta tramite curl
	 * @param string $verb Il verbo http
	 * @param string $request La richiesta
	 * @param JsonSerializable $json I dati da inviare convertiti in json
	 * @return string
	 * @throws RequestFailureException
	 */
	private function curl( string $verb, string $request, JsonSerializable $json = null ) {
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

		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $this->VerifyCertificate );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $this->VerifyCertificate ? 2 : 0 );
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
			$errorMessage = new ErrorMessage( $result );
			$exception = new RequestFailureException( sprintf( 'Http request "%s%s" was wrong. Code [%s] Message "%s"', $this->Endpoint, $request, $errorMessage->code, $errorMessage->message ) );
			$exception->setResponse( $errorMessage );
			throw $exception;
		}
		
		return $result;
	}
	
	
	/**
	 * Ritorna la lista degli id degli Utenti in gestione
	 * @return array
	 */
	public function getUserList() {
		return json_decode( 
			$this->curl( 'GET', '/user' )
		, true );
	}
	
	
	/**
	 * Ritorna i dettagli di un utente in gestione
	 * @param int $id
	 * @return User
	 */
	public function getUser( $id ) {
		return new Types\User( 
			$this->curl( 'GET', '/user/details/' . $id ) 
		);
	}
	
	
	/**
	 * Crea un nuovo utente
	 * @param UserInfo $user
	 * @return User
	 */
	public function createUser( Types\UserInfo $user ) {
		return new Types\User(
			$this->curl( 'POST', '/user/create', $user )
		);
	}
	
	
	/**
	 * Modifica un utente in gestione
	 * @param int $userId
	 * @param UserInfo $user
	 * @return User
	 */
	public function editUser( $userId, Types\UserInfo $user ) {
		return new Types\User(
			$this->curl( 'PUT', '/user/edit/' . $userId, $user )
		);
	}
	
	
	/**
	 * Modifica lo stato di attivazione di un utente in gestione
	 * @param int $userId
	 * @param bool $activeStatus
	 * @return User
	 */
	public function editUserActiveStatus( $userId, bool $activeStatus ) {
		$params = new Types\GenericType();
		$params->active_status = $activeStatus;
		
		return new Types\User(
			$this->curl( 'PUT', '/user/active/' . $userId, $params )
		);
	}
	
	
	/**
	 * Modifica P.Iva e C.F. dell'utente
	 * creando un utente nuovo
	 * disattivando l'utente precedente
	 *
	 * @param $userId
	 * @param UserInfo $user
	 * @return User
	 */
	public function editBilling ( $userId, Types\UserInfo $user ) {
		return new Types\User(
			$this->curl('PATCH', '/user/edit_billing/' . $userId, $user)
		);
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
	 * @return DocumentSent
	 */
	public function getDocumentSent( $id ) {
		return new Types\DocumentSent( 
			$this->curl( 'GET', '/document_sent/details/' . $id ) 
		);
	}
	
	
	/**
	 * Crea un nuovo documento da inviare allo sdi
	 * @param DocumentInfo $document
	 * @return DocumentSent
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
	 * @return DocumentSentNotification
	 */
	public function getDocumentSentNotification( $id ) {
		return new Types\DocumentSentNotification( 
			$this->curl( 'GET', '/document_sent_notification/details/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna l'allegato di una notifica
	 * @param int $id
	 * @return File
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
	 * @return DocumentReceived
	 */
	public function getDocumentReceived( $id ) {
		return new Types\DocumentReceived( 
			$this->curl( 'GET', '/document_received/details/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna il file del documento ricevuto
	 * @param int $id
	 * @return File
	 */
	public function getDocumentReceivedFile( $id ) {
		return new Types\File( 
			$this->curl( 'GET', '/document_received/attachment/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna il metafile del documento ricevuto
	 * @param int $id
	 * @return File
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
	 * @return File
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
	 * @return OutcomeSent
	 */
	public function getOutcomeSent( $id ) {
		return new Types\OutcomeSent( 
			$this->curl( 'GET', '/outcome_sent/details/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna l'allegato di una notifica di esito inviata
	 * @param int $id
	 * @return File
	 */
	public function getOutcomeSentFile( $id ) {
		return new Types\File( 
			$this->curl( 'GET', '/outcome_sent/attachment/' . $id ) 
		);
	}
	
	
	/**
	 * Ritorna il file che spiega l'errore di una notifica di esito inviata
	 * @param int $id
	 * @return File
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
	 * @return OutcomeSent
	 */
	public function sendOutcome( $documentId, Types\OutcomeInfo $outcome ) {
		return new Types\OutcomeSent(
			$this->curl( 'POST', '/outcome_sent/create/'. $documentId, $outcome )
		);
	}
}
