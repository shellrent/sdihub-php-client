<?php

namespace SHL\SdiClient\Types;

class DocumentReceivedNotification extends GenericType {

	/**
	 * @property int
	 */
	public $id;
	
	/**
	 * @property int
	 */
	public $document_received_id;
	
	/**
	 * @property string
	 */
	public $request_name;
	
	/**
	 * @property int
	 */
	public $sdi_identifier;
	
	/**
	 * @property string
	 */
	public $file_name;
	
	/**
	 * @property string
	 */
	public $message;
	
	/**
	 * @property string
	 */
	public $updated_at;
	
	/**
	 * @property string
	 */
	public $created_at;
}