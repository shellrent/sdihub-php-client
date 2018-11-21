<?php

namespace SHL\SdiClient\Types;

class OutcomeInfo extends GenericType {
	/**
	 * Il valore che indica se l'esito è una acccettazione o un rifiuto
	 * @var bool
	 */
	public $acceptance;
	
	/**
	 * La descrizione dell'esito
	 * @var string
	 */
	public $description = null;
}
