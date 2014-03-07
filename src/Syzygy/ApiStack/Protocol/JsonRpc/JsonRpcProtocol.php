<?php

namespace Syzygy\ApiStack\Protocol\JsonRpc;

use Syzygy\ApiStack\Exception\ArgumentIssue\ArgumentInvalid;
use Syzygy\ApiStack\Exception\ArgumentIssue\ArgumentMissing;
use Syzygy\ApiStack\Exception\ArgumentIssue\ArgumentNotSupported;
use Syzygy\ApiStack\Exception\BadArgumentsException;
use Syzygy\ApiStack\Exception\FunctionException;
use Syzygy\ApiStack\Protocol\Base\ServiceAwareStringProtocol;
use Syzygy\ApiStack\Service\Base\FunctionUtility;
use Syzygy\ApiStack\Utility\ArrayUtility;

class JsonRpcProtocol extends ServiceAwareStringProtocol {

	const CODE_PARSE_ERROR        = -32700; // Invalid JSON was received by the server. An error occurred on the server while parsing the JSON text.
	const CODE_INVALID_REQUEST    = -32600; // The JSON sent is not a valid Request object.
	const CODE_METHOD_NOT_FOUND   = -32601; // The method does not exist / is not available.
	const CODE_INVALID_PARAMETERS = -32602; // Invalid method parameter(s).
	const CODE_INTERNAL_ERROR     = -32603; // Internal JSON-RPC error.
	const CODE_SERVER_ERROR_BASE  = -32000; // -32000 to -32099 reserved for implementation-defined server-errors.

	const MESSAGE_PARSE_ERROR            = 'Parse error';
	const MESSAGE_INVALID_REQUEST    = 'Invalid Request';
	const MESSAGE_METHOD_NOT_FOUND   = 'Method not found';
	const MESSAGE_INVALID_PARAMETERS = 'Invalid params';
	const MESSAGE_INTERNAL_ERROR     = 'Internal error';
	const MESSAGE_SERVER_ERROR       = self::MESSAGE_INTERNAL_ERROR;

	const CODE_CUSTOM_VERSION   = -32000; // Invalid protocol version requested
	const CODE_CUSTOM_EXCEPTION = -32001; // Exception occurred in called function

	const MESSAGE_CUSTOM_VERSION   = 'The JSON-RPC call version is not supported';
	const MESSAGE_CUSTOM_EXCEPTION = self::MESSAGE_SERVER_ERROR;

	/**
	 * @param string $requestString
	 * @return string
	 */
	public function handleString($requestString) {
		try {
			$response = $this->doHandleString($requestString);
		} catch(\Exception $exception) {
			$response = new MethodFault(null, self::CODE_INTERNAL_ERROR, self::MESSAGE_INTERNAL_ERROR, 'server error', $exception);
		}
		return $this->createResponseString($response);
	}

	/**
	 * @param string $requestString
	 * @return MethodResponse
	 */
	protected function doHandleString($requestString) {
		if(!strlen($requestString)) {
			return new MethodFault(null, self::CODE_PARSE_ERROR, self::MESSAGE_PARSE_ERROR, 'empty request');
		}

		$data = json_decode($requestString, true);

		if(JSON_ERROR_NONE !== json_last_error()) {
			return new MethodFault(null, self::CODE_PARSE_ERROR, self::MESSAGE_PARSE_ERROR, 'not a JSON');
		}
		if(!is_array($data)) {
			return new MethodFault(null, self::CODE_INVALID_REQUEST, self::CODE_INVALID_REQUEST, 'not an array');
		}

		if($this->checkRequestIsBatch($data)) {
			return $this->handleBatchRequest($data);
		} else {
			return $this->handleSingleRequest($data);
		}
	}

	/**
	 * @param array $batchData
	 * @return BatchResponse
	 */
	protected function handleBatchRequest($batchData) {
		if(!is_array($batchData)) {
			return new MethodFault(null, self::CODE_INVALID_REQUEST, self::CODE_INVALID_REQUEST, 'not an array');
		}
		$responses = array();
		foreach($batchData as $requestData) {
			$responses[] = $this->handleSingleRequest($requestData);
		}
		return new BatchResponse($responses);
	}

	/**
	 * @param $requestData
	 * @return MethodResponse
	 */
	protected function handleSingleRequest($requestData) {
		if(!is_array($requestData)) {
			return new MethodFault(null, self::CODE_INVALID_REQUEST, self::CODE_INVALID_REQUEST, 'not an array');
		}

		$callId = isset($requestData['id']) ? $requestData['id'] : null;

		if(empty($requestData['jsonrpc']) || version_compare($requestData['jsonrpc'], '2.0') < 0) {
			return new MethodFault($callId, self::CODE_CUSTOM_VERSION, self::MESSAGE_CUSTOM_VERSION);
		}

		if(empty($requestData['method'])) {
			return new MethodFault($callId, self::CODE_INVALID_REQUEST, self::MESSAGE_INVALID_REQUEST, '`method` property missing');
		}

		$call = new MethodCall(
			$requestData['method'],
			isset($requestData['params']) ? $requestData['params'] : array(),
			$callId
		);
		return $this->handleCall($call);
	}

