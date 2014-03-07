<?php

namespace Syzygy\ApiStack\Protocol\JsonRpc;

abstract class MethodResponse {

	/** @var int|null|string */
	protected $callId = null;

	/**
	 * @param int|null|string $callId
	 */
	public function __construct($callId = null)
	{
		$this->callId = $callId;
	}

	/**
	 * @return int|null|string
	 */
	public function getCallId() {
		return $this->callId;
	}

}
