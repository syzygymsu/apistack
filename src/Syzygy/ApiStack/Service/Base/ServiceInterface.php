<?php

namespace Syzygy\ApiStack\Service\Base;

interface ServiceInterface {

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasFunction($name);

	/**
	 * @param string $name
	 * @return FunctionInterface
	 */
	public function getFunction($name);

}
