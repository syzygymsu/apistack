<?php

namespace Syzygy\ApiStack\Protocol\Base;

class FunctionCall {

	/** @var string */
	protected $name;

	/** @var array */
	protected $parameters;

	/**
	 * @param string $name
	 * @param array $parameters
	 */
	public function __construct($name, $parameters = array()) {
		$this->name = $name;
		$this->parameters = $parameters;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}
}
