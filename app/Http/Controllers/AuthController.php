<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
 
    /**
     * login 
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:150',
            'password' => 'required|string|min:6|max:150',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $user = User::where('email', $request->input('email'))->first();
      
         //Check password hash
	    if($user  && Hash::check($request->input('password'), $user->password)){
            $now_seconds = time();
            $payload = [
                'user' => $user,
                "iat" => $now_seconds,
                "exp" => $now_seconds+(60*60),
            ];
            
          $token = JWT::encode($payload, env("JWT_SECRET") , 'HS256');
          return response()->json([
            'status' => 'success',
            'data' => $user,
           'token' => $token
           ], 201);
    
	    } else {
            return response()->json([
                'status' => 'error',
                'message' => 'wrong credintals'
               
               ], 400);
	   }
       
      }
     

     /**
     * Store a newly created user in DB.
     */
    public function register(Request $request)
    {

     $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:6|max:150|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $user = User::create([
           
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        $now_seconds = time();
        $payload = [
            'user' => $user,
            "iat" => $now_seconds,
            "exp" => $now_seconds+(60*60),
        ];
        
      $token = JWT::encode($payload, env("JWT_SECRET") , 'HS256');
      return response()->json([
        'status' => 'success',
        'data' => $user,
        'token' => $token
    ], 201);
    }



   
}
