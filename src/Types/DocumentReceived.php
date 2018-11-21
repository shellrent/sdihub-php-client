<?php

namespace SHL\SdiClient\Types;

class DocumentReceived extends GenericType {

	/**
	 * @property int 
	 */
	public $id;

	/**
	 * @property int 
	 */
	public $user_id;

	/**
	 * @property string 
	 */
	public $recipient_code;

	/**
	 * @property string 
	 */
	public $document_format;

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
	public $metafile_name;

	/**
	 * @property bool 
	 */
	public $notification_deadline;

	/**
	 * @property string 
	 */
	public $updated_at;

	/**
	 * @property string 
	 */
	public $created_at;
}
