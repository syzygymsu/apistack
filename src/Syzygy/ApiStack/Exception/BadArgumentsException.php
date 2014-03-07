<?php

namespace Syzygy\ApiStack\Exception;

use Syzygy\ApiStack\Exception\ArgumentIssue\ArgumentInvalid;
use Syzygy\ApiStack\Exception\ArgumentIssue\ArgumentIssue;
use Syzygy\ApiStack\Exception\ArgumentIssue\ArgumentMissing;
use Syzygy\ApiStack\Exception\ArgumentIssue\ArgumentNotSupported;

class BadArgumentsException extends \Exception {

	/** @var ArgumentIssue[] */
	protected $issues = array();

	public function __construct(array $issues, $message='', $code=0, $previous=null) {
		parent::__construct($message, $code, $previous);
		$this->issues = $issues;
	}

	/**
	 * @return ArgumentIssue[]
	 */
	public function getIssues() {
		return $this->issues;
	}

	/**
	 * @param string|string[] $names Names of missing arguments
	 * @return BadArgumentsException
	 */
	public static function missing($names) {
		if(!is_array($names)) {
			$names = array($names);
		}
		$errors = array();
		foreach($names as $name) {
			$errors[] = new ArgumentMissing($name);
		}
		return new self($errors);
	}

	/**
	 * @param string|string[] $names Names of not supported arguments
	 * @return BadArgumentsException
	 */
	public static function notSupported($names) {
		if(!is_array($names)) {
			$names = array($names);
		}
		$errors = array();
		foreach($names as $name) {
			$errors[] = new ArgumentNotSupported($name);
		}
		return new self($errors);
	}

	/**
	 * @param string $name
	 * @param string $validationMessage
	 * @return BadArgumentsException
	 */
	public static function invalid($name, $validationMessage) {
		return new self(
			array(new ArgumentInvalid($name, $validationMessage))
		);
	}

}
