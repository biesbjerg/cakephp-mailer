<?php
/**
 * Used when a mailer cannot be found.
 */
class MissingMailerException extends CakeException {

	protected $_messageTemplate = 'Mailer class "%s" could not be found.';

}
