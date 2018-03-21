<?php
namespace App\Services;
use Exception;

class APIException extends Exception
{
	protected $exception_message = null;

	public function __construct( $message = null )
	{
		$this->exception_message = $message ? $message : 'Exception has no message';
		parent::__construct( $message );
	}

	public function apiHandler()
	{
		return response()->json([
				'success' => false,
				'message' => $this->exception_message,
				'error_code' => static::ERR_CODE,
			], static::ERR_CODE);
	}
}
