<?php

namespace Syzygy\ApiStack\Exception\ArgumentIssue;

class ArgumentInvalid extends ArgumentIssue {

	/** @var string */
	protected $validationMessage;

	function __construct($name, $validationMessage) {
		parent::__construct($name);
		$this->validationMessage = $validationMessage;
	}

	/**
	 * @return string
	 */
	public function getValidationMessage() {
		return $this->validationMessage;
	}

}
