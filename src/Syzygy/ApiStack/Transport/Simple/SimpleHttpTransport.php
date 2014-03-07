<?php

namespace Syzygy\ApiStack\Transport\Simple;

use Syzygy\ApiStack\Transport\Base\BaseHttpTransport;

class SimpleHttpTransport extends BaseHttpTransport {

	/**
	 * @var string|null Saved HTTP body
	 */
	protected $body = null;

	public function handleRequest() {
		try {
			$plainRequest = $this->getRequestString();
			$plainResponse = $this->stringProtocol->handleString($plainRequest);
			$this->doResponse($plainResponse);
		} catch(\Exception $exception) {
			$this->doExceptionResponse($exception);
		}
	}

	/**
	 * @param string $plainResponse
	 */
	protected function doResponse($plainResponse) {
		echo $plainResponse;
	}

	/**
	 * @param \Exception $exception
	 */
	protected function doExceptionResponse(\Exception $exception) {
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		$code = 500;
		$text = 'Internal server error';
		header(sprintf('%s %s %s', $protocol, $code, $text));
		echo $text;
	}

	/**
	 * @return bool Whether the request has a body
	 */
	protected function hasBody() {
		return 0!==strlen($this->getBody());
	}

	/**
	 * @return string HTTP body
	 */
	protected function getBody() {
		if(is_null($this->body)) {
			$this->body = stream_get_contents(STDIN);
		}
		return $this->body;
	}

	/**
	 * @param string $name
	 * @return bool Whether the request has named GET part
	 */
	protected function hasGet($name) {
		return array_key_exists($_GET, $name);
	}

	/**
	 * @param string $name
	 * @return string Named GET part
	 */
	protected function getGet($name) {
		return $_GET[$name];
	}

	/**
	 * @param string $name
	 * @return bool Whether the request has named POST part
	 */
	protected function hasPost($name) {
		return array_key_exists($_POST, $name);
	}

	/**
	 * @param string $name
	 * @return string Named POST part
	 */
	protected function getPost($name) {
		return $_POST[$name];
	}

}
