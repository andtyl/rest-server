<?php
namespace RestServer\Exception;

use Exception;

class RestServerException extends Exception
{
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const INTERNAL_SERVER_ERROR = 500;
    const UNAVAILABLE = 503;

    protected $http_code;

    protected $error_code;

    protected $error_message_developer;

    protected $error_message_user;
    
	public function __construct($http_code = 500, $error_code = 0, $error_message_developer = "", $error_message_user = "")
	{
		$this->http_code = $http_code;
		$this->error_code = $error_code;
		$this->error_message_developer = $error_message_developer;
		$this->error_message_user = $error_message_user;
		parent::__construct($error_message_developer, $error_code);
	}

	public function getHttpCode()
	{
		return $this->http_code;
	}	
	
	public function getErrorCode()
	{
		return $this->error_code;
	}

	public function getErrorMessageDeveloper()
	{
		return $this->error_message_developer;
	}	
	
	public function getErrorMessageUser()
	{
		return $this->error_message_user;
	}

	public function asArray()
	{
		return array(
			'http_code' => $this->getHttpCode(),
			'error_code' => $this->getErrorCode(),
			'error_message_developer' => $this->getErrorMessageDeveloper(),
			'error_message_user' => $this->getErrorMessageUser()
		);		
	}
}