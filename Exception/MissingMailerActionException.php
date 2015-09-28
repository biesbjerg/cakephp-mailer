<?php
class MissingMailerActionException extends CakeException {

	/**
	 * {@inheritDoc}
	 */
	protected $_messageTemplate = 'Mail %s::%s() could not be found, or is not accessible.';

	/**
	 * {@inheritDoc}
	 */
	public function __construct($message, $code = 404) {
		parent::__construct($message, $code);
	}
}
