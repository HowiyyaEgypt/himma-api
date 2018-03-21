<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use App\User;

use App\Http\Resources\User\UserResource;

use App\Exceptions\Api\ValidationException;
use App\Exceptions\Api\InvalidCredentialsException;

class UserController extends Controller
{
    /**
     * Register a new user
     * 
     * TODO: store FCM token
     * 
     * @returns User Resource + JWT so he can login
     */
    public function register(Request $request)
    {
        // TODO: store FCM token
        
        $validator = Validator::make($request->all(), [
            'name'      => 'required|min:2',
            'password'  => 'required|min:6',
            'gender'    => 'required|in:1,2',
            'email'     => 'required|email|unique:users,email'
        ]);

        if ( $validator->fails() ){
            throw new ValidationException($validator->errors()->first());       
        }

        $user = User::create($request->only('name', 'password', 'email', 'gender'));

        return (new UserResource($user))
                ->response()
                ->setStatusCode(200);
    }
    

    /**
     * Login via app
     * 
     * TODO: store new FCM token
     * 
     * @returns User Resource + JWT 
     */
    public function login(Request $request)
    {
        // TODO: store FCM token
        
        $validator = Validator::make($request->all(), [
            'password'  => 'required',
            'email'     => 'required|email|exists:users,email'
        ]);

        if ( $validator->fails() ){
            throw new InvalidCredentialsException($validator->errors()->first());       
        }

        $email = $request->get('email');
        $password = $request->get('password');

        $user = User::where('email', $email)->first();

        if(empty($user))
            throw new InvalidCredentialsException('Invalid Email');

        if(!empty($user) && !\Hash::check($password, $user->password))
            throw new InvalidCredentialsException('Invalid Password');
        
        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }
}
