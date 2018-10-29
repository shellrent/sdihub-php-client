<?php

namespace SHL\SdiClient\Types;

use SHL\SdiClient\Exceptions\TypeException;

class GenericType {
	/**
	 * Costruisce l'oggetto passando un json
	 * @param string $json
	 * @throws TypeException
	 */
	public function __construct( string $json = null ) {
		if( is_null( $json ) ) {
			return;
		}
		
		$obj = json_decode( $json );
		
		foreach ( $obj as $key => $value ) {
			$key = ucfirst( $key );
			if( ! property_exists( $this, $key ) ) {
				throw new TypeException( sprintf( 'Does not exist "%s" property on model %s', $key, get_class($this) ) );
			}
			
			if( is_object($value) ) {
				$this->$key = (array) $value;
			}
			
			$this->$key = $value;
		}
	}
}