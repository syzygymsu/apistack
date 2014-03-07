<?php

namespace Syzygy\ApiStack\Protocol\JsonRpc;

class MethodReturn extends MethodResponse {

	/** @var mixed */
	protected $returnValue;

	/**
	 * @param null|string|int $callId
	 * @param mixed $returnValue
	 */
	public function __construct($callId = null, $returnValue = null) {
		parent::__construct($callId);
		$this->returnValue = $returnValue;
	}

	/**
	 * @return mixed
	 */
	public function getReturnValue() {
		return $this->returnValue;
	}

}
