<?php
namespace App\Exceptions\Api;
use App\Services\APIException;

class UnauthenticatedException extends APIException
{
	const ERR_CODE = 401;

	public function __construct( $message = 'Token is required' )
	{
		parent::__construct( $message );
	}
}