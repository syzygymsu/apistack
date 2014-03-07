<?php

namespace Syzygy\ApiStack\Protocol\JsonRpc;

use Syzygy\ApiStack\Protocol\Base\FunctionCall as BaseMethodCall;

class MethodCall extends BaseMethodCall {

	/** @var int|null|string */
	protected $callId;

	/**
	 * @param string $name
	 * @param array $parameters
	 * @param int|null|string $callId
	 */
	public function __construct($name, $parameters = array(), $callId=null) {
		parent::__construct($name, $parameters);
		$this->callId = $callId;
	}

	/**
	 * @return int|null|string
	 */
	public function getCallId() {
		return $this->callId;
	}

}
