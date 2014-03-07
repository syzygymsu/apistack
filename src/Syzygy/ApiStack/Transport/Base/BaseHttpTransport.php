<?php

namespace Syzygy\ApiStack\Transport\Base;

abstract class BaseHttpTransport extends StringProtocolAwareTransport {

	const SOURCE_BODY = 'body';
	const SOURCE_GET = 'get';
	const SOURCE_POST = 'post';

	protected $sources = array(array(self::SOURCE_BODY), array(self::SOURCE_POST, 'jsonrpc'));

	public function setSources() {
		$this->sources = array();
		foreach(func_get_args() as $argument) {
			if(!is_array($argument)) {
				$argument = array($argument);
			}
			switch(count($argument)) {
				case 0:
					throw new \Exception('Argument should contain one or more elements');
				case 1:
					if(self::SOURCE_BODY !== $argument) {
						throw new \Exception('Arguments other than BODY should contain list of names');
					}
					break;
			}
			if(!in_array(reset($argument), array(self::SOURCE_BODY, self::SOURCE_GET, self::SOURCE_POST))) {
				throw new \Exception('Unknown source type');
			}
			$this->sources[] = $argument;
		}
	}

	public function getRequestString() {
		foreach($this->sources as $source) {
			$type = array_shift($source);
			if(self::SOURCE_BODY === $type) {
				if($this->hasBody()) {
					return $this->getBody();
				}
			} else {
				foreach($source as $name) {
					switch($type) {
						case self::SOURCE_GET;
							if($this->hasGet($name)) {
								return $this->getGet($name);
							}
							break;
						case self::SOURCE_POST;
							if($this->hasPost($name)) {
								return $this->getPost($name);
							}
							break;
						default:
							throw new \Exception(sprintf('Type %s cannot have parameters'), $type);
					}
				}
			}
		}
		return null;
	}

	/**
	 * @return bool Whether the request has a body
	 */
	abstract protected function hasBody();

	/**
	 * @return string HTTP body
	 */
	abstract protected function getBody();

	/**
	 * @param string $name
	 * @return bool Whether the request has named GET part
	 */
	abstract protected function hasGet($name);

	/**
	 * @param string $name
	 * @return string Named GET part
	 */
	abstract protected function getGet($name);

	/**
	 * @param string $name
	 * @return bool Whether the request has named POST part
	 */
	abstract protected function hasPost($name);

	/**
	 * @param string $name
	 * @return string Named POST part
	 */
	abstract protected function getPost($name);

}
