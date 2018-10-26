<?php

namespace SHL\SdiClient;

class Client {
	
	private $Endpoint;
	
	private $Token;
	
	
	public function __construct( $endpoint, $username, $apiToken ) {
		$this->Endpoint = $endpoint;
		$this->Token = $username . '.' . $apiToken;
	}
	
}
