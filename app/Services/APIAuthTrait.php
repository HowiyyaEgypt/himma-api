<?php
/**
 * @author 	Ahmed Saad, 15 Nov 2016
 *
 * Trait To Get Authenticated user from request token
 * otherwise throws Exceptions which will be handled with exception handler render method
 */
namespace App\Services;

use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Utils as JWTUtils;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidExce;

use App\Exceptions\Api\UnauthenticatedException;
use App\Exceptions\Api\UserNotFoundException;

trait APIAuthTrait
{
	static $payload;

	/**
	 * Get User From Request Token
	 *
	 * @return App\User OR Error Json Response
	 */
	public function APIAuthenticate( $is_refresh = false )
	{

		if( !(\Request::get( 'token' )) && !(app('request')->bearerToken()) )
			throw new UnauthenticatedException;

        try{
	        if ( ! ( $user = JWTAuth::parseToken()->authenticate() ) )
	        {
	            throw new UserNotFoundException;
	        }
        }
        catch( TokenExpiredException $e ){
        	// Detecte Expiration Time
            throw new TokenExpiredException('Token has expired', 400);
        }

        return $user;
	}
}