	/**
	 * @param MethodCall $call
	 * @return MethodResponse
	 */
	protected function handleCall(MethodCall $call) {
		if(!$this->service->hasFunction($call->getName())) {
			return new MethodFault($call->getCallId(), self::CODE_METHOD_NOT_FOUND, self::MESSAGE_METHOD_NOT_FOUND);
		}
		$function = $this->service->getFunction($call->getName());

		try {
			$result = $function->invoke(
				FunctionUtility::normalizeArguments($function, $call->getParameters())
			);
			return new MethodReturn($call->getCallId(), $result);
		} catch(BadArgumentsException $exception) {
			$data = array();
			foreach($exception->getIssues() as $issue) {
				$message = 'bad';
				switch(true) {
					case $issue instanceof ArgumentInvalid:
						$message = $issue->getValidationMessage();
						break;
					case $issue instanceof ArgumentNotSupported:
						$message = 'not supported';
						break;
					case $issue instanceof ArgumentMissing:
						$message = 'missing';
						break;
				}
				$data[] = array(
					'argument' => $issue->getName(),
					'message' => $message,
				);
			}
			return new MethodFault(
				$call->getCallId(),
				self::CODE_INVALID_PARAMETERS,
				self::MESSAGE_INVALID_PARAMETERS,
				$data,
				$exception
			);
		} catch(FunctionException $exception) {
			return new MethodFault(
				$call->getCallId(),
				$exception->getCode(),
				$exception->getMessage(),
				$exception->getData(),
				$exception
			);
		} catch(\Exception $exception) {
			return new MethodFault(
				$call->getCallId(),
				self::CODE_CUSTOM_EXCEPTION,
				self::MESSAGE_CUSTOM_EXCEPTION,
				'method error',
				$exception
			);
		}
	}

	/**
	 * @param MethodResponse $methodResponse
	 * @return string
	 */
	protected function createResponseString(MethodResponse $methodResponse) {
		return $this->createPlainResponse(
			$this->createStructuredResponse($methodResponse)
		);
	}

	/**
	 * @param MethodResponse $methodResponse
	 * @return string
	 * @throws \Exception
	 */
	protected function createStructuredResponse(MethodResponse $methodResponse) {
		if($methodResponse instanceof BatchResponse) {
			$structuredResponse = array();
			foreach($methodResponse->getResponses() as $singleResponse) {
				if($singleResponse instanceof BatchResponse) {
					throw new \Exception('Nested batch responses not allowed');
				}
				$structuredMethodResponse = $this->createSingleStructuredResponse($singleResponse);
				if($structuredMethodResponse) {
					$structuredResponse[] = $structuredMethodResponse;
				}
			}
			return $structuredResponse;
		}
		return $this->createSingleStructuredResponse($methodResponse);
	}

	/**
	 * @param MethodResponse $methodResponse
	 * @return string
	 * @throws \Exception
	 */
	protected function createSingleStructuredResponse(MethodResponse $methodResponse) {
		$callId = $methodResponse->getCallId();

		$structuredResponse = array(
			'jsonrpc' => '2.0',
		);
		if(!is_null($callId)) {
			$structuredResponse['id'] = $callId;
		}

		if($methodResponse instanceof MethodFault) {
			$error = array(
				'code' => $methodResponse->getCode(),
				'message' => $methodResponse->getMessage(),
			);
			$errorData = $methodResponse->getData();
			if(!is_null($errorData)) {
				$error['data'] = $errorData;
			}
			$structuredResponse['error'] = $error;
		} else
		if($methodResponse instanceof MethodReturn) {
			if(is_null($callId)) {
				return null;
			}
			$structuredResponse['result'] = $this->createStructuredReturnValue(
				$methodResponse->getReturnValue()
			);
		} else {
			throw new \Exception('Unknown response type');
		}
		return $structuredResponse;
	}

	/**
	 * @param mixed $returnValue
	 * @return mixed
	 */
	protected function createStructuredReturnValue($returnValue) {
		// TODO: check type and convert to primitive or structured value or just throw exception
		// you might want to override this method to make return value serializable
		return $returnValue;
	}

	/**
	 * @param mixed $structuredResponse
	 * @return string
	 */
	protected function createPlainResponse($structuredResponse) {
		if(is_null($structuredResponse)) {
			return '';
		} else {
			return json_encode($structuredResponse);
		}
	}

	/**
	 * @param array $requestData
	 * @return bool
	 */
	protected function checkRequestIsBatch(array $requestData) {
		return !ArrayUtility::isAssociative($requestData);
	}
}
