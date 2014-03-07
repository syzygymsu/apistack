<?php

namespace Syzygy\ApiStack\Protocol\JsonRpc;

class BatchResponse extends MethodResponse implements \IteratorAggregate {

	/** @var MethodResponse */
	protected $responses;

	/**
	 * @param MethodResponse[] $responses
	 */
	public function __construct(array $responses) {
		$this->responses = $responses;
	}

	/**
	 * @return array|MethodResponse|MethodResponse[]
	 */
	public function getResponses() {
		return $this->responses;
	}

	/**
	 * @return \Traversable
	 */
	public function getIterator() {
		return new \ArrayIterator($this->responses);
	}

}
