<?php

namespace Syzygy\ApiStack\Exception\ArgumentIssue;

class ArgumentMissing extends ArgumentIssue {

	/**
	 * {@inheritdoc}
	 */
	function __construct($name) {
		parent::__construct($name);
	}

}
