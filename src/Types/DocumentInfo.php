<?php

namespace SHL\SdiClient\Types;

class DocumentInfo extends GenericType {
	/**
	 * L'external id utilizzato per identificare esternemanete all'hub il documento
	 * @var string
	 */
	public $external_id = null;
	
	/**
	 * Il mimetype del documento
	 * @var string
	 */
	public $mimetype;
	
	/**
	 * Il file convertito in base64
	 * @var string
	 */
	public $file;
	
	
	/**
	 * Costruisce le info di un file a partire dal path
	 * @param string $path
	 * @return \SHL\SdiClient\Types\DocumentInfo
	 */
	public static function createByFilepath( $path ) {
		$document = new DocumentInfo();
		
		$document->mimetype = mime_content_type( $path );
		$document->file = base64_encode( file_get_contents( $path ) );
		
		return $document;
	}
}
