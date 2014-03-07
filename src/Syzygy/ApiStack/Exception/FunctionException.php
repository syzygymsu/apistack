<?php

namespace Syzygy\ApiStack\Exception;

/**
 * An exception function can throw to display an error to the caller
 */
class FunctionException extends ExposedException {

	/** @var mixed */
	protected $data;

	/**
	 * @param string $message
	 * @param int $code
	 * @param null $data Any data to be displayed with error
	 * @param \Exception $previous
	 */
	public function __construct($message = '', $code = 0, $data=null, \Exception $previous = null) {
		parent::__construct($message, $code, $previous);
		$this->data = $data;
	}

	public function getData() {
		return $this->data;
	}

}
