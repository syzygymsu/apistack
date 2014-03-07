<?php

namespace Syzygy\ApiStack\Utility;

class ArrayUtility {

	/**
	 * @param array $array
	 * @return bool
	 */
	static function isAssociative(array $array) {
		return array_keys($array) !== range(0, count($array) - 1);
	}

}
