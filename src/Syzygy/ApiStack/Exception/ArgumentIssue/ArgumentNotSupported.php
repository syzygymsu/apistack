<?php

namespace Syzygy\ApiStack\Exception\ArgumentIssue;

class ArgumentNotSupported extends ArgumentIssue {

	/**
	 * {@inheritdoc}
	 */
	function __construct($name) {
		parent::__construct($name);
	}

}
