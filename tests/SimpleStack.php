<?php

class MyService {
	public function ping() {
		return 'pong';
	}
}
$service = new MyService();

$stack = new \Syzygy\ApiStack\Stack\SimpleStack(
	new \Syzygy\ApiStack\Transport\Simple\SimpleHttpTransport(),
	new \Syzygy\ApiStack\Protocol\JsonRpc\JsonRpcProtocol(),
	new \Syzygy\ApiStack\Service\Reflection\ObjectService($service)
);
$stack->getTransport()->handleRequest();
