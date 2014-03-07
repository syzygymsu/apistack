<?php

namespace Syzygy\ApiStack\Service\Reflection;

use Syzygy\ApiStack\Service\Base\FunctionInterface;

class FunctionImpl implements FunctionInterface {

	/** @var \ReflectionFunction */
	protected $reflectionFunction;

	/**
	 * @param \ReflectionFunction $reflectionFunction
	 */
	public function __construct(\ReflectionFunction $reflectionFunction) {
		$this->reflectionFunction = $reflectionFunction;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getArguments() {
		$parameters = array();
		foreach($this->reflectionFunction->getParameters() as $reflectionParameter) {
			$parameters[] = new ParameterImpl($reflectionParameter);
		}
		return $parameters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function invoke(array $arguments) {
		return $this->reflectionFunction->invokeArgs($arguments);
	}

	/**
	 * @param callback $callback
	 * @return FunctionImpl
	 */
	public static function createFromCallback($callback) {
		return new self(new \ReflectionFunction($callback));
	}
}
