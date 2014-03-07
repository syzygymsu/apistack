<?php

namespace Syzygy\ApiStack\Service\Reflection;

use Syzygy\ApiStack\Service\Base\ServiceInterface;

class ObjectService implements ServiceInterface {

	/** @var object */
	protected $object;

	/** @var \ReflectionObject|null */
	protected $reflectionObject = null;

	/**
	 * @param object $object
	 */
	public function __construct($object) {
		$this->object = $object;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasFunction($name) {
		if(!$this->getReflectionObject()->hasMethod($name)) {
			return false;
		}
		$methodReflection = $this->getReflectionObject()->getMethod($name);
		return $methodReflection->isPublic() &&
		       !$methodReflection->isAbstract() &&
		       !$methodReflection->isStatic() &&
		       !$methodReflection->isConstructor();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunction($name) {
		return new MethodImpl(
			$this->object,
			$this->getReflectionObject()->getMethod($name)
		);
	}

	/**
	 * @return \ReflectionObject
	 */
	protected function getReflectionObject() {
		if(!$this->reflectionObject) {
			$this->reflectionObject = new \ReflectionObject($this->object);
		}
		return $this->reflectionObject;
	}

}
