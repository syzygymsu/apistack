<?php

namespace Syzygy\ApiStack\Service\Complex;

use Syzygy\ApiStack\Service\Base\FunctionInterface;
use Syzygy\ApiStack\Service\Base\ServiceInterface;
use Syzygy\ApiStack\Service\Reflection\FunctionImpl;

class CollectionService implements ServiceInterface {

	/** @var FunctionInterface[] */
	protected $functions = array();

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasFunction($name) {
		return isset($this->functions[$name]);
	}

	/**
	 * @param string $name
	 * @return FunctionInterface
	 */
	public function getFunction($name) {
		return $this->functions[$name];
	}

	/**
	 * @param string $name
	 * @param FunctionInterface $function
	 */
	public function addFunction($name, FunctionInterface $function) {
		$this->functions[$name] = $function;
	}

	/**
	 * @param string $name
	 * @param callback $callback
	 * @return FunctionImpl
	 */
	public function addCallback($name, $callback) {
		$function = FunctionImpl::createFromCallback($callback);
		$this->addFunction($name, $function);
		return $function;
	}

	/**
	 * @param string $name
	 */
	public function removeFunction($name) {
		unset($this->functions[$name]);
	}

}
