<?php

namespace Syzygy\ApiStack\Protocol\JsonRpc;

use Syzygy\ApiStack\Dummy\DummyServiceObject;
use Syzygy\ApiStack\Service\Reflection\ObjectService;

class JsonRpcProtocolTest extends \PHPUnit_Framework_TestCase {
	public function testNotification() {
		// notification
		$this->assertResponse(
			'{"jsonrpc":"2.0","method":"nothing"}',
			''
		);
		// notification
		$this->assertResponse(
			'{"jsonrpc":"2.0","method":"ping"}',
			''
		);
	}

	public function testSingle() {
		// ping-pong
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"ping"}',
			'{"jsonrpc":"2.0","id":1,"result":"pong"}'
		);
		// simple array as parameter and result
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"identity","params":{"a":[1,2,3]}}',
			'{"jsonrpc":"2.0","id":1,"result":[1,2,3]}'
		);
		// associative array as parameter and result
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"identity","params":{"a":{"x":1,"y":2}}}',
			'{"jsonrpc":"2.0","id":1,"result":{"x":1,"y":2}}'
		);
		// parameters as plain array
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"sum","params":[1,2]}',
			'{"jsonrpc":"2.0","id":1,"result":3}'
		);
	}

	public function testBatch() {
		// ping-pong
		$this->assertResponse(
			'[{"jsonrpc":"2.0","id":1,"method":"ping"}]',
			'[{"jsonrpc":"2.0","id":1,"result":"pong"}]'
		);
		// notification inside batch
		$this->assertResponse(
			'[{"jsonrpc":"2.0","method":"nothing"}]',
			'[]'
		);
		// multiple calls
		$requests = array(
			'{"jsonrpc":"2.0","id":2,"method":"sum","params":[1,2]}', // sum
			'{"jsonrpc":"2.0","method":"nothing"}',                   // notification
			'{"jsonrpc":"2.0","id":1,"method":"ping"}',               // ping-pong
			'{"jsonrpc":"2.0","id":"qwerty","method":"identity","params":[{"a":"b"}]}', // identity for complex parameter
		);
		$expectedResponses = array(
			'{"jsonrpc":"2.0","id":2,"result":3}',      // sum
			                                            // notification - shouldn't be answered
			'{"jsonrpc":"2.0","id":1,"result":"pong"}', // ping-pong
			'{"jsonrpc":"2.0","id":"qwerty","result":{"a":"b"}}', // identity for complex parameter
		);
		$this->assertResponse(
			'['. implode(',', $requests).']',
			'['. implode(',', $expectedResponses).']'
		);
	}

	public function testRequestErrors() {
		// empty request
		$this->assertResponse(
			'',
			'{"jsonrpc":"2.0","error":{"code":-32700,"message":"Parse error","data":"empty request"}}'
		);
		// not a valid JSON request
		$this->assertResponse(
			'NoT a JsOn',
			'{"jsonrpc":"2.0","error":{"code":-32700,"message":"Parse error","data":"not a JSON"}}'
		);
		// missing version
		$this->assertResponse(
			'{}',
			'{"jsonrpc":"2.0","error":{"code":-32000,"message":"The JSON-RPC call version is not supported"}}'
		);
		// unsupported version
		$this->assertResponse(
			'{"jsonrpc":"1.0"}',
			'{"jsonrpc":"2.0","error":{"code":-32000,"message":"The JSON-RPC call version is not supported"}}'
		);
	}

	public function testParameterErrors() {
		// missing parameter
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"identity"}',
			'{"jsonrpc":"2.0","id":1,"error":{"code":-32602,"message":"Invalid params","data":[{"argument":"a","message":"missing"}]}}'
		);
		// unsupported parameter
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"ping","params":{"a":1}}',
			'{"jsonrpc":"2.0","id":1,"error":{"code":-32602,"message":"Invalid params","data":[{"argument":"a","message":"not supported"}]}}'
		);
		// invalid parameter
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"invalid","params":{"x":1}}',
			'{"jsonrpc":"2.0","id":1,"error":{"code":-32602,"message":"Invalid params","data":[{"argument":"x","message":"x is always invalid"}]}}'
		);
	}

	public function testMethodErrors() {
		// general exception
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"throwGeneralException"}',
			'{"jsonrpc":"2.0","id":1,"error":{"code":-32001,"message":"Internal error","data":"method error"}}'
		);
		// disclosed exception
		$this->assertResponse(
			'{"jsonrpc":"2.0","id":1,"method":"throwExposedException"}',
			'{"jsonrpc":"2.0","id":1,"error":{"code":123,"message":"This is function exception","data":["a","b","c"]}}'
		);
	}

	protected function assertResponse($request, $expectedResponse) {
		$response = $this->getJsonRpcProtocol()->handleString($request);
		$this->assertJsonStringEqualsJsonString(
			$expectedResponse,
			$response,
			sprintf('Expected: `%s`, got `%s`', $expectedResponse, $response)
		);
	}

	/** @var JsonRpcProtocol */
	protected $jsonRpcProtocol;

	/**
	 * @return JsonRpcProtocol
	 */
	protected function getJsonRpcProtocol() {
		if(!$this->jsonRpcProtocol) {
			$service = new ObjectService(new DummyServiceObject());
			$this->jsonRpcProtocol = new JsonRpcProtocol($service);
		}

		return $this->jsonRpcProtocol;
	}

}
