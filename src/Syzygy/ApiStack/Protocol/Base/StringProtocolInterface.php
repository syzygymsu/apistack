<?php

namespace Syzygy\ApiStack\Protocol\Base;

interface StringProtocolInterface {

	/**
	 * @param string $request Request
	 * @return string Response
	 */
	public function handleString($request);

}
