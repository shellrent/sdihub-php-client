<?php

namespace SHL\SdiClient\Types;

class File extends GenericType {
	/**
	 * @var string
	 */
	public $filename;
	
	/**
	 * @var string
	 */
	public $file;
	
	
	/**
	 * Decodifica il file
	 * @return string
	 */
	public function decodeFile() {
		return base64_decode( $this->file );
	}
}
