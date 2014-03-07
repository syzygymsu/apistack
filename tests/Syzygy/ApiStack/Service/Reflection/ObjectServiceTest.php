<?php

namespace Syzygy\ApiStack\Service\Reflection;

use Syzygy\ApiStack\Dummy\DummyServiceObject;

class ObjectServiceTest extends \PHPUnit_Framework_TestCase {

	public function testAccessibility() {
		$service = $this->getService();

		$this->assertTrue($service->hasFunction('publicFunction'));

		$this->assertFalse($service->hasFunction('protectedFunction'));
		$this->assertFalse($service->hasFunction('privateFunction'));
		$this->assertFalse($service->hasFunction('staticFunction'));
		$this->assertFalse($service->hasFunction('__construct'));
	}

	/** @var ObjectService */
	protected $service;

	/**
	 * @return ObjectService
	 */
	protected function getService() {
		if(!$this->service) {
			$this->service = new ObjectService(new DummyServiceObject());
		}
		return $this->service;
	}

}
