<?php
namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken{
    public static function CreateToken($userEmail, $userId)
    {
        $key = env('JWT_KEY');


        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time()+60*24*30,   //30 days
            'userEmail' => $userEmail,
            'userId' => $userId
        ];

        return JWT::encode($payload, $key, 'HS256');
    }//end method


    public static function VerifyToken($token)
    {
        try{
            if($token === null){
                return 'unauthorized';
            }else{
                $key = env('JWT_KEY');
                $decoded = JWT::decode($token, new Key($key, 'HS256'));

                return $decoded;
            }
        }catch(\Exception $e){
            return 'unauthorized';
        }
    }

    public static function CreateTokenForSetPassword($userEmail):string
    {
         $key = env('JWT_KEY');


        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time()+60*24*30,   //30 days
            'userEmail' => $userEmail,
            'userId' => '0'
        ];

        return JWT::encode($payload, $key, 'HS256');

    }//end method

}
