<?php
App::uses('MissingMailerException', 'Mailer.Exception');

/**
 * Provides functionality for loading mailer classes
 * onto properties of the host object.
 */
trait MailerAwareTrait {

	/**
	 * Returns a mailer instance.
	 *
	 * @param string $name Mailer's name.
	 * @param CakeEmail|null $email Email instance.
	 * @return Mailer
	 * @throws MissingMailerException if undefined mailer class.
	 */
	public function getMailer($name, CakeEmail $email = null) {
		list($pluginDot, $name) = pluginSplit($name, true);

		$className = $name . 'Mailer';
		$location = $pluginDot . 'Mailer';

		App::uses($className, $location);
		if (!class_exists($className)) {
			throw new MissingMailerException(compact('name'));
		}

		return (new $className($email));
	}
}
