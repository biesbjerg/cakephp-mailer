<?php
/**
 * Provides functionality for loading table classes
 * and other repositories onto properties of the host object.
 *
 * Example users of this trait are Cake\Controller\Controller and
 * Cake\Console\Shell.
 */
trait ModelAwareTrait {

	/**
	 * Loads and instantiates models
	 * If the model is non existent, it will throw a missing database table error, as CakePHP generates
	 * dynamic models for the time being.
	 *
	 * @param string $modelClass Name of model class to load
	 * @return object The model instance created.
	 * @throws MissingModelException if the model class cannot be found.
	 */
	public function loadModel($modelClass = null) {
		if ($modelClass === null) {
			$modelClass = $this->modelClass;
		}

		list($pluginDot, $name) = pluginSplit($modelClass, true);

		if (isset($this->{$name})) {
			return $this->{$name};
		}

		$this->{$name} = ClassRegistry::init($pluginDot . $name);
		if (!$this->{$name}) {
			throw new MissingModelException($name);
		}
		return $this->{$name};
	}
}
