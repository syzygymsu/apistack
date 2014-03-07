<?php

namespace Syzygy\ApiStack\Service\Complex;

use Syzygy\ApiStack\Service\Base\FunctionInterface;
use Syzygy\ApiStack\Service\Base\ServiceInterface;

class CompoundService implements ServiceInterface {

	/** @var ServiceInterface[] */
	protected $services = array();

	/** @var string */
	protected $delimiter = '.';

	/**
	 * @param string $delimiter
	 */
	public function __construct($delimiter = null) {
		if(!is_null($delimiter)) {
			$this->setDelimiter($delimiter);
		}
	}

	/**
	 * @param string $delimiter
	 */
	public function setDelimiter($delimiter) {
		$this->delimiter = $delimiter;
	}

	/**
	 * @return string
	 */
	public function getDelimiter() {
		return $this->delimiter;
	}

	/**
	 * @param $name
	 * @param ServiceInterface $service
	 */
	public function addService($name, ServiceInterface $service) {
		$this->services[$name] = $service;
	}

	/**
	 * @param string $serviceName
	 */
	public function removeService($serviceName) {
		unset($this->services[$serviceName]);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasFunction($name) {
		list($serviceName, $remainderName) = explode($this->delimiter, $name, 2);
		return array_key_exists($serviceName, $this->services) && $this->services[$serviceName]->hasFunction($remainderName);
	}

	/**
	 * @param string $name
	 * @return FunctionInterface
	 */
	public function getFunction($name) {
		list($serviceName, $remainderName) = explode($this->delimiter, $name, 2);
		return $this->services[$serviceName]->getFunction($remainderName);
	}

}