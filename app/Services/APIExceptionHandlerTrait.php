<?php
namespace App\Services;

use Illuminate\Support\Facades\Request;

trait APIExceptionHandlerTrait
{
	/**
	 * Native Exception Data Mapping
	 */
	protected static $exception_data_mapping = [
		'NotFoundHttpException' => [
			'message' => 'Not Found',
			'error_code' => 404,
		],

		'TokenExpiredException' => [
			'message' => 'Token Expired',
			'error_code' => 485,
		],

		'TokenInvalidException' => [
			'message' => 'Invalid Token',
			'error_code' => 488,
		],
		'ModelNotFoundException' => [
			'message' => 'Record not found',
			'error_code' => 404,
		],
	];
	/**
	 * Detect if request is API Request Or Not
	 * @return boolean [description]
	 */
	public function isApiRequest()
	{
        return ( Request::root() == env( 'API_URL' ) );
	}


	/**
	 * Detect if to handle exception With Json Response
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function handleWithJsonResponse()
	{
		return ( $this->isApiRequest() && env( 'ENABLE_API_EXCEPTION', true ) );
	}


	/**
	 * Get Exception JSON Response
	 * @param  Exception $exception [description]
	 * @return [type]               [description]
	 */
	public function getJsonResponse( \Exception $exception )
	{
		$exception_data = $this->getExceptionData( $exception );

		// dd( $exception_data );

		return response()->json( [
		            'success' => false,
		            'message' => $exception_data[ 'message' ], //$exception->getMessage() ? $exception->getMessage() : 'Route Not Found Exception',
		            'error_code' => $exception_data[ 'error_code' ],
	            ], $exception_data[ 'error_code' ] );
	}


	/**
	 * Get Exception Message And Response Error Code [Status Code]
	 * @param  Exception $exception [description]
	 * @return [type]               [description]
	 */
	private function getExceptionData( \Exception $exception )
	{
		$exception = class_basename( $exception );

		// dd( $exception );

		if( isset( self::$exception_data_mapping[ $exception ] ) )
			return self::$exception_data_mapping[ $exception ];
		else
			// return [ 'error_code' => 400, 'message' => $exception->getMessage() ? $exception->getMessage() : 'Exception has No Message' ];
			return [ 'error_code' => 400, 'message' => 'Exception has No Message' ];
	}

	
}
