<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;


class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
           
           $header = $request->header('Authorization');
           $jwttoken = explode(" ", $header);
          
           $payload = JWT::decode($jwttoken[1], new Key(env("JWT_SECRET"), 'HS256'));
         
            $request->user = $payload->user;
           
            return $next($request);
        }catch (SignatureInvalidException $e) {
            return response()->json([
                'status' => 'error',
               'message' => 'token sign invalid'
            ], 401);
        } catch (ExpiredException $e) {
            return response()->json([
                'status' => 'error',
               'message' => 'token expired'
            ], 401);
      } catch(Exception $e){
            return response()->json([
                'status' => 'error',
               'message' => 'token not found'
            ], 401);
           }
      }

 
}