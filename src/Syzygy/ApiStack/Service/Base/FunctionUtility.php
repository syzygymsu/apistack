<?php

namespace Syzygy\ApiStack\Service\Base;

use Syzygy\ApiStack\Exception\BadArgumentsException;
use Syzygy\ApiStack\Service\Base\FunctionInterface;
use Syzygy\ApiStack\Utility\ArrayUtility;

class FunctionUtility {

	/**
	 * @param FunctionInterface $function
	 * @param array $arguments
	 * @return array
	 * @throws
	 * @throws
	 */
	static function normalizeArguments(FunctionInterface $function, array $arguments) {
		if(!ArrayUtility::isAssociative($arguments)) {
			return $arguments;
		}

		$result = array();
		foreach($function->getArguments() as $argumentDescription) {
			$name = $argumentDescription->getName();
			$position = $argumentDescription->getPosition();

			if(array_key_exists($name, $arguments)) {
				$value = $arguments[$name];
				unset($arguments[$name]);
			} else {
				if(!$argumentDescription->isOptional()) {
					throw BadArgumentsException::missing($name);
				}
				$value = $argumentDescription->getDefaultValue();
			}

			$result[$position] = $value;
		}

		if(!empty($arguments)) {
			throw BadArgumentsException::notSupported(array_keys($arguments));
		}

		return $result;
	}

}
