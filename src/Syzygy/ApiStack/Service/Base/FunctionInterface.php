<?php

namespace Syzygy\ApiStack\Service\Base;

interface FunctionInterface {

	/**
	 * @return FunctionArgumentInterface[]
	 */
	public function getArguments();

	/**
	 * @param array $arguments
	 * @return mixed
	 */
	public function invoke(array $arguments);

}
