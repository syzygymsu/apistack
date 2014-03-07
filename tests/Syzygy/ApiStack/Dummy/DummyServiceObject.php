<?php

namespace Syzygy\ApiStack\Dummy;

use Syzygy\ApiStack\Exception\BadArgumentsException;
use Syzygy\ApiStack\Exception\FunctionException;

class DummyServiceObject {

	public function __construct() {

	}

	public function throwGeneralException() {
		throw new \Exception('This is general exception');
	}

	public function throwExposedException() {
		throw new FunctionException('This is function exception', 123, array('a', 'b', 'c'));
	}

	public function ping() {
		return 'pong';
	}

	public function sum($x, $y) {
		return $x + $y;
	}

	public function identity($a) {
		return $a;
	}

	public function nothing() {

	}

	public function invalid($x) {
		throw BadArgumentsException::invalid('x', 'x is always invalid');
	}

	public function publicFunction() {

	}

	protected function protectedFunction() {

	}

	private function privateFunction() {

	}

	public static function staticFunction() {

	}

}
