<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeEventListener', 'Event');
App::uses('ModelAwareTrait', 'Mailer.Mailer');
App::uses('MissingMailerActionException', 'Mailer.Exception');

abstract class Mailer implements CakeEventListener {

	use ModelAwareTrait;

	/**
	 * Mailer's name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * CakeEmail instance.
	 *
	 * @var CakeEmail
	 */
	protected $_email;

	/**
	 * Cloned CakeEmail instance for restoring instance after email is sent by
	 * mailer action.
	 *
	 * @var string
	 */
	protected $_clonedEmail;

	/**
	 * Constructor.
	 *
	 * @param CakeEmail|null $email CakeEmail instance.
	 */
	public function __construct(CakeEmail $email = null) {
		if ($email === null) {
			$email = new CakeEmail();
		}

		$this->_email = $email;
		$this->_clonedEmail = clone $email;
	}

	/**
	 * Returns the mailer's name.
	 *
	 * @return string
	 */
	public function getName() {
		if (!$this->name) {
			$this->name = str_replace(
				'Mailer',
				'',
				join('', array_slice(explode('\\', get_class($this)), -1))
			);
		}
		return $this->name;
	}

	public function template($template) {
		$views = $this->_email->template(false);
		$this->_email->template($template, $views['layout']);
		return $this;
	}

	public function layout($layout) {
		$views = $this->_email->template(false);
		$this->_email->template($views['template'], $layout);
		return $this;
	}

	/**
	 * Magic method to forward method class to CakeEmail instance.
	 *
	 * @param string $method Method name.
	 * @param array $args Method arguments
	 * @return $this
	 */
	public function __call($method, $args) {
		call_user_func_array([$this->_email, $method], $args);
		return $this;
	}

	/**
	 * Sets email view vars.
	 *
	 * @param string|array $key Variable name or hash of view variables.
	 * @param mixed $value View variable value.
	 * @return $this object.
	 */
	public function set($key, $value = null) {
		$this->_email->viewVars(is_string($key) ? [$key => $value] : $key);
		return $this;
	}

	/**
	 * Sends email.
	 *
	 * @param string $action The name of the mailer action to trigger.
	 * @param array $args Arguments to pass to the triggered mailer action.
	 * @param array $headers Headers to set.
	 * @return array
	 * @throws MissingMailerActionException
	 */
	public function send($action, $args = [], $headers = []) {
		if (!method_exists($this, $action)) {
			throw new MissingMailerActionException([
				'mailer' => $this->getName() . 'Mailer',
				'action' => $action,
			]);
		}

		$views = $this->_email->template(false);
		if (!$views['template']) {
			$this->template($action);
		}

		call_user_func_array([$this, $action], $args);

		$result = $this->_email->send();
		$this->_reset();

		return $result;
	}

	/**
	 * Reset email instance.
	 *
	 * @return $this
	 */
	protected function _reset() {
		$this->_email = clone $this->_clonedEmail;
		return $this;
	}

	/**
	 * Implemented events.
	 *
	 * @return array
	 */
	public function implementedEvents() {
		return [];
	}
}
