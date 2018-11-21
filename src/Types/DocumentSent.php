<?php

namespace SHL\SdiClient\Types;

class DocumentSent extends GenericType {
	/**
	 * @property int 
	 */
	public $id;
	
	/**
	 * @var int
	 */
	public $user_id;
	
	/**
	 * @property string 
	 */
	public $external_id;
			
	/**
	 * @property string 
	 */
	public $file_transimission_name;
			
	/**
	 * @property bool 
	 */
	public $is_submitted;
			
	/**
	 * @property int 
	 */
	public $sdi_identifier;
			
	/**
	 * @property string 
	 */
	public $reception_date;
			
	/**
	 * @property bool 
	 */
	public $is_valid;
			
	/**
	 * @property string 
	 */
	public $date_validation;
			
	/**
	 * @property string 
	 */
	public $validation_message;
			
	/**
	 * @property bool 
	 */
	public $is_delivered;
			
	/**
	 * @property string 
	 */
	public $date_delivered;
			
	/**
	 * @property bool 
	 */
	public $recipient_outcome;
			
	/**
	 * @property bool 
	 */
	public $transmission_completed;
			
	/**
	 * @property bool 
	 */
	public $outcome_expired;
			
	/**
	 * @property string 
	 */
	public $updated_at;
			
	/**
	 * @property string 
	 */
	public $created_at;
}
