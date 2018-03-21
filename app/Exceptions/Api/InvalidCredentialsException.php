<?php

namespace App\Exceptions\Api;

use App\Services\APIException;

class InvalidCredentialsException extends APIException
{
    const ERR_CODE = 481;

    public function __construct( $message = 'Invalid Credentials' )
	{
		parent::__construct( $message );
	}
}
