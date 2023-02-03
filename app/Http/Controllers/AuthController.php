<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // called automatically when an object of a class is created => sets up middleware for the auth process
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        // auth::api ensures that the api req is authenticated and only allows access to the api if the user is authenticated/has a valid token
        // except => ['login', 'register'] => allows access to the login and register methods without a token
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        if(! $token = auth()->attempt($validator->validated())){
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized, the username or password is incorrect'
            ], 401);
        }

        return $this->createNewToken($token);
    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'User successfully signed out'
        ], 200);
    }

    public function refresh(){
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token);

        return $this->createNewToken($newToken);
    }

    public function userProfile(){
        return response()->json(auth()->user());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl'),
            'user' => auth()->user()
        ]);
    }
}
