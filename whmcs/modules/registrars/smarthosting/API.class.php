<?php

namespace SmartHosting\API1;

use \SmartHosting\API1\Exceptions\Exception;
use \SmartHosting\API1\Exceptions\AuthenticationException;
use \SmartHosting\API1\Exceptions\AuthorisationException;
use \SmartHosting\API1\Exceptions\FatalAPIException;
use \SmartHosting\API1\Exceptions\UserInputAPIException;
use \SmartHosting\API1\Exceptions\HTTPException;
use \SmartHosting\API1\Exceptions\NotFoundException;

class API
{
	private $_username;
	private $_secret;
	private $_ipAddress;
	
	function __construct($username, $secret)
	{
		$this->_path = dirname(__FILE__) . "/";
		require_once($this->_path . "Exceptions.php");
		
		if(!$username) throw new AuthenticationException("No API username supplied");
		if(!$secret) throw new AuthenticationException("No API secret supplied");
		
		$this->_username = $username;
		$this->_secret = $secret;
	}

	function __destruct()
	{
		//
	}
	
	public function _ipAddress()
	{
		if($this->_ipAddress) return $this->_ipAddress;
		$this->_ipAddress = $this->call("ping", "GET", [], [], ["authenticated_request" => false])["payload"]["accessing_ip"];
		return $this->_ipAddress;
	}
	
	private function _username()
	{
		return $this->_username;
	}
	
	private function _password()
	{
		return md5($this->_secret . $this->_ipAddress());
	}
	
	public function call($route, $method, $url_params = [], $body = [], $options = [])
	{
		$excludedFromLogging = [];
		
		$options = array_merge(
		[
			"authenticated_request" => true
		], $options);
		
		// Get cURL resource
		$cURL = curl_init();
		
		// Set url
		curl_setopt($cURL, CURLOPT_URL, "https://www.bestwebhosting.co.uk/api/1/{$route}?" . http_build_query($url_params));
		
		// Set method
		curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "POST");
		
		// Set options
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
		
		// Set headers
		$headers = [];
		$headers[] = "X-HTTP-Method-Override: {$method}";
		$headers[] = "Content-Type: application/json; charset=utf-8";
		
		if($options["authenticated_request"])
		{
			$authHeaderValue = base64_encode($this->_username() . ":" . $this->_password());
			$excludedFromLogging[] = $authHeaderValue;
			$headers[] = "Authorization: Basic {$authHeaderValue}";
		}
		
		curl_setopt($cURL, CURLOPT_HTTPHEADER, $headers);
		
		// Create body
		$body = json_encode($body);
		
		// Set body
		curl_setopt($cURL, CURLOPT_POST, 1);
		curl_setopt($cURL, CURLOPT_POSTFIELDS, $body);
		
		// Send the request & save response to $response
		$response = curl_exec($cURL);
		
		// Got a response?
		if(!$response) throw new HTTPException("No response: " . curl_error($cURL) . " - Code: " . curl_errno($cURL));
		
		// Get the body
		$response = json_decode($response, true);
		$response["http_code"] = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
		
		// Log the result
		\logModuleCall("smarthosting", "{$method} {$route}", ["params" => $url_params, "body" => $body, "options" => $options, "headers" => $headers], null, ["response" => $response], $excludedFromLogging);
		
		// Check the response code
		$responseonseCode = (string)$response["http_code"];
		if($responseonseCode[0] == 5) throw new FatalAPIException($response);
		if($responseonseCode == 401) throw new AuthenticationException($response);
		if($responseonseCode == 403) throw new AuthorisationException($response);
		if($responseonseCode == 404) throw new NotFoundException($response);
		if($responseonseCode[0] != 2) throw new UserInputAPIException($response);
		
		// Done
		curl_close($cURL);
		
		return $response;
	}
}

