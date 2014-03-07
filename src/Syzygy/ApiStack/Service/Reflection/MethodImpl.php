<?php

namespace Syzygy\ApiStack\Service\Reflection;

use Syzygy\ApiStack\Service\Base\FunctionInterface;

class MethodImpl implements FunctionInterface {

	/** @var object */
	protected $boundObject;

	/** @var \ReflectionMethod */
	protected $reflectionMethod;

	/**
	 * @param object $boundObject
	 * @param \ReflectionMethod $reflectionMethod
	 */
	public function __construct($boundObject, \ReflectionMethod $reflectionMethod) {
		$this->boundObject = $boundObject;
		$this->reflectionMethod = $reflectionMethod;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getArguments() {
		return array_map(
			function(\ReflectionParameter $reflectionParameter) {
				return new ParameterImpl($reflectionParameter);
			},
			$this->reflectionMethod->getParameters()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function invoke(array $arguments) {
		return $this->reflectionMethod->invokeArgs($this->boundObject, $arguments);
	}

}
