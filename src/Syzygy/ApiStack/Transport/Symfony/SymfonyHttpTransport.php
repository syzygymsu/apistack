<?php

namespace Syzygy\ApiStack\Transport\Symfony;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Syzygy\ApiStack\Transport\Base\BaseHttpTransport;

class SymfonyHttpTransport extends BaseHttpTransport {

	/** @var Request $request */
	protected $request;

	public function handleRequest(Request $request) {
		$this->request = $request;
		try {
			$plainRequest = $this->getRequestString();
			$plainResponse = $this->stringProtocol->handleString($plainRequest);
			return $this->createResponse($plainResponse);
		} catch(\Exception $exception) {
			return $this->createExceptionResponse($exception);
		}
	}

	/**
	 * @param string $plainResponse
	 * @return Response
	 */
	protected function createResponse($plainResponse) {
		return new Response($plainResponse);
	}

	/**
	 * @param \Exception $exception
	 * @return Response
	 */
	protected function createExceptionResponse(\Exception $exception) {
		return new Response('Internal Server Error', 500);
	}

	/**
	 * @return bool Whether the request has a body
	 */
	protected function hasBody() {
		return 0!==strlen($this->request->getContent());
	}

	/**
	 * @return string HTTP body
	 */
	protected function getBody() {
		return $this->request->getContent();
	}

	/**
	 * @param string $name
	 * @return bool Whether the request has named GET part
	 */
	protected function hasGet($name) {
		return $this->request->query->has($name);
	}

	/**
	 * @param string $name
	 * @return string Named GET part
	 */
	protected function getGet($name) {
		return $this->request->query->get($name);
	}

	/**
	 * @param string $name
	 * @return bool Whether the request has named POST part
	 */
	protected function hasPost($name) {
		return $this->request->request->has($name);
	}

	/**
	 * @param string $name
	 * @return string Named POST part
	 */
	protected function getPost($name) {
		return $this->request->request->get($name);
	}

}
