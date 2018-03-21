<?php
namespace App\Exceptions\Api;
use App\Services\APIException;

class UserNotFoundException extends APIException
{
	const ERR_CODE = 401;

	public function __construct( $message = null )
	{
		parent::__construct( $message );
	}
}