<?php

namespace SmartHosting\API1\Exceptions;

class Exception extends \Exception
{
	protected $_apiResponse;
	
	public function __construct($messageBody, $code = 0, Exception $previous = null)
	{
		if(is_array($messageBody))
		{
			$this->_apiResponse = $messageBody;
			$message = $this->_apiResponse["msg"];
		}
		else
		{
			$message = $messageBody;
		}
		
		// Make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
	
	public function _apiResponse()
	{
		return $this->_apiResponse;
	}
}

class AuthenticationException extends Exception {};
class AuthorisationException extends Exception {};
class FatalAPIException extends Exception {};
class UserInputAPIException extends Exception {};
class HTTPException extends Exception {};
class NotFoundException extends Exception {};
