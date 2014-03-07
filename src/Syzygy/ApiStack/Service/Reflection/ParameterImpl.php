<?php

namespace Syzygy\ApiStack\Service\Reflection;

use Syzygy\ApiStack\Service\Base\FunctionArgumentInterface;

class ParameterImpl implements FunctionArgumentInterface {

	/** @var \ReflectionParameter */
	protected $reflectionParameter;

	/** @param \ReflectionParameter $reflectionParameter */
	public function __construct(\ReflectionParameter $reflectionParameter) {
		$this->reflectionParameter = $reflectionParameter;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPosition() {
		return $this->reflectionParameter->getPosition();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return $this->reflectionParameter->getName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isOptional() {
		return $this->reflectionParameter->isOptional();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultValue() {
		// TODO: handle exception when default value is not available
		return $this->reflectionParameter->getDefaultValue();
	}

}
