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

    /** @var int HTTP code */
    protected $http_code;

    /** @var int Error code */
    protected $error_code;

    /** @var string Error message to the developer of the client */
    protected $error_message_developer;

    /** @var string Error message to the user of the client */
    protected $error_message_user;
    
    /**
     * Constructor
     *
     * @param int $http_code HTTP code
     * @param int $error_code Error code
     * @param string $error_message_developer Error message to the developer of the client
     * @param string $error_message_user Error message to the user of the client
     */
	public function __construct($http_code = 500, $error_code = 0, $error_message_developer = "", $error_message_user = "")
	{
		$this->http_code = $http_code;
		$this->error_code = $error_code;
		$this->error_message_developer = $error_message_developer;
		$this->error_message_user = $error_message_user;
		parent::__construct($error_message_developer, $error_code);
	}

    /**
     * Get the HTTP code
     *
     * @return int
     */
	public function getHttpCode()
	{
		return $this->http_code;
	}	
	
    /**
     * Get the error code
     *
     * @return int|null
     */
	public function getErrorCode()
	{
		return $this->error_code;
	}

    /**
     * Get the error message to the developer
     *
     * @return string
     */
	public function getErrorMessageDeveloper()
	{
		return $this->error_message_developer;
	}	
	
    /**
     * Get the error message to the user
     *
     * @return string
     */
	public function getErrorMessageUser()
	{
		return $this->error_message_user;
	}

    /**
     * Return the Exception as a Array
     *
     * @return array
     */
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