<?php

namespace Syzygy\ApiStack\Transport\Base;

use Syzygy\ApiStack\Protocol\Base\StringProtocolInterface;

abstract class StringProtocolAwareTransport {

	/**
	 * @var \Syzygy\ApiStack\Protocol\Base\StringProtocolInterface
	 */
	protected $stringProtocol;

	public function __construct(StringProtocolInterface $stringProtocol=null) {
		if(!is_null($stringProtocol)) {
			$this->setStringProtocol($stringProtocol);
		}
	}

	public function setStringProtocol(StringProtocolInterface $stringProtocol) {
		$this->stringProtocol = $stringProtocol;
	}

}
