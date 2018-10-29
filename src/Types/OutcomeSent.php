<?php

namespace SHL\SdiClient\Types;

class OutcomeSent extends GenericType {
	/**
	 * @property int 
	 */
	public $id;

	/**
	 * @property int 
	 */
	public $document_received_id;

	/**
	 * @property bool 
	 */
	public $accepted;

	/**
	 * @property string 
	 */
	public $file_name;

	/**
	 * @property bool 
	 */
	public $in_charge;

	/**
	 * @property bool 
	 */
	public $is_submitted;

	/**
	 * @property bool 
	 */
	public $response_success;

	/**
	 * @property string 
	 */
	public $response_message;

	/**
	 * @property string 
	 */
	public $response_error_file_name;

	/**
	 * @property string 
	 */
	public $updated_at;

	/**
	 * @property string 
	 */
	public $created_at;
}
