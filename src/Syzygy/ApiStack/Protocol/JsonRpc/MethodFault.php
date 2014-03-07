<?php

namespace Syzygy\ApiStack\Protocol\JsonRpc;

class MethodFault extends MethodResponse {

	/** @var null|int */
	protected $code;

	/** @var  null|string */
	protected $message;

	/** @var mixed */
	protected $data;

	/** @var \Exception|null */
	protected $exception;

	/**
	 * @param int|null|string $callId
	 * @param null|int $code
	 * @param null|string $message
	 * @param mixed $data
	 * @param \Exception $exception
	 */
	public function __construct($callId=null, $code=null, $message=null, $data=null, \Exception $exception=null) {
		parent::__construct($callId);
		$this->code = $code;
		$this->message = $message;
		$this->data = $data;
		$this->exception = $exception;
	}

	/**
	 * @return int|null
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @return null|string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return \Exception
	 */
	public function getException() {
		return $this->exception;
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

}
