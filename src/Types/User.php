<?php

namespace SHL\SdiClient\Types;

class User extends UserInfo {
	
	/**
	 * @var int
	 */
	public $id;
		
	/**
	 * @var bool
	 */
	public $active;
	
	/**
	 * @var string
	 */
	public $updated_at;
	
	/**
	 * @var string
	 */
	public $created_at;
}
