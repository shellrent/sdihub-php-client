<?php

namespace SHL\SdiClient\Types;

use SHL\SdiClient\Exceptions\TypeException;
use JsonSerializable;

class GenericType implements JsonSerializable {
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
		if( ! $obj ) {
			throw new TypeException( sprintf( 'Invalid json for Model %s construction', get_class($this) ) );
		}
		
		foreach ( $obj as $key => $value ) {
			if( ! property_exists( $this, $key ) ) {
				continue;
			}
			
			if( is_object($value) ) {
				$this->$key = (array) $value;
			} else {
				$this->$key = $value;
			}
		}
	}
	
	
	/**
	 * Ritorna le proprietà della classe serializzabili
	 * @return array
	 */
	public function jsonSerialize() {
		$properties = [];
		
		foreach ( get_object_vars( $this ) as $key => $value ) {
			if( is_null( $value ) ) {
				continue;
			}
			
			$properties[$key] = $value;
		}
		
		return $properties;
	}
}