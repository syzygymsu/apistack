<?php

namespace Syzygy\ApiStack\Service\Base;

interface FunctionArgumentInterface {

	/**
	 * @return int Argument position (starting with zero)
	 */
	public function getPosition();

	/**
	 * @return string|null Argument name or null if not available
	 */
	public function getName();

	/**
	 * @return bool Whether this argument can be omitted
	 */
	public function isOptional();

	/**
	 * @return mixed Default value. Will be used if argument is optional and no explicit value provided.
	 */
	public function getDefaultValue();

	// TODO: probably add `getType` method

}
